<?php
session_start();
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

$bf_id = getenv("BRANDFETCH_CLIENT_ID");

$success = isset($_GET['success']);
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasiTow - Launching March 2026</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Fraunces:wght@600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Italiana&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0a0a0a;
            color: #ffffff;
            line-height: 1.6;
            overflow-x: hidden;
            overflow-y: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            height: 100vh;
        }
        
        .page-wrapper {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .container {
            max-width: 70%;
            margin: 0 auto;
            padding: 12vh 32px 0;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .logo {
            font-size: 20px;
            font-weight: 600;
            letter-spacing: -0.01em;
            margin-bottom: 12vh;
            color: #ffffff;
        }
        
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .hero-section {
            margin-bottom: auto;
        }
        
        h1 {
            font-family: 'Inter', sans-serif;
            font-size: 68px;
            font-weight: 500;
            line-height: 1.05;
            letter-spacing: -0.03em;
            margin-bottom: 20px;
            color: #ffffff;
        }
        
        .subheading {
            font-size: 18px;
            color: #999999;
            margin-bottom: 14px;
            font-weight: 400;
            line-height: 1.6;
            max-width: 520px;
        }
        
        .launch-date {
            display: inline-flex;
            align-items: center;
            font-size: 13px;
            color: #777777;
            font-weight: 400;
            padding: 7px 15px;
            background: rgba(255, 255, 255, 0.04);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        .form-wrapper {
            padding: 40px 0;
            display: flex;
            justify-content: center;
        }
        
        form {
            width: 100%;
            max-width: 480px;
        }
        
        .input-group {
            display: flex;
            gap: 10px;
        }
        
        input[type="email"] {
            flex: 1;
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            font-size: 15px;
            color: #fff;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }
        
        input[type="email"]:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.06);
        }
        
        input[type="email"]::placeholder {
            color: #666666;
        }
        
        button {
            padding: 15px 32px;
            background: #ffffff;
            color: #000000;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
            font-family: 'Inter', sans-serif;
        }
        
        button:hover {
            background: #f0f0f0;
            transform: translateY(-1px);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .message {
            padding: 10px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 12px;
            max-width: 480px;
        }
        
        .success {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.2);
        }
        
        .error {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .honeypot {
            position: absolute;
            left: -9999px;
        }
        
        .trust-section {
            width: 100vw;
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            padding: 36px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            background: #0a0a0a;
        }
        
        .trust-label {
            font-size: 11px;
            font-weight: 400;
            letter-spacing: 0.1em;
            color: #666666;
            margin-bottom: 28px;
            text-align: center;
            text-transform: uppercase;
        }
        
        .logos-container {
            width: 100%;
            overflow: hidden;
            padding: 4px 0;
        }
        
        .logos-scroll {
            display: flex;
            width: max-content;
            gap: 50px;
            animation: scroll 20s linear infinite;
        }
        
        .logo-item {
            flex: 0 0 auto;
            width: 150px;
            height: 80px;
            display: flex;
			flex-direction: row;
            align-items: center;
            justify-content: center;
            opacity: 0.45;
            transition: opacity 0.3s;
            gap: 10px;
        }
        
        .logo-item img {
            height: 34px;
            width: auto;
            color: #666;
            object-fit: contain;
        }
        
        /*.logo-item {
            display: flex;
            flex-direction: column;
            flex: 0 0 auto;
            align-items: center;
            opacity: 0.25;
            transition: opacity 0.3s;
            min-width: 90px;
        }*/
        
        .logo-item:hover {
            opacity: 0.75;
            cursor: pointer;
        }
        
        /*.logo-item img {
            height: 32px;
            width: auto;
            object-fit: contain;
        }*/
        
        .logo-name {
            font-size: 18px;
            color: #999;
            font-weight: 400;
            white-space: nowrap;
        }
        
        @keyframes scroll {
            0% {
                transform: translateX(0);
            }
            
            100% {
                transform: translateX(-50%);
            }
        }
        
        .italiana-regular {
            font-family: "Italiana", sans-serif;
            font-weight: 400;
            font-style: normal;
            letter-spacing: -0.04em;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10vh 24px 0;
            }
            
            .logo {
                font-size: 18px;
                margin-bottom: 10vh;
            }
            
            h1 {
                font-size: 48px;
            }
            
            .subheading {
                font-size: 16px;
            }
            
            .input-group {
                flex-direction: column;
            }
            
            button {
                width: 100%;
            }
            
            .trust-section {
                padding: 32px 0;
            }
            
            /* .logos-scroll {
                gap: 44px;
            } */
            
            .logo-item img {
                height: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="container">
            <div class="logo">EasiTow</div>
            
            <div class="main-content">
                <div class="hero-section">
                    <h1 class="italiana-regular">Modern Towing Software</h1>
                    <p class="subheading">Built for operators who need reliable tools that work when it matters.</p>
                    <div class="launch-date">Launching March 2026</div>
                </div>
                

                
                <?php if ($success): ?>
                	<div class="message success">You're on the list. We'll notify you at launch.</div>
                <?php endif; ?>

                <?php if ($error === 'invalid'): ?>
                	<div class="message error">Please enter a valid email address.</div>
                <?php elseif ($error === 'rate_limit'): ?>
                	<div class="message error">Too many requests. Try again later.</div>
                <?php endif; ?>
                
                <div class="form-wrapper">
                    
                    <form action="demo_list.php" method="POST">
                        <div class="input-group">
                            <input type="email" name="email" placeholder="Enter your email" required />
                            <button type="submit">Get Access</button>
                        </div>
                        <input type="text" name="website" class="honeypot" tabindex="-1" autocomplete="off" />
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>" />
                    </form>
                    
                </div>
            </div>
        </div>
        
        <div class="trust-section">
            <div class="trust-label">Powered by modern technology</div>
            <div class="logos-container">
                <div class="logos-scroll">
                    <div class="logo-item">
                        <img src="https://cdn.simpleicons.org/flutter/ffffff" alt="Flutter" />
                        <span class="logo-name">Flutter</span>
                    </div>
                    <div class="logo-item">
                        <img src="https://cdn.simpleicons.org/laravel/ffffff" alt="Laravel" />
                        <span class="logo-name">Laravel</span>
                    </div>
                    <div class="logo-item">
                        <img src="https://datf9eqtimx2sf0v.public.blob.vercel-storage.com/aws.svg" alt="AWS" />
                        <span class="logo-name">AWS</span>
                    </div>
                    <div class="logo-item">
                        <img src="https://cdn.simpleicons.org/postgresql/ffffff" alt="PostgreSQL" />
                        <span class="logo-name">PostgreSQL</span>
                    </div>
                    <div class="logo-item">
                        <img src="https://cdn.simpleicons.org/stripe/ffffff" alt="Stripe" />
                        <span class="logo-name">Stripe</span>
                    </div>
                    <div class="logo-item">
                        <img src="https://cdn.simpleicons.org/googlemaps/ffffff" alt="Google Maps" />
                        <span class="logo-name">Google Maps</span>
                    </div>
                    <div class="logo-item">
                        <img src="https://cdn.simpleicons.org/docker/ffffff" alt="Docker" />
                        <span class="logo-name">Docker</span>
                    </div>
                    <div class="logo-item">
                        <img src="https://datf9eqtimx2sf0v.public.blob.vercel-storage.com/twilio.svg" alt="Twilio" />
                        <span class="logo-name">Twilio</span>
                    </div>
                    <!-- Duplicate set for seamless infinite loop -->
                    <div class="logo-item">
                        <img src="https://cdn.simpleicons.org/flutter/ffffff" alt="Flutter" />
                        <span class="logo-name">Flutter</span>
                    </div>
                    <div class="logo-item">
                        <img src="https://cdn.simpleicons.org/laravel/ffffff" alt="Laravel" />
                        <span class="logo-name">Laravel</span>
                    </div>
                    <div class="logo-item">
                        <img src="https://datf9eqtimx2sf0v.public.blob.vercel-storage.com/aws.svg" alt="AWS"/>
                        <span class="logo-name">AWS</span>
                    </div>
                    <div class="logo-item">
                        <img src="https://cdn.simpleicons.org/postgresql/ffffff" alt="PostgreSQL" />
                        <span class="logo-name">PostgreSQL</span>
                    </div>
                    <div class="logo-item">
                        <img src="https://cdn.simpleicons.org/stripe/ffffff" alt="Stripe" />
                        <span class="logo-name">Stripe</span>
                    </div>
                    <div class="logo-item">
                        <!-- <img src="https://cdn.simpleicons.org/googlemaps/ffffff" alt="Google Maps" /> -->
                        <img src="https://cdn.brandfetch.io/google.com?c=YOUR_CLIENT_ID" alt="Logo by Brandfetch" />
                        <span class="logo-name">Google Maps</span>
                    </div>
                    <div class="logo-item">
                        <img src="https://cdn.simpleicons.org/docker/ffffff" alt="Docker" />
                        <span class="logo-name">Docker</span>
                    </div>
                    <div class="logo-item">
                        <img src="https://datf9eqtimx2sf0v.public.blob.vercel-storage.com/twilio.svg" alt="Twilio" />
                        <span class="logo-name">Twilio</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>