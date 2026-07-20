<?php
// Initialize dummy data for testing if not provided by the platform
if (!isset($data)) {
    $data = new stdClass();
    $data->menu = new stdClass();
    
    $data->menu->primary_menu = [
        (object)['label' => 'Home', 'link' => '#home'],
        (object)[
            'label' => 'Suites',
            'link' => '#suites',
            'child' => [
                (object)['label' => 'Canopy Villa', 'link' => '#canopy'],
                (object)['label' => 'Forest Cabin', 'link' => '#cabin']
            ]
        ],
        (object)[
            'label' => 'Wellness',
            'link' => '#wellness',
            'child' => [
                (object)['label' => 'Jungle Yoga', 'link' => '#yoga'],
                (object)['label' => 'Herbal Spa', 'link' => '#spa']
            ]
        ],
        (object)['label' => 'Experiences', 'link' => '#experiences'],
        (object)['label' => 'Journal', 'link' => '#journal'],
        (object)['label' => 'Inquire', 'link' => '#inquire']
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

if (!function_exists('render_desktop_menu_retreat')) {
    function render_desktop_menu_retreat($menu_items)
    {
        // Limit visible menu items to 3 on desktop, others go to "More" dropdown
        $max_visible = 3;
        $visible = array_slice($menu_items, 0, $max_visible);
        $overflow = array_slice($menu_items, $max_visible);
        
        $html = '<ul class="desktop-nav">';
        // Sliding gold line tracker
        $html .= '<div class="nav-tracker-line"></div>';
        
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
        
        // Overflow menu item
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
        --header-bg: rgba(13, 31, 26, 0.45); /* Translucent forest dark green */
        --header-text: #ffffff;
        --header-text-hover: #dfb76c; /* Warm Gold */
        --header-border: rgba(255, 255, 255, 0.08);
        --header-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        --accent-gold: #dfb76c;
        
        position: fixed;
        top: 24px;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        max-width: 1200px;
        background-color: var(--header-bg);
        border: 1px solid var(--header-border);
        border-radius: 99px; /* Floating capsule */
        box-shadow: var(--header-shadow);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        z-index: 100000;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        font-family: var(--primtext), sans-serif;
        opacity: 0; /* Animated by GSAP */
    }

    #header.scrolled {
        top: 12px;
        background-color: rgba(13, 31, 26, 0.95);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
    }

    .container-global {
        width: 100%;
    }

    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px 8px 24px;
        box-sizing: border-box;
    }

    /* Logo styling */
    .header-logo {
        display: flex;
        align-items: center;
        text-decoration: none;
        gap: 8px;
    }

    .header-logo img {
        height: 32px;
        display: block;
    }

    .logo-text-fallback {
        font-family: 'Playfair Display', Georgia, serif;
        font-weight: 700;
        font-size: 1.25rem;
        color: #ffffff;
        letter-spacing: 0.5px;
    }

    /* Desktop Navigation */
    .desktop-nav {
        display: none;
        align-items: center;
        gap: 8px;
        list-style: none;
        padding: 0;
        margin: 0;
        position: relative;
    }

    .nav-item-container {
        position: relative;
        z-index: 2;
    }

    .nav-item {
        font-size: 0.85rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.75);
        text-decoration: none;
        padding: 10px 18px;
        display: block;
        transition: color 0.3s ease;
    }

    .nav-item:hover,
    .nav-item-container:hover > .nav-item {
        color: var(--accent-gold);
        text-decoration: none;
    }

    /* Desktop dropdown styling */
    .desktop-dropdown {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(12px);
        opacity: 0;
        visibility: hidden;
        background-color: rgba(13, 31, 26, 0.98);
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

    .nav-item-container:hover .desktop-dropdown {
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
        color: var(--accent-gold) !important;
        background-color: rgba(255, 255, 255, 0.03);
    }

    /* Gliding Gold underline tracker */
    .nav-tracker-line {
        position: absolute;
        bottom: 4px;
        height: 2px;
        background-color: var(--accent-gold);
        pointer-events: none;
        z-index: 1;
        opacity: 0;
        transition: width 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                    left 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                    opacity 0.25s ease;
    }

    /* Header Actions: CTA & Hamburger */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .btn-cta {
        display: none;
        align-items: center;
        justify-content: center;
        background-color: #dfb76c;
        color: #0d1f1a;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 1.5px;
        padding: 10px 24px;
        border-radius: 99px;
        text-decoration: none;
        text-transform: uppercase;
        transition: all 0.3s ease;
        border: 1px solid #dfb76c;
    }

    .btn-cta:hover {
        background-color: transparent;
        color: #dfb76c;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(223, 183, 108, 0.25);
    }

    /* Circular Hamburger Button */
    .hamburger-btn-retreat {
        width: 38px;
        height: 38px;
        background-color: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.1);
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

    .hamburger-btn-retreat:hover {
        background-color: rgba(255, 255, 255, 0.15);
        border-color: var(--accent-gold);
    }

    .hamburger-btn-retreat .bar {
        width: 16px;
        height: 1.5px;
        background-color: #ffffff;
        transition: all 0.3s ease;
    }

    /* Fullscreen 3D Curtain Menu Overlay */
    .curtain-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 1000000;
        display: none;
        perspective: 1000px; /* 3D Perspective setup */
        overflow: hidden;
    }

    /* Fall down curtain background */
    .overlay-curtain {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #0b1714;
        background-image: radial-gradient(circle at 50% 50%, rgba(223,183,108,0.03) 0%, transparent 60%);
        transform-origin: top center;
        transform: scaleY(0); /* Animated by GSAP */
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

    @media (min-width: 768px) {
        .overlay-content {
            grid-template-columns: 1.2fr 0.8fr;
            max-width: 1200px;
            margin: 0 auto;
            align-items: center;
            padding: 80px 40px;
        }
    }

    /* Left Column: 3D Flip Menu Links */
    .overlay-menu-side {
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 100%;
        padding-top: 40px;
    }

    .close-btn-retreat {
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
        margin-bottom: 40px;
        width: fit-content;
        transition: color 0.3s ease;
    }

    .close-btn-retreat:hover {
        color: #ffffff;
    }

    .close-btn-retreat svg {
        transition: transform 0.3s ease;
    }

    .close-btn-retreat:hover svg {
        transform: rotate(90deg);
    }

    .curtain-menu-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 16px;
        max-height: 70vh;
        overflow-y: auto;
    }

    .curtain-menu-item {
        perspective: 800px;
        transform-style: preserve-3d;
    }

    .curtain-menu-item > a {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(1.4rem, 4vw, 2.2rem);
        font-weight: 500;
        color: #ffffff;
        text-decoration: none;
        display: inline-block;
        transform-origin: top center;
        /* Initial 3D state before GSAP */
        transform: rotateX(-90deg) translateZ(-50px);
        opacity: 0;
        transition: color 0.3s ease, transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .curtain-menu-item > a:hover {
        color: var(--accent-gold);
        transform: translateX(12px) translateZ(10px) rotateY(5deg);
    }

    /* Submenus inside the curtain full screen overlay drawer */
    .curtain-submenu {
        list-style: none;
        padding: 4px 0 0 16px;
        margin: 4px 0 12px 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
        border-left: 1px solid rgba(255,255,255,0.12);
    }

    .curtain-submenu a {
        font-family: var(--primtext) !important;
        font-size: 0.95rem !important;
        text-transform: capitalize !important;
        color: var(--accent-gold) !important;
        letter-spacing: 0.5px !important;
        transform: none !important;
        opacity: 1 !important;
    }

    .curtain-submenu a:hover {
        color: #ffffff !important;
        transform: translateX(6px) !important;
    }

    /* Right Column: Visual cards staggered up on open */
    .overlay-cards-side {
        display: none;
        flex-direction: column;
        gap: 20px;
        opacity: 0;
        transform: translateY(40px);
    }

    @media (min-width: 768px) {
        .overlay-cards-side {
            display: flex;
        }
    }

    .retreat-card {
        background-color: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.04);
        padding: 24px;
        border-radius: 12px;
        display: flex;
        gap: 20px;
        align-items: center;
        transition: all 0.3s ease;
    }

    .retreat-card:hover {
        border-color: rgba(223, 183, 108, 0.25);
        background-color: rgba(255, 255, 255, 0.04);
        transform: translateY(-3px);
    }

    .retreat-card-img {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        background-size: cover;
        background-position: center;
        flex-shrink: 0;
        opacity: 0.8;
    }

    .retreat-card-info h4 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.1rem;
        margin: 0 0 6px 0;
        color: var(--accent-gold);
    }

    .retreat-card-info p {
        margin: 0;
        font-size: 0.85rem;
        line-height: 1.4;
        color: rgba(255, 255, 255, 0.6);
    }

    @media (min-width: 992px) {
        .desktop-nav {
            display: flex;
        }
        .btn-cta {
            display: inline-flex;
        }
    }
