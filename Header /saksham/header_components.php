<?php
// Initialize dummy data for testing if not provided by the platform
if (!isset($data)) {
    $data = new stdClass();
    $data->menu = new stdClass();
    
    // Primary menu structure matching the design
    $data->menu->primary_menu = [
        (object)['label' => 'About', 'link' => '#about'],
        (object)['label' => 'Resources', 'link' => '#resources'],
        (object)['label' => 'Testimonials', 'link' => '#testimonials'],
        (object)['label' => 'Blog', 'link' => '#blog']
    ];

    $data->web = new stdClass();
    // Default empty to trigger the beautiful hey@silver.info text logo
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
        foreach ($menu_items as $item) {
            $has_child = isset($item->child) && !empty($item->child);
            $li_class = $has_child ? 'nav-item-container has-children' : 'nav-item-container';
            
            $html .= '<li class="' . $li_class . '">';
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
        --header-text: #000000;
        --header-text-hover: #555555;
        --header-border: rgba(0, 0, 0, 0.05);
        --header-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        --cta-bg: #000000;
        --cta-text: #ffffff;
        --cta-hover-bg: #333333;
        --dot-color: #a0aec0;
        
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        width: 92%;
        max-width: 1200px;
        background-color: var(--header-bg);
        border: 1px solid var(--header-border);
        border-radius: 16px; /* Floating card style rounded corners */
        box-shadow: var(--header-shadow);
        z-index: 100000;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        font-family: var(--primtext), sans-serif;
    }

    #header.scrolled {
        top: 10px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
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
        padding: 14px 24px;
        box-sizing: border-box;
    }

    /* Logo styling */
    .header-logo {
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: opacity 0.2s ease;
    }

    .header-logo:hover {
        opacity: 0.8;
        text-decoration: none;
    }

    .header-logo img {
        height: 32px;
        display: block;
    }

    /* Desktop Navigation Menu */
    .desktop-nav {
        display: none;
        align-items: center;
        gap: 16px;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .nav-item-container {
        position: relative;
        display: flex;
        align-items: center;
    }

    /* Dot separators between menu items on desktop */
    .nav-item-container::after {
        content: '·';
        margin-left: 16px;
        color: var(--dot-color);
        font-weight: bold;
        font-size: 1.1rem;
    }

    .nav-item-container:last-child::after {
        content: none;
    }

    .nav-item {
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--header-text);
        text-decoration: none;
        transition: color 0.2s ease;
        padding: 6px 0;
        display: inline-flex;
        align-items: center;
    }

    .nav-item:hover,
    .nav-item.active {
        color: var(--header-text-hover);
        text-decoration: none;
    }

    .chevron-icon {
        transition: transform 0.2s ease;
    }

    .nav-item-container.has-children:hover .chevron-icon {
        transform: rotate(180deg);
    }

    /* Desktop Dropdown */
    .dropdown-menus {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(12px) scale(0.95);
        opacity: 0;
        visibility: hidden;
        background-color: #ffffff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
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
        background-color: #f7fafc;
        color: var(--header-text);
        text-decoration: none;
    }

    /* CTA Button (Black capsule pill) */
    .btn-cta {
        display: none;
        align-items: center;
        justify-content: center;
        background-color: var(--cta-bg);
        color: var(--cta-text);
        font-size: 0.85rem;
        font-weight: 600;
        padding: 10px 24px;
        border-radius: 12px; /* Smooth card-rounded button style */
        text-decoration: none;
        border: 1px solid var(--cta-bg);
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-cta:hover {
        background-color: var(--cta-hover-bg);
        border-color: var(--cta-hover-bg);
        color: var(--cta-text);
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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

    @media (min-width: 768px) {
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

    @media (max-width: 767px) {
        #header {
            width: calc(100% - 30px);
            top: 15px;
            max-height: 58px;
            overflow: hidden;
            transition: max-height 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        #header.menu-open {
            max-height: 90vh;
            overflow-y: auto;
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
            gap: 16px;
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
            font-size: 1rem;
            font-weight: 500;
            color: var(--header-text);
            text-decoration: none;
            padding: 4px 0;
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
            color: #555555;
            text-decoration: none;
            padding: 4px 0;
        }

        .mobile-cta {
            display: flex !important;
            width: 100%;
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
                    <img src="<?= $data->web->site_logo_alternative ?>" alt="Logo" onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='block';">
                <?php endif; ?>
                <span id="logo-fallback" class="logo-text-fallback" style="<?= empty($data->web->site_logo_alternative) ? 'display: block;' : 'display: none;' ?> font-family: Inter, sans-serif; font-weight: 700; font-size: 1.2rem; color: #000000; text-decoration: none; letter-spacing: -0.3px;">
                    hey@silver.info
                </span>
            </a>

            <!-- Desktop Menu -->
            <?= render_desktop_menu($filtered_menu) ?>

            <!-- CTA and Hamburger -->
            <div class="header-actions">
                <a href="#signup" class="btn-cta">
                    <span>Sign up</span>
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
                $li_class = $has_child ? 'mobile-nav-item-container' : 'mobile-nav-item-container';
                ?>
                <li class="<?= $li_class ?>">
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

        <a href="#signup" class="btn-cta mobile-cta">
            <span>Sign up</span>
        </a>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const header = document.getElementById('header');
        const hamburgerBtn = document.querySelector('.hamburger-btn');
        const submenuToggles = document.querySelectorAll('.mobile-submenu-toggle');
        const sections = document.querySelectorAll('section, footer');
        const navItems = document.querySelectorAll('.desktop-nav .nav-item');
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
