module.exports = async function handler(req, res) {
  const debugInfo = {
    method: req.method,
    hasBody: !!req.body,
    body: req.body,
    hasDatabaseUrl: !!process.env.DATABASE_URL,
    databaseUrlPrefix: process.env.DATABASE_URL ? process.env.DATABASE_URL.substring(0, 10) + '...' : 'NOT SET',
    nodeVersion: process.version,
    env: process.env.NODE_ENV
  };

  // Try to load Prisma
  try {
    const { PrismaClient } = require('@prisma/client');
    debugInfo.prismaLoaded = true;
    
    const prisma = new PrismaClient();
    
    // Try to connect
    try {
      await prisma.$connect();
      debugInfo.databaseConnected = true;
      
      // Try a simple query
      try {
        const count = await prisma.user.count();
        debugInfo.userCount = count;
        debugInfo.queryWorked = true;
      } catch (queryError) {
        debugInfo.queryError = queryError.message;
        debugInfo.queryErrorCode = queryError.code;
      }
      
      await prisma.$disconnect();
    } catch (connectError) {
      debugInfo.databaseConnected = false;
      debugInfo.connectionError = connectError.message;
    }
  } catch (prismaError) {
    debugInfo.prismaLoaded = false;
    debugInfo.prismaError = prismaError.message;
  }

  return res.status(200).json(debugInfo);
}
