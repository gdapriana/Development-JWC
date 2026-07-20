<?php
// Initialize dummy data for testing if not provided by the platform
if (!isset($data)) {
    $data = new stdClass();
    $data->menu = new stdClass();
    
    // Primary menu structure matching the design
    $data->menu->primary_menu = [
        (object)[
            'label' => 'Features',
            'link' => '#features',
            'child' => [
                (object)['label' => 'Video Editing', 'link' => '#video-editing'],
                (object)['label' => 'Audio Production', 'link' => '#audio'],
                (object)['label' => 'Color Grading', 'link' => '#color-grading']
            ]
        ],
        (object)['label' => 'Pricing', 'link' => '#pricing'],
        (object)[
            'label' => 'Resources',
            'link' => '#resources',
            'child' => [
                (object)['label' => 'Blog', 'link' => '#blog'],
                (object)['label' => 'Tutorials', 'link' => '#tutorials'],
                (object)['label' => 'FAQ', 'link' => '#faq']
            ]
        ],
        (object)['label' => 'Support', 'link' => '#support']
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
        $html = '<ul class="desktop-nav">';
        foreach ($menu_items as $item) {
            $has_child = isset($item->child) && !empty($item->child);
            $li_class = $has_child ? 'nav-item-container has-children' : 'nav-item-container';
            
            $html .= '<li class="' . $li_class . '">';
            $html .= '<a href="' . htmlspecialchars($item->link) . '" class="nav-item">';
            $html .= '<span>' . htmlspecialchars($item->label) . '</span>';
            if ($has_child) {
                $html .= '<svg class="chevron-icon" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left:4px;"><polyline points="6 9 12 15 18 9"></polyline></svg>';
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
        --header-bg: #04060f; /* Dark Navy Blue */
        --header-text: #ffffff;
        --header-text-hover: #a5b4fc; /* Indigo/Lavender */
        --header-border: rgba(255, 255, 255, 0.05);
        --header-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        
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
        background-color: rgba(4, 6, 15, 0.95);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
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
        transition: opacity 0.2s ease;
    }

    .header-logo:hover {
        opacity: 0.9;
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
        font-weight: 500;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.25s ease;
        padding: 8px 0;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .nav-item:hover,
    .nav-item-container:hover > .nav-item {
        color: var(--header-text);
        text-decoration: none;
    }

    .chevron-icon {
        transition: transform 0.2s ease;
        opacity: 0.8;
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
        background-color: #0b0e1a;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
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
        transform: translateX(-50%) translateY(0) scale(1);
    }

    .dropdown-item {
        display: block;
        padding: 8px 20px;
        font-size: 0.85rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: all 0.2s ease;
        text-align: left;
    }

    .dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.06);
        color: var(--header-text);
        text-decoration: none;
    }

    /* Header Actions & CTA */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .actions-group {
        display: none;
        align-items: center;
        gap: 16px;
    }

    .btn-icon {
        background: transparent;
        border: none;
        color: rgba(255, 255, 255, 0.8);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px;
        transition: color 0.2s ease;
        text-decoration: none;
    }

    .btn-icon:hover {
        color: var(--header-text-hover);
        text-decoration: none;
    }

    .action-divider {
        width: 1px;
        height: 18px;
        background-color: rgba(255, 255, 255, 0.15);
    }

    .btn-outline {
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #ffffff;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 10px 22px;
        border-radius: 999px;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-outline:hover {
        border-color: #ffffff;
        background-color: rgba(255, 255, 255, 0.05);
        color: #ffffff;
        text-decoration: none;
    }

    .btn-solid {
        background-color: #ffffff;
        color: #04060f;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 10px 22px;
        border-radius: 999px;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-solid:hover {
        background-color: #e5e7eb;
        color: #04060f;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(255, 255, 255, 0.1);
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
        .desktop-nav {
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
            background-color: #04060f;
            border-bottom: 1px solid var(--header-border);
            overflow: hidden;
            transition: height 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
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
            font-size: 1rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            padding: 4px 0;
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
            padding: 4px 0 0 16px;
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
            color: #ffffff;
        }

        /* Combined Actions on mobile */
        .mobile-actions-wrapper {
            display: flex;
            flex-direction: column;
            gap: 16px;
            width: 100%;
            margin-top: auto;
        }

        .mobile-icons-row {
            display: flex;
            justify-content: center;
            gap: 24px;
            padding-bottom: 8px;
        }

        .mobile-actions-wrapper .btn-outline,
        .mobile-actions-wrapper .btn-solid {
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
                    <img src="<?= $data->web->site_logo_alternative ?>" alt="DudeStudios Logo" onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='flex';">
                <?php endif; ?>
                <span id="logo-fallback" class="logo-text-fallback" style="<?= empty($data->web->site_logo_alternative) ? 'display: flex;' : 'display: none;' ?> align-items: center; gap: 8px; text-decoration: none;">
                    <!-- Futuristic/Creative DudeStudios fallback icon and logo text -->
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 22 8.5 22 15.5 12 22 2 15.5 2 8.5 12 2"></polygon>
                        <line x1="12" y1="22" x2="12" y2="12.5"></line>
                        <line x1="2" y1="8.5" x2="12" y2="12.5"></line>
                        <line x1="22" y1="8.5" x2="12" y2="12.5"></line>
                    </svg>
                    <span style="font-family: sans-serif; font-weight: 800; font-size: 1.35rem; color: #ffffff; letter-spacing: -0.5px;">DudeStudios</span>
                </span>
            </a>

            <!-- Desktop Menu -->
            <?= render_desktop_menu($filtered_menu) ?>

            <!-- CTA and Hamburger -->
            <div class="header-actions">
                <div class="actions-group">
                    <!-- Translation characters glyph icon -->
                    <a href="#lang" class="btn-icon" aria-label="Language selection">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 8l6 6"></path>
                            <path d="M4 14l6-6 2-3"></path>
                            <path d="M2 5h12"></path>
                            <path d="M7 2h1"></path>
                            <path d="M22 22l-5-10-5 10"></path>
                            <path d="M14 18h6"></path>
                        </svg>
                    </a>
                    
                    <!-- Globe Icon -->
                    <a href="#region" class="btn-icon" aria-label="Region selection">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                        </svg>
                    </a>
                    
                    <span class="action-divider"></span>
                    
                    <a href="#contact" class="btn-outline">Contact Us</a>
                    <a href="#start" class="btn-solid">Get Started</a>
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
            <div class="mobile-icons-row">
                <a href="#lang" class="btn-icon" aria-label="Language selection">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 8l6 6"></path>
                        <path d="M4 14l6-6 2-3"></path>
                        <path d="M2 5h12"></path>
                        <path d="M7 2h1"></path>
                        <path d="M22 22l-5-10-5 10"></path>
                        <path d="M14 18h6"></path>
                    </svg>
                </a>
                <a href="#region" class="btn-icon" aria-label="Region selection">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="2" y1="12" x2="22" y2="12"></line>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                    </svg>
                </a>
            </div>
            <a href="#contact" class="btn-outline">Contact Us</a>
            <a href="#start" class="btn-solid">Get Started</a>
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
