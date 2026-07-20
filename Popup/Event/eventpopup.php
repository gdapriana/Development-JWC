<?php
/**
 * ============================================================
 *  POPUP EVENT / UPCOMING EVENT (ELEGANT DESIGN)
 * ------------------------------------------------------------
 *  - Popup HANYA muncul jika $data->data (list event) tidak kosong.
 *  - Tampilan ultra-premium dengan Glassmorphism, smooth gradients,
 *    dan animasi dialog yang modern.
 *  - Menampilkan metadata event: Tanggal, Jam, Lokasi, Executor.
 *  - Otomatis Slider jika event > 1.
 * ============================================================
 */

$popupItems = (isset($data) && isset($data->data) && is_array($data->data)) ? $data->data : [];

if (!empty($popupItems)):
    $popupTitle = isset($data->title) ? $data->title : 'Upcoming Event';
    $isSlider   = count($popupItems) > 1;
    $popupUid   = 'popup-event-' . substr(md5(json_encode(array_column($popupItems, 'id'))), 0, 8);

    if (!function_exists('formatEventPopupDate')) {
        function formatEventPopupDate($start, $finish) {
            if (empty($start)) return '';
            $t1 = strtotime($start);
            if (!$t1) return htmlspecialchars($start);
            
            if (empty($finish) || $start === $finish) {
                return date('d M Y', $t1);
            }
            
            $t2 = strtotime($finish);
            if (!$t2) return htmlspecialchars($start . ' - ' . $finish);
            
            if (date('Y-m', $t1) === date('Y-m', $t2)) {
                return date('d', $t1) . ' - ' . date('d M Y', $t2);
            }
            return date('d M', $t1) . ' - ' . date('d M Y', $t2);
        }
    }
?>

