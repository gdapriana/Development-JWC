<?php
// Initialize dummy data for testing if not provided by the platform
if (!isset($data)) {
    $data = new stdClass();
    $data->menu = new stdClass();
    
    // Primary menu structure matching the design
    $data->menu->primary_menu = [
        (object)['label' => 'Home', 'link' => '#home'],
        (object)[
            'label' => 'Properties',
            'link' => '#properties',
            'child' => [
                (object)['label' => 'Villas', 'link' => '#villas'],
                (object)['label' => 'Apartments', 'link' => '#apartments'],
                (object)['label' => 'Houses', 'link' => '#houses']
            ]
        ],
        (object)['label' => 'About', 'link' => '#about'],
        (object)['label' => 'Contact', 'link' => '#contact']
    ];

    $data->web = new stdClass();
    // Default empty to trigger fallback
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
        $html = '<div class="desktop-menu-capsule">';
        foreach ($menu_items as $item) {
            $has_child = isset($item->child) && !empty($item->child);
            $active_class = (strtolower($item->label) === 'home') ? 'active' : '';
            $li_class = $has_child ? 'nav-item-container has-children ' . $active_class : 'nav-item-container ' . $active_class;
            
            $html .= '<div class="' . $li_class . '">';
            $html .= '<a href="' . htmlspecialchars($item->link) . '" class="nav-item">';
            $html .= htmlspecialchars($item->label);
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
            $html .= '</div>';
        }
        $html .= '</div>';
        return $html;
    }
}
?>

<style>
    #header {
        --header-bg: transparent;
        --header-text: #ffffff;
        --header-text-hover: #bef700; /* Lime Green */
        --header-border: rgba(255, 255, 255, 0.08);
        --header-shadow: 0 4px 30px rgba(0, 0, 0, 0.15);
        --accent-lime: #bef700;
        --capsule-bg: rgba(255, 255, 255, 0.08);
        --capsule-border: rgba(255, 255, 255, 0.25);
        
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 90px;
        background-color: var(--header-bg);
        z-index: 100000;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        font-family: var(--primtext), sans-serif;
    }

    #header.scrolled {
        height: 78px;
        background-color: rgba(20, 27, 37, 0.95);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 1px solid var(--header-border);
        box-shadow: var(--header-shadow);
    }

    .container-global {
        width: 100%;
        height: 100%;
    }

    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 24px;
        box-sizing: border-box;
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
        transform: scale(1.02);
        text-decoration: none;
    }

    .header-logo img {
        height: 36px;
        display: block;
    }

    /* Desktop Navigation Menu (Floating Capsule style) */
    .desktop-menu-capsule {
        display: none;
        align-items: center;
        background-color: var(--capsule-bg);
        border: 1px solid var(--capsule-border);
        border-radius: 999px;
        padding: 6px;
        gap: 4px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .nav-item-container {
        position: relative;
    }

    .nav-item {
        display: inline-flex;
        align-items: center;
        padding: 10px 24px;
        font-size: 0.85rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        border-radius: 999px;
        transition: all 0.25s ease;
    }

    .nav-item-container:hover .nav-item {
        color: var(--header-text-hover);
        text-decoration: none;
    }

    /* Active nav item (Home) - Solid white background with black text */
    .nav-item-container.active .nav-item {
        background-color: #ffffff;
        color: #000000;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .nav-item-container.active:hover .nav-item {
        color: #000000;
    }

    .chevron-icon {
        transition: transform 0.2s ease;
    }

    .nav-item-container.has-children:hover .chevron-icon {
        transform: rotate(180deg);
    }

    /* Desktop Dropdown (Dark theme) */
    .dropdown-menus {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(12px) scale(0.95);
        opacity: 0;
        visibility: hidden;
        background-color: #1e293b; /* Dark slate */
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 8px 0;
        min-width: 180px;
        list-style: none;
        z-index: 1000;
        margin: 0;
        transition: transform 0.2s ease, opacity 0.2s ease, visibility 0.2s ease;
    }

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
        transform: translateX(-50%) translateY(4px) scale(1);
    }

    .dropdown-item {
        display: block;
        padding: 8px 20px;
        font-size: 0.85rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.75);
        text-decoration: none;
        transition: all 0.2s ease;
        text-align: left;
    }

    .dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.06);
        color: var(--accent-lime);
        text-decoration: none;
    }

    /* Header Actions CTA & Hamburger */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Lime Green pill button with white arrow box inside */
    .btn-lime {
        display: none;
        align-items: center;
        background-color: var(--accent-lime);
        color: #000000;
        font-size: 0.85rem;
        font-weight: 700;
        padding: 6px 6px 6px 20px; /* Snug spacing for the inner white box */
        border-radius: 999px;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        gap: 12px;
        border: 1px solid var(--accent-lime);
    }

    .btn-lime:hover {
        background-color: #aae000;
        border-color: #aae000;
        color: #000000;
        text-decoration: none;
        box-shadow: 0 4px 20px rgba(190, 247, 0, 0.4);
    }

    .arrow-box {
        width: 32px;
        height: 32px;
        background-color: #ffffff;
        border-radius: 8px; /* Rounded square box */
        display: flex;
        align-items: center;
        justify-content: center;
        color: #000000;
        transition: transform 0.2s ease;
    }

    .btn-lime:hover .arrow-box {
        transform: translateX(3px);
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
        background-color: #ffffff;
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
        .desktop-menu-capsule {
            display: flex;
        }
        .btn-lime {
            display: inline-flex;
        }
        .hamburger-btn {
            display: none;
        }
    }

    @media (max-width: 991px) {
        #header {
            height: 70px;
            background-color: rgba(20, 27, 37, 0.95);
            border-bottom: 1px solid var(--header-border);
        }

        #header.menu-open {
            max-height: 90vh;
            overflow-y: auto;
        }

        .header-content {
            padding: 0 20px;
        }

        .header-logo img {
            height: 30px;
        }

        /* Hamburger Open State Animation */
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

        /* Mobile Slide-down container */
        .mobile-nav-container {
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 70px;
            left: 0;
            width: 100%;
            height: 0;
            background-color: #141b25;
            border-bottom: 1px solid var(--header-border);
            overflow: hidden;
            transition: height 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            box-sizing: border-box;
        }

        #header.scrolled.menu-open .mobile-nav-container {
            top: 78px;
            height: calc(100vh - 78px);
        }

        #header.menu-open .mobile-nav-container {
            height: calc(100vh - 70px);
            overflow-y: auto;
            padding: 24px 20px 32px 20px;
        }

        .mobile-nav-list {
            list-style: none;
            padding: 0;
            margin: 0 0 28px 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .mobile-nav-item-container {
            display: flex;
            flex-direction: column;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding-bottom: 12px;
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
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.75);
            font-size: 1rem;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 999px;
            transition: all 0.2s ease;
        }

        .mobile-nav-item-container.active .mobile-nav-item {
            background-color: #ffffff;
            color: #000000;
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
            padding: 0 0 0 24px;
            margin: 4px 0 0 0;
            display: none;
            flex-direction: column;
            gap: 12px;
            border-left: 2px solid rgba(255, 255, 255, 0.1);
        }

        .mobile-submenu-open .mobile-submenu {
            display: flex;
        }

        .mobile-sublink {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            padding: 4px 0;
            display: block;
            text-align: left;
        }

        .mobile-sublink:hover {
            color: var(--accent-lime);
        }

        /* Combined Actions on mobile */
        .mobile-actions-wrapper {
            width: 100%;
            margin-top: auto;
        }

        .mobile-actions-wrapper .btn-lime {
            display: flex !important;
            justify-content: space-between;
            width: 100%;
            box-sizing: border-box;
            padding: 6px 6px 6px 20px;
        }
    }
