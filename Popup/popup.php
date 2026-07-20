<?php
/**
 * ============================================================
 *  POPUP PROMO / RECOMMENDED POST
 * ------------------------------------------------------------
 *  - Popup HANYA muncul jika $data->data tidak kosong.
 *  - Muncul otomatis saat halaman pertama kali di-load.
 *  - Tetap terbuka sampai user menekan tombol close (X)
 *    atau klik area gelap di luar kartu (overlay).
 *  - Jika item > 1, otomatis jadi SLIDER (carousel) dengan
 *    tombol prev/next + dots, jadi walau datanya banyak,
 *    tampilan tetap rapi (tidak numpuk ke bawah).
 *  - Sekali di-close, popup tidak akan muncul lagi selama
 *    tab browser masih terbuka (pakai sessionStorage).
 *    Kalau mau muncul lagi tiap reload, tinggal hapus
 *    blok "session guard" di bagian JS paling bawah.
 * ============================================================
 */

// Guard utama: kalau data kosong / bukan array/objek yang valid -> tidak render apa-apa
$popupItems = (isset($data) && isset($data->data) && is_array($data->data)) ? $data->data : [];

if (!empty($popupItems)):
    $popupTitle = isset($data->title) ? $data->title : '';
    $isSlider   = count($popupItems) > 1;
    // id unik biar aman kalau popup ini dipakai berkali-kali dalam satu halaman
    $popupUid = 'popup-' . substr(md5(json_encode(array_column($popupItems, 'id'))), 0, 8);
?>