</style>

<header id="header">
    <div class="container-global">
        <div class="content header-content">
            <!-- Left: Golden Leaf Fallback Logo -->
            <a href="<?= function_exists('base_url') ? base_url() : '#'; ?>" class="header-logo">
                <?php if (!empty($data->web->site_logo_alternative)): ?>
                    <img src="<?= $data->web->site_logo_alternative ?>" alt="Forest Retreat Logo" onerror="this.style.display='none'; document.getElementById('logo-fallback-retreat').style.display='flex';">
                <?php endif; ?>
                <div id="logo-fallback-retreat" class="logo-text-fallback" style="<?= empty($data->web->site_logo_alternative) ? 'display: flex;' : 'display: none;' ?> align-items: center; gap: 8px; text-decoration: none;">
                    <!-- Elegant golden leaf SVG -->
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#dfb76c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2C6.5 2 2 6.5 2 12c0 5.5 4.5 10 10 10V2z" fill="rgba(223,183,108,0.12)"/>
                        <path d="M12 22c5.5 0 10-4.5 10-10 0-5.5-4.5-10-10-10v20z"/>
                        <path d="M12 2c0 5-3 9-6 12M12 7c0 4-2 7-4 9M12 12c0 3-1 5-2 6"/>
                    </svg>
                    <span class="logo-text-fallback">Forest Retreat</span>
                </div>
            </a>

            <!-- Center Menu Links -->
            <?= render_desktop_menu_retreat($filtered_menu) ?>

            <!-- Right Actions: Inquire & Hamburger -->
            <div class="header-actions">
                <a href="#inquire" class="btn-cta">Inquire</a>

                <button class="hamburger-btn-retreat" id="hamburger-btn-retreat" aria-label="Open Menu">
                    <span class="bar bar-1"></span>
                    <span class="bar bar-2"></span>
                    <span class="bar bar-3"></span>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Fullscreen 3D Curtain Fall Overlay -->
