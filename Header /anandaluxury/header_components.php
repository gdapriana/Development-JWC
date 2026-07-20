<?php
// Initialize dummy data for testing if not provided by the platform
if (!isset($data)) {
    $data = new stdClass();
    $data->menu = new stdClass();

    // Primary menu structure matching the design
    $data->menu->primary_menu = [
        (object)['label' => 'Home', 'link' => '#home'],
        (object)[
            'label' => 'Wellness & Experiences',
            'link' => '#wellness',
            'child' => [
                (object)['label' => 'Yoga & Meditation', 'link' => '#yoga'],
                (object)['label' => 'Holistic Spa', 'link' => '#spa'],
                (object)['label' => 'Cultural Tours', 'link' => '#tours']
            ]
        ],
        (object)['label' => 'Dining', 'link' => '#dining'],
        (object)['label' => 'Offers', 'link' => '#offers'],
        (object)['label' => 'Our Story', 'link' => '#our-story'],
        (object)['label' => 'Facilities', 'link' => '#facilities']
    ];

    $data->web = new stdClass();
    $data->web->site_logo_alternative = '';
}

if (!function_exists('simplify_menu')) {
    function simplify_menu($menu)
    {
        $result = [];
        foreach ($menu as $item) {
            $new_item = [
                'label' => $item->label ?? '',
                'link'  => $item->link ?? '',
            ];
            if (!empty($item->child)) {
                $new_item['child'] = simplify_menu($item->child);
            }
            $result[] = $new_item;
        }
        return $result;
    }
}

$filtered_menu = json_decode(json_encode(simplify_menu($data->menu->primary_menu)));

if (!function_exists('render_desktop_header_menu')) {
    function render_desktop_header_menu($menu_items)
    {
        // Limit visible menu items to 3 on desktop, others go to "More" dropdown
        $max_visible = 3;
        $visible = array_slice($menu_items, 0, $max_visible);
        $overflow = array_slice($menu_items, $max_visible);

        $html = '<ul class="header-desktop-links">';
        foreach ($visible as $item) {
            $has_child = isset($item->child) && !empty($item->child);
            $li_class = $has_child ? 'header-link-container has-children' : 'header-link-container';

            $html .= '<li class="' . $li_class . '">';
            $html .= '<a href="' . htmlspecialchars($item->link) . '">' . htmlspecialchars($item->label) . '</a>';
            if ($has_child) {
                $html .= '<svg class="chevron-icon" width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-left:4px;"><polyline points="6 9 12 15 18 9"></polyline></svg>';
                $html .= '<ul class="desktop-dropdown">';
                foreach ($item->child as $child) {
                    $html .= '<li><a href="' . htmlspecialchars($child->link) . '">' . htmlspecialchars($child->label) . '</a></li>';
                }
                $html .= '</ul>';
            }
            $html .= '</li>';
        }

        // Overflow dropdown
        if (!empty($overflow)) {
            $html .= '<li class="header-link-container has-children more-menu-item">';
            $html .= '<a href="#">More</a>';
            $html .= '<svg class="chevron-icon" width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-left:4px;"><polyline points="6 9 12 15 18 9"></polyline></svg>';
            $html .= '<ul class="desktop-dropdown">';
            foreach ($overflow as $item) {
                $html .= '<li><a href="' . htmlspecialchars($item->link) . '">' . htmlspecialchars($item->label) . '</a></li>';
            }
            $html .= '</ul>';
            $html .= '</li>';
        }

        $html .= '</ul>';
        return $html;
    }
}
?>

