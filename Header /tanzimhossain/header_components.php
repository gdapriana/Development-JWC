<?php
// Initialize dummy data for testing if not provided by the platform
if (!isset($data)) {
    $data = new stdClass();
    $data->menu = new stdClass();
    
    // Primary menu structure matching the design, with potential children on all items
    $data->menu->primary_menu = [
        (object)['label' => 'Home', 'link' => '#home'],
        (object)[
            'label' => 'Top Picks',
            'link' => '#top-picks',
            'child' => [
                (object)['label' => 'Gold IRA Companies', 'link' => '#gold-companies'],
                (object)['label' => 'Silver IRA Companies', 'link' => '#silver-companies'],
                (object)['label' => 'Platinum IRA Companies', 'link' => '#platinum-companies']
            ]
        ],
        (object)[
            'label' => 'Reviews',
            'link' => '#reviews',
            'child' => [
                (object)['label' => 'Augusta Precious Metals', 'link' => '#augusta-review'],
                (object)['label' => 'Goldco Review', 'link' => '#goldco-review'],
                (object)['label' => 'American Hartford Review', 'link' => '#hartford-review']
            ]
        ],
        (object)[
            'label' => 'How to Choose',
            'link' => '#how-to-choose',
            'child' => [
                (object)['label' => 'Choosing a Custodian', 'link' => '#custodian'],
                (object)['label' => 'Avoiding Scams', 'link' => '#scams'],
                (object)['label' => 'Gold Storage Options', 'link' => '#storage']
            ]
        ],
        (object)['label' => 'What is a Gold IRA?', 'link' => '#what-is-gold-ira'],
        (object)['label' => 'FAQ', 'link' => '#faq']
    ];

    $data->web = new stdClass();
    // Default empty to trigger the beautiful golden SVG logo
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
                $html .= '<svg class="chevron-icon" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>';
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
        --header-text: #062c1e; /* Deep forest green */
        --header-text-hover: #aa8010; /* Gold */
        --header-border: rgba(0, 0, 0, 0.05);
        --header-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        --accent-gold: #d4af37;
        
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
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
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
        transform: scale(1.01);
        text-decoration: none;
    }

    .header-logo img {
        height: 42px;
        display: block;
    }

    /* Desktop Navigation Menu */
    .desktop-nav {
        display: none;
        align-items: center;
        gap: 28px;
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
        font-weight: 600;
        color: var(--header-text);
        text-decoration: none;
        transition: color 0.25s ease;
        padding: 8px 0;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        position: relative;
    }

    /* Hover Gold Underline Effect */
    .nav-item::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 50%;
        width: 0;
        height: 3px;
        background-color: var(--accent-gold);
        transition: width 0.3s ease, left 0.3s ease;
    }

    .nav-item:hover::after,
    .nav-item-container:hover > .nav-item::after {
        width: 100%;
        left: 0;
    }

    .nav-item:hover,
    .nav-item-container:hover > .nav-item {
        color: var(--header-text-hover);
        text-decoration: none;
    }

    /* Active Menu State */
    .nav-item.active {
        color: var(--header-text);
    }

    .nav-item.active::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: var(--accent-gold);
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
        left: 0;
        transform: translateY(12px);
        opacity: 0;
        visibility: hidden;
        background-color: #ffffff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 8px;
        padding: 10px 0;
        min-width: 220px;
        list-style: none;
        z-index: 1000;
        margin: 0;
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                    opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                    visibility 0.25s cubic-bezier(0.16, 1, 0.3, 1);
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

    .nav-item-container.has-children:hover .dropdown-menus,
    .nav-item-container.has-children.menu-active .dropdown-menus {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-item {
        display: block;
        padding: 8px 20px;
        font-size: 0.85rem;
        font-weight: 500;
        color: #4a5568;
        text-decoration: none;
        transition: all 0.2s ease;
        text-align: left;
    }

    .dropdown-item:hover {
        background-color: #f7fafc;
        color: var(--header-text-hover);
        text-decoration: none;
    }

    /* CTA Button (Forest Green Pill) */
    .btn-cta {
        display: none;
        align-items: center;
        justify-content: center;
        background-color: var(--header-text);
        color: #ffffff;
        font-size: 0.9rem;
        font-weight: 600;
        padding: 10px 22px;
        border-radius: 8px;
        text-decoration: none;
        border: 1px solid var(--header-text);
        transition: all 0.25s ease;
    }

    .btn-cta:hover {
        background-color: var(--accent-gold);
        border-color: var(--accent-gold);
        color: #ffffff;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
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
            height: 70px;
        }

        #header.scrolled {
            height: 64px;
        }

        .header-content {
            padding: 0 20px;
        }

        .header-logo img {
            height: 34px;
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
            font-weight: 600;
            color: var(--header-text);
            text-decoration: none;
            padding: 4px 0;
        }

        .mobile-nav-item.active {
            color: var(--accent-gold);
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
            border-left: 2px solid var(--accent-gold);
        }

        .mobile-submenu-open .mobile-submenu {
            display: flex;
        }

        .mobile-sublink {
            font-size: 0.95rem;
            color: #4a5568;
            text-decoration: none;
            padding: 4px 0;
            display: block;
            text-align: left;
        }

        .mobile-sublink:hover {
            color: var(--accent-gold);
        }

        .mobile-cta-wrapper {
            width: 100%;
            margin-top: auto;
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
                    <img src="<?= $data->web->site_logo_alternative ?>" alt="Gold IRA For You Logo" onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='flex';">
                <?php endif; ?>
                <span id="logo-fallback" class="logo-text-fallback" style="<?= empty($data->web->site_logo_alternative) ? 'display: flex;' : 'display: none;' ?> align-items: center; gap: 10px; text-decoration: none;">
                    <!-- 3D Gold Bar block fallback SVG -->
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="none">
                        <path d="M12 2L3 7l9 5 9-5-9-5z" fill="#d4af37"/>
                        <path d="M3 7v10l9 5V12L3 7z" fill="#aa8010"/>
                        <path d="M21 7v10l-9 5V12l9-5z" fill="#f3cb52"/>
                        <circle cx="12" cy="12" r="4.5" fill="#ffffff" stroke="#aa8010" stroke-width="1"/>
                        <text x="12" y="15.2" font-family="sans-serif" font-weight="900" font-size="9" fill="#aa8010" text-anchor="middle">$</text>
                    </svg>
                    <span style="font-family: Georgia, serif; font-weight: 800; font-size: 1.4rem; color: #062c1e; letter-spacing: -0.3px;">Gold IRA For You</span>
                </span>
            </a>

            <!-- Desktop Menu -->
            <?= render_desktop_menu($filtered_menu) ?>

            <!-- CTA and Hamburger -->
            <div class="header-actions">
                <a href="#cta" class="btn-cta">
                    <span>See Top Picks</span>
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

        <div class="mobile-cta-wrapper">
            <a href="#cta" class="btn-cta mobile-cta">
                <span>See Top Picks</span>
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

        // Close other desktop dropdowns when one is clicked (for touch screens/click fallback)
        const hasChildrenItems = document.querySelectorAll('.nav-item-container.has-children');
        hasChildrenItems.forEach(item => {
            const link = item.querySelector('.nav-item');
            link.addEventListener('click', (e) => {
                if (window.innerWidth >= 992) {
                    // Prevent navigation if clicking the toggle link itself and show/hide dropdown
                    e.preventDefault();
                    e.stopPropagation();
                    hasChildrenItems.forEach(other => {
                        if (other !== item) other.classList.remove('menu-active');
                    });
                    item.classList.toggle('menu-active');
                }
            });
        });

        // Click outside closes desktop dropdowns
        document.addEventListener('click', () => {
            hasChildrenItems.forEach(item => item.classList.remove('menu-active'));
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
