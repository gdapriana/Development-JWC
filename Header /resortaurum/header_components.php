<?php
// Initialize dummy data for testing if not provided by the platform
if (!isset($data)) {
    $data = new stdClass();
    $data->menu = new stdClass();
    
    $data->menu->primary_menu = [
        (object)['label' => 'Home', 'link' => '#home'],
        (object)[
            'label' => 'Villas',
            'link' => '#villas',
            'child' => [
                (object)['label' => 'Royal Aurum Suite', 'link' => '#royal'],
                (object)['label' => 'Terrace Pool Villa', 'link' => '#terrace'],
                (object)['label' => 'Ocean Sanctuary', 'link' => '#ocean']
            ]
        ],
        (object)[
            'label' => 'Gastronomy',
            'link' => '#gastronomy',
            'child' => [
                (object)['label' => 'L\'Or Restaurant', 'link' => '#lor'],
                (object)['label' => 'Céleste Bar', 'link' => '#celeste']
            ]
        ],
        (object)['label' => 'Wellness', 'link' => '#wellness'],
        (object)['label' => 'Heritage', 'link' => '#heritage'],
        (object)['label' => 'Inquire', 'link' => '#inquire']
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

if (!function_exists('render_desktop_menu_aurum')) {
    function render_desktop_menu_aurum($menu_items)
    {
        $max_visible = 3;
        $visible = array_slice($menu_items, 0, $max_visible);
        $overflow = array_slice($menu_items, $max_visible);
        
        $html = '<ul class="desktop-nav">';
        $first = true;
        
        foreach ($visible as $item) {
            $has_child = isset($item->child) && !empty($item->child);
            $li_class = $has_child ? 'nav-item-container has-children' : 'nav-item-container';
            
            if (!$first) {
                // Golden diamond separator
                $html .= '<li class="nav-separator">✦</li>';
            }
            $first = false;
            
            $html .= '<li class="' . $li_class . '">';
            $html .= '<a href="' . htmlspecialchars($item->link) . '" class="nav-item">' . htmlspecialchars($item->label) . '</a>';
            if ($has_child) {
                $html .= '<ul class="desktop-dropdown">';
                foreach ($item->child as $child) {
                    $html .= '<li><a href="' . htmlspecialchars($child->link) . '">' . htmlspecialchars($child->label) . '</a></li>';
                }
                $html .= '</ul>';
            }
            $html .= '</li>';
        }
        
        if (!empty($overflow)) {
            $html .= '<li class="nav-separator">✦</li>';
            $html .= '<li class="nav-item-container has-children more-menu-item">';
            $html .= '<a href="#" class="nav-item">More</a>';
            $html .= '<ul class="desktop-dropdown">';
            foreach ($overflow as $item) {
                $html .= '<li><a href="' . htmlspecialchars($item->link) . '">' . htmlspecialchars($item->label) . '</a></li>';
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
        --header-bg: rgba(255, 255, 255, 0.03);
        --header-text: #ffffff;
        --header-text-hover: #d4af37; /* Royal Gold */
        --header-border: rgba(255, 255, 255, 0.08);
        --header-shadow: none;
        --accent-gold: #d4af37;
        
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 84px;
        background-color: var(--header-bg);
        border-bottom: 1px solid var(--header-border);
        z-index: 100000;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        font-family: var(--primtext), sans-serif;
        opacity: 0; /* Animated by GSAP on load */
    }

    #header.scrolled {
        height: 74px;
        background-color: rgba(10, 10, 10, 0.96); /* Dark luxury gold theme */
        border-bottom-color: rgba(212, 175, 55, 0.15); /* Warm gold border */
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    }

    .container-global {
        width: 100%;
        height: 100%;
    }

    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        max-width: 1300px;
        margin: 0 auto;
        padding: 0 32px;
        box-sizing: border-box;
        height: 100%;
    }

    /* Logo styling */
    .header-logo {
        display: flex;
        align-items: center;
        text-decoration: none;
        gap: 8px;
    }

    .header-logo img {
        height: 30px;
        display: block;
    }

    .logo-text-fallback {
        font-family: 'Playfair Display', Georgia, serif;
        font-weight: 600;
        font-size: 1.25rem;
        color: #ffffff;
        letter-spacing: 2.5px;
        text-transform: uppercase;
    }

    /* Desktop Navigation with Diamond Separators */
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

    .nav-separator {
        color: var(--accent-gold);
        font-size: 0.65rem;
        padding: 0 16px;
        opacity: 0.6;
        user-select: none;
    }

    .nav-item {
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 1.5px;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        text-transform: uppercase;
        padding: 10px 0;
        transition: color 0.3s ease, text-shadow 0.3s ease;
    }

    .nav-item:hover,
    .nav-item-container:hover > .nav-item {
        color: var(--header-text-hover);
        text-decoration: none;
        text-shadow: 0 0 10px rgba(212,175,55,0.3);
    }

    /* Desktop dropdown */
    .desktop-dropdown {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(12px);
        opacity: 0;
        visibility: hidden;
        background-color: #0d0d0d;
        border: 1px solid rgba(212, 175, 55, 0.2);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
        border-radius: 4px;
        padding: 8px 0;
        min-width: 180px;
        list-style: none;
        z-index: 1000;
        margin: 0;
        transition: all 0.3s ease;
    }

    /* Bridge gap */
    .desktop-dropdown::before {
        content: '';
        position: absolute;
        top: -12px;
        left: 0;
        width: 100%;
        height: 12px;
        background-color: transparent;
    }

    .nav-item-container:hover .desktop-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0);
    }

    .desktop-dropdown a {
        display: block;
        padding: 8px 16px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 1px;
        color: rgba(255, 255, 255, 0.7) !important;
        text-decoration: none;
        text-transform: uppercase;
        text-align: left;
    }

    .desktop-dropdown a:hover {
        color: var(--accent-gold) !important;
        background-color: rgba(255, 255, 255, 0.02);
    }

    /* Actions side */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .btn-cta {
        display: none;
        align-items: center;
        justify-content: center;
        background: transparent;
        color: #ffffff;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 1.5px;
        padding: 10px 24px;
        border-radius: 0;
        text-decoration: none;
        text-transform: uppercase;
        transition: all 0.3s ease;
        border: 1px solid var(--accent-gold);
    }

    .btn-cta:hover {
        background-color: var(--accent-gold);
        color: #000000;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
    }

    /* Gold lined Hamburger */
    .hamburger-btn-aurum {
        width: 40px;
        height: 40px;
        background: transparent;
        border: none;
        outline: none;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 5px;
        padding: 0;
        z-index: 1001;
    }

    .hamburger-btn-aurum .bar {
        width: 20px;
        height: 1.5px;
        background-color: #ffffff;
        transition: all 0.3s ease;
    }

    .hamburger-btn-aurum:hover .bar {
        background-color: var(--accent-gold);
    }

    /* Fullscreen Overlay with Split vertical slices */
    .slices-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 1000000;
        display: none;
        overflow: hidden;
    }

    /* 4 Vertical slices sliding down */
    .overlay-slices-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        z-index: 1;
    }

    .slice {
        height: 100%;
        width: 25%;
        background-color: #0a0a0a;
        border-right: 1px solid rgba(212, 175, 55, 0.02);
        transform: scaleY(0);
        transform-origin: top center;
    }

    .overlay-content {
        position: relative;
        z-index: 2;
        width: 100%;
        height: 100%;
        display: grid;
        grid-template-columns: 1fr;
        box-sizing: border-box;
    }

    @media (min-width: 768px) {
        .overlay-content {
            grid-template-columns: 1fr 1fr;
        }
    }

    /* Left Side: Large index navigation */
    .overlay-menu-panel {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 40px;
        height: 100%;
        box-sizing: border-box;
    }

    @media (min-width: 768px) {
        .overlay-menu-panel {
            padding: 80px;
        }
    }

    .close-btn-aurum {
        background: transparent;
        border: none;
        color: rgba(255,255,255,0.6);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-family: var(--primtext), sans-serif;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 2.5px;
        cursor: pointer;
        padding: 8px 0;
        width: fit-content;
        transition: color 0.3s ease;
    }

    .close-btn-aurum:hover {
        color: var(--accent-gold);
    }

    .close-btn-aurum svg {
        transition: transform 0.3s ease;
    }

    .close-btn-aurum:hover svg {
        transform: rotate(90deg);
    }

    .aurum-menu-list {
        list-style: none;
        padding: 0;
        margin: auto 0;
        display: flex;
        flex-direction: column;
        gap: 20px;
        max-height: 60vh;
        overflow-y: auto;
    }

    .aurum-menu-item {
        opacity: 0; /* Animated by GSAP */
        transform: translateY(20px);
    }

    .aurum-menu-item > a {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(1.4rem, 4vw, 2.2rem);
        font-weight: 500;
        letter-spacing: 2px;
        color: #ffffff;
        text-decoration: none;
        text-transform: uppercase;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .aurum-menu-item > a:hover {
        color: var(--accent-gold);
        transform: translateX(10px);
    }

    /* Submenus nested */
    .aurum-submenu {
        list-style: none;
        padding: 4px 0 0 16px;
        margin: 4px 0 8px 0;
        display: flex;
        flex-direction: column;
        gap: 6px;
        border-left: 1px solid rgba(212, 175, 55, 0.25);
    }

    .aurum-submenu a {
        font-family: var(--primtext) !important;
        font-size: 0.9rem !important;
        text-transform: capitalize !important;
        color: rgba(255,255,255,0.6) !important;
        letter-spacing: 0.5px !important;
    }

    .aurum-submenu a:hover {
        color: var(--accent-gold) !important;
        transform: translateX(6px) !important;
    }

    /* Right Side: Autoplay Ken Burns Slider */
    .overlay-slider-panel {
        display: none;
        position: relative;
        height: 100%;
        overflow: hidden;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
    }

    @media (min-width: 768px) {
        .overlay-slider-panel {
            display: flex;
        }
    }

    .slider-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        opacity: 0; /* Animated crossfade by GSAP */
        z-index: 1;
    }

    .slider-img.active {
        opacity: 0.45;
        z-index: 2;
    }

    /* Overlay visual text elements */
    .slider-caption {
        position: relative;
        z-index: 10;
        text-align: center;
        padding: 40px;
        max-width: 360px;
        opacity: 0;
        transform: translateY(20px);
    }

    .slider-caption h3 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.6rem;
        margin: 0 0 12px 0;
        color: var(--accent-gold);
        letter-spacing: 1px;
    }

    .slider-caption p {
        margin: 0;
        font-size: 0.9rem;
        line-height: 1.5;
        color: rgba(255, 255, 255, 0.8);
    }

    @media (min-width: 992px) {
        .desktop-nav {
            display: flex;
        }
        .btn-cta {
            display: inline-flex;
        }
    }
