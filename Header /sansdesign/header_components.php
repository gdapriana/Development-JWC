<?php
// Initialize dummy data for testing if not provided by the platform
if (!isset($data)) {
    $data = new stdClass();
    $data->menu = new stdClass();
    
    // Primary menu structure matching the design
    $data->menu->primary_menu = [
        (object)['label' => 'Home', 'link' => '#home'],
        (object)['label' => 'Shop', 'link' => '#shop'],
        (object)['label' => 'Delivery', 'link' => '#delivery'],
        (object)['label' => 'Boxes', 'link' => '#boxes'],
        (object)['label' => 'About', 'link' => '#about']
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
                $html .= '<div class="dropdown-menus">';
                foreach ($item->child as $child) {
                    $html .= '<a href="' . htmlspecialchars($child->link) . '" class="dropdown-item">' . htmlspecialchars($child->label) . '</a>';
                }
                $html .= '</div>';
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
        --header-bg: #000000;
        --header-text: #ffffff;
        --header-text-hover: #dfdaff; /* Lavender */
        --header-border: rgba(255, 255, 255, 0.08);
        --header-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
        --accent-lavender: #dfdaff;
        --accent-mint: #e6f9f0;
        --capsule-bg: rgba(255, 255, 255, 0.08);
        
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
        box-shadow: 0 12px 45px rgba(0, 0, 0, 0.4);
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

    /* Desktop Navigation Menu (Capsule style) */
    .desktop-menu-capsule {
        display: none;
        align-items: center;
        background-color: var(--capsule-bg);
        border-radius: 999px;
        padding: 4px;
        gap: 2px;
    }

    .nav-item-container {
        position: relative;
    }

    .nav-item {
        display: inline-flex;
        align-items: center;
        padding: 8px 18px;
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--header-text);
        text-decoration: none;
        border-radius: 999px;
        transition: all 0.25s ease;
    }

    .nav-item-container:hover .nav-item {
        color: var(--header-text-hover);
        text-decoration: none;
    }

    /* Active nav item (Home) - Lavender background with black text */
    .nav-item-container.active .nav-item {
        background-color: var(--accent-lavender);
        color: #000000;
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

    /* Desktop Dark Dropdown */
    .dropdown-menus {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(12px) scale(0.95);
        opacity: 0;
        visibility: hidden;
        background-color: #161617;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.08);
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
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        border-radius: 6px;
        transition: all 0.15s ease;
        text-align: left;
    }

    .dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.08);
        color: #ffffff;
        text-decoration: none;
    }

    /* Header Actions CTA & Hamburger */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .actions-group {
        display: none;
        align-items: center;
        gap: 12px;
    }

    .action-divider {
        width: 1px;
        height: 16px;
        background-color: rgba(255, 255, 255, 0.15);
    }

    .btn-icon-dark {
        width: 38px;
        height: 38px;
        background-color: var(--capsule-bg);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-icon-dark:hover {
        background-color: rgba(255, 255, 255, 0.15);
        color: var(--accent-lavender);
        text-decoration: none;
    }

    .btn-dark {
        background-color: var(--capsule-bg);
        color: #ffffff;
        font-size: 0.85rem;
        font-weight: 500;
        padding: 10px 22px;
        border-radius: 999px;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-dark:hover {
        background-color: rgba(255, 255, 255, 0.15);
        color: var(--accent-lavender);
        text-decoration: none;
    }

    .btn-mint {
        background-color: var(--accent-mint);
        color: #000000;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 10px 22px;
        border-radius: 999px;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-mint:hover {
        background-color: #d1f5e2;
        box-shadow: 0 4px 15px rgba(230, 249, 240, 0.3);
        color: #000000;
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
        background-color: #ffffff;
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
        .desktop-menu-capsule {
            display: flex;
        }
        .actions-group {
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
            gap: 12px;
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
            text-decoration: none;
            color: rgba(255, 255, 255, 0.75);
            font-size: 1rem;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 999px;
            transition: all 0.2s ease;
        }

        .mobile-nav-item-container.active .mobile-nav-item {
            background-color: var(--accent-lavender);
            color: #000000;
        }

        .mobile-submenu-toggle {
            background: none;
            border: none;
            padding: 8px;
            cursor: pointer;
            color: #888888;
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
            color: #888888;
            text-decoration: none;
            padding: 4px 0;
        }

        /* Combined Actions on mobile */
        .mobile-actions-wrapper {
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
        }

        .mobile-actions-row {
            display: flex;
            gap: 12px;
            width: 100%;
        }

        .mobile-actions-row .btn-icon-dark {
            flex-shrink: 0;
        }

        .mobile-actions-row .btn-dark {
            flex-grow: 1;
            text-align: center;
        }

        .mobile-actions-wrapper .btn-mint {
            width: 100%;
            text-align: center;
            box-sizing: border-box;
        }
    }
</style>

<header id="header">
    <div class="container-global">
        <div class="content header-content">
            <!-- Logo -->
            <a href="<?= function_exists('base_url') ? base_url() : '#'; ?>" class="header-logo">
                <?php if (!empty($data->web->site_logo_alternative)): ?>
                    <img src="<?= $data->web->site_logo_alternative ?>" alt="SoRun Logo" onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='flex';">
                <?php endif; ?>
                <span id="logo-fallback" class="logo-text-fallback" style="<?= empty($data->web->site_logo_alternative) ? 'display: flex;' : 'display: none;' ?> align-items: center; gap: 8px; text-decoration: none;">
                    <!-- Archimedean Spiral SVG -->
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round">
                        <circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,0.2)"/>
                        <path d="M12 12a2 2 0 1 0-2-2 4 4 0 0 0 4 4 6 6 0 1 0-6-6 8 8 0 0 0 8 8"/>
                    </svg>
                    <span style="font-family: sans-serif; font-weight: 800; font-size: 1.4rem; color: #ffffff; letter-spacing: -0.5px;">SoRun</span>
                </span>
            </a>

            <!-- Desktop Menu -->
            <?= render_desktop_menu($filtered_menu) ?>

            <!-- CTA and Hamburger -->
            <div class="header-actions">
                <div class="actions-group">
                    <a href="#cart" class="btn-icon-dark" aria-label="Cart">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                    </a>
                    
                    <span class="action-divider"></span>
                    
                    <a href="#login" class="btn-dark">Login</a>
                    
                    <span class="action-divider"></span>
                    
                    <a href="#signup" class="btn-mint">Sign up</a>
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
            <div class="mobile-actions-row">
                <a href="#cart" class="btn-icon-dark" aria-label="Cart">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                </a>
                <a href="#login" class="btn-dark">Login</a>
            </div>
            <a href="#signup" class="btn-mint">Sign up</a>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const header = document.getElementById('header');
        const hamburgerBtn = document.querySelector('.hamburger-btn');
        const submenuToggles = document.querySelectorAll('.mobile-submenu-toggle');
        const sections = document.querySelectorAll('section, footer');
        const navItems = document.querySelectorAll('.nav-item-container');
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
                // Remove active classes
                navItems.forEach(item => item.classList.remove('active'));
                mobileNavItems.forEach(item => item.classList.remove('active'));

                // Find matching item by link target
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