<div class="pp-overlay" id="<?= $popupUid ?>" aria-hidden="true">
    <div class="pp-modal" role="dialog" aria-modal="true" aria-labelledby="<?= $popupUid ?>-title">

        <button type="button" class="pp-close" data-pp-close aria-label="Tutup popup">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="4" y1="4" x2="20" y2="20"></line>
                <line x1="20" y1="4" x2="4" y2="20"></line>
            </svg>
        </button>

        <?php if ($popupTitle): ?>
            <p class="pp-eyebrow">Special Offers!</p>
            <h2 class="pp-title" id="<?= $popupUid ?>-title"><?= htmlspecialchars($popupTitle) ?></h2>
        <?php endif; ?>

        <div class="pp-track-wrap">
            <div class="pp-track" data-pp-track style="--pp-count: <?= count($popupItems) ?>;">
                <?php foreach ($popupItems as $item):
                    $img   = !empty($item->img_cover_url) ? $item->img_cover_url : (!empty($item->img_thumb_url) ? $item->img_thumb_url : '');
                    $title = isset($item->title) ? $item->title : '';
                    $slug  = isset($item->slug) ? $item->slug : '';
                    $price = isset($item->price) ? (int) $item->price : 0;
                    $hidePrice = !empty($item->hide_price);
                    $waLink = !empty($item->custom_field_1) ? $item->custom_field_1 : '';
                    $excerpt = isset($item->content) ? trim(strip_tags($item->content)) : '';
                    if (strlen($excerpt) > 110) {
                        $excerpt = substr($excerpt, 0, 110) . '…';
                    }
                ?>
                <div class="pp-slide">
                    <div class="pp-card">
                        <?php if ($img): ?>
                            <div class="pp-card-img">
                                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($title) ?>" loading="lazy">
                            </div>
                        <?php endif; ?>

                        <div class="pp-card-body">
                            <h3 class="pp-card-title"><?= htmlspecialchars($title) ?></h3>

                            <?php if ($excerpt): ?>
                                <p class="pp-card-text"><?= htmlspecialchars($excerpt) ?></p>
                            <?php endif; ?>

                            <div class="pp-card-footer">
                                <?php if (!$hidePrice): ?>
                                    <span class="pp-price">
                                        <?= $price > 0 ? 'Rp ' . number_format($price, 0, ',', '.') : 'Hubungi Kami' ?>
                                    </span>
                                <?php endif; ?>

                                <a class="pp-btn"
                                   href="<?= $func->link(ROUTE_PRODUCT_VIEW. $item->slug) ?>"
                                   target="<?= $waLink ? '_blank' : '_self' ?>"
                                   rel="noopener">
                                    Booking Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($isSlider): ?>
                <button type="button" class="pp-nav pp-nav-prev" data-pp-prev aria-label="Sebelumnya">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <button type="button" class="pp-nav pp-nav-next" data-pp-next aria-label="Berikutnya">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
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
    .pp-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(20, 16, 12, 0.55);
        backdrop-filter: blur(3px);
    }
    .pp-overlay.is-open { display: flex; }

    .pp-modal {
        position: relative;
        width: 100%;
        max-width: 480px;
        max-height: 90vh;
        overflow-y: auto;
        background: #fff;
        border-radius: 18px;
        padding: 40px 32px 32px;
        text-align: center;
        box-shadow: 0 30px 60px -15px rgba(0,0,0,0.35);
        animation: pp-pop .28s cubic-bezier(.2,.9,.3,1.2);
    }
    @keyframes pp-pop {
        from { opacity: 0; transform: translateY(14px) scale(.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .pp-close {
        position: absolute;
        top: 14px;
        right: 14px;
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 50%;
        background: #E4622B;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform .15s ease, background .15s ease;
    }
    .pp-close:hover { background: #c9501f; transform: scale(1.06); }

    .pp-eyebrow {
        margin: 0 0 6px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: #E4622B;
    }
    .pp-title {
        margin: 0 0 20px;
        font-family: Georgia, 'Times New Roman', serif;
        font-size: 30px;
        line-height: 1.2;
        color: #1f1a15;
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
        border-radius: 14px;
    }
    .pp-track::-webkit-scrollbar { display: none; }

    .pp-slide {
        flex: 0 0 100%;
        scroll-snap-align: start;
    }

    .pp-card {
        border: 1px solid #eee;
        border-radius: 14px;
        overflow: hidden;
        text-align: left;
        background: #fff;
    }
    .pp-card-img { width: 100%; aspect-ratio: 4 / 5; overflow: hidden; background: #f2f2f2; }
    .pp-card-img img { width: 100%; height: 100%; object-fit: cover; display: block; }

    .pp-card-body { padding: 16px 18px 18px; }
    .pp-card-title {
        margin: 0 0 6px;
        font-size: 17px;
        font-weight: 700;
		font-family: var(--primtext);
        color: #1f1a15;
    }
    .pp-card-text {
        margin: 0 0 14px;
		font-family: var(--subtext);
        font-size: 13.5px;
        line-height: 1.5;
        color: #666;
    }
    .pp-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
    }
    .pp-price { font-weight: 700; color: #1f1a15; font-size: 15px; font-family: var(--primtext) }

    .pp-btn {
        display: inline-block;
        padding: 10px 20px;
        border-radius: 999px;
        background: #E4622B;
		font-family: var(--subtext);
        color: #fff !important;
        font-weight: 600;
        font-size: 13.5px;
        text-decoration: none;
        letter-spacing: .03em;
        transition: background .15s ease, transform .15s ease;
    }
    .pp-btn:hover { background: #c9501f; transform: translateY(-1px); }

    .pp-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        background: rgba(255,255,255,.92);
        box-shadow: 0 4px 14px rgba(0,0,0,.15);
        color: #1f1a15;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 2;
    }
    .pp-nav:hover { background: #fff; }
    .pp-nav-prev { left: -6px; }
    .pp-nav-next { right: -6px; }

    .pp-dots {
        display: flex;
        justify-content: center;
        gap: 7px;
        margin-top: 16px;
    }
    .pp-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        border: none;
        background: #e2ddd5;
        cursor: pointer;
        padding: 0;
        transition: background .15s ease, width .15s ease;
    }
    .pp-dot.is-active { background: #E4622B; width: 20px; border-radius: 4px; }

    @media (max-width: 480px) {
        .pp-modal { padding: 32px 20px 24px; border-radius: 14px; }
        .pp-title { font-size: 24px; }
        .pp-nav { display: none; } /* di mobile pakai swipe/scroll snap saja */
    }
</style>

<script>
(function () {
    var overlay = document.getElementById('<?= $popupUid ?>');
    if (!overlay) return;

    var STORAGE_KEY = '<?= $popupUid ?>-closed';

    // ---- Session guard: kalau sudah pernah di-close, jangan tampilkan lagi
    //      selama tab masih terbuka. Hapus blok "try" ini kalau mau popup
    //      selalu muncul tiap kali halaman di-reload.
	
    var alreadyClosed = false;
    // try { alreadyClosed = sessionStorage.getItem(STORAGE_KEY) === '1'; } catch (e) {}

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

    if (!alreadyClosed) {
        // munculkan begitu halaman selesai load
        window.addEventListener('load', openPopup);
    }

    // klik tombol close
    overlay.querySelectorAll('[data-pp-close]').forEach(function (btn) {
        btn.addEventListener('click', closePopup);
    });

    // klik area gelap di luar kartu
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closePopup();
    });

    // tombol Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.classList.contains('is-open')) closePopup();
    });

    // ---- Slider logic (hanya jalan kalau item > 1) ----
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

        // update dot aktif kalau user scroll/swipe manual
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

<?php endif; // !empty($popupItems) ?>