<div class="pp-event-overlay" id="<?= $popupUid ?>" aria-hidden="true">
    <div class="pp-event-modal" role="dialog" aria-modal="true" aria-labelledby="<?= $popupUid ?>-title">

        <!-- Tombol Close (X) -->
        <button type="button" class="pp-event-close" data-pp-close aria-label="Tutup popup">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="4" y1="4" x2="20" y2="20"></line>
                <line x1="20" y1="4" x2="4" y2="20"></line>
            </svg>
        </button>

        <!-- Header Modal -->
        <div class="pp-event-header">
            <span class="pp-event-badge-tag">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Special Event
            </span>
            <?php if ($popupTitle): ?>
                <h2 class="pp-event-title" id="<?= $popupUid ?>-title"><?= htmlspecialchars($popupTitle) ?></h2>
            <?php endif; ?>
        </div>

        <div class="pp-event-track-wrap">
            <div class="pp-event-track" data-pp-track style="--pp-count: <?= count($popupItems) ?>;">
                <?php foreach ($popupItems as $item):
                    $img      = !empty($item->img_cover) ? $item->img_cover : (!empty($item->img_cover_url) ? $item->img_cover_url : (!empty($item->img_thumb_url) ? $item->img_thumb_url : ''));
                    $title    = isset($item->title) ? $item->title : '';
                    $slug     = isset($item->slug) ? $item->slug : '';
                    $dateStr  = formatEventPopupDate($item->date_start ?? '', $item->date_finish ?? '');
                    $timeStr  = isset($item->start_at) ? $item->start_at : '';
                    $location = isset($item->location) ? $item->location : '';
                    $executor = isset($item->executor) ? $item->executor : '';
                    $excerpt  = isset($item->content) ? trim(strip_tags($item->content)) : '';
                    if (strlen($excerpt) > 100) {
                        $excerpt = substr($excerpt, 0, 100) . '…';
                    }

                    $routeConst = defined('ROUTE_EVENT_VIEW') ? ROUTE_EVENT_VIEW : 'event/';
                    $eventUrl   = isset($func) && method_exists($func, 'link') ? $func->link($routeConst . $slug) : '#';
                ?>
                <div class="pp-event-slide">
                    <div class="pp-event-card">
                        <?php if ($img): ?>
                            <div class="pp-event-card-img">
                                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($title) ?>" loading="lazy">
                                <div class="pp-event-img-gradient"></div>
                                <?php if ($dateStr): ?>
                                    <div class="pp-event-date-pill">
                                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                        <span><?= $dateStr ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="pp-event-card-body">
                            <h3 class="pp-event-card-title"><?= htmlspecialchars($title) ?></h3>

                            <!-- Event Metadata Info -->
                            <div class="pp-event-meta-grid">
                                <?php if ($timeStr): ?>
                                    <div class="pp-event-meta-chip">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                        <span><?= htmlspecialchars($timeStr) ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($location): ?>
                                    <div class="pp-event-meta-chip">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                        <span><?= htmlspecialchars($location) ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($executor): ?>
                                    <div class="pp-event-meta-chip">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        <span><?= htmlspecialchars($executor) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if ($excerpt): ?>
                                <p class="pp-event-card-text"><?= htmlspecialchars($excerpt) ?></p>
                            <?php endif; ?>

                            <div class="pp-event-card-footer">
                                <a class="pp-event-btn" href="<?= htmlspecialchars($eventUrl) ?>">
                                    <span>Lihat Detail Event</span>
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($isSlider): ?>
                <button type="button" class="pp-event-nav pp-event-nav-prev" data-pp-prev aria-label="Sebelumnya">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <button type="button" class="pp-event-nav pp-event-nav-next" data-pp-next aria-label="Berikutnya">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            <?php endif; ?>
        </div>

        <?php if ($isSlider): ?>
            <div class="pp-event-dots" data-pp-dots>
                <?php foreach ($popupItems as $i => $item): ?>
                    <button type="button" class="pp-event-dot<?= $i === 0 ? ' is-active' : '' ?>" data-pp-dot="<?= $i ?>" aria-label="Slide <?= $i + 1 ?>"></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .pp-event-overlay {
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
    .pp-event-overlay.is-open { display: flex; }

    .pp-event-modal {
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
        animation: pp-event-smooth-in .35s cubic-bezier(0.16, 1, 0.3, 1);
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .pp-event-modal::-webkit-scrollbar { display: none; }

    @keyframes pp-event-smooth-in {
        from { opacity: 0; transform: translateY(20px) scale(0.95); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .pp-event-close {
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
    .pp-event-close:hover {
        background: #0f172a;
        color: #ffffff;
        transform: rotate(90deg) scale(1.05);
    }

    .pp-event-header { margin-bottom: 20px; text-align: center; }

    .pp-event-badge-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 99px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.08), rgba(59, 130, 246, 0.15));
        border: 1px solid rgba(37, 99, 235, 0.2);
        color: #2563eb;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .pp-event-title {
        margin: 0;
        font-size: 22px;
        font-weight: 800;
        line-height: 1.25;
        color: #0f172a;
        letter-spacing: -0.02em;
    }

    .pp-event-track-wrap { position: relative; }
    .pp-event-track {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        scroll-behavior: smooth;
        gap: 16px;
        -ms-overflow-style: none;
        scrollbar-width: none;
        border-radius: 18px;
    }
    .pp-event-track::-webkit-scrollbar { display: none; }

    .pp-event-slide {
        flex: 0 0 100%;
        scroll-snap-align: start;
    }

    .pp-event-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
        text-align: left;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        transition: transform .2s ease;
    }

    .pp-event-card-img {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 9;
        overflow: hidden;
        background: #0f172a;
    }
    .pp-event-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .4s ease;
    }
    .pp-event-card:hover .pp-event-card-img img {
        transform: scale(1.04);
    }

    .pp-event-img-gradient {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(15, 23, 42, 0.6) 0%, transparent 60%);
    }

    .pp-event-date-pill {
        position: absolute;
        bottom: 12px;
        left: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(15, 23, 42, 0.85);
        color: #38bdf8;
        border: 1px solid rgba(56, 189, 248, 0.3);
        font-size: 11.5px;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 99px;
        backdrop-filter: blur(8px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .pp-event-card-body { padding: 18px 20px 20px; }
    
    .pp-event-card-title {
        margin: 0 0 12px;
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.35;
        letter-spacing: -0.01em;
    }

    /* Metadata Info Event */
    .pp-event-meta-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 14px;
    }
    .pp-event-meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #475569;
        font-weight: 600;
        padding: 5px 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
    }
    .pp-event-meta-chip svg { color: #2563eb; flex-shrink: 0; }

    .pp-event-card-text {
        margin: 0 0 18px;
        font-size: 13px;
        line-height: 1.6;
        color: #64748b;
    }

    .pp-event-card-footer {
        display: flex;
        align-items: center;
    }

    .pp-event-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        text-align: center;
        padding: 12px 20px;
        border-radius: 12px;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff !important;
        font-weight: 700;
        font-size: 13.5px;
        text-decoration: none;
        letter-spacing: 0.01em;
        transition: all .2s ease;
        box-shadow: 0 6px 16px -2px rgba(37, 99, 235, 0.35);
    }
    .pp-event-btn:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 22px -2px rgba(37, 99, 235, 0.45);
    }

    /* Navigation Arrows for Slider */
    .pp-event-nav {
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
    .pp-event-nav:hover { background: #ffffff; transform: translateY(-50%) scale(1.08); }
    .pp-event-nav-prev { left: -8px; }
    .pp-event-nav-next { right: -8px; }

    /* Indicator Dots */
    .pp-event-dots {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
        margin-top: 18px;
    }
    .pp-event-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        border: none;
        background: #cbd5e1;
        cursor: pointer;
        padding: 0;
        transition: all .25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .pp-event-dot.is-active {
        background: #2563eb;
        width: 22px;
        border-radius: 10px;
    }

    @media (max-width: 480px) {
        .pp-event-modal { padding: 28px 16px 20px; border-radius: 20px; }
        .pp-event-title { font-size: 20px; }
        .pp-event-nav { display: none; }
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
        var slides = track.querySelectorAll('.pp-event-slide');
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
