<?php
// Initialize dummy data for testing if not provided by the platform
if (!isset($data)) {
    $data = new stdClass();
    $data->menu = new stdClass();
    
    $data->menu->primary_menu = [
        (object)['label' => 'Sanctuary', 'link' => '#sanctuary'],
        (object)[
            'label' => 'Wellness',
            'link' => '#wellness',
            'child' => [
                (object)['label' => 'Chakra Healing', 'link' => '#chakra'],
                (object)['label' => 'Sound Therapy', 'link' => '#sound'],
                (object)['label' => 'Ayurvedic Massage', 'link' => '#ayurvedic']
            ]
        ],
        (object)[
            'label' => 'Retreats',
            'link' => '#retreats',
            'child' => [
                (object)['label' => 'Silent Retreat', 'link' => '#silent'],
                (object)['label' => 'Yoga Immersion', 'link' => '#yoga']
            ]
        ],
        (object)['label' => 'Spaces', 'link' => '#spaces'],
        (object)['label' => 'Philosophie', 'link' => '#philosophie'],
        (object)['label' => 'Contact', 'link' => '#contact']
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

if (!function_exists('render_desktop_menu_serenity')) {
    function render_desktop_menu_serenity($menu_items)
    {
        $max_visible = 3;
        $visible = array_slice($menu_items, 0, $max_visible);
        $overflow = array_slice($menu_items, $max_visible);
        
        $html = '<ul class="desktop-nav">';
        
        foreach ($visible as $item) {
            $has_child = isset($item->child) && !empty($item->child);
            $li_class = $has_child ? 'nav-item-container has-children' : 'nav-item-container';
            
            $html .= '<li class="' . $li_class . '">';
            $html .= '<a href="' . htmlspecialchars($item->link) . '" class="nav-item">' . htmlspecialchars($item->label) . '</a>';
            if ($has_child) {
                $html .= '<ul class="desktop-dropdown">';
                foreach ($item->child as $child) {
                    $html .= '<li><a href="' . htmlspecialchars($child->link) . '">' . htmlspecialchars($child->label) . '</a></li>';
                }
                $html .= '</ul>';
            }
            $html .= '</li>';
        }
        
        if (!empty($overflow)) {
            $html .= '<li class="nav-item-container has-children more-menu-item">';
            $html .= '<a href="#" class="nav-item">More</a>';
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
        --header-bg: rgba(255, 255, 255, 0.02);
        --header-text: #ffffff;
        --header-text-hover: #e0d0b0; /* Antique Soft Gold */
        --header-border: rgba(255, 255, 255, 0.05);
        --header-shadow: none;
        --accent-gold: #e0d0b0;
        
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        max-width: 1200px;
        background-color: var(--header-bg);
        border: 1px solid var(--header-border);
        border-radius: 16px;
        z-index: 100000;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        font-family: var(--primtext), sans-serif;
        opacity: 0; /* Animated by GSAP on load */
    }

    #header.scrolled {
        top: 10px;
        background-color: rgba(18, 18, 18, 0.96); /* Serene dark grey/black */
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
    }

    .container-global {
        width: 100%;
        height: 100%;
    }

    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 24px;
        box-sizing: border-box;
        height: 100%;
    }

    /* Logo styling */
    .header-logo {
        display: flex;
        align-items: center;
        text-decoration: none;
        gap: 8px;
    }

    .header-logo img {
        height: 30px;
        display: block;
    }

    .logo-text-fallback {
        font-family: 'Playfair Display', Georgia, serif;
        font-weight: 500;
        font-size: 1.35rem;
        color: #ffffff;
        letter-spacing: 1px;
    }

    /* Desktop Navigation */
    .desktop-nav {
        display: none;
        align-items: center;
        gap: 12px;
        list-style: none;
        padding: 0;
        margin: 0;
        height: 100%;
    }

    .nav-item-container {
        position: relative;
        display: flex;
        align-items: center;
        height: 100%;
    }

    .nav-item {
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        padding: 8px 16px;
        transition: color 0.3s ease;
        border-radius: 99px;
    }

    .nav-item:hover,
    .nav-item-container:hover > .nav-item {
        color: var(--header-text-hover);
        text-decoration: none;
        background-color: rgba(255, 255, 255, 0.03);
    }

    /* Desktop dropdown */
    .desktop-dropdown {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(12px);
        opacity: 0;
        visibility: hidden;
        background-color: #121212;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
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

    .nav-item-container:hover .desktop-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0);
    }

    .desktop-dropdown a {
        display: block;
        padding: 8px 16px;
        font-size: 0.8rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.7) !important;
        text-decoration: none;
        text-align: left;
    }

    .desktop-dropdown a:hover {
        color: var(--accent-gold) !important;
        background-color: rgba(255, 255, 255, 0.02);
    }

    /* Actions side */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .btn-cta {
        display: none;
        align-items: center;
        justify-content: center;
        background: transparent;
        color: #ffffff;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 1px;
        padding: 10px 24px;
        border-radius: 99px;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .btn-cta:hover {
        background-color: #ffffff;
        color: #000000;
        border-color: #ffffff;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(255, 255, 255, 0.15);
    }

    /* Circular Hamburger Button */
    .hamburger-btn-serenity {
        width: 38px;
        height: 38px;
        background-color: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 4px;
        padding: 0;
        transition: all 0.3s ease;
    }

    .hamburger-btn-serenity:hover {
        background-color: rgba(255, 255, 255, 0.08);
        border-color: var(--accent-gold);
    }

    .hamburger-btn-serenity .bar {
        width: 16px;
        height: 1.5px;
        background-color: #ffffff;
        transition: all 0.3s ease;
    }

    /* Fullscreen Bubble Reveal Overlay */
    .bubble-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 1000000;
        background-color: #0d0e11;
        display: none;
        /* Radial clip-path start value */
        clip-path: circle(0% at 90% 40px);
        -webkit-clip-path: circle(0% at 90% 40px);
        transition: none;
        overflow: hidden;
    }

    /* Glowing ambient lights inside overlay */
    .overlay-glow {
        position: absolute;
        width: 600px;
        height: 600px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(224, 208, 176, 0.03) 0%, transparent 70%);
        filter: blur(80px);
        pointer-events: none;
        top: -100px;
        left: -100px;
    }

    .overlay-content {
        position: relative;
        z-index: 2;
        width: 100%;
        height: 100%;
        display: grid;
        grid-template-columns: 1fr;
        padding: 40px;
        box-sizing: border-box;
    }

    @media (min-width: 992px) {
        .overlay-content {
            grid-template-columns: 0.8fr 1.4fr 0.8fr;
            max-width: 1300px;
            margin: 0 auto;
            align-items: center;
            padding: 80px 40px;
        }
    }

    /* Column 1: Info & Connections */
    .overlay-info-side {
        display: none;
        flex-direction: column;
        gap: 32px;
        opacity: 0;
        transform: translateY(20px);
    }

    @media (min-width: 992px) {
        .overlay-info-side {
            display: flex;
        }
    }

    .overlay-info-section h4 {
        font-family: 'Playfair Display', serif;
        font-size: 1.1rem;
        color: var(--accent-gold);
        margin: 0 0 12px 0;
        letter-spacing: 0.5px;
    }

    .overlay-info-section p {
        margin: 0;
        font-size: 0.9rem;
        line-height: 1.5;
        color: rgba(255, 255, 255, 0.6);
    }

    /* Column 2: Centered Large Menu Links (Gold Stroke Fill hover) */
    .overlay-menu-side {
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 100%;
    }

    .close-btn-serenity {
        background: transparent;
        border: none;
        color: rgba(255,255,255,0.6);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-family: var(--primtext), sans-serif;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 2px;
        cursor: pointer;
        padding: 8px 0;
        margin-bottom: 30px;
        width: fit-content;
        transition: color 0.3s ease;
    }

    .close-btn-serenity:hover {
        color: var(--accent-gold);
    }

    .close-btn-serenity svg {
        transition: transform 0.3s ease;
    }

    .close-btn-serenity:hover svg {
        transform: rotate(90deg);
    }

    .serenity-menu-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 16px;
        max-height: 70vh;
        overflow-y: auto;
    }

    .serenity-menu-item {
        opacity: 0;
        transform: translateY(20px);
        display: flex;
        align-items: baseline;
        gap: 16px;
    }

    .serenity-menu-num {
        font-family: 'Playfair Display', serif;
        font-size: 1rem;
        font-style: italic;
        color: rgba(255, 255, 255, 0.25);
    }

    .serenity-menu-item > a {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(1.8rem, 4vw, 2.8rem);
        font-weight: 500;
        letter-spacing: 1.5px;
        text-decoration: none;
        text-transform: uppercase;
        /* Outline effect */
        -webkit-text-stroke: 1px rgba(255, 255, 255, 0.4);
        color: transparent;
        transition: all 0.4s ease;
    }

    .serenity-menu-item > a:hover {
        -webkit-text-stroke: 1px var(--accent-gold);
        color: var(--accent-gold);
        transform: translateX(10px);
    }

    /* Submenus indented gold style */
    .serenity-submenu {
        list-style: none;
        padding: 4px 0 0 16px;
        margin: 4px 0 8px 0;
        display: flex;
        flex-direction: column;
        gap: 6px;
        border-left: 1px solid rgba(224, 208, 176, 0.15);
    }

    .serenity-submenu a {
        font-family: var(--primtext) !important;
        font-size: 0.9rem !important;
        text-transform: capitalize !important;
        color: rgba(255, 255, 255, 0.5) !important;
        -webkit-text-stroke: none !important;
    }

    .serenity-submenu a:hover {
        color: var(--accent-gold) !important;
        transform: translateX(6px) !important;
    }

    /* Column 3: Philosophie / Booking Widget */
    .overlay-widget-side {
        display: none;
        flex-direction: column;
        gap: 32px;
        opacity: 0;
        transform: translateY(20px);
        border-left: 1px solid rgba(255, 255, 255, 0.05);
        padding-left: 40px;
    }

    @media (min-width: 992px) {
        .overlay-widget-side {
            display: flex;
        }
    }

    .overlay-widget-side h4 {
        font-family: 'Playfair Display', serif;
        font-size: 1.1rem;
        color: var(--accent-gold);
        margin: 0 0 12px 0;
        letter-spacing: 0.5px;
    }

    .overlay-widget-side p {
        margin: 0;
        font-size: 0.9rem;
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.6);
    }

    @media (min-width: 768px) {
        .btn-cta {
            display: inline-flex;
        }
    }

    @media (min-width: 992px) {
        .desktop-nav {
            display: flex;
        }
    }