<style>
    #header {
        --header-bg: transparent;
        --header-text: #ffffff;
        --header-border: rgba(255, 255, 255, 0.1);
        --header-shadow: none;

        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 90px;
        background-color: var(--header-bg);
        border-bottom: 1px solid var(--header-border);
        box-shadow: var(--header-shadow);
        z-index: 99999;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        font-family: var(--primtext), sans-serif;
    }

    #header.scrolled {
        height: 80px;
        background-color: rgba(8, 26, 21, 0.95);
        /* Deep Balinese Forest Green */
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom-color: rgba(255, 255, 255, 0.05);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .container-global {
        width: 100%;
        height: 100%;
    }

    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 32px;
        box-sizing: border-box;
        height: 100%;
    }

    /* 3-column layout structure to center logo and align left menu and right actions */
    .header-left {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 24px;
        height: 100%;
    }

    .header-center {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
    }

    .header-right {
        flex: 1;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        height: 100%;
    }

    /* Logo styling */
    .header-logo {
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: transform 0.3s ease;
    }

    .header-logo:hover {
        transform: scale(1.01);
        text-decoration: none;
    }

    /* Hamburger & Menu text */
    .hamburger-btn-ananda {
        background: transparent;
        border: none;
        outline: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 0;
        color: #ffffff;
        font-family: var(--primtext), sans-serif;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }

    .hamburger-icon-bars {
        display: flex;
        flex-direction: column;
        gap: 5px;
        justify-content: center;
    }

    .hamburger-icon-bars .bar {
        width: 18px;
        height: 1.5px;
        background-color: #ffffff;
        transition: all 0.3s ease;
    }

    /* Visible links next to Hamburger on desktop */
    .header-desktop-links {
        display: none;
        align-items: center;
        gap: 20px;
        list-style: none;
        padding: 0;
        margin: 0;
        border-left: 1px solid rgba(255, 255, 255, 0.2);
        padding-left: 20px;
        height: 100%;
    }

    .header-link-container {
        position: relative;
        display: flex;
        align-items: center;
        height: 100%;
    }

    .header-desktop-links a {
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 1px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        text-transform: uppercase;
        transition: color 0.3s ease;
        display: inline-flex;
        align-items: center;
    }

    .header-desktop-links a:hover,
    .header-link-container:hover>a {
        color: var(--accent-gold, #c5a880);
    }

    /* Desktop dropdown inside header links */
    .desktop-dropdown {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(12px);
        opacity: 0;
        visibility: hidden;
        background-color: rgba(8, 26, 21, 0.98);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        border-radius: 8px;
        padding: 8px 0;
        min-width: 180px;
        list-style: none;
        z-index: 1000;
        margin: 0;
        transition: all 0.3s ease;
    }

    /* Bridge gap */
    .desktop-dropdown::before {
        content: '';
        position: absolute;
        top: -12px;
        left: 0;
        width: 100%;
        height: 12px;
        background-color: transparent;
    }

    .header-link-container:hover .desktop-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0);
    }

    .desktop-dropdown a {
        display: block;
        padding: 8px 16px;
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.8) !important;
        text-decoration: none;
        text-transform: capitalize;
        text-align: left;
        letter-spacing: 0.5px;
    }

    .desktop-dropdown a:hover {
        color: var(--accent-gold, #c5a880) !important;
        background-color: rgba(255, 255, 255, 0.03);
    }

    .chevron-icon {
        transition: transform 0.25s ease;
        opacity: 0.6;
        margin-left: 4px;
    }

    .header-link-container:hover .chevron-icon {
        transform: rotate(180deg);
    }

    /* Booking Now Button styling */
    .btn-booking {
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 2px;
        color: #ffffff;
        text-decoration: none;
        text-transform: uppercase;
        transition: color 0.3s ease;
        padding: 8px 0;
        position: relative;
    }

    .btn-booking::after {
        content: '';
        position: absolute;
        bottom: 4px;
        left: 0;
        width: 0;
        height: 1px;
        background-color: #ffffff;
        transition: width 0.3s ease;
    }

    .btn-booking:hover {
        color: #ffffff;
        text-decoration: none;
    }

    .btn-booking:hover::after {
        width: 100%;
    }

    /* Fullscreen Overlay Style */
    .fullscreen-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 1000000;
        display: none;
        /* Controlled by GSAP */
        overflow: hidden;
    }

    /* Left Panel: Bali Image & Large Menu links */
    .overlay-left-panel {
        width: 100%;
        height: 100%;
        position: relative;
        background-color: #050f0c;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 40px;
        box-sizing: border-box;
    }

    @media (min-width: 768px) {
        .overlay-left-panel {
            width: 50%;
        }
    }

    /* Image Background for left panel */
    .left-panel-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1200&q=80');
        background-size: cover;
        background-position: center;
        opacity: 0.25;
        /* Translucent background image */
        z-index: 1;
    }

    .left-panel-content {
        position: relative;
        z-index: 2;
        max-width: 500px;
        margin: 0 auto;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding-top: 20px;
        padding-bottom: 40px;
        box-sizing: border-box;
    }

    /* Close Button inside Left Panel */
    .overlay-close-btn {
        background: transparent;
        border: none;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-family: var(--primtext), sans-serif;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 2px;
        cursor: pointer;
        padding: 8px 0;
        width: fit-content;
    }

    .overlay-close-btn svg {
        transition: transform 0.3s ease;
    }

    .overlay-close-btn:hover svg {
        transform: rotate(90deg);
    }

    /* Vertical Navigation List */
    .overlay-menu-list {
        list-style: none;
        padding: 0;
        margin: auto 0;
        display: flex;
        flex-direction: column;
        gap: 16px;
        max-height: 70vh;
        overflow-y: auto;
    }

    .overlay-menu-item {
        opacity: 0;
        /* Animated by GSAP */
        transform: translateY(20px);
    }

    .overlay-menu-item>a {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(1.3rem, 3.5vw, 1.8rem);
        font-weight: 500;
        letter-spacing: 2px;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        text-transform: uppercase;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .overlay-menu-item>a:hover {
        color: #ffffff;
        transform: translateX(10px);
        text-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
    }

    /* Submenus inside the fullscreen overlay drawer */
    .overlay-submenu {
        list-style: none;
        padding: 4px 0 0 16px;
        margin: 4px 0 12px 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
        border-left: 1px solid rgba(255, 255, 255, 0.15);
    }

    .overlay-submenu a {
        font-family: var(--primtext) !important;
        font-size: 0.9rem !important;
        text-transform: capitalize !important;
        color: var(--accent-gold) !important;
        letter-spacing: 0.5px !important;
    }

    .overlay-submenu a:hover {
        color: #ffffff !important;
        transform: translateX(6px) !important;
    }

    /* Right Panel: Cream info details */
    .overlay-right-panel {
        display: none;
        width: 50%;
        height: 100%;
        background-color: #f5f2e6;
        /* Balinese cream / straw color */
        color: #08261e;
        /* Ubud dark forest green text */
        box-sizing: border-box;
        align-items: center;
        justify-content: center;
        padding: 60px;
        position: relative;
    }

    @media (min-width: 768px) {
        .overlay-right-panel {
            display: flex;
        }
    }

    .right-panel-content {
        max-width: 420px;
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 36px;
        opacity: 0;
        /* Animated by GSAP */
    }

    .resort-brand {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 12px;
    }

    .resort-title-dark h2 {
        font-family: 'Playfair Display', Georgia, serif;
        font-weight: 600;
        font-size: 1.5rem;
        letter-spacing: 3px;
        margin: 0;
        color: #08261e;
        line-height: 1.1;
    }

    .resort-title-dark h3 {
        font-family: 'Playfair Display', Georgia, serif;
        font-style: italic;
        font-weight: 400;
        font-size: 1rem;
        margin: 4px 0 0 0;
        color: rgba(8, 38, 30, 0.75);
    }

    .resort-description {
        font-size: 0.95rem;
        line-height: 1.6;
        color: rgba(8, 38, 30, 0.85);
        text-align: center;
        margin: 0;
    }

    .info-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .info-section h4 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0 0 6px 0;
        color: #08261e;
        letter-spacing: 0.5px;
    }

    .info-section p {
        margin: 0;
        font-size: 0.9rem;
        line-height: 1.5;
        color: rgba(8, 38, 30, 0.85);
    }

    .info-section p.email {
        color: #aa8010;
        font-weight: 600;
        margin-top: 4px;
    }

    @media (min-width: 992px) {
        .header-desktop-links {
            display: flex;
        }
    }