<div class="curtain-overlay" id="curtain-overlay">
    <!-- Fall Down curtain layer -->
    <div class="overlay-curtain"></div>

    <div class="overlay-content">
        <!-- Left Side: 3D Flip Menu Links -->
        <div class="overlay-menu-side">
            <button class="close-btn-retreat" id="close-btn-retreat">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                <span>CLOSE</span>
            </button>

            <ul class="curtain-menu-list">
                <?php foreach ($filtered_menu as $item): ?>
                    <?php $has_child = isset($item->child) && !empty($item->child); ?>
                    <li class="curtain-menu-item">
                        <a href="<?= htmlspecialchars($item->link) ?>"><?= htmlspecialchars($item->label) ?></a>
                        <?php if ($has_child): ?>
                            <ul class="curtain-submenu">
                                <?php foreach ($item->child as $child): ?>
                                    <li><a href="<?= htmlspecialchars($child->link) ?>"><?= htmlspecialchars($child->label) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Right Side: Visual Cards staggered up -->
        <div class="overlay-cards-side" id="overlay-cards-side">
            <!-- Card 1 -->
            <div class="retreat-card">
                <div class="retreat-card-img" style="background-image: url('https://images.unsplash.com/photo-1546412414-803b9a79a520?auto=format&fit=crop&w=150&q=80')"></div>
                <div class="retreat-card-info">
                    <h4>Luxury Canopy Villa</h4>
                    <p>Experience sleeping inside Ubud's jungle canopy with premium comfort.</p>
                </div>
            </div>
            
            <!-- Card 2 -->
            <div class="retreat-card">
                <div class="retreat-card-img" style="background-image: url('https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=150&q=80')"></div>
                <div class="retreat-card-info">
                    <h4>Holistic Wellness Spa</h4>
                    <p>Balinese massage treatments and organic herbal baths designed for healing.</p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="retreat-card">
                <div class="retreat-card-img" style="background-image: url('https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=150&q=80')"></div>
                <div class="retreat-card-info">
                    <h4>Organic Culinary Experience</h4>
                    <p>Farm-to-table dining overlooking historical tropical landscapes.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const header = document.getElementById('header');
        const hamburger = document.getElementById('hamburger-btn-retreat');
        const closeBtn = document.getElementById('close-btn-retreat');
        const overlay = document.getElementById('curtain-overlay');

        if (typeof gsap === 'undefined') {
            console.warn('GSAP is not loaded. Falling back to CSS toggle for curtain overlay.');
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
            top: 24,
            duration: 0.8,
            ease: 'power3.out'
        });

        // 2. Gold line glider animation
        const links = document.querySelectorAll('.desktop-nav .nav-item');
        const tracker = document.querySelector('.nav-tracker-line');
        const container = document.querySelector('.desktop-nav');

        if (tracker && links.length > 0) {
            links.forEach(link => {
                link.addEventListener('mouseenter', () => {
                    const rect = link.getBoundingClientRect();
                    const parentRect = container.getBoundingClientRect();
                    
                    tracker.style.opacity = '1';
                    tracker.style.left = (rect.left - parentRect.left + 18) + 'px'; // account for padding offset
                    tracker.style.width = (rect.width - 36) + 'px';
                });
            });

            container.addEventListener('mouseleave', () => {
                tracker.style.opacity = '0';
            });
        }

        // 3. Scroll changes
        window.addEventListener('scroll', () => {
            if (window.scrollY > 30) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // 4. GSAP Fullscreen Curtain Fall timeline
        const menuTl = gsap.timeline({ paused: true });

        menuTl.set(overlay, { display: 'block' })
            .to('.overlay-curtain', {
                scaleY: 1,
                duration: 0.6,
                ease: 'power2.inOut'
            })
            // Stagger 3D unfold of links
            .to('.curtain-menu-item > a', {
                opacity: 1,
                rotateX: 0,
                z: 0,
                stagger: 0.08,
                duration: 0.5,
                ease: 'back.out(1.2)'
            }, '-=0.1')
            // Slide up info cards
            .to('.overlay-cards-side', {
                opacity: 1,
                y: 0,
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
                menuTl.reverse();
                document.body.style.overflow = '';
            });
        }

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                menuTl.reverse();
                document.body.style.overflow = '';
            }
        });
    });
</script>
