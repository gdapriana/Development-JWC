<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forest Retreat Header Showcase</title>
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    
    <!-- GSAP CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <style>
        :root {
            --primtext: 'Plus Jakarta Sans', sans-serif;
            --font-serif: 'Playfair Display', Georgia, serif;
            --bg-forest: #0d1f1a;
            --accent-gold: #dfb76c;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--bg-forest);
            color: #ffffff;
            font-family: var(--primtext);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* Beautiful glowing dynamic backdrop for forest context */
        .showcase-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: radial-gradient(circle at 90% 10%, rgba(223, 183, 108, 0.06) 0%, transparent 60%),
                        radial-gradient(circle at 10% 90%, rgba(255, 255, 255, 0.02) 0%, transparent 40%),
                        var(--bg-forest);
            z-index: -1;
        }

        /* Full-screen Hero */
        .hero {
            position: relative;
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 24px;
            box-sizing: border-box;
        }

        .hero-content {
            max-width: 900px;
            opacity: 0;
            transform: translateY(30px);
        }

        .hero-badge {
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 4px;
            color: var(--accent-gold);
            text-transform: uppercase;
            margin-bottom: 24px;
            display: inline-block;
        }

        h1 {
            font-family: var(--font-serif);
            font-size: clamp(2.5rem, 6.5vw, 5rem);
            font-weight: 400;
            line-height: 1.1;
            margin: 0 0 24px 0;
        }

        h1 span {
            font-style: italic;
            color: var(--accent-gold);
        }

        p.hero-desc {
            font-size: clamp(1.05rem, 2vw, 1.3rem);
            color: rgba(255, 255, 255, 0.7);
            max-width: 680px;
            margin: 0 auto 36px auto;
            line-height: 1.6;
        }

        .btn-showcase {
            font-family: var(--primtext);
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 2px;
            color: var(--bg-forest);
            background-color: var(--accent-gold);
            border: 1px solid var(--accent-gold);
            padding: 14px 32px;
            border-radius: 99px;
            text-decoration: none;
            text-transform: uppercase;
            transition: all 0.3s ease;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(223, 183, 108, 0.2);
        }

        .btn-showcase:hover {
            background-color: transparent;
            color: var(--accent-gold);
            box-shadow: 0 6px 20px rgba(223, 183, 108, 0.4);
            text-decoration: none;
        }

        .scroll-down {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.8rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.4);
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translate(-50%, 0); }
            40% { transform: translate(-50%, -10px); }
            60% { transform: translate(-50%, -5px); }
        }

        /* Features Section */
        .features-section {
            padding: 100px 24px;
            background-color: #081411;
            text-align: center;
        }

        .features-container {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 56px;
        }

        .features-container h2 {
            font-family: var(--font-serif);
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 500;
            margin: 0 0 16px 0;
        }

        .features-container p.intro-desc {
            font-size: 1.15rem;
            color: rgba(255, 255, 255, 0.6);
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .grid-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 32px;
        }

        .feature-card {
            background-color: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.04);
            padding: 40px;
            border-radius: 16px;
            text-align: left;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            border-color: var(--accent-gold);
            background-color: rgba(255, 255, 255, 0.04);
            transform: translateY(-5px);
        }

        .feature-num {
            font-family: var(--font-serif);
            font-style: italic;
            font-size: 2rem;
            color: var(--accent-gold);
            margin-bottom: 20px;
            display: block;
        }

        .feature-card h3 {
            font-family: var(--font-serif);
            font-size: 1.4rem;
            margin: 0 0 12px 0;
            font-weight: 500;
        }

        .feature-card p {
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.6);
        }
    </style>
</head>
<body>

    <div class="showcase-bg"></div>

    <?php
    // Include the Forest Retreat Header Component
    include __DIR__ . '/header_components.php';
    ?>

    <!-- Hero -->
    <section class="hero" id="home">
        <div class="hero-content">
            <span class="hero-badge">Ecological Luxury Retreat</span>
            <h1>Reconnect with <span>Nature</span>.</h1>
            <p class="hero-desc">An organic, floating header style featuring center navigation, glided underlines, and a stunning 3D Curtain Fall curtain overlay animation triggered by the circular hamburger menu.</p>
            <a href="#showcase" class="btn-showcase">Begin Journey</a>
        </div>
        <div class="scroll-down">Scroll Down to Explore Features</div>
    </section>

    <!-- Features -->
    <section class="features-section" id="showcase">
        <div class="features-container">
            <div>
                <h2>Immersive Interaction</h2>
                <p class="intro-desc">Witness a unique combination of organic forest shapes and advanced web animation kinematics.</p>
            </div>

            <div class="grid-features">
                <div class="feature-card">
                    <span class="feature-num">01</span>
                    <h3>3D Curtain Fall</h3>
                    <p>Clicking the circular hamburger drops down a dark green full-screen curtain overlay (`scaleY` zoom) from the top ceiling.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-num">02</span>
                    <h3>3D Flip Link Unfolds</h3>
                    <p>Large overlay link items unfold from -90 degrees on the X-axis (`rotationX` flip) in a staggered, elastic entrance.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-num">03</span>
                    <h3>Staggered Visual Cards</h3>
                    <p>The right side of the curtain displays luxury retreat service cards (Villas, Spa, Dining) that slide up with smooth delay offsets.</p>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Animate hero text on load
            gsap.to('.hero-content', {
                opacity: 1,
                y: 0,
                duration: 1.2,
                ease: 'power3.out',
                delay: 0.5
            });
        });
    </script>
</body>
</html>
