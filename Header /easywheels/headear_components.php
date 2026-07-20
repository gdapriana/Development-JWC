<?php
$filtered_menu = json_decode(json_encode(simplify_menu($data->menu->primary_menu)));
?>


<style>
    #header {
        width: 100%;
        position: fixed;
        background-color: var(--light-color);
        top: 0;
        z-index: 999999999;

        transition: border-bottom 0.2s ease, box-shadow 0.2s ease;
        border-bottom: 1px solid transparent;

        &.scrolled {
            border-bottom: 1px solid #e0e0dc;
            box-shadow: 0 1px 8px rgba(0, 0, 0, 0.06);
        }

        .container-global {
            padding-block: 1rem;

            .content {
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;

                .brand {
                    img {
                        height: 50px;
                    }
                }

                .nav-items {
                    display: none;
                    gap: 2px;
                    position: relative;
                    padding: 4px;
                    width: fit-content;

                    a.nav-item {
                        text-decoration: none;
                        font-family: var(--primtext);
                        padding: .6rem 1.2rem;
                        position: relative;
                        z-index: 1;
                        cursor: pointer;
                        transition: color 0.2s ease;

                        &.hovered,
                        &:hover {
                            color: var(--light-color);
                        }
                    }

                    .has-dropdown {
                        position: relative;
                        z-index: 1;

                        .nav-link {
                            font-family: var(--primtext);
                            padding: .6rem 1.2rem;
                            cursor: pointer;
                            display: flex;
                            align-items: center;
                            gap: .4rem;
                            position: relative;
                            z-index: 1;
                            transition: color 0.2s ease;
                            user-select: none;
                            white-space: nowrap;

                            i {
                                font-size: .65rem;
                                transition: transform 0.25s ease;
                            }
                        }

                        &.hovered .nav-link,
                        &:hover .nav-link {
                            color: var(--light-color);

                            i {
                                transform: rotate(180deg);
                            }
                        }

                        &::after {
                            content: '';
                            position: absolute;
                            top: 100%;
                            left: 0;
                            width: 100%;
                            height: 16px;
                            background: transparent;
                        }

                        .dropdown {
                            position: absolute;
                            top: calc(100% + 16px);
                            left: 50%;
                            transform: translateX(-50%) translateY(-6px);
                            background: white;
                            border: 1px solid rgba(0, 0, 0, 0.07);
                            border-radius: 12px;
                            padding: 6px;
                            min-width: 160px;
                            opacity: 0;
                            visibility: hidden;
                            pointer-events: none;
                            transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
                            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
                            z-index: 100;

                            .dropdown-item {
                                display: block;
                                padding: .6rem 1rem;
                                font-family: var(--primtext);
                                color: var(--dark-color);
                                text-decoration: none;
                                border-radius: 8px;
                                font-size: .9rem;
                                transition: background 0.15s;

                                &:hover {
                                    background: rgba(0, 0, 0, 0.04);
                                }
                            }
                        }

                        &:hover .dropdown {
                            opacity: 1;
                            visibility: visible;
                            pointer-events: all;
                            transform: translateX(-50%) translateY(0);
                        }
                    }

                    .invert-pill {
                        position: absolute;
                        background: var(--colors);
                        border-radius: 999rem;
                        transition: left 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                            width 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                            opacity 0.2s ease;
                        pointer-events: none;
                        z-index: 0;
                    }

                    @media (min-width: 768px) {
                        display: flex;
                    }
                }

                .cta {
                    display: none;
                    justify-content: center;
                    align-items: stretch;
                    padding-inline: 1rem;
                    gap: .3rem;
                    background-color: var(--colors);
                    padding: .4rem 1.2rem;
                    padding-left: .4rem;
                    border-radius: 999em;
                    text-decoration: none;

                    .icon {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        background-color: transparent;
                        border-radius: 999em;
                        width: 40px;
                        height: 40px;
                        aspect-ratio: 1/1;

                        i {
                            font-size: 1.8rem;
                            color: white;
                        }
                    }

                    .text {
                        gap: .2rem;
                        flex: 1;
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        align-items: start;
                        font-size: .9rem;

                        span {
                            color: var(--light-color);
                            font-size: .9rem;

                            &:first-child {
                                font-size: .7rem;
                                color: var(--light-foreground-color);
                            }
                        }
                    }

                    @media (min-width: 768px) {
                        display: flex;
                    }
                }

                #hamburger-btn {
                    background-color: transparent;
                    color: var(--dark-color);
                    border: none;
                    cursor: pointer;

                    i {
                        font-size: 1.8rem;
                    }

                    @media (min-width: 768px) {
                        display: none;
                    }
                }
            }
        }
    }


    #hamburger-content {
        transform: translateY(-20px);
        position: fixed;
        top: 0;
        z-index: 99999999;
        width: 100%;
        height: 100dvh;
        background-color: white;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.25s ease, transform 0.25s ease, visibility 0.25s;

        .container-global {
            padding-block: 1rem;
            height: 100%;

            .content {
                height: 100%;
                width: 100%;
                display: flex;
                flex-direction: column;
                justify-content: start;
                align-items: stretch;

                .header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;

                    .brand {
                        img {
                            height: 50px;
                        }
                    }

                    #close-hamburger-btn {
                        background-color: transparent;
                        border: none;
                        cursor: pointer;

                        i {
                            font-size: .8rem;
                        }
                    }
                }

                .nav-items {
                    margin-top: 1rem;
                    display: flex;
                    flex-direction: column;
                    justify-content: start;
                    align-items: stretch;

                    a.nav-item {
                        padding-block: 1rem;
                        font-family: var(--primtext);
                        color: var(--dark-color);
                        text-decoration: none;
                        border-bottom: 1px solid rgba(0, 0, 0, 0.05);

                        &:last-child {
                            border-bottom: none;
                        }
                    }

                    .has-dropdown {
                        border-bottom: 1px solid rgba(0, 0, 0, 0.05);

                        &:last-child {
                            border-bottom: none;
                        }

                        .nav-link {
                            width: 100%;
                            background: none;
                            border: none;
                            padding-block: 1rem;
                            padding-inline: 0;
                            font-family: var(--primtext);
                            color: var(--dark-color);
                            font-size: 1rem;
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            cursor: pointer;
                            text-align: left;

                            i {
                                font-size: .65rem;
                                transition: transform 0.25s ease;
                            }
                        }

                        &.open .nav-link i {
                            transform: rotate(180deg);
                        }

                        .accordion-body {
                            max-height: 0;
                            overflow: hidden;
                            transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);

                            .dropdown-item {
                                display: block;
                                padding: .7rem 1rem;
                                color: var(--dark-color);
                                font-family: var(--primtext);
                                text-decoration: none;
                                font-size: .9rem;
                                opacity: 0.65;

                                &:last-child {
                                    margin-bottom: .5rem;
                                }

                                &:hover {
                                    opacity: 1;
                                }
                            }
                        }
                    }
                }

                .cta {
                    margin-top: auto;
                    justify-content: center;
                    align-items: stretch;
                    padding-inline: 1rem;
                    gap: .3rem;
                    display: flex;
                    background-color: var(--colors);
                    padding: .4rem 1.2rem;
                    padding-left: .4rem;
                    border-radius: 999em;
                    text-decoration: none;

                    .icon {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        background-color: transparent;
                        border-radius: 999em;
                        width: 40px;
                        height: 40px;
                        aspect-ratio: 1/1;

                        i {
                            font-size: 1.8rem;
                            color: white;
                        }
                    }

                    .text {
                        gap: .2rem;
                        flex: 1;
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        align-items: start;
                        font-size: .9rem;

                        span {
                            color: var(--light-color);
                            font-size: .9rem;

                            &:first-child {
                                font-size: .7rem;
                                color: var(--light-foreground-color);
                            }
                        }
                    }
                }
            }
        }

        &.active {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }

        @media (min-width: 768px) {
            display: none;
        }
    }
