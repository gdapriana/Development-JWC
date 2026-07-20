<?php
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

$filtered_menu = json_decode(json_encode(simplify_menu($data->menu->primary_menu)));
if (!function_exists('render_desktop_menu')) {
    function render_desktop_menu($menu_items, $is_submenu = false)
    {
        $html = $is_submenu ? '<ul class="dropdown-menus">' : '<ul class="desktop-nav">';
        foreach ($menu_items as $item) {
            $has_child = isset($item->child) && !empty($item->child);
            $li_class = $has_child ? 'nav-item-container has-children' : 'nav-item-container';
            $html .= '<li class="' . $li_class . '">';

            $html .= '<a href="' . htmlspecialchars($item->link) . '" class="nav-item">';
            $html .= htmlspecialchars($item->label);
            if ($has_child) {
                $html .= ' <span class="chevron-arrow">▼</span>';
            }
            $html .= '</a>';

            if ($has_child) {
                $html .= render_desktop_menu($item->child, true);
            }

            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
}

if (!function_exists('render_mobile_menu')) {
    function render_mobile_menu($menu_items, $is_submenu = false)
    {
        $html = $is_submenu ? '<ul class="mobile-submenu">' : '<ul class="mobile-nav-list">';
        foreach ($menu_items as $item) {
            $has_child = isset($item->child) && !empty($item->child);
            $li_class = $has_child ? 'mobile-nav-item-container has-children' : 'mobile-nav-item-container';
            $html .= '<li class="' . $li_class . '">';

            $html .= '<div class="mobile-nav-item-row">';
            $html .= '<a href="' . htmlspecialchars($item->link) . '" class="mobile-nav-item">';
            $html .= htmlspecialchars($item->label);
            $html .= '</a>';

            if ($has_child) {
                $html .= '<button class="mobile-submenu-toggle" aria-label="Toggle Submenu">';
                $html .= '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">';
                $html .= '<polyline points="6 9 12 15 18 9"></polyline>';
                $html .= '</svg>';
                $html .= '</button>';
            }
            $html .= '</div>';

            if ($has_child) {
                $html .= render_mobile_menu($item->child, true);
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
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 90px;
        z-index: 1000;
        background-color: transparent;
        border-bottom: 1px solid rgba(250, 248, 245, 0.08);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        align-items: center;

        .container-global {
            /* Remove default section padding */
            padding-top: 0;
            padding-bottom: 0;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0;
        }

        /* Logo styling */
        .header-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: transform 0.3s ease;

            &:hover {
                transform: scale(1.02);
                text-decoration: none;
            }

            .logo-icon {
                color: var(--color-gold);
                transition: color 0.4s ease;
                height: 70px;
                filter: brightness(0) saturate(100%) invert(100%) sepia(0%) saturate(0%) hue-rotate(137deg) brightness(103%) contrast(101%);
            }

            .logo-text {
                display: flex;
                flex-direction: column;

                .brand-name {
                    font-family: var(--primtext);
                    font-size: 1.1rem;
                    font-weight: 500;
                    letter-spacing: 0.05em;
                    color: var(--text-light);
                    line-height: 1.2;
                    transition: color 0.4s ease;
                }

                .brand-sub {
                    font-family: var(--subtext);
                    font-size: 0.65rem;
                    font-weight: 500;
                    letter-spacing: 0.25em;
                    color: var(--color-gold);
                    line-height: 1.1;
                    margin-top: 0.1rem;
                    transition: color 0.4s ease;
                }
            }
        }

        /* Hide desktop nav by default on mobile */
        .desktop-nav {
            display: none;
        }

        /* Header action CTA & Hamburger */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .btn-header-cta {
            display: none;
            /* Hidden on mobile by default */
        }

        /* Hamburger button */
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

            .bar {
                width: 22px;
                height: 1.5px;
                background-color: var(--text-light);
                transition: all 0.3s ease;
            }

            &:focus {
                outline: none;
            }
        }

        /* Mobile nav overlay */
        .mobile-nav-overlay {
            position: fixed;
            top: 70px;
            left: 0;
            width: 100%;
            height: 0;
            background-color: rgba(15, 34, 20, 0.98);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            z-index: 999;
            overflow-y: auto;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            justify-content: center;

            .mobile-nav-menu {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 1.5rem;
                width: 100%;
                padding: 3rem 1.5rem;
                opacity: 0;
                transform: translateY(20px);
                transition: all 0.4s ease;
            }

            .mobile-nav-list {
                list-style: none;
                padding: 0;
                margin: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 1.25rem;
                width: 100%;
            }

            .mobile-nav-item-container {
                list-style: none;
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;

                .mobile-nav-item-row {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                }

                .mobile-submenu-toggle {
                    background: transparent;
                    border: none;
                    outline: none;
                    color: var(--text-muted-light);
                    cursor: pointer;
                    padding: 0.25rem;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: transform 0.3s ease, color 0.3s ease;

                    svg {
                        width: 16px;
                        height: 16px;
                    }

                    &:hover {
                        color: var(--color-gold);
                    }
                }

                /* Submenu vertical expansion styling */
                .mobile-submenu {
                    list-style: none;
                    padding: 0;
                    margin: 0;
                    max-height: 0;
                    overflow: hidden;
                    opacity: 0;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 0.75rem;
                    width: 100%;
                    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);

                    .mobile-nav-item {
                        font-size: 0.95rem;
                        text-transform: capitalize;
                    }
                }

                /* State when submenu is open */
                &.submenu-open {
                    >.mobile-nav-item-row .mobile-submenu-toggle {
                        transform: rotate(180deg);
                        color: var(--color-gold);
                    }

                    >.mobile-submenu {
                        max-height: 800px;
                        opacity: 1;
                        padding-top: 0.75rem;
                        padding-bottom: 0.25rem;
                    }
                }
            }

            .mobile-nav-item {
                font-family: var(--subtext);
                font-size: 1.15rem;
                font-weight: 500;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                color: var(--text-muted-light);
                text-decoration: none;
                transition: color 0.3s ease;

                &.active,
                &:hover {
                    color: var(--color-gold);
                }
            }

            .btn-mobile-nav-cta {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0.75rem 2rem;
                font-family: var(--subtext);
                font-weight: 500;
                font-size: 0.85rem;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                color: var(--bg-dark);
                background-color: var(--color-gold);
                border: 1px solid var(--color-gold);
                border-radius: 50px;
                transition: all 0.3s ease;

                &:hover {
                    background-color: var(--color-gold-hover);
                    border-color: var(--color-gold-hover);
                    color: var(--bg-dark);
                    text-decoration: none;
                }
            }
        }

        /* Scrolled down state (White background header) */
        &.scrolled {
            background-color: #ffffff;
            height: 80px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border-bottom-color: rgba(15, 34, 20, 0.06);

            .header-logo {

                .logo-icon {
                    filter: none;
                }

                .logo-text {
                    .brand-name {
                        color: var(--text-dark);
                    }
                }
            }

            .hamburger-btn {
                .bar {
                    background-color: var(--text-dark);
                }
            }

            .mobile-nav-overlay {
                top: 80px;
                background-color: rgba(255, 255, 255, 0.98);

                .mobile-nav-item {
                    color: var(--text-muted-dark);

                    &.active,
                    &:hover {
                        color: var(--color-gold);
                    }
                }

                .mobile-submenu-toggle {
                    color: var(--text-muted-dark);

                    &:hover {
                        color: var(--color-gold);
                    }
                }
            }
        }

        /* Menu Open state styles */
        &.menu-open {
            background-color: rgba(15, 34, 20, 0.98) !important;
            border-bottom-color: rgba(250, 248, 245, 0.08);

            .header-logo {
                .logo-icon {
                    filter: none;
                    margin-bottom: 20px;
                    filter: brightness(0) saturate(100%) invert(100%) sepia(0%) saturate(0%) hue-rotate(137deg) brightness(103%) contrast(101%);
                }

                .logo-text {
                    .brand-name {
                        color: var(--text-light) !important;
                    }
                }
            }

            .hamburger-btn {
                .bar {
                    background-color: var(--text-light) !important;
                }

                .bar-1 {
                    transform: translateY(6.5px) rotate(45deg);
                }

                .bar-2 {
                    opacity: 0;
                    transform: scaleX(0);
                }

                .bar-3 {
                    transform: translateY(-6.5px) rotate(-45deg);
                }
            }

            .mobile-nav-overlay {
                height: calc(100vh - 70px);

                .mobile-nav-menu {
                    opacity: 1;
                    transform: translateY(0);
                    transition-delay: 0.15s;
                }
            }

            &.scrolled {
                .mobile-nav-overlay {
                    height: calc(100vh - 80px);
                    top: 80px;
                }
            }
        }
    }


    @media (min-width: 768px) {
        #header {
            height: 100px;

            .header-logo {
                .logo-text {
                    .brand-name {
                        font-size: 1.35rem;
                    }

                    .brand-sub {
                        font-size: 0.75rem;
                        letter-spacing: 0.35em;
                    }
                }
            }

            .desktop-nav {
                display: flex;
                align-items: center;
                gap: 1.25rem;
                list-style: none;
                padding: 0;
                margin: 0;

                .nav-item-container {
                    position: relative;
                    list-style: none;
                }

                .nav-item {
                    font-family: var(--subtext);
                    font-size: 0.75rem;
                    font-weight: 500;
                    letter-spacing: 0.08em;
                    text-transform: uppercase;
                    color: var(--text-light);
                    text-decoration: none;
                    transition: all 0.3s ease;
                    position: relative;
                    padding: 0.5rem 0;

                    &::after {
                        content: '';
                        position: absolute;
                        bottom: 0;
                        left: 0;
                        width: 0;
                        height: 1px;
                        background-color: var(--color-gold);
                        transition: width 0.3s ease;
                    }

                    &.active,
                    &:hover {
                        color: var(--color-gold);

                        &::after {
                            width: 100%;
                        }
                    }
                }
            }

            /* Desktop Dropdown Submenus Styling (Interactive In-Out Animations) */
            .has-children {
                position: relative;

                .chevron-arrow {
                    font-size: 0.6rem;
                    margin-left: 4px;
                    vertical-align: middle;
                    display: inline-block;
                    transition: transform 0.3s ease;
                }

                &:hover>a .chevron-arrow {
                    transform: rotate(180deg);
                }

                /* Initial Closed State (Interactive Out Animation Setup) */
                .dropdown-menus {
                    position: absolute;
                    top: 100%;
                    left: 50%;
                    transform: translateX(-50%) translateY(12px) scale(0.95);
                    opacity: 0;
                    visibility: hidden;
                    background-color: #ffffff;
                    box-shadow: 0 10px 30px rgba(15, 34, 20, 0.12);
                    border-radius: 8px;
                    padding: 0.75rem 0;
                    min-width: 200px;
                    list-style: none;
                    z-index: 100;
                    margin: 0;

                    /* Transition for smooth fade, scale, and slide out */
                    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                        opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                        visibility 0.25s cubic-bezier(0.16, 1, 0.3, 1);

                    /* Hover bridge pseudo-element: prevents menu closure when cursor moves over the gap */
                    &::before {
                        content: '';
                        position: absolute;
                        top: -12px;
                        left: 0;
                        width: 100%;
                        height: 12px;
                        background-color: transparent;
                    }

                    .nav-item-container {
                        width: 100%;

                        .nav-item {
                            display: block;
                            padding: 0.5rem 1.5rem;
                            font-size: 0.8rem;
                            text-transform: capitalize;
                            color: var(--text-dark) !important;
                            letter-spacing: 0.03em;
                            transition: all 0.2s ease;
                            text-align: left;

                            &::after {
                                display: none;
                                /* Hide bottom line effect for dropdown sub-items */
                            }

                            &:hover {
                                background-color: #f7f4ee;
                                color: var(--color-gold) !important;
                            }
                        }
                    }

                    /* Nested dropdown submenus (3rd level flyouts) */
                    .dropdown-menus {
                        top: 0;
                        left: 100%;
                        transform: translateX(12px) scale(0.95);
                        margin-top: -0.75rem;
                        /* Aligns 3rd level menu vertically with its parent top padding */

                        &::before {
                            content: '';
                            position: absolute;
                            top: 0;
                            left: -12px;
                            width: 12px;
                            height: 100%;
                            background-color: transparent;
                        }
                    }

                    .has-children:hover>.dropdown-menus {
                        transform: translateX(4px) scale(1);
                        opacity: 1;
                        visibility: visible;
                    }
                }

                /* Active Hover State (Interactive In Animation) */
                &:hover>.dropdown-menus {
                    opacity: 1;
                    visibility: visible;
                    transform: translateX(-50%) translateY(4px) scale(1);
                }
            }

            .btn-header-cta {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0.65rem 1.5rem;
                font-family: var(--subtext);
                font-weight: 500;
                font-size: 0.75rem;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                color: var(--text-light);
                background-color: rgba(15, 34, 20, 0.4);
                border: 1px solid var(--color-gold);
                border-radius: 50px;
                /* Pill button shape as in the design */
                transition: all 0.3s ease;
                backdrop-filter: blur(5px);
                -webkit-backdrop-filter: blur(5px);

                &:hover {
                    background-color: var(--color-gold);
                    color: var(--bg-dark);
                    text-decoration: none;
                    box-shadow: 0 4px 15px rgba(196, 162, 117, 0.25);
                }
            }

            .hamburger-btn {
                display: none;
                /* Hide hamburger on desktop */
            }

            /* Scrolled overrides */
            &.scrolled {
                height: 110px;

                .desktop-nav {
                    .nav-item {
                        color: var(--text-dark);

                        &.active,
                        &:hover {
                            color: var(--color-gold);
                        }
                    }
                }

                .btn-header-cta {
                    background-color: var(--bg-dark);
                    border-color: var(--bg-dark);

                    &:hover {
                        background-color: var(--color-gold);
                        border-color: var(--color-gold);
                        color: var(--bg-dark);
                    }
                }
            }
        }
    }
</style>


<header id="header">
    <div class="container-global">
        <div class="content header-content">
            <!-- Logo -->
            <a href="<?= base_url(); ?>" class="header-logo">
                <img class="logo-icon" src="<?= $data->web->site_logo_alternative ?>" alt="Drago Wijaya Estate">
            </a>

            <!-- Desktop Navigation Menu Links (Rendered dynamically with children dropdowns) -->
            <?= render_desktop_menu($filtered_menu) ?>

            <!-- Desktop CTA and Mobile Toggle Button -->
            <div class="header-actions">
                <a href="#cta" class="btn-header-cta">
                    Book A Site Visit
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 6px;">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                </a>

                <!-- Mobile Hamburger Button -->
                <button class="hamburger-btn" aria-label="Toggle Navigation Menu">
                    <span class="bar bar-1"></span>
                    <span class="bar bar-2"></span>
                    <span class="bar bar-3"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Fullscreen Overlay Dropdown -->
    <div class="mobile-nav-overlay">
        <div class="mobile-nav-menu">
            <!-- Mobile Navigation Menu Links (Rendered dynamically with expandable accordions) -->
            <?= render_mobile_menu($filtered_menu) ?>

            <a href="#cta" class="btn-mobile-nav-cta mt-4">Book A Site Visit</a>
        </div>
    </div>
</header>




<script>
    document.addEventListener('DOMContentLoaded', () => {
        const header = document.getElementById('header');
        const hamburgerBtn = document.querySelector('.hamburger-btn');
        const mobileNavLinks = document.querySelectorAll('.mobile-nav-item');
        const sections = document.querySelectorAll('section, footer');
        const navItems = document.querySelectorAll('.nav-item');
        const mobileNavItems = document.querySelectorAll('.mobile-nav-item');

        // 1. Sticky Header Scroll Animation (Background Transition)
        const handleHeaderScroll = () => {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        };

        window.addEventListener('scroll', handleHeaderScroll);
        handleHeaderScroll(); // Trigger on load in case page starts scrolled down

        // 2. Hamburger Mobile Navigation Toggle
        if (hamburgerBtn) {
            hamburgerBtn.addEventListener('click', () => {
                header.classList.toggle('menu-open');
                if (header.classList.contains('menu-open')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });
        }

        mobileNavLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                // Only close the menu if the link is a target link, not toggling submenus
                if (!link.closest('.mobile-nav-item-container').classList.contains('has-children') || e.target.tagName === 'A') {
                    header.classList.remove('menu-open');
                    document.body.style.overflow = '';
                }
            });
        });

        const mobileSubmenuToggles = document.querySelectorAll('.mobile-submenu-toggle');
        mobileSubmenuToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const parentLi = toggle.closest('.mobile-nav-item-container');
                parentLi.classList.toggle('submenu-open');
            });
        });

        const handleScrollspy = () => {
            let currentSectionId = 'hero';

            sections.forEach(section => {
                const sectionTop = section.offsetTop - 120;
                if (window.scrollY >= sectionTop) {
                    currentSectionId = section.getAttribute('id');
                }
            });

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
        };

        window.addEventListener('scroll', handleScrollspy);
        handleScrollspy();
    });
</script>