</style>

<header id="header">
    <div class="container-global">
        <div class="content header-content">
            <!-- Left Side: Hamburger toggle & Horizontal Links -->
            <div class="header-left">
                <button class="hamburger-btn-ananda" id="hamburger-btn-ananda">
                    <span class="hamburger-icon-bars">
                        <span class="bar bar-1"></span>
                        <span class="bar bar-2"></span>
                    </span>
                    <span class="menu-label">Menu</span>
                </button>

                <?= render_desktop_header_menu($filtered_menu) ?>
            </div>

            <!-- Center Side: Centered Balinese Logo -->
            <div class="header-center">
                <a href="<?= function_exists('base_url') ? base_url() : '#'; ?>" class="header-logo">
                    <?php if (!empty($data->web->site_logo_alternative)): ?>
                        <img src="<?= $data->web->site_logo_alternative ?>" alt="Ananda Ubud Logo" onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='flex';">
                    <?php endif; ?>
                    <div id="logo-fallback" class="logo-text-fallback" style="<?= empty($data->web->site_logo_alternative) ? 'display: flex;' : 'display: none;' ?> flex-direction: column; align-items: center; text-align: center; color: #ffffff; text-decoration: none;">
                        <!-- Balinese umbrella SVG logo -->
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 4px;">
                            <path d="M12 3c-4.5 0-8 3-8 5h16c0-2-3.5-5-8-5z" fill="rgba(255,255,255,0.15)" />
                            <line x1="12" y1="8" x2="12" y2="21"></line>
                            <path d="M12 3v5M8 4.5V8M16 4.5V8M6 6.5V8M18 6.5V8"></path>
                            <path d="M4 8l1 3M8 8l0.5 3M12 8l0 3M16 8l-0.5 3M20 8l-1 3" stroke-width="1"></path>
                            <path d="M12 3l-3-1 3-1"></path>
                        </svg>
                        <span style="font-family: 'Playfair Display', Georgia, serif; font-weight: 500; font-size: 1.15rem; letter-spacing: 2px; text-transform: uppercase; line-height: 1;">Ananda Ubud</span>
                        <span style="font-family: 'Playfair Display', Georgia, serif; font-style: italic; font-weight: 400; font-size: 0.8rem; color: rgba(255,255,255,0.75); margin-top: 1px;">Resort</span>
                    </div>
                </a>
            </div>

            <!-- Right Side: Booking CTA -->
            <div class="header-right">
                <a href="#booking" class="btn-booking">
                    Booking Now
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Fullscreen GSAP Animated Overlay Menu -->
<div class="fullscreen-overlay" id="fullscreen-overlay">
    <!-- Left Panel (Bali Image & Navigation List) -->
    <div class="overlay-left-panel">
        <div class="left-panel-bg"></div>
        <div class="left-panel-content">
            <!-- Close Button -->
            <button class="overlay-close-btn" id="overlay-close-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                <span>CLOSE</span>
            </button>

            <!-- Menu list -->
            <ul class="overlay-menu-list">
                <?php foreach ($filtered_menu as $item): ?>
                    <?php $has_child = isset($item->child) && !empty($item->child); ?>
                    <li class="overlay-menu-item">
                        <a href="<?= htmlspecialchars($item->link) ?>"><?= htmlspecialchars(strtoupper($item->label)) ?></a>
                        <?php if ($has_child): ?>
                            <ul class="overlay-submenu">
                                <?php foreach ($item->child as $child): ?>
                                    <li><a href="<?= htmlspecialchars($child->link) ?>"><?= htmlspecialchars($child->label) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Right Panel (Balinese Straw Cream Information details) -->
    <div class="overlay-right-panel">
        <div class="right-panel-content" id="right-panel-content">
            <!-- Resort Brand -->
            <div class="resort-brand">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#08261e" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3c-4.5 0-8 3-8 5h16c0-2-3.5-5-8-5z" fill="rgba(8,38,30,0.1)" />
                    <line x1="12" y1="8" x2="12" y2="21"></line>
                    <path d="M12 3v5M8 4.5V8M16 4.5V8M6 6.5V8M18 6.5V8"></path>
                    <path d="M4 8l1 3M8 8l0.5 3M12 8l0 3M16 8l-0.5 3M20 8l-1 3" stroke-width="1"></path>
                    <path d="M12 3l-3-1 3-1"></path>
                </svg>
                <div class="resort-title-dark">
                    <h2>ANANDA UBUD</h2>
                    <h3>Resort</h3>
                </div>
            </div>

            <p class="resort-description">
                Stay amid Ubud rice fields at Ananda Ubud Resort, with Balinese heritage, yoga, cultural experiences, tranquil pools, and easy access to Ubud Centre.
            </p>

            <div class="info-section">
                <h4>Location</h4>
                <p>Jl. Raya Sanggingan, Kedewatan, Kecamatan Ubud, Kabupaten Gianyar, Bali 80571</p>
            </div>

            <div class="info-section">
                <h4>Phone Support</h4>
                <p>(0361) 975376</p>
                <p class="email">reservations@anandaubud.com</p>
            </div>

            <div class="info-section">
                <h4>Connect With Us</h4>
                <p>(0361) 975376</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Fallback check for GSAP
        if (typeof gsap === 'undefined') {
            const overlay = document.getElementById('fullscreen-overlay');
            const hamburger = document.getElementById('hamburger-btn-ananda');
            const closeBtn = document.getElementById('overlay-close-btn');

            if (hamburger && overlay) {
                hamburger.addEventListener('click', () => {
                    overlay.style.display = 'flex';
                });
            }
            if (closeBtn && overlay) {
                closeBtn.addEventListener('click', () => {
                    overlay.style.display = 'none';
                });
            }
            return;
        }

        const overlay = document.getElementById('fullscreen-overlay');
        const hamburger = document.getElementById('hamburger-btn-ananda');
        const closeBtn = document.getElementById('overlay-close-btn');

        // Scroll sticky background transitions
        const header = document.getElementById('header');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 30) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Initialize GSAP values
        gsap.set('.overlay-left-panel', {
            xPercent: -100
        });
        gsap.set('.overlay-right-panel', {
            xPercent: 100
        });

        // Timeline for Fullscreen Menu Opening
        const menuTimeline = gsap.timeline({
            paused: true,
            defaults: {
                ease: 'power3.inOut'
            }
        });

        menuTimeline.set(overlay, {
                display: 'flex'
            })
            .to('.overlay-left-panel', {
                xPercent: 0,
                duration: 0.65
            })
            .to('.overlay-right-panel', {
                xPercent: 0,
                duration: 0.65
            }, '-=0.65')
            .to('.overlay-menu-item', {
                opacity: 1,
                y: 0,
                stagger: 0.08,
                duration: 0.5,
                ease: 'power2.out'
            }, '-=0.2')
            .to('.right-panel-content', {
                opacity: 1,
                y: 0,
                duration: 0.5,
                ease: 'power2.out'
            }, '-=0.4');

        // Toggle Open Fullscreen
        if (hamburger) {
            hamburger.addEventListener('click', () => {
                menuTimeline.play();
                document.body.style.overflow = 'hidden';
            });
        }

        // Toggle Close Fullscreen
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                menuTimeline.reverse();
                document.body.style.overflow = '';
            });
        }

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                menuTimeline.reverse();
                document.body.style.overflow = '';
            }
        });
    });
</script>