</style>


<?php
$nav = json_decode(json_encode([
    ['name' => 'Home', 'url' => '/'],
    [
        'name' => 'Vehicles',
        'url' => '/',
        'childs' => [
            ['name' => 'Cars', 'url' => '/'],
            ['name' => 'Bikes', 'url' => '/'],
        ]
    ],
    ['name' => 'Story', 'url' => '/stories.php'],
    ['name' => 'Booking Now', 'url' => '/booking.php'],
]))
?>

<header id="header">
    <div class="container-global">
        <div class="content">
            <a href="/" class="brand">
                <img src="<?= $data->web->site_logo_alternative ?>" alt="logo">
            </a>
            <nav class="nav-items" id="nav-items">
                <?php foreach ($filtered_menu as $key => $items): ?>
                    <?php if (!empty($items->child)): ?>
                        <div class="nav-item has-dropdown">
                            <span class="nav-link">
                                <?= $items->label ?>
                                <i class="fas fa-chevron-down"></i>
                            </span>
                            <div class="dropdown">
                                <?php foreach ($items->child as $child): ?>
                                    <a class="dropdown-item" href="<?= $child->url ?>"><?= $child->label ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <a class="nav-item" href="<?= $items->link ?>"><?= $items->label ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
                <div class="invert-pill" id="pill"></div>
            </nav>
            <a href="<?= $func->dm_whatsapp() ?>" class="cta">
                <div class="icon">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <div class="text">
                    <span>Need Help?</span>
                    <span>+<?= $data->social->whatsapp ?></span>
                </div>
            </a>
            <button id="hamburger-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</header>