</style>

<header id="header">
    <div class="container-global">
        <div class="content header-content">
            <!-- Left: Lotus Zenith Fallback Logo -->
            <a href="<?= function_exists('base_url') ? base_url() : '#'; ?>" class="header-logo">
                <?php if (!empty($data->web->site_logo_alternative)): ?>
                    <img src="<?= $data->web->site_logo_alternative ?>" alt="Serenity Logo" onerror="this.style.display='none'; document.getElementById('logo-fallback-serenity').style.display='flex';">
                <?php endif; ?>
                <div id="logo-fallback-serenity" class="logo-text-fallback" style="<?= empty($data->web->site_logo_alternative) ? 'display: flex;' : 'display: none;' ?> align-items: center; gap: 8px; text-decoration: none;">
                    <!-- Elegant lotus zen stone SVG -->
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#e0d0b0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <!-- Zen stone arch geometry -->
                        <ellipse cx="12" cy="18" rx="8" ry="3" fill="rgba(224, 208, 176, 0.1)"/>
                        <ellipse cx="12" cy="13" rx="5" ry="2"/>
                        <ellipse cx="12" cy="9" rx="3" ry="1.5"/>
                        <path d="M12 2v5.5"/>
                    </svg>
                    <span class="logo-text-fallback">Serenity</span>
                </div>
            </a>

            <!-- Center Menu Links -->
            <?= render_desktop_menu_serenity($filtered_menu) ?>

            <!-- Right Actions: Rates CTA & Hamburger -->
            <div class="header-actions">
                <a href="#rates" class="btn-cta">Check Rates</a>

                <button class="hamburger-btn-serenity" id="hamburger-btn-serenity" aria-label="Open Menu">
                    <span class="bar bar-1"></span>
                    <span class="bar bar-2"></span>
                    <span class="bar bar-3"></span>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Fullscreen Bubble Radial Expand Overlay Menu -->
