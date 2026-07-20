<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ananda Ubud Resort Header Showcase</title>
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
            --bg-forest: #050f0c;
            --accent-gold: #c5a880;
            --cream-bg: #f5f2e6;
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

        /* Hero banner with Balinese rice fields background */
        .hero-banner {
            position: relative;
            width: 100%;
            height: 100vh;
            background-image: linear-gradient(rgba(5, 15, 12, 0.5), rgba(5, 15, 12, 0.9)), 
                              url('https://images.unsplash.com/photo-1546412414-803b9a79a520?auto=format&fit=crop&w=1600&q=80');
            background-size: cover;
            background-position: center;
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
            transform: translateY(20px);
        }

        .hero-badge {
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 3px;
            color: var(--accent-gold);
            text-transform: uppercase;
            margin-bottom: 20px;
            display: inline-block;
        }

        h1 {
            font-family: var(--font-serif);
            font-size: clamp(2.5rem, 6vw, 5rem);
            font-weight: 500;
            line-height: 1.1;
            margin: 0 0 20px 0;
        }

        h1 span {
            font-style: italic;
            color: var(--accent-gold);
        }

        p.hero-desc {
            font-size: clamp(1.05rem, 2vw, 1.35rem);
            color: rgba(255, 255, 255, 0.75);
            max-width: 680px;
            margin: 0 auto 36px auto;
            line-height: 1.6;
        }

        /* Gold outline button */
        .btn-showcase {
            font-family: var(--primtext);
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 2px;
            color: #ffffff;
            background: transparent;
            border: 1px solid var(--accent-gold);
            padding: 14px 32px;
            border-radius: 0;
            text-decoration: none;
            text-transform: uppercase;
            transition: all 0.3s ease;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(197, 168, 128, 0.1);
        }

        .btn-showcase:hover {
            background-color: var(--accent-gold);
            color: var(--bg-forest);
            box-shadow: 0 6px 20px rgba(197, 168, 128, 0.3);
            text-decoration: none;
        }

        /* Scroll indicator */
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

        /* Description/Showcase sections */
        .showcase-section {
            padding: 100px 24px;
            background-color: #0b1a16;
            display: flex;
            justify-content: center;
        }

        .showcase-card {
            max-width: 1000px;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 48px;
        }

        .showcase-intro {
            text-align: center;
            max-width: 700px;
            margin: 0 auto;
        }

        .showcase-intro h2 {
            font-family: var(--font-serif);
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 500;
            margin: 0 0 16px 0;
        }

        .showcase-intro p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.65);
            line-height: 1.6;
            margin: 0;
        }

        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 32px;
        }

        .card-item {
            background-color: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 40px;
            text-align: left;
            transition: all 0.3s ease;
        }

        .card-item:hover {
            border-color: var(--accent-gold);
            background-color: rgba(255, 255, 255, 0.04);
            transform: translateY(-5px);
        }

        .card-number {
            font-family: var(--font-serif);
            font-style: italic;
            font-size: 2rem;
            color: var(--accent-gold);
            margin-bottom: 20px;
        }

        .card-item h3 {
            font-family: var(--font-serif);
            font-size: 1.35rem;
            margin: 0 0 12px 0;
            font-weight: 600;
        }

        .card-item p {
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.6);
        }

        /* Footer context */
        footer {
            padding: 60px 24px;
            background-color: var(--bg-forest);
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.4);
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

    <?php
    // Include the Balinese Ananda Luxury Header Component
    include __DIR__ . '/header_components.php';
    ?>

    <!-- Hero Section -->
    <section class="hero-banner" id="home">
        <div class="hero-content">
            <span class="hero-badge">Ubud Heritage Sanctuary</span>
            <h1>A Quiet Haven for the <span>Soul</span>.</h1>
            <p class="hero-desc">Experience the true essence of Bali. Try clicking the "Menu" button on the left to witness the split-panel GSAP fullscreen menu overlay animation in action.</p>
            <a href="#showcase" class="btn-showcase">Explore Sanctuary</a>
        </div>
        <div class="scroll-down">Scroll Down to Explore</div>
    </section>

    <!-- Showcase Section -->
    <section class="showcase-section" id="showcase">
        <div class="showcase-card">
            <div class="showcase-intro">
                <h2>Designed for Sophistication</h2>
                <p>Explore the features of the Ananda Luxury header, combining structural balance with smooth cinematic animations.</p>
            </div>

            <div class="grid-cards">
                <div class="card-item">
                    <div class="card-number">01</div>
                    <h3>Split-Screen Transition</h3>
                    <p>Clicking "Menu" slides the dark Balinese image panel from the left and the straw-cream information panel from the right using synchronized GSAP timelines.</p>
                </div>
                <div class="card-item">
                    <div class="card-number">02</div>
                    <h3>Staggered Content Stacking</h3>
                    <p>Links and details inside the overlay fade and slide up sequentially, adding an organic, high-end feel to the navigation.</p>
                </div>
                <div class="card-item">
                    <div class="card-number">03</div>
                    <h3>Centered Heritage Symbol</h3>
                    <p>Includes a stylized Balinese ceremonial umbrella (Tedung) SVG logo, representing cultural protection and harmony.</p>
                </div>
            </div>
        </div>
    </section>

    <footer>
        © ANANDA UBUD RESORT SHOWCASE. ALL RIGHTS RESERVED.
    </footer>

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
