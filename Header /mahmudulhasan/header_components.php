<?php
// Initialize dummy data for testing if not provided by the platform
if (!isset($data)) {
    $data = new stdClass();
    $data->menu = new stdClass();
    
    // Primary menu structure with 5 items to trigger the "More" dropdown
    $data->menu->primary_menu = [
        (object)['label' => 'Cameras', 'link' => '#cameras'],
        (object)['label' => 'Printers', 'link' => '#printers'],
        (object)['label' => 'Films', 'link' => '#films'],
        (object)['label' => 'Lenses', 'link' => '#lenses'],
        (object)['label' => 'Accessories', 'link' => '#accessories']
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
        // Limit visible menu items to 3 on desktop, others go to "More" dropdown
        $max_visible = 3;
        $visible_items = array_slice($menu_items, 0, $max_visible);
        $overflow_items = array_slice($menu_items, $max_visible);
        
        $html = '<ul class="desktop-nav">';
        
        // Visible items
        foreach ($visible_items as $item) {
            $has_child = isset($item->child) && !empty($item->child);
            $li_class = $has_child ? 'nav-item-container has-children' : 'nav-item-container';
            $active_class = (strtolower($item->label) === 'cameras') ? ' active' : '';
            
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
        
        // Overflow items inside "More" dropdown
        if (!empty($overflow_items)) {
            $html .= '<li class="nav-item-container has-children more-menu-item">';
            $html .= '<a href="#" class="nav-item">';
            $html .= '<span>More</span>';
            $html .= '<svg class="chevron-icon" width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-left:4px;"><polyline points="6 9 12 15 18 9"></polyline></svg>';
            $html .= '</a>';
            
            $html .= '<ul class="dropdown-menus">';
            foreach ($overflow_items as $item) {
                $html .= '<li><a href="' . htmlspecialchars($item->link) . '" class="dropdown-item">' . htmlspecialchars($item->label) . '</a></li>';
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
        --header-bg: #ffffff;
        --header-text: #111111;
        --header-text-hover: #000000;
        --header-text-muted: #888888;
        --header-border: rgba(0, 0, 0, 0.05);
        --header-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        
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

    /* 3-column layout structure to center logo and align left menu and right actions */
    .header-left {
        flex: 1;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        height: 100%;
    }

    .header-center {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .header-right {
        flex: 1;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 16px;
        height: 100%;
    }

    /* Logo styling centered */
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

    /* Desktop Navigation Menu (Left aligned) */
    .desktop-nav {
        display: none;
        align-items: center;
        gap: 24px;
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
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--header-text-muted);
        text-decoration: none;
        transition: color 0.25s ease;
        padding: 8px 0;
        display: inline-flex;
        align-items: center;
        gap: 4px;
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
        opacity: 0.6;
    }

    .nav-item-container:hover .chevron-icon {
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

    /* Header Actions Group (Right aligned) */
    .actions-group {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .btn-icon {
        background: transparent;
        border: none;
        color: var(--header-text);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px;
        transition: color 0.2s ease;
        text-decoration: none;
    }

    .btn-icon:hover {
        color: var(--header-text-muted);
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
            height: 28px;
        }

        /* Shift centered logo to left on mobile */
        .header-content {
            justify-content: space-between;
        }
        
        .header-left {
            flex: initial;
            display: none;
        }
        
        .header-center {
            justify-content: flex-start;
        }
        
        .header-right {
            flex: initial;
        }

        /* Hamburger Animation */
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

        /* Mobile Drawer slide down */
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
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            box-sizing: border-box;
        }

        #header.scrolled.menu-open .mobile-nav-container {
            top: 64px;
            height: calc(100vh - 64px);
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
            color: var(--header-text-muted);
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
            border-left: 2px solid var(--header-text-muted);
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

        .mobile-actions-wrapper {
            display: flex;
            justify-content: center;
            gap: 24px;
            padding-top: 16px;
            border-top: 1px solid #f7fafc;
            width: 100%;
            margin-top: auto;
        }
    }
</style>

<header id="header">
    <div class="container-global">
        <div class="content header-content">
            <!-- Left flex item (Desktop Menu) -->
            <div class="header-left">
                <?= render_desktop_menu($filtered_menu) ?>
            </div>

            <!-- Center flex item (Centered Logo) -->
            <div class="header-center">
                <a href="<?= function_exists('base_url') ? base_url() : '#'; ?>" class="header-logo">
                    <?php if (!empty($data->web->site_logo_alternative)): ?>
                        <img src="<?= $data->web->site_logo_alternative ?>" alt="devignededge Logo" onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='flex';">
                    <?php endif; ?>
                    <span id="logo-fallback" class="logo-text-fallback" style="<?= empty($data->web->site_logo_alternative) ? 'display: flex;' : 'display: none;' ?> align-items: center; text-decoration: none;">
                        <span style="font-family: sans-serif; font-weight: 800; font-size: 1.45rem; color: #111111; letter-spacing: -0.5px;">devignededge</span>
                    </span>
                </a>
            </div>

            <!-- Right flex item (Desktop Actions & Hamburger) -->
            <div class="header-right">
                <div class="actions-group">
                    <!-- Search Icon -->
                    <button class="btn-icon" aria-label="Search">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                    
                    <!-- Basket Icon -->
                    <a href="#cart" class="btn-icon" aria-label="Cart">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 10h20"></path>
                            <path d="M3.5 10l1.5 10h14l1.5-10"></path>
                            <path d="M8 10L12 3l4 7"></path>
                        </svg>
                    </a>
                    
                    <!-- User Icon -->
                    <a href="#profile" class="btn-icon" aria-label="Profile">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </a>
                </div>

                <!-- Mobile Hamburger -->
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
                $active_class = (strtolower($item->label) === 'cameras') ? 'active' : '';
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

        <div class="mobile-actions-wrapper">
            <!-- Search -->
            <button class="btn-icon" aria-label="Search">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </button>
            
            <!-- Basket -->
            <a href="#cart" class="btn-icon" aria-label="Cart">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 10h20"></path>
                    <path d="M3.5 10l1.5 10h14l1.5-10"></path>
                    <path d="M8 10L12 3l4 7"></path>
                </svg>
            </a>
            
            <!-- User -->
            <a href="#profile" class="btn-icon" aria-label="Profile">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
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
