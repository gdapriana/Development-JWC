<?php
// Initialize dummy data for testing if not provided by the platform
if (!isset($data)) {
    $data = new stdClass();
    $data->menu = new stdClass();
    
    // Primary menu structure matching the design
    $data->menu->primary_menu = [
        (object)['label' => 'Home', 'link' => '#home'],
        (object)['label' => 'Features', 'link' => '#features'],
        (object)['label' => 'Plan & Pricing', 'link' => '#pricing'],
        (object)['label' => 'About Us', 'link' => '#about']
    ];

    $data->web = new stdClass();
    // Default empty to trigger the beautiful fallback logo
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
            $active_class = (strtolower($item->label) === 'home') ? ' active' : '';
            
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
        --header-bg: #ffffff;
        --header-text: #111111;
        --header-text-hover: #ff5f1f; /* Lulu Orange */
        --header-border: rgba(0, 0, 0, 0.05);
        --header-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        --slash-color: #babcbf;
        
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 76px;
        background-color: var(--header-bg);
        border-bottom: 1px solid var(--header-border);
        box-shadow: var(--header-shadow);
        z-index: 100000;
        transition: all 0.3s ease;
        font-family: var(--primtext), sans-serif;
    }

    #header.scrolled {
        height: 68px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04);
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

    /* Desktop Navigation Menu (Separated by Slashes) */
    .desktop-nav {
        display: none;
        align-items: center;
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

    /* Slash Separator styling */
    .nav-item-container::after {
        content: '/';
        margin-left: 14px;
        margin-right: 14px;
        color: var(--slash-color);
        font-weight: 300;
        font-size: 0.9rem;
    }

    .nav-item-container:last-child::after {
        content: none;
    }

    .nav-item {
        font-size: 0.9rem;
        font-weight: 500;
        color: #555555;
        text-decoration: none;
        transition: color 0.25s ease;
        padding: 8px 0;
        display: inline-flex;
        align-items: center;
    }

    .nav-item:hover,
    .nav-item-container:hover > .nav-item {
        color: var(--header-text-hover);
        text-decoration: none;
    }

    /* Active Menu State */
    .nav-item.active {
        color: var(--header-text);
        font-weight: 700;
    }

    .chevron-icon {
        transition: transform 0.25s ease;
    }

    .nav-item-container.has-children:hover .chevron-icon {
        transform: rotate(180deg);
    }

    /* Dropdown Menus (Desktop) */
    .dropdown-menus {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(12px);
        opacity: 0;
        visibility: hidden;
        background-color: #ffffff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 8px;
        padding: 8px 0;
        min-width: 180px;
        list-style: none;
        z-index: 1000;
        margin: 0;
        transition: transform 0.2s ease, opacity 0.2s ease, visibility 0.2s ease;
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
        color: #555555;
        text-decoration: none;
        transition: all 0.2s ease;
        text-align: left;
    }

    .dropdown-item:hover {
        background-color: #f7fafc;
        color: var(--header-text-hover);
        text-decoration: none;
    }

    /* Header Actions & Hamburger */
    .header-actions {
        display: flex;
        align-items: center;
    }

    /* Mobile 2-line Hamburger */
    .hamburger-btn {
        width: 32px;
        height: 32px;
        background: transparent;
        border: none;
        outline: none;
        position: relative;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 6px;
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

    @media (min-width: 768px) {
        .desktop-nav {
            display: flex;
        }
    }

    @media (max-width: 767px) {
        #header {
            height: 70px;
        }

        #header.scrolled {
            height: 64px;
        }

        .header-content {
            padding: 0 20px;
        }

        .header-logo img {
            height: 30px;
        }

        /* Hamburger 2-line animation to Cross */
        #header.menu-open .bar-1 {
            transform: translateY(4px) rotate(45deg);
        }

        #header.menu-open .bar-2 {
            transform: translateY(-4px) rotate(-45deg);
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
            background-color: #ffffff;
            border-bottom: 1px solid var(--header-border);
            overflow: hidden;
            transition: height 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            box-sizing: border-box;
        }

        #header.scrolled.menu-open .mobile-nav-container {
            top: 64px;
            height: calc(100vh - 64px);
        }

        #header.menu-open .mobile-nav-container {
            height: calc(100vh - 70px);
            overflow-y: auto;
            padding: 24px 20px;
        }

        .mobile-nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .mobile-nav-item-container {
            display: flex;
            flex-direction: column;
            border-bottom: 1px solid #f7fafc;
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
            font-size: 1rem;
            font-weight: 500;
            color: #555555;
            text-decoration: none;
            padding: 4px 0;
        }

        .mobile-nav-item.active {
            color: var(--header-text-hover);
            font-weight: 700;
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
            padding: 4px 0 0 16px;
            margin: 4px 0 0 0;
            display: none;
            flex-direction: column;
            gap: 12px;
            border-left: 2px solid var(--header-text-hover);
        }

        .mobile-submenu-open .mobile-submenu {
            display: flex;
        }

        .mobile-sublink {
            font-size: 0.95rem;
            color: #555555;
            text-decoration: none;
            padding: 4px 0;
            display: block;
            text-align: left;
        }

        .mobile-sublink:hover {
            color: var(--header-text-hover);
        }
    }
</style>

<header id="header">
    <div class="container-global">
        <div class="content header-content">
            <!-- Logo -->
            <a href="<?= function_exists('base_url') ? base_url() : '#'; ?>" class="header-logo">
                <?php if (!empty($data->web->site_logo_alternative)): ?>
                    <img src="<?= $data->web->site_logo_alternative ?>" alt="Lulu Logo" onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='flex';">
                <?php endif; ?>
                <span id="logo-fallback" class="logo-text-fallback" style="<?= empty($data->web->site_logo_alternative) ? 'display: flex;' : 'display: none;' ?> align-items: center; gap: 8px; text-decoration: none;">
                    <!-- Orange butterfly / wing stylized geometric shape logo -->
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="none">
                        <path d="M12 2a4 4 0 0 1 4 4v6h-4V2z" fill="#ff5f1f"/>
                        <path d="M12 22a4 4 0 0 1-4-4v-6h4v10z" fill="#ff5f1f"/>
                        <path d="M2 12a4 4 0 0 1 4-4h6v4H2v-0z" fill="#ff5f1f"/>
                        <path d="M22 12a4 4 0 0 1-4 4h-6v-4h10v0z" fill="#ff5f1f"/>
                    </svg>
                    <span style="font-family: sans-serif; font-weight: 800; font-size: 1.4rem; color: #111111; letter-spacing: -0.5px;">lulu</span>
                </span>
            </a>

            <!-- Desktop Menu -->
            <?= render_desktop_menu($filtered_menu) ?>

            <!-- Hamburger Button -->
            <div class="header-actions">
                <button class="hamburger-btn" aria-label="Toggle Navigation">
                    <span class="bar bar-1"></span>
                    <span class="bar bar-2"></span>
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
                <li class="mobile-nav-item-container">
                    <div class="mobile-nav-item-row">
                        <a href="<?= htmlspecialchars($item->link) ?>" class="mobile-nav-item <?= $active_class ?>"><?= htmlspecialchars($item->label) ?></a>
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