<div class="bubble-overlay" id="bubble-overlay">
    <div class="overlay-glow"></div>

    <div class="overlay-content">
        <!-- Left Column: Support & Info -->
        <div class="overlay-info-side" id="overlay-info-side">
            <div class="overlay-info-section">
                <h4>Sanctuary Address</h4>
                <p>Ubud Highlands Retreat, Banjar Penestanan Kelod, Sayan, Kecamatan Ubud, Gianyar, Bali 80571</p>
            </div>
            
            <div class="overlay-info-section">
                <h4>Inquiries</h4>
                <p>reservations@serenityubud.com</p>
                <p>+62 (361) 975 8821</p>
            </div>

            <div class="overlay-info-section">
                <h4>Temple Hours</h4>
                <p>Yoga Shala: 06:00 - 20:00</p>
                <p>Ayurvedic Spa: 09:00 - 21:00</p>
            </div>
        </div>

        <!-- Center Column: Fullscreen links with outline hover -->
        <div class="overlay-menu-side">
            <!-- Close Button -->
            <button class="close-btn-serenity" id="close-btn-serenity">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                <span>CLOSE</span>
            </button>

            <ul class="serenity-menu-list">
                <?php $idx = 1; foreach ($filtered_menu as $item): ?>
                    <?php $has_child = isset($item->child) && !empty($item->child); ?>
                    <li class="serenity-menu-item">
                        <span class="serenity-menu-num"><?= sprintf('%02d', $idx++) ?>.</span>
                        <div style="display: flex; flex-direction: column;">
                            <a href="<?= htmlspecialchars($item->link) ?>"><?= htmlspecialchars($item->label) ?></a>
                            <?php if ($has_child): ?>
                                <ul class="serenity-submenu">
                                    <?php foreach ($item->child as $child): ?>
                                        <li><a href="<?= htmlspecialchars($child->link) ?>"><?= htmlspecialchars($child->label) ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Right Column: Philosophie Widget -->
        <div class="overlay-widget-side" id="overlay-widget-side">
            <div class="overlay-info-section">
                <h4>The Philosophie</h4>
                <p>At Serenity, we believe luxury lies in silence, spaciousness, and self-connection. Our shala and villas are designed to merge with Ubud's primary jungle, preserving the natural wind flows and spiritual frequencies of the land.</p>
            </div>

            <div class="overlay-info-section">
                <h4>Reserve a Sanctuary</h4>
                <p>All bookings include personalized Ayurvedic consultations, daily meditation classes, and organic garden-to-plate meals.</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const header = document.getElementById('header');
        const hamburger = document.getElementById('hamburger-btn-serenity');
        const closeBtn = document.getElementById('close-btn-serenity');
        const overlay = document.getElementById('bubble-overlay');

        if (typeof gsap === 'undefined') {
            console.warn('GSAP is not loaded. Falling back to CSS toggle for bubble overlay.');
            if (hamburger && overlay) {
                hamburger.addEventListener('click', () => { overlay.style.display = 'block'; });
            }
            if (closeBtn && overlay) {
                closeBtn.addEventListener('click', () => { overlay.style.display = 'none'; });
            }
            return;
        }

        // 1. Entrance timeline
        gsap.to(header, {
            opacity: 1,
            duration: 0.8,
            ease: 'power2.out'
        });

        // 2. Scroll transitions
        window.addEventListener('scroll', () => {
            if (window.scrollY > 30) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Get exact hamburger coords for the radial reveal anchor
        const getHamburgerCoords = () => {
            const rect = hamburger.getBoundingClientRect();
            const x = rect.left + rect.width / 2;
            const y = rect.top + rect.height / 2;
            return { x, y };
        };

        // 3. GSAP Radial Clip-Path Expansion Timeline
        const menuTl = gsap.timeline({ paused: true });

        menuTl.set(overlay, { display: 'block' })
            .call(() => {
                const coords = getHamburgerCoords();
                // Set radial clip-path circle starting size at coordinates of the clicked button
                gsap.set(overlay, { 
                    clipPath: `circle(0% at ${coords.x}px ${coords.y}px)`,
                    webkitClipPath: `circle(0% at ${coords.x}px ${coords.y}px)`
                });
            })
            .to(overlay, {
                clipPath: `circle(150% at 85% 40px)`,
                webkitClipPath: `circle(150% at 85% 40px)`,
                duration: 0.75,
                ease: 'power3.inOut'
            })
            // Stagger nav links slide-up and outline text fade
            .to('.serenity-menu-item', {
                opacity: 1,
                y: 0,
                stagger: 0.06,
                duration: 0.5,
                ease: 'power2.out'
            }, '-=0.15')
            // Slide up left/right columns
            .to('#overlay-info-side, #overlay-widget-side', {
                opacity: 1,
                y: 0,
                stagger: 0.08,
                duration: 0.5,
                ease: 'power2.out'
            }, '-=0.3');

        if (hamburger) {
            hamburger.addEventListener('click', () => {
                menuTl.play();
                document.body.style.overflow = 'hidden';
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                const coords = getHamburgerCoords();
                // Custom close timeline to collapse back into the hamburger button
                gsap.timeline()
                    .to('#overlay-info-side, #overlay-widget-side, .serenity-menu-item', {
                        opacity: 0,
                        y: 10,
                        duration: 0.3
                    })
                    .to(overlay, {
                        clipPath: `circle(0% at ${coords.x}px ${coords.y}px)`,
                        webkitClipPath: `circle(0% at ${coords.x}px ${coords.y}px)`,
                        duration: 0.65,
                        ease: 'power3.inOut'
                    }, '-=0.1')
                    .set(overlay, { display: 'none' })
                    .call(() => {
                        document.body.style.overflow = '';
                    });
            });
        }
    });
</script>
