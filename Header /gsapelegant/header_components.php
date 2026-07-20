<?php
// Initialize dummy data for testing if not provided by the platform
if (!isset($data)) {
    $data = new stdClass();
    $data->menu = new stdClass();
    
    // Primary menu structure matching the design
    $data->menu->primary_menu = [
        (object)['label' => 'Home', 'link' => '#home'],
        (object)[
            'label' => 'Showcase',
            'link' => '#showcase',
            'child' => [
                (object)['label' => 'Architecture', 'link' => '#architecture'],
                (object)['label' => 'Interior Design', 'link' => '#interior'],
                (object)['label' => 'Digital Art', 'link' => '#digital']
            ]
        ],
        (object)['label' => 'Philosophy', 'link' => '#philosophy'],
        (object)['label' => 'Studio', 'link' => '#studio'],
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

if (!function_exists('render_desktop_menu')) {
    function render_desktop_menu($menu_items)
    {
        $html = '<ul class="desktop-nav">';
        // Render the sliding tracker element inside the nav container
        $html .= '<div class="nav-tracker"></div>';
        
        foreach ($menu_items as $item) {
            $has_child = isset($item->child) && !empty($item->child);
            $li_class = $has_child ? 'nav-item-container has-children' : 'nav-item-container';
            $active_class = (strtolower($item->label) === 'home') ? ' active-link' : '';
            
            $html .= '<li class="' . $li_class . '">';
            $html .= '<a href="' . htmlspecialchars($item->link) . '" class="nav-item' . $active_class . '">';
            $html .= '<span>' . htmlspecialchars($item->label) . '</span>';
            if ($has_child) {
                $html .= '<svg class="chevron-icon" width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-left:4px;"><polyline points="6 9 12 15 18 9"></polyline></svg>';
            }
            $html .= '</a>';
            
            if ($has_child) {
                $html .= '<ul class="dropdown-menus">';
                foreach ($item->child as $child) {
                    $html .= '<li><a href="' . htmlspecialchars($child->link) . '" class="dropdown-item">' . htmlspecialchars($child->label) . '</a></li>';
                }
                $html .= '</ul>';
            }
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
}
?>

<style>
    #header {
        --header-bg: rgba(10, 10, 12, 0.45);
        --header-text: #ffffff;
        --header-text-hover: #c5a880; /* Champagne Gold */
        --header-border: rgba(255, 255, 255, 0.06);
        --header-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        --accent-gold: #c5a880;
        
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        width: 92%;
        max-width: 1200px;
        background-color: var(--header-bg);
        border: 1px solid var(--header-border);
        border-radius: 20px;
        box-shadow: var(--header-shadow);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        z-index: 100000;
        font-family: var(--primtext), sans-serif;
        opacity: 0; /* Animated by GSAP on load */
    }

    .container-global {
        width: 100%;
        padding-left: 0;
        padding-right: 0;
    }

    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 24px;
        box-sizing: border-box;
    }

    /* Logo styling */
    .header-logo {
        display: flex;
        align-items: center;
        text-decoration: none;
        opacity: 0; /* Animated by GSAP */
    }

    .header-logo img {
        height: 36px;
        display: block;
    }

    /* Desktop Navigation Menu */
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
        font-weight: 500;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: color 0.3s ease;
        padding: 10px 20px;
        display: inline-flex;
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .nav-item:hover,
    .nav-item.active-link {
        color: #ffffff;
        text-decoration: none;
    }

    /* Sliding pill background tracker */
    .nav-tracker {
        position: absolute;
        height: 36px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 99px;
        pointer-events: none;
        z-index: 1;
        opacity: 0;
        top: 50%;
        transform: translateY(-50%);
        box-sizing: border-box;
    }

    .chevron-icon {
        transition: transform 0.3s ease;
        opacity: 0.6;
    }

    .nav-item-container.has-children:hover .chevron-icon {
        transform: rotate(180deg);
    }

    /* Dropdown Menus (Glassmorphic) */
    .dropdown-menus {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(12px);
        opacity: 0;
        visibility: hidden;
        background-color: rgba(10, 10, 12, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        padding: 8px 0;
        min-width: 180px;
        list-style: none;
        z-index: 1000;
        margin: 0;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1),
                    opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1),
                    visibility 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Bridge gap */
    .dropdown-menus::before {
        content: '';
        position: absolute;
        top: -12px;
        left: 0;
        width: 100%;
        height: 12px;
        background-color: transparent;
    }

    .nav-item-container.has-children:hover .dropdown-menus {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0);
    }

    .dropdown-item {
        display: block;
        padding: 8px 20px;
        font-size: 0.85rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.6);
        text-decoration: none;
        transition: all 0.2s ease;
        text-align: left;
    }

    .dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.05);
        color: var(--accent-gold);
        text-decoration: none;
    }

    /* Header Actions CTA */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        opacity: 0; /* Animated by GSAP */
    }

    /* Champagne Gold Luxury Button */
    .btn-cta {
        display: none;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #d8c3a5, #ae9473);
        color: #0b0b0d;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 10px 24px;
        border-radius: 999px;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        border: none;
        box-shadow: 0 4px 15px rgba(197, 168, 128, 0.2);
    }

    .btn-cta:hover {
        color: #0b0b0d;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(197, 168, 128, 0.4);
        text-decoration: none;
    }

    /* Mobile Hamburger */
    .hamburger-btn {
        width: 40px;
        height: 40px;
        background: transparent;
        border: none;
        outline: none;
        position: relative;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 5px;
        padding: 0;
        z-index: 1001;
    }

    .hamburger-btn .bar {
        width: 22px;
        height: 2px;
        background-color: var(--header-text);
        transition: all 0.3s ease;
    }

    .hamburger-btn:focus {
        outline: none;
    }

    /* Mobile Drawer */
    .mobile-nav-container {
        display: none;
    }

    @media (min-width: 992px) {
        .desktop-nav {
            display: flex;
        }
        .btn-cta {
            display: inline-flex;
        }
        .hamburger-btn {
            display: none;
        }
    }

    @media (max-width: 991px) {
        #header {
            width: calc(100% - 30px);
            top: 15px;
            max-height: 60px;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.16, 1, 0.3, 1), border-radius 0.3s ease;
        }

        #header.menu-open {
            max-height: 90vh;
            border-radius: 20px;
        }

        .header-content {
            padding: 10px 16px;
        }

        .header-logo img {
            height: 28px;
        }

        /* Hamburger Open State animation */
        #header.menu-open .bar-1 {
            transform: translateY(7px) rotate(45deg);
        }

        #header.menu-open .bar-2 {
            opacity: 0;
            transform: scaleX(0);
        }

        #header.menu-open .bar-3 {
            transform: translateY(-7px) rotate(-45deg);
        }

        /* Mobile drawer */
        .mobile-nav-container {
            display: flex;
            flex-direction: column;
            padding: 16px 20px 24px 20px;
            border-top: 1px solid var(--header-border);
            margin-top: 5px;
            box-sizing: border-box;
        }

        .mobile-nav-list {
            list-style: none;
            padding: 0;
            margin: 0 0 24px 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .mobile-nav-item-container {
            display: flex;
            flex-direction: column;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding-bottom: 12px;
            opacity: 0; /* Animated by GSAP on open */
        }

        .mobile-nav-item-container:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .mobile-nav-item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mobile-nav-item {
            font-size: 1rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 4px 0;
        }

        .mobile-nav-item-container.active .mobile-nav-item {
            color: var(--accent-gold);
            font-weight: 600;
        }

        .mobile-submenu-toggle {
            background: none;
            border: none;
            padding: 8px;
            cursor: pointer;
            color: rgba(255, 255, 255, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mobile-submenu-toggle svg {
            transition: transform 0.3s ease;
        }

        .mobile-submenu-open .mobile-submenu-toggle svg {
            transform: rotate(180deg);
        }

        .mobile-submenu {
            list-style: none;
            padding: 0 0 0 16px;
            margin: 4px 0 0 0;
            display: none;
            flex-direction: column;
            gap: 12px;
            border-left: 2px solid var(--header-border);
        }

        .mobile-submenu-open .mobile-submenu {
            display: flex;
        }

        .mobile-sublink {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            padding: 4px 0;
        }

        .mobile-cta-wrapper {
            opacity: 0; /* Animated by GSAP */
        }

        .mobile-cta {
            display: flex !important;
            width: 100%;
            box-sizing: border-box;
            text-align: center;
        }
    }
</style>

<header id="header">
    <div class="container-global">
        <div class="content header-content">
            <!-- Logo -->
            <a href="<?= function_exists('base_url') ? base_url() : '#'; ?>" class="header-logo">
                <?php if (!empty($data->web->site_logo_alternative)): ?>
                    <img src="<?= $data->web->site_logo_alternative ?>" alt="Aura Logo" onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='flex';">
                <?php endif; ?>
                <span id="logo-fallback" class="logo-text-fallback" style="<?= empty($data->web->site_logo_alternative) ? 'display: flex;' : 'display: none;' ?> align-items: center; gap: 8px; text-decoration: none;">
                    <!-- Elegant crescent gold SVG logo -->
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#c5a880" stroke-width="2" stroke-linecap="round">
                        <circle cx="12" cy="12" r="10" stroke="rgba(197, 168, 128, 0.3)"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10" fill="none"/>
                        <path d="M12 2a6 6 0 0 1 0 12" fill="#c5a880"/>
                    </svg>
                    <span style="font-family: 'Playfair Display', Georgia, serif; font-weight: 700; font-size: 1.45rem; color: #ffffff; letter-spacing: 0.5px;">Aura</span>
                </span>
            </a>

            <!-- Desktop Menu -->
            <?= render_desktop_menu($filtered_menu) ?>

            <!-- CTA and Hamburger -->
            <div class="header-actions">
                <a href="#inquire" class="btn-cta">
                    <span>Inquire Now</span>
                    <!-- Sparkle icon -->
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" style="margin-left: 6px;">
                        <path d="M12 2l2.4 7.2L21.6 11.6l-5.4 4.8L17.6 22 12 18.2 6.4 22l1.4-5.6-5.4-4.8 7.2-2.4z"/>
                    </svg>
                </a>

                <button class="hamburger-btn" aria-label="Toggle Navigation">
                    <span class="bar bar-1"></span>
                    <span class="bar bar-2"></span>
                    <span class="bar bar-3"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div class="mobile-nav-container">
        <ul class="mobile-nav-list">
            <?php foreach ($filtered_menu as $item): ?>
                <?php
                $has_child = isset($item->child) && !empty($item->child);
                $active_class = (strtolower($item->label) === 'home') ? 'active' : '';
                ?>
                <li class="mobile-nav-item-container <?= $active_class ?>">
                    <div class="mobile-nav-item-row">
                        <a href="<?= htmlspecialchars($item->link) ?>" class="mobile-nav-item"><?= htmlspecialchars($item->label) ?></a>
                        <?php if ($has_child): ?>
                            <button class="mobile-submenu-toggle" aria-label="Toggle Submenu">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                        <?php endif; ?>
                    </div>
                    <?php if ($has_child): ?>
                        <ul class="mobile-submenu">
                            <?php foreach ($item->child as $child): ?>
                                <li>
                                    <a href="<?= htmlspecialchars($child->link) ?>" class="mobile-sublink"><?= htmlspecialchars($child->label) ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="mobile-cta-wrapper">
            <a href="#inquire" class="btn-cta mobile-cta">
                <span>Inquire Now</span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" style="margin-left: 6px;">
                    <path d="M12 2l2.4 7.2L21.6 11.6l-5.4 4.8L17.6 22 12 18.2 6.4 22l1.4-5.6-5.4-4.8 7.2-2.4z"/>
                </svg>
            </a>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Ensure GSAP is loaded before executing animations
        if (typeof gsap === 'undefined') {
            console.warn('GSAP is not loaded. Skipping header animations.');
            document.getElementById('header').style.opacity = '1';
            document.querySelector('.header-logo').style.opacity = '1';
            document.querySelector('.header-actions').style.opacity = '1';
            return;
        }

        const header = document.getElementById('header');
        const hamburgerBtn = document.querySelector('.hamburger-btn');
        const submenuToggles = document.querySelectorAll('.mobile-submenu-toggle');
        const sections = document.querySelectorAll('section, footer');
        const navItems = document.querySelectorAll('.desktop-nav .nav-item-container');
        const activeLink = document.querySelector('.desktop-nav .active-link');
        const tracker = document.querySelector('.nav-tracker');

        // 1. Entrance Animations (GSAP Reveal)
        const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });
        
        tl.to(header, {
            opacity: 1,
            top: 20,
            duration: 0.8,
            delay: 0.1
        });
        
        tl.to('.header-logo', {
            opacity: 1,
            x: 0,
            duration: 0.6
        }, '-=0.4');
        
        // Stagger nav links fade in
        if (window.innerWidth >= 992) {
            tl.from('.desktop-nav .nav-item-container', {
                opacity: 0,
                y: -10,
                stagger: 0.08,
                duration: 0.5
            }, '-=0.3');
        }
        
        tl.to('.header-actions', {
            opacity: 1,
            x: 0,
            duration: 0.6
        }, '-=0.4');

        // Set initial tracker position to active link if it exists
        const setInitialTracker = () => {
            if (activeLink && tracker && window.innerWidth >= 992) {
                const linkRect = activeLink.getBoundingClientRect();
                const parentRect = activeLink.closest('.desktop-nav').getBoundingClientRect();
                
                gsap.set(tracker, {
                    left: linkRect.left - parentRect.left,
                    width: linkRect.width,
                    opacity: 1
                });
            }
        };
        
        setTimeout(setInitialTracker, 900); // Wait for entrance timeline to finish

        // 2. Magnetic Navigation Underline/Pill Tracker (GSAP)
        if (tracker && window.innerWidth >= 992) {
            const links = document.querySelectorAll('.desktop-nav .nav-item');
            const navContainer = document.querySelector('.desktop-nav');

            links.forEach(link => {
                link.addEventListener('mouseenter', () => {
                    const linkRect = link.getBoundingClientRect();
                    const parentRect = navContainer.getBoundingClientRect();

                    gsap.to(tracker, {
                        left: linkRect.left - parentRect.left,
                        width: linkRect.width,
                        opacity: 1,
                        duration: 0.35,
                        ease: 'power2.out'
                    });
                });
            });

            navContainer.addEventListener('mouseleave', () => {
                if (activeLink) {
                    const activeRect = activeLink.getBoundingClientRect();
                    const parentRect = navContainer.getBoundingClientRect();

                    gsap.to(tracker, {
                        left: activeRect.left - parentRect.left,
                        width: activeRect.width,
                        opacity: 1,
                        duration: 0.35,
                        ease: 'power2.out'
                    });
                } else {
                    gsap.to(tracker, {
                        opacity: 0,
                        duration: 0.3
                    });
                }
            });
        }

        // 3. Hide on Scroll Down, Show on Scroll Up (GSAP)
        let lastScrollY = window.scrollY;
        
        window.addEventListener('scroll', () => {
            const currentScrollY = window.scrollY;
            
            // Background blur/scrolled transitions
            if (currentScrollY > 50) {
                header.classList.add('scrolled');
                gsap.to(header, {
                    backgroundColor: 'rgba(10, 10, 12, 0.96)',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    boxShadow: '0 12px 40px rgba(0, 0, 0, 0.4)',
                    top: 10,
                    borderRadius: 14,
                    duration: 0.3,
                    ease: 'power1.out'
                });
            } else {
                header.classList.remove('scrolled');
                gsap.to(header, {
                    backgroundColor: 'rgba(10, 10, 12, 0.45)',
                    borderColor: 'rgba(255, 255, 255, 0.06)',
                    boxShadow: '0 10px 40px rgba(0, 0, 0, 0.2)',
                    top: 20,
                    borderRadius: 20,
                    duration: 0.3,
                    ease: 'power1.out'
                });
            }

            // Hide/Show header on scroll direction
            if (currentScrollY > lastScrollY && currentScrollY > 150 && !header.classList.contains('menu-open')) {
                // Scrolling down - hide
                gsap.to(header, {
                    y: -120,
                    opacity: 0,
                    duration: 0.4,
                    ease: 'power2.out'
                });
            } else {
                // Scrolling up - show
                gsap.to(header, {
                    y: 0,
                    opacity: 1,
                    duration: 0.4,
                    ease: 'power2.out'
                });
            }
            
            lastScrollY = currentScrollY;
        });

        // 4. Mobile Drawer Animation (GSAP Stagger)
        let isMenuOpen = false;
        
        if (hamburgerBtn) {
            hamburgerBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                isMenuOpen = !isMenuOpen;
                header.classList.toggle('menu-open');
                
                if (isMenuOpen) {
                    // Stagger mobile items entrance
                    gsap.fromTo('.mobile-nav-item-container', 
                        { opacity: 0, x: -20 },
                        { opacity: 1, x: 0, stagger: 0.08, duration: 0.4, delay: 0.1, ease: 'power2.out' }
                    );
                    gsap.fromTo('.mobile-cta-wrapper',
                        { opacity: 0, y: 10 },
                        { opacity: 1, y: 0, duration: 0.3, delay: 0.3 }
                    );
                }
            });
        }

        // Close mobile drawer on outside click
        document.addEventListener('click', (e) => {
            if (isMenuOpen && !header.contains(e.target)) {
                isMenuOpen = false;
                header.classList.remove('menu-open');
            }
        });

        // Toggle submenus on mobile
        submenuToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const parent = toggle.closest('.mobile-nav-item-container');
                if (parent) {
                    parent.classList.toggle('mobile-submenu-open');
                }
            });
        });
    });
</script>