</style>

<header id="header">
    <div class="container-global">
        <div class="content header-content">
            <!-- Left: Logo with Golden Sun shape -->
            <a href="<?= function_exists('base_url') ? base_url() : '#'; ?>" class="header-logo">
                <?php if (!empty($data->web->site_logo_alternative)): ?>
                    <img src="<?= $data->web->site_logo_alternative ?>" alt="Aurum Logo" onerror="this.style.display='none'; document.getElementById('logo-fallback-aurum').style.display='flex';">
                <?php endif; ?>
                <div id="logo-fallback-aurum" class="logo-text-fallback" style="<?= empty($data->web->site_logo_alternative) ? 'display: flex;' : 'display: none;' ?> align-items: center; gap: 8px; text-decoration: none;">
                    <!-- Golden Sun Crown geometry SVG -->
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#d4af37" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="5" fill="rgba(212, 175, 55, 0.15)"/>
                        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
                    </svg>
                    <span class="logo-text-fallback">Aurum</span>
                </div>
            </a>

            <!-- Center: Links with Stars -->
            <?= render_desktop_menu_aurum($filtered_menu) ?>

            <!-- Right: Inquire CTA & Hamburger -->
            <div class="header-actions">
                <a href="#booking" class="btn-cta">Reserve</a>

                <button class="hamburger-btn-aurum" id="hamburger-btn-aurum" aria-label="Open Menu">
                    <span class="bar bar-1"></span>
                    <span class="bar bar-2"></span>
                    <span class="bar bar-3"></span>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Fullscreen Slices Overlay Menu -->
<div class="slices-overlay" id="slices-overlay">
    <!-- 4 vertical slicing curtains -->
    <div class="overlay-slices-bg">
        <div class="slice"></div>
        <div class="slice"></div>
        <div class="slice"></div>
        <div class="slice"></div>
    </div>

    <div class="overlay-content">
        <!-- Left Side: Large index list -->
        <div class="overlay-menu-panel">
            <button class="close-btn-aurum" id="close-btn-aurum">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                <span>CLOSE</span>
            </button>

            <ul class="aurum-menu-list">
                <?php foreach ($filtered_menu as $item): ?>
                    <?php $has_child = isset($item->child) && !empty($item->child); ?>
                    <li class="aurum-menu-item">
                        <a href="<?= htmlspecialchars($item->link) ?>"><?= htmlspecialchars($item->label) ?></a>
                        <?php if ($has_child): ?>
                            <ul class="aurum-submenu">
                                <?php foreach ($item->child as $child): ?>
                                    <li><a href="<?= htmlspecialchars($child->link) ?>"><?= htmlspecialchars($child->label) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Right Side: Autoplay Crossfading Ken Burns Showcase Slider -->
        <div class="overlay-slider-panel">
            <!-- Images -->
            <div class="slider-img active" style="background-image: url('https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=800&q=80')"></div>
            <div class="slider-img" style="background-image: url('https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80')"></div>
            <div class="slider-img" style="background-image: url('https://images.unsplash.com/photo-1546412414-803b9a79a520?auto=format&fit=crop&w=800&q=80')"></div>

            <!-- Slide captions -->
            <div class="slider-caption" id="caption-container">
                <h3>Royal Aurum Sanctuary</h3>
                <p>An exquisite luxury experience blending gold architecture with pristine landscapes.</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const header = document.getElementById('header');
        const hamburger = document.getElementById('hamburger-btn-aurum');
        const closeBtn = document.getElementById('close-btn-aurum');
        const overlay = document.getElementById('slices-overlay');

        if (typeof gsap === 'undefined') {
            console.warn('GSAP is not loaded. Falling back to CSS toggle for slices overlay.');
            if (hamburger && overlay) {
                hamburger.addEventListener('click', () => { overlay.style.display = 'block'; });
            }
            if (closeBtn && overlay) {
                closeBtn.addEventListener('click', () => { overlay.style.display = 'none'; });
            }
            return;
        }

        // 1. Entrance animation
        gsap.to(header, {
            opacity: 1,
            duration: 0.8,
            ease: 'power2.out'
        });

        // 2. Scroll transitions
        window.addEventListener('scroll', () => {
            if (window.scrollY > 30) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // 3. Slices falling reveal timeline
        const menuTl = gsap.timeline({ paused: true });

        menuTl.set(overlay, { display: 'block' })
            .to('.slice', {
                scaleY: 1,
                stagger: 0.08,
                duration: 0.6,
                ease: 'power3.inOut'
            })
            .to('.aurum-menu-item', {
                opacity: 1,
                y: 0,
                stagger: 0.06,
                duration: 0.5,
                ease: 'power2.out'
            }, '-=0.2')
            .to('.slider-caption', {
                opacity: 1,
                y: 0,
                duration: 0.5,
                ease: 'power2.out'
            }, '-=0.3');

        if (hamburger) {
            hamburger.addEventListener('click', () => {
                menuTl.play();
                document.body.style.overflow = 'hidden';
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                menuTl.reverse();
                document.body.style.overflow = '';
            });
        }

        // 4. Ken Burns Crossfade image rotation
        const slides = document.querySelectorAll('.slider-img');
        const captions = [
            { title: "Royal Aurum Sanctuary", text: "An exquisite luxury experience blending gold architecture with pristine landscapes." },
            { title: "Celestial Infinity Pool", text: "Dip into infinite beauty where Ubud's forest canopy merges with the sky." },
            { title: "L'Or Gastronomy", text: "Indulge in Michelin-inspired dining under golden dome architectures." }
        ];
        
        let activeIdx = 0;
        const rotateSlides = () => {
            if (!overlay.style.display || overlay.style.display === 'none') return;
            
            const nextIdx = (activeIdx + 1) % slides.length;
            const currentSlide = slides[activeIdx];
            const nextSlide = slides[nextIdx];
            const captionBox = document.getElementById('caption-container');

            // Crossfade slides
            gsap.to(currentSlide, { opacity: 0, zIndex: 1, duration: 1.5 });
            gsap.to(nextSlide, { opacity: 0.45, zIndex: 2, duration: 1.5 });

            // Fade out caption, swap contents, fade in caption
            gsap.timeline()
                .to(captionBox, { opacity: 0, y: 15, duration: 0.4 })
                .call(() => {
                    captionBox.querySelector('h3').innerText = captions[nextIdx].title;
                    captionBox.querySelector('p').innerText = captions[nextIdx].text;
                })
                .to(captionBox, { opacity: 1, y: 0, duration: 0.6, ease: 'power2.out' });

            activeIdx = nextIdx;
        };

        // Rotate slides every 5 seconds
        setInterval(rotateSlides, 5000);
    });
</script>
