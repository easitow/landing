import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

// Rate limiting storage (in-memory, resets on cold starts)
const rateLimitStore = new Map();

// Clean up old entries from rate limit store
function cleanupRateLimit(ip) {
  const now = Date.now();
  const hourAgo = now - 3600000; // 1 hour in milliseconds
  
  if (rateLimitStore.has(ip)) {
    const timestamps = rateLimitStore.get(ip).filter(time => time > hourAgo);
    if (timestamps.length > 0) {
      rateLimitStore.set(ip, timestamps);
    } else {
      rateLimitStore.delete(ip);
    }
    return timestamps.length;
  }
  return 0;
}

// Add rate limit entry
function addRateLimit(ip) {
  const timestamps = rateLimitStore.get(ip) || [];
  timestamps.push(Date.now());
  rateLimitStore.set(ip, timestamps);
}

export default async function handler(req, res) {
  // Only allow POST requests
  if (req.method !== 'POST') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  try {
    // Get IP address
    const ip = req.headers['x-forwarded-for']?.split(',')[0].trim() || 
               req.headers['x-real-ip'] || 
               req.socket.remoteAddress || 
               'unknown';

    // Check rate limit (max 10 per hour per IP)
    const submissionCount = cleanupRateLimit(ip);
    if (submissionCount >= 10) {
      return res.status(429).json({ 
        error: 'Rate limit exceeded. Please try again later.' 
      });
    }

    // Get email from request body
    const { email, website } = req.body;

    // Honeypot check - if website field is filled, it's likely a bot
    if (website) {
      addRateLimit(ip);
      return res.status(200).json({ success: true });
    }

    // Validate email
    if (!email || typeof email !== 'string') {
      return res.status(400).json({ error: 'Email is required' });
    }

    // Basic email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      return res.status(400).json({ error: 'Invalid email address' });
    }

    // Normalize email (lowercase and trim)
    const normalizedEmail = email.toLowerCase().trim();

    try {
      // Try to create user in database
      const user = await prisma.user.create({
        data: {
          email: normalizedEmail,
        },
      });

      // Add to rate limit
      addRateLimit(ip);

      return res.status(200).json({ 
        success: true, 
        message: 'Successfully subscribed!',
        userId: user.id 
      });

    } catch (dbError) {
      // Check if it's a unique constraint error (duplicate email)
      if (dbError.code === 'P2002') {
        addRateLimit(ip);
        return res.status(200).json({ 
          success: true, 
          message: 'You are already subscribed!' 
        });
      }
      
      // Re-throw other database errors
      throw dbError;
    }

  } catch (error) {
    console.error('Error processing subscription:', error);
    return res.status(500).json({ 
      error: 'An error occurred. Please try again later.' 
    });
  } finally {
    // Disconnect Prisma Client (important for serverless)
    await prisma.$disconnect();
  }
}