</style>

<header id="header">
    <div class="container-global">
        <div class="content header-content">
            <!-- Logo -->
            <a href="<?= function_exists('base_url') ? base_url() : '#'; ?>" class="header-logo">
                <?php if (!empty($data->web->site_logo_alternative)): ?>
                    <img src="<?= $data->web->site_logo_alternative ?>" alt="BrightNest Logo" onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='flex';">
                <?php endif; ?>
                <span id="logo-fallback" class="logo-text-fallback" style="<?= empty($data->web->site_logo_alternative) ? 'display: flex;' : 'display: none;' ?> align-items: center; text-decoration: none;">
                    <span style="font-family: sans-serif; font-weight: 800; font-size: 1.45rem; letter-spacing: -0.5px;">
                        <span style="color: #ffffff;">Bright</span><span style="color: #bef700;">Nest</span>
                    </span>
                </span>
            </a>

            <!-- Desktop Menu -->
            <?= render_desktop_menu($filtered_menu) ?>

            <!-- CTA and Hamburger -->
            <div class="header-actions">
                <a href="#get-started" class="btn-lime">
                    <span>Get Started</span>
                    <span class="arrow-box">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </span>
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

        <div class="mobile-actions-wrapper">
            <a href="#get-started" class="btn-lime">
                <span>Get Started</span>
                <span class="arrow-box">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </span>
            </a>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const header = document.getElementById('header');
        const hamburgerBtn = document.querySelector('.hamburger-btn');
        const submenuToggles = document.querySelectorAll('.mobile-submenu-toggle');
        const sections = document.querySelectorAll('section, footer');
        const navItems = document.querySelectorAll('.desktop-menu-capsule .nav-item-container');
        const mobileNavItems = document.querySelectorAll('.mobile-nav-item-container');

        // Toggle mobile menu
        if (hamburgerBtn) {
            hamburgerBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                header.classList.toggle('menu-open');
            });
        }

        // Close mobile menu if clicked outside
        document.addEventListener('click', (e) => {
            if (header.classList.contains('menu-open') && !header.contains(e.target)) {
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

        // Scroll header styling
        const handleScroll = () => {
            if (window.scrollY > 30) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        };

        window.addEventListener('scroll', handleScroll);
        handleScroll();

        // Scrollspy (Highlighting active section)
        const handleScrollspy = () => {
            let currentSectionId = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 120;
                if (window.scrollY >= sectionTop) {
                    currentSectionId = section.getAttribute('id');
                }
            });

            if (currentSectionId) {
                // Reset active classes
                navItems.forEach(item => item.classList.remove('active'));
                mobileNavItems.forEach(item => item.classList.remove('active'));

                navItems.forEach(item => {
                    const link = item.querySelector('.nav-item');
                    if (link && link.getAttribute('href') === `#${currentSectionId}`) {
                        item.classList.add('active');
                    }
                });

                mobileNavItems.forEach(item => {
                    const link = item.querySelector('.mobile-nav-item');
                    if (link && link.getAttribute('href') === `#${currentSectionId}`) {
                        item.classList.add('active');
                    }
                });
            }
        };

        window.addEventListener('scroll', handleScrollspy);
        handleScrollspy();
    });
</script>
