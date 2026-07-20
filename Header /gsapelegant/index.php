<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elegant GSAP Header Showcase</title>
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    
    <!-- GSAP Animation Library CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <style>
        :root {
            --primtext: 'Plus Jakarta Sans', sans-serif;
            --font-serif: 'Playfair Display', Georgia, serif;
            --bg-dark: #0a0a0c;
            --text-light: #ffffff;
            --accent-gold: #c5a880;
            --accent-gold-hover: #b59870;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--bg-dark);
            color: var(--text-light);
            font-family: var(--primtext);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* Beautiful glowing dynamic backdrop for showcase context */
        .showcase-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: radial-gradient(circle at 80% 20%, rgba(197, 168, 128, 0.08) 0%, transparent 50%),
                        radial-gradient(circle at 10% 80%, rgba(99, 102, 241, 0.05) 0%, transparent 40%),
                        var(--bg-dark);
            z-index: -1;
        }

        /* Scrollable dummy sections to test scroll physics */
        section {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 24px;
            box-sizing: border-box;
            text-align: center;
            position: relative;
        }

        .content-card {
            max-width: 800px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 24px;
            padding: 48px;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            transform: translateY(20px);
            opacity: 0; /* Animated by GSAP on load */
        }

        h1 {
            font-family: var(--font-serif);
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            margin: 0 0 16px 0;
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: -0.5px;
        }

        h1 span {
            color: var(--accent-gold);
            font-style: italic;
        }

        p {
            font-size: clamp(1rem, 2vw, 1.25rem);
            color: rgba(255, 255, 255, 0.65);
            line-height: 1.6;
            margin: 0 0 32px 0;
        }

        .scroll-down {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.85rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translate(-50%, 0); }
            40% { transform: translate(-50%, -10px); }
            60% { transform: translate(-50%, -5px); }
        }

        .badge {
            background: rgba(197, 168, 128, 0.1);
            border: 1px solid rgba(197, 168, 128, 0.2);
            color: var(--accent-gold);
            padding: 6px 16px;
            border-radius: 99px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 24px;
            display: inline-block;
        }

        /* Feature grid styling */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            width: 100%;
            max-width: 1000px;
            margin-top: 48px;
        }

        .feature-item {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 32px;
            text-align: left;
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            background: rgba(255, 255, 255, 0.04);
            border-color: var(--accent-gold);
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 1.8rem;
            color: var(--accent-gold);
            margin-bottom: 16px;
        }

        .feature-item h3 {
            margin: 0 0 8px 0;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .feature-item p {
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.5;
        }
    </style>
</head>
<body>

    <div class="showcase-bg"></div>

    <?php
    // Include the GSAP Elegant Header component
    include __DIR__ . '/header_components.php';
    ?>

    <!-- Hero Section -->
    <section id="home">
        <div class="content-card hero-card">
            <span class="badge">Aura Creative Studio</span>
            <h1>Artistic <span>Motion</span> Meets Digital Design.</h1>
            <p>An elite, fluid website header option fully driven by GSAP animations, featuring magnetic trackers and responsive scroll physics.</p>
            <a href="#showcase" class="btn-cta" style="display: inline-flex;">Explore Showcase</a>
        </div>
        <div class="scroll-down">Scroll Down to Test Physics</div>
    </section>

    <!-- Showcase Section -->
    <section id="showcase">
        <div class="content-card">
            <span class="badge">Interactive Elements</span>
            <h1>Engineered for <span>Emotion</span>.</h1>
            <p>Try hovering and moving your cursor across the navigation links above to see the magnetic tracker glide smoothly, or scroll up/down to see the header automatically conceal or reveal itself.</p>
            
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">✦</div>
                    <h3>Magnetic Tracker</h3>
                    <p>Pill background glides organically to match the hovered link's width and position using GSAP power easing.</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">✧</div>
                    <h3>Smart Scroll Physics</h3>
                    <p>Header conceals itself on scroll-down to maximize readability, and slides back into view on scroll-up.</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">❖</div>
                    <h3>Entrance Stagger</h3>
                    <p>All items on the header slide and fade in one-by-one with organic timing offsets on page load.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Philosophy Section -->
    <section id="philosophy">
        <div class="content-card">
            <span class="badge">Our Philosophy</span>
            <h1>Crafting Premium <span>Identities</span>.</h1>
            <p>We build spaces, interfaces, and motions that stand the test of time, marrying technical robustness with unparalleled aesthetic quality.</p>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Animate hero and showcase cards on load
            gsap.to('.hero-card', {
                opacity: 1,
                y: 0,
                duration: 1,
                ease: 'power3.out',
                delay: 0.6
            });

            // Scroll Trigger simulation for second card
            window.addEventListener('scroll', () => {
                const cards = document.querySelectorAll('.content-card:not(.hero-card)');
                cards.forEach(card => {
                    const rect = card.getBoundingClientRect();
                    if (rect.top < window.innerHeight * 0.8) {
                        gsap.to(card, {
                            opacity: 1,
                            y: 0,
                            duration: 1,
                            ease: 'power3.out'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
