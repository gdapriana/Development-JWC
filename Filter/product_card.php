<?php


if (!isset($items) || !is_object($items)) {
    return;
}

static $flCardStyleRendered = false;
if (!$flCardStyleRendered):
    $flCardStyleRendered = true;
?>
    <style>
        .fl-card-item {
            transition: all .3s ease;
        }

        .fl-card {
            display: flex;
            flex-direction: column;
            height: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            overflow: hidden;
            background: #ffffff;
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .fl-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08);
            border-color: #cbd5e1;
        }

        .fl-card-img {
            position: relative;
            width: 100%;
            aspect-ratio: 4 / 3;
            overflow: hidden;
            background: #f1f5f9;
        }

        .fl-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .4s ease;
        }

        .fl-card:hover .fl-card-img img {
            transform: scale(1.05);
        }

        .fl-card-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(15, 23, 42, 0.75);
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 99px;
            backdrop-filter: blur(6px);
            text-transform: lowercase;
        }

        .fl-card-body {
            display: flex;
            flex-direction: column;
            flex: 1;
            padding: 20px;
        }

        .fl-card-title {
            margin: 0 0 8px;
            font-size: 17px;
            font-weight: 700;
            line-height: 1.35;
        }

        .fl-card-title a {
            color: #0f172a;
            text-decoration: none;
            transition: color .2s ease;
        }

        .fl-card-title a:hover {
            color: #2563eb;
        }

        .fl-card-excerpt {
            margin: 0 0 16px;
            font-size: 13px;
            line-height: 1.55;
            color: #64748b;
            flex: 1;
        }

        .fl-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-top: 14px;
            border-top: 1px solid #f1f5f9;
            margin-top: auto;
        }

        .fl-price-box {
            display: flex;
            flex-direction: column;
        }

        .fl-price-label {
            font-size: 10.5px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
        }

        .fl-price-val {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
        }

        .fl-card-btn {
            display: inline-flex;
            align-items: center;
            padding: 9px 18px;
            border-radius: 10px;
            background: #2563eb;
            color: #ffffff !important;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: all .2s ease;
        }

        .fl-card-btn:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
    </style>
<?php endif; ?>

<div class="fl-card-item"
    data-id="<?= isset($items->id) ? $items->id : 0 ?>"
    data-title="<?= htmlspecialchars(mb_strtolower($items->title ?? '')) ?>"
    data-tags="<?= htmlspecialchars(mb_strtolower($items->tags ?? '')) ?>"
    data-price="<?= isset($items->price) ? (int)$items->price : 0 ?>"
    data-visit="<?= isset($items->visit) ? (int)$items->visit : 0 ?>"
    data-date="<?= isset($items->created_at) ? strtotime($items->created_at) : 0 ?>">

    <div class="fl-card">
        <?php if (!empty($items->img_cover_url) || !empty($items->img_thumb_url) || !empty($items->img_cover)): ?>
            <div class="fl-card-img">
                <a href="<?= htmlspecialchars(isset($func) && method_exists($func, 'link') ? $func->link((defined('ROUTE_PRODUCT_VIEW') ? ROUTE_PRODUCT_VIEW : 'product/') . trim($items->slug ?? '')) : '#') ?>">
                    <img src="<?= htmlspecialchars(!empty($items->img_cover_url) ? $items->img_cover_url : (!empty($items->img_thumb_url) ? $items->img_thumb_url : $items->img_cover)) ?>"
                        alt="<?= htmlspecialchars($items->title ?? '') ?>"
                        loading="lazy">
                </a>
                <?php if (!empty($items->tags)): ?>
                    <span class="fl-card-badge">#<?= htmlspecialchars(trim(explode(',', $items->tags)[0])) ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="fl-card-body">
            <h3 class="fl-card-title">
                <a href="<?= htmlspecialchars(isset($func) && method_exists($func, 'link') ? $func->link((defined('ROUTE_PRODUCT_VIEW') ? ROUTE_PRODUCT_VIEW : 'product/') . trim($items->slug ?? '')) : '#') ?>">
                    <?= htmlspecialchars($items->title ?? '') ?>
                </a>
            </h3>

            <?php if (!empty($items->content)): ?>
                <p class="fl-card-excerpt">
                    <?= htmlspecialchars(strlen(trim(strip_tags($items->content))) > 110 ? substr(trim(strip_tags($items->content)), 0, 110) . '…' : trim(strip_tags($items->content))) ?>
                </p>
            <?php endif; ?>

            <div class="fl-card-footer">
                <?php if (empty($items->hide_price)): ?>
                    <div class="fl-price-box">
                        <span class="fl-price-label">Mulai Dari</span>
                        <div class="price price-convert fl-price-val" data-price-idr="<?= isset($items->price) ? (int)$items->price : 0 ?>">
                            <?= (!empty($items->price) && (int)$items->price > 0) ? 'Rp ' . number_format((int)$items->price, 0, ',', '.') : 'Hubungi Kami' ?>
                        </div>
                    </div>
                <?php endif; ?>

                <a href="<?= htmlspecialchars(isset($func) && method_exists($func, 'link') ? $func->link((defined('ROUTE_PRODUCT_VIEW') ? ROUTE_PRODUCT_VIEW : 'product/') . trim($items->slug ?? '')) : '#') ?>" class="fl-card-btn">
                    Detail
                </a>
            </div>
        </div>
    </div>
</div>