<section id="hamburger-content">
    <div class="container-global">
        <div class="content">
            <header class="header">
                <a href="/" class="brand">
                    <img src="<?= $data->web->site_logo_alternative ?>" alt="logo">
                </a>
                <button id="close-hamburger-btn">
                    <i class="fas fa-x"></i>
                </button>
            </header>
            <nav class="nav-items">
                <?php foreach ($filtered_menu as $key => $items): ?>
                    <?php if (!empty($items->child)): ?>
                        <div class="nav-item has-dropdown">
                            <button class="nav-link accordion-toggle">
                                <?= $items->label ?>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="accordion-body">
                                <?php foreach ($items->child as $child): ?>
                                    <a class="dropdown-item" href="<?= $child->link ?>"><?= $child->label ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <a class="nav-item" href="<?= $items->link ?>"><?= $items->label ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>
            <a href="<?= $func->dm_whatsapp() ?>" class="cta">
                <div class="icon">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <div class="text">
                    <span>Need Help?</span>
                    <span>+<?= $data->social->whatsapp ?></span>
                </div>
            </a>
        </div>
    </div>



</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        function relRect(child, parent) {
            const pR = parent.getBoundingClientRect();
            const cR = child.getBoundingClientRect();
            return {
                left: cR.left - pR.left,
                top: cR.top - pR.top,
                width: cR.width,
                height: cR.height,
            };
        }

        // Desktop pill hover
        (function() {
            const nav = document.getElementById('nav-items');
            const pill = document.getElementById('pill');
            const links = nav.querySelectorAll('a.nav-item, .has-dropdown');
            let active = null;

            function move(el) {
                const r = relRect(el, nav);
                pill.style.left = r.left + 'px';
                pill.style.top = r.top + 'px';
                pill.style.width = r.width + 'px';
                pill.style.height = r.height + 'px';
                pill.style.opacity = '1';
                if (active) active.classList.remove('hovered');
                el.classList.add('hovered');
                active = el;
            }

            links.forEach(a => {
                a.addEventListener('mouseenter', () => move(a));
            });

            nav.addEventListener('mouseleave', () => {
                pill.style.opacity = '0';
                if (active) {
                    active.classList.remove('hovered');
                    active = null;
                }
            });
        })();

        // Hamburger open/close
        const hamburgerBtn = document.querySelector('#header #hamburger-btn');
        const hamburgerCloseBtn = document.querySelector('#hamburger-content #close-hamburger-btn');
        const hamburgerContent = document.querySelector('#hamburger-content');
        hamburgerBtn.addEventListener('click', () => hamburgerContent.classList.add('active'));
        hamburgerCloseBtn.addEventListener('click', () => hamburgerContent.classList.remove('active'));

        // Hamburger accordion for child items
        document.querySelectorAll('#hamburger-content .accordion-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                const item = btn.closest('.has-dropdown');
                const body = item.querySelector('.accordion-body');
                const isOpen = item.classList.contains('open');

                document.querySelectorAll('#hamburger-content .has-dropdown').forEach(i => {
                    i.classList.remove('open');
                    i.querySelector('.accordion-body').style.maxHeight = '0';
                });

                if (!isOpen) {
                    item.classList.add('open');
                    body.style.maxHeight = body.scrollHeight + 'px';
                }
            });
        });

        // Scroll border
        const header = document.getElementById('header');
        window.addEventListener('scroll', () => {
            header.classList.toggle('scrolled', window.scrollY > 0);
        });

    });
</script>