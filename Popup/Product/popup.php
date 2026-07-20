<?php
/**
 * ============================================================
 *  POPUP PROMO / RECOMMENDED PRODUCT (ELEGANT DESIGN)
 * ------------------------------------------------------------
 *  - Popup HANYA muncul jika $data->data tidak kosong.
 *  - Tampilan ultra-premium dengan Glassmorphism, smooth gradients,
 *    dan animasi dialog yang modern.
 *  - Otomatis Slider jika produk > 1.
 * ============================================================
 */

$popupItems = (isset($data) && isset($data->data) && is_array($data->data)) ? $data->data : [];

if (!empty($popupItems)):
    $popupTitle = isset($data->title) ? $data->title : 'Special Offers!';
    $isSlider   = count($popupItems) > 1;
    $popupUid   = 'popup-' . substr(md5(json_encode(array_column($popupItems, 'id'))), 0, 8);
?>

<div class="pp-overlay" id="<?= $popupUid ?>" aria-hidden="true">
    <div class="pp-modal" role="dialog" aria-modal="true" aria-labelledby="<?= $popupUid ?>-title">

        <!-- Tombol Close (X) -->
        <button type="button" class="pp-close" data-pp-close aria-label="Tutup popup">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="4" y1="4" x2="20" y2="20"></line>
                <line x1="20" y1="4" x2="4" y2="20"></line>
            </svg>
        </button>

        <!-- Header Modal -->
        <div class="pp-header">
            <span class="pp-badge-tag">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                Special Offer
            </span>
            <?php if ($popupTitle): ?>
                <h2 class="pp-title" id="<?= $popupUid ?>-title"><?= htmlspecialchars($popupTitle) ?></h2>
            <?php endif; ?>
        </div>

        <div class="pp-track-wrap">
            <div class="pp-track" data-pp-track style="--pp-count: <?= count($popupItems) ?>;">
                <?php foreach ($popupItems as $item):
                    $img       = !empty($item->img_cover_url) ? $item->img_cover_url : (!empty($item->img_thumb_url) ? $item->img_thumb_url : '');
                    $title     = isset($item->title) ? $item->title : '';
                    $slug      = isset($item->slug) ? $item->slug : '';
                    $price     = isset($item->price) ? (int) $item->price : 0;
                    $hidePrice = !empty($item->hide_price);
                    $waLink    = !empty($item->custom_field_1) ? $item->custom_field_1 : '';
                    $excerpt   = isset($item->content) ? trim(strip_tags($item->content)) : '';
                    if (strlen($excerpt) > 100) {
                        $excerpt = substr($excerpt, 0, 100) . '…';
                    }

                    $productUrl = isset($func) && method_exists($func, 'link') ? $func->link(ROUTE_PRODUCT_VIEW . $item->slug) : '#';
                ?>
                <div class="pp-slide">
                    <div class="pp-card">
                        <?php if ($img): ?>
                            <div class="pp-card-img">
                                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($title) ?>" loading="lazy">
                                <div class="pp-img-gradient"></div>
                            </div>
                        <?php endif; ?>

                        <div class="pp-card-body">
                            <h3 class="pp-card-title"><?= htmlspecialchars($title) ?></h3>

                            <?php if ($excerpt): ?>
                                <p class="pp-card-text"><?= htmlspecialchars($excerpt) ?></p>
                            <?php endif; ?>

                            <div class="pp-card-footer">
                                <?php if (!$hidePrice): ?>
                                    <div class="pp-price-wrap">
                                        <span class="pp-price-label">Harga</span>
                                        <span class="pp-price">
                                            <?= $price > 0 ? 'Rp ' . number_format($price, 0, ',', '.') : 'Hubungi Kami' ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <a class="pp-btn"
                                   href="<?= htmlspecialchars($productUrl) ?>"
                                   target="<?= $waLink ? '_blank' : '_self' ?>"
                                   rel="noopener">
                                    <span>Booking Now</span>
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($isSlider): ?>
                <button type="button" class="pp-nav pp-nav-prev" data-pp-prev aria-label="Sebelumnya">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <button type="button" class="pp-nav pp-nav-next" data-pp-next aria-label="Berikutnya">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            <?php endif; ?>
        </div>

        <?php if ($isSlider): ?>
            <div class="pp-dots" data-pp-dots>
                <?php foreach ($popupItems as $i => $item): ?>
                    <button type="button" class="pp-dot<?= $i === 0 ? ' is-active' : '' ?>" data-pp-dot="<?= $i ?>" aria-label="Slide <?= $i + 1 ?>"></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .pp-overlay {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px 16px;
        background: radial-gradient(circle at 50% 30%, rgba(30, 41, 59, 0.75), rgba(15, 23, 42, 0.92));
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    .pp-overlay.is-open { display: flex; }

    .pp-modal {
        position: relative;
        width: 100%;
        max-width: 440px;
        max-height: 90vh;
        overflow-y: auto;
        background: #ffffff;
        border-radius: 24px;
        padding: 32px 24px 24px;
        text-align: center;
        box-shadow: 0 30px 70px -15px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.2) inset;
        animation: pp-smooth-in .35s cubic-bezier(0.16, 1, 0.3, 1);
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .pp-modal::-webkit-scrollbar { display: none; }

    @keyframes pp-smooth-in {
        from { opacity: 0; transform: translateY(20px) scale(0.95); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .pp-close {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 50%;
        background: #f1f5f9;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all .2s ease;
        z-index: 10;
    }
    .pp-close:hover {
        background: #e4622b;
        color: #ffffff;
        transform: rotate(90deg) scale(1.05);
    }

    .pp-header { margin-bottom: 20px; text-align: center; }

    .pp-badge-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 99px;
        background: linear-gradient(135deg, rgba(228, 98, 43, 0.08), rgba(228, 98, 43, 0.15));
        border: 1px solid rgba(228, 98, 43, 0.2);
        color: #e4622b;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .pp-title {
        margin: 0;
        font-size: 22px;
        font-weight: 800;
        line-height: 1.25;
        color: #0f172a;
        letter-spacing: -0.02em;
    }

    .pp-track-wrap { position: relative; }
    .pp-track {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        scroll-behavior: smooth;
        gap: 16px;
        -ms-overflow-style: none;
        scrollbar-width: none;
        border-radius: 18px;
    }
    .pp-track::-webkit-scrollbar { display: none; }

    .pp-slide {
        flex: 0 0 100%;
        scroll-snap-align: start;
    }

    .pp-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
        text-align: left;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    }

    .pp-card-img {
        position: relative;
        width: 100%;
        aspect-ratio: 4 / 3;
        overflow: hidden;
        background: #0f172a;
    }
    .pp-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .4s ease;
    }
    .pp-card:hover .pp-card-img img {
        transform: scale(1.04);
    }

    .pp-img-gradient {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(15, 23, 42, 0.4) 0%, transparent 60%);
    }

    .pp-card-body { padding: 18px 20px 20px; }
    
    .pp-card-title {
        margin: 0 0 8px;
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.35;
        letter-spacing: -0.01em;
    }

    .pp-card-text {
        margin: 0 0 18px;
        font-size: 13px;
        line-height: 1.6;
        color: #64748b;
    }

    .pp-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .pp-price-wrap {
        display: flex;
        flex-direction: column;
    }
    .pp-price-label {
        font-size: 11px;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .pp-price {
        font-weight: 800;
        color: #0f172a;
        font-size: 16px;
    }

    .pp-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 20px;
        border-radius: 12px;
        background: linear-gradient(135deg, #e4622b 0%, #c9501f 100%);
        color: #ffffff !important;
        font-weight: 700;
        font-size: 13.5px;
        text-decoration: none;
        letter-spacing: 0.01em;
        transition: all .2s ease;
        box-shadow: 0 6px 16px -2px rgba(228, 98, 43, 0.35);
    }
    .pp-btn:hover {
        background: linear-gradient(135deg, #c9501f 0%, #b23f12 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 22px -2px rgba(228, 98, 43, 0.45);
    }

    /* Navigation Arrows for Slider */
    .pp-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.8);
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 4px 14px rgba(0,0,0,0.15);
        color: #0f172a;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 5;
        transition: all .2s ease;
    }
    .pp-nav:hover { background: #ffffff; transform: translateY(-50%) scale(1.08); }
    .pp-nav-prev { left: -8px; }
    .pp-nav-next { right: -8px; }

    /* Indicator Dots */
    .pp-dots {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
        margin-top: 18px;
    }
    .pp-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        border: none;
        background: #cbd5e1;
        cursor: pointer;
        padding: 0;
        transition: all .25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .pp-dot.is-active {
        background: #e4622b;
        width: 22px;
        border-radius: 10px;
    }

    @media (max-width: 480px) {
        .pp-modal { padding: 28px 16px 20px; border-radius: 20px; }
        .pp-title { font-size: 20px; }
        .pp-nav { display: none; }
    }
</style>

<script>
(function () {
    var overlay = document.getElementById('<?= $popupUid ?>');
    if (!overlay) return;

    var STORAGE_KEY = '<?= $popupUid ?>-closed';

    function openPopup() {
        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closePopup() {
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        try { sessionStorage.setItem(STORAGE_KEY, '1'); } catch (e) {}
    }

    window.addEventListener('load', openPopup);

    overlay.querySelectorAll('[data-pp-close]').forEach(function (btn) {
        btn.addEventListener('click', closePopup);
    });

    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closePopup();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.classList.contains('is-open')) closePopup();
    });

    var track = overlay.querySelector('[data-pp-track]');
    var dots  = overlay.querySelectorAll('[data-pp-dot]');
    var prevBtn = overlay.querySelector('[data-pp-prev]');
    var nextBtn = overlay.querySelector('[data-pp-next]');

    if (track && dots.length) {
        var slides = track.querySelectorAll('.pp-slide');
        var current = 0;

        function goTo(index) {
            current = Math.max(0, Math.min(index, slides.length - 1));
            track.scrollTo({ left: track.clientWidth * current, behavior: 'smooth' });
            dots.forEach(function (d, i) { d.classList.toggle('is-active', i === current); });
        }

        dots.forEach(function (dot, i) {
            dot.addEventListener('click', function () { goTo(i); });
        });
        if (prevBtn) prevBtn.addEventListener('click', function () { goTo(current - 1); });
        if (nextBtn) nextBtn.addEventListener('click', function () { goTo(current + 1); });

        var scrollTimeout;
        track.addEventListener('scroll', function () {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(function () {
                var idx = Math.round(track.scrollLeft / track.clientWidth);
                current = idx;
                dots.forEach(function (d, i) { d.classList.toggle('is-active', i === idx); });
            }, 100);
        });
    }
})();
</script>

<?php endif; ?>
