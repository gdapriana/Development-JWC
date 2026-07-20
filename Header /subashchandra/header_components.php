<?php
// Initialize dummy data for testing if not provided by the platform
if (!isset($data)) {
    $data = new stdClass();
    $data->menu = new stdClass();
    
    // Primary menu structure matching the design
    $data->menu->primary_menu = [
        (object)['label' => 'Home', 'link' => '#home'],
        (object)['label' => 'Services', 'link' => '#services'],
        (object)['label' => 'Pricing', 'link' => '#pricing'],
        (object)['label' => 'Features', 'link' => '#features']
    ];

    $data->web = new stdClass();
    // Default empty to trigger the beautiful SVG fallback logo
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

if (!function_exists('get_nav_icon')) {
    function get_nav_icon($label)
    {
        $label = strtolower(trim($label));
        if ($label === 'home') {
            return '<svg class="nav-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>';
        } elseif ($label === 'services' || $label === 'service') {
            return '<svg class="nav-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>';
        } elseif ($label === 'pricing') {
            return '<svg class="nav-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>';
        } elseif ($label === 'features' || $label === 'feature') {
            // Shield-like Features icon
            return '<svg class="nav-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>';
        }
        // Fallback simple dot icon
        return '<svg class="nav-icon" width="6" height="6" viewBox="0 0 24 24" fill="currentColor" stroke="none"><circle cx="12" cy="12" r="10"></circle></svg>';
    }
}

if (!function_exists('render_desktop_menu')) {
    function render_desktop_menu($menu_items)
    {
        $html = '<ul class="desktop-nav">';
        foreach ($menu_items as $item) {
            $has_child = isset($item->child) && !empty($item->child);
            $li_class = $has_child ? 'nav-item-container has-children' : 'nav-item-container';
            $html .= '<li class="' . $li_class . '">';

            $html .= '<a href="' . htmlspecialchars($item->link) . '" class="nav-item">';
            $html .= '<span class="nav-item-icon-wrapper">' . get_nav_icon($item->label) . '</span>';
            $html .= '<span class="nav-item-label">' . htmlspecialchars($item->label) . '</span>';
            if ($has_child) {
                $html .= '<svg class="chevron-icon" width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-left:4px;"><polyline points="6 9 12 15 18 9"></polyline></svg>';
            }
            $html .= '</a>';

            if ($has_child) {
                $html .= '<div class="dropdown-menus">';
                foreach ($item->child as $child) {
                    $html .= '<a href="' . htmlspecialchars($child->link) . '" class="dropdown-item">' . htmlspecialchars($child->label) . '</a>';
                }
                $html .= '</div>';
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
        --header-bg: #ffffff;
        --header-text: #0d1117;
        --header-text-hover: #000000;
        --header-border: rgba(0, 0, 0, 0.05);
        --header-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        --accent-lime: #d4f953;
        --accent-dark: #0d1117;
        
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        width: 92%;
        max-width: 1200px;
        background-color: var(--header-bg);
        border: 1px solid var(--header-border);
        border-radius: 999px; /* Pill-shaped header container */
        box-shadow: var(--header-shadow);
        z-index: 100000;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        font-family: var(--primtext), sans-serif;
    }

    #header.scrolled {
        top: 10px;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.06);
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
        padding: 8px 12px 8px 24px; /* Less left padding for balanced logo visual */
        box-sizing: border-box;
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

    /* Desktop Navigation Menu */
    .desktop-nav {
        display: none;
        align-items: center;
        gap: 12px;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .nav-item-container {
        position: relative;
    }

    .nav-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background-color: #f1f3f6;
        padding: 6px 16px 6px 6px; /* Icon wrapper margin offset */
        border-radius: 99px;
        text-decoration: none;
        transition: all 0.25s ease;
        color: var(--header-text);
        font-size: 0.85rem;
        font-weight: 500;
    }

    .nav-item-icon-wrapper {
        width: 28px;
        height: 28px;
        background-color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #555555;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: all 0.25s ease;
    }

    .nav-item:hover {
        background-color: #e5e8ec;
        color: var(--header-text-hover);
        text-decoration: none;
    }

    .nav-item:hover .nav-item-icon-wrapper {
        color: var(--accent-dark);
        transform: scale(1.05);
    }

    .nav-item.active {
        background-color: var(--accent-dark);
        color: #ffffff;
    }

    .nav-item.active .nav-item-icon-wrapper {
        background-color: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        box-shadow: none;
    }

    .chevron-icon {
        transition: transform 0.2s ease;
    }

    .nav-item-container.has-children:hover .chevron-icon {
        transform: rotate(180deg);
    }

    /* Desktop Dropdown List */
    .dropdown-menus {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(12px) scale(0.95);
        opacity: 0;
        visibility: hidden;
        background-color: #ffffff;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 12px;
        padding: 8px;
        min-width: 160px;
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
        padding: 8px 16px;
        font-size: 0.85rem;
        font-weight: 500;
        color: #555555;
        text-decoration: none;
        border-radius: 6px;
        transition: all 0.15s ease;
        text-align: left;
    }

    .dropdown-item:hover {
        background-color: #f1f3f6;
        color: var(--accent-dark);
        text-decoration: none;
    }

    /* Header Actions CTA & Hamburger */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .cta-group {
        display: none;
        align-items: center;
        gap: 8px;
    }

    .btn-lime {
        background-color: var(--accent-lime);
        color: var(--accent-dark);
        font-size: 0.85rem;
        font-weight: 700;
        padding: 11px 24px;
        border-radius: 999px;
        text-decoration: none;
        transition: all 0.25s ease;
        white-space: nowrap;
    }

    .btn-lime:hover {
        background-color: #c2e646;
        box-shadow: 0 4px 15px rgba(212, 249, 83, 0.35);
        color: var(--accent-dark);
        text-decoration: none;
    }

    .btn-arrow {
        background-color: var(--accent-dark);
        color: #ffffff;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.25s ease;
    }

    .btn-arrow:hover {
        background-color: #232d3d;
        transform: rotate(45deg);
        color: #ffffff;
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
        width: 20px;
        height: 2px;
        background-color: var(--header-text);
        transition: all 0.3s ease;
    }

    .hamburger-btn:focus {
        outline: none;
    }

    /* Mobile Navigation Container inside Header */
    .mobile-nav-container {
        display: none;
    }

    @media (min-width: 992px) {
        .desktop-nav {
            display: flex;
        }
        .cta-group {
            display: flex;
        }
        .hamburger-btn {
            display: none;
        }
    }

    @media (max-width: 991px) {
        #header {
            width: calc(100% - 30px);
            top: 15px;
            max-height: 56px;
            border-radius: 28px;
            overflow: hidden;
            transition: max-height 0.3s cubic-bezier(0.16, 1, 0.3, 1), border-radius 0.3s ease;
        }

        #header.menu-open {
            max-height: 90vh;
            border-radius: 24px;
            overflow-y: auto;
        }

        .header-content {
            padding: 8px 12px 8px 20px;
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
            gap: 14px;
        }

        .mobile-nav-item-container {
            display: flex;
            flex-direction: column;
        }

        .mobile-nav-item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mobile-nav-item {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--header-text);
            font-size: 1rem;
            font-weight: 500;
            padding: 4px 0;
        }

        .mobile-nav-item-icon-wrapper {
            width: 28px;
            height: 28px;
            background-color: #f1f3f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #555555;
        }

        .mobile-submenu-toggle {
            background: none;
            border: none;
            padding: 8px;
            cursor: pointer;
            color: #555555;
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
            padding: 0 0 0 38px;
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
            color: #555555;
            text-decoration: none;
            padding: 4px 0;
        }

        .mobile-cta-wrapper {
            width: 100%;
        }

        /* Combined full-width lime CTA on mobile */
        .mobile-cta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            box-sizing: border-box;
            padding: 6px 6px 6px 20px;
        }

        .cta-arrow-circle {
            background-color: var(--accent-dark);
            color: #ffffff;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }
</style>

<header id="header">
    <div class="container-global">
        <div class="content header-content">
            <!-- Logo -->
            <a href="<?= function_exists('base_url') ? base_url() : '#'; ?>" class="header-logo">
                <?php if (!empty($data->web->site_logo_alternative)): ?>
                    <img src="<?= $data->web->site_logo_alternative ?>" alt="Banking Logo" onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='flex';">
                <?php endif; ?>
                <span id="logo-fallback" class="logo-text-fallback" style="<?= empty($data->web->site_logo_alternative) ? 'display: flex;' : 'display: none;' ?> align-items: center; gap: 8px; text-decoration: none;">
                    <!-- Stylized ribbon N logo -->
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" style="color: #0d1117;">
                        <path d="M6 3h4v12.5L14.5 3H18v18h-4V8.5L9.5 21H6V3z"/>
                    </svg>
                    <span style="font-family: sans-serif; font-weight: 800; font-size: 1.35rem; color: #0d1117; letter-spacing: -0.5px;">Banking</span>
                </span>
            </a>

            <!-- Desktop Menu -->
            <?= render_desktop_menu($filtered_menu) ?>

            <!-- CTA and Hamburger -->
            <div class="header-actions">
                <div class="cta-group">
                    <a href="#open-account" class="btn-lime">Open Account</a>
                    <a href="#open-account" class="btn-arrow">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>
                </div>

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
                ?>
                <li class="mobile-nav-item-container">
                    <div class="mobile-nav-item-row">
                        <a href="<?= htmlspecialchars($item->link) ?>" class="mobile-nav-item">
                            <span class="mobile-nav-item-icon-wrapper"><?= get_nav_icon($item->label) ?></span>
                            <span><?= htmlspecialchars($item->label) ?></span>
                        </a>
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
            <a href="#open-account" class="btn-lime mobile-cta">
                <span>Open Account</span>
                <span class="cta-arrow-circle">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="7" y1="17" x2="17" y2="7"></line>
                        <polyline points="7 7 17 7 17 17"></polyline>
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
        const navItems = document.querySelectorAll('.nav-item');
        const mobileNavItems = document.querySelectorAll('.mobile-nav-item');

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
                navItems.forEach(item => {
                    item.classList.remove('active');
                    // Check direct link or hash matching
                    if (item.getAttribute('href') === `#${currentSectionId}`) {
                        item.classList.add('active');
                    }
                });

                mobileNavItems.forEach(item => {
                    item.classList.remove('active');
                    if (item.getAttribute('href') === `#${currentSectionId}`) {
                        item.classList.add('active');
                    }
                });
            }
        };

        window.addEventListener('scroll', handleScrollspy);
        handleScrollspy();
    });
</script>
