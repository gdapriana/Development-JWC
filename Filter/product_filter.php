<?php

/**
 * ============================================================
 *  DYNAMIC FILTER & SORT SYSTEM (JWC DEVELOPMENT)
 * ------------------------------------------------------------
 *  - Mengolah data produk/paket dari $data->result secara otomatis.
 *  - Memanggil komponen terpisah `product_card.php` dalam loop foreach.
 *  - Real-time search (Judul, Konten, Tag).
 *  - Filter Chip Tag otomatis diekstrak dari seluruh data.
 *  - Filter Rentang Harga (Range / Preset).
 *  - Sorting: Termurah, Termahal, Terbaru, Terpopuler, A-Z.
 *  - Terintegrasi dengan modul Currency Exchange (.price-convert).
 * ============================================================
 */

$itemList = (isset($data) && isset($data->result) && is_array($data->result)) ? $data->result : [];

// Kumpulkan semua tag unik secara otomatis dari data
$allTags = [];
foreach ($itemList as $item) {
    if (!empty($item->tags)) {
        $splitTags = preg_split('/[\s,]+/', $item->tags);
        foreach ($splitTags as $t) {
            $cleaned = trim(mb_strtolower($t));
            if ($cleaned !== '' && !in_array($cleaned, $allTags)) {
                $allTags[] = $cleaned;
            }
        }
    }
}
sort($allTags);
?>

<section class="fl-section">
    <div class="fl-container">

        <!-- Control Panel Toolbar -->
        <div class="fl-toolbar">

            <!-- Row 1: Search Bar & Sorting Dropdown -->
            <div class="fl-toolbar-top">
                <div class="fl-search-box">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="flSearchInput" placeholder="Cari paket, kata kunci, atau tag..." autocomplete="off">
                    <button type="button" id="flSearchClear" class="fl-clear-btn" style="display:none;" aria-label="Clear search">&times;</button>
                </div>

                <div class="fl-filter-actions">
                    <!-- Dropdown Rentang Harga -->
                    <div class="fl-select-wrap">
                        <select id="flPriceSelect">
                            <option value="all">Semua Harga</option>
                            <option value="0-10m">Di bawah Rp 10 Juta</option>
                            <option value="10m-50m">Rp 10 - 50 Juta</option>
                            <option value="50m-100m">Rp 50 - 100 Juta</option>
                            <option value="100m-above">Di atas Rp 100 Juta</option>
                        </select>
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>

                    <!-- Dropdown Sorting -->
                    <div class="fl-select-wrap">
                        <select id="flSortSelect">
                            <option value="default">Urutan Default</option>
                            <option value="price_asc">Harga: Termurah</option>
                            <option value="price_desc">Harga: Termahal</option>
                            <option value="popular">Terpopuler</option>
                            <option value="newest">Terbaru</option>
                            <option value="title_asc">Nama: A - Z</option>
                        </select>
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Row 2: Tag Filter Chips (Jika ada tag) -->
            <?php if (!empty($allTags)): ?>
                <div class="fl-tags-bar">
                    <span class="fl-tags-label">Tag:</span>
                    <div class="fl-tags-scroll" id="flTagsContainer">
                        <button type="button" class="fl-tag-chip is-active" data-tag="all">Semua</button>
                        <?php foreach ($allTags as $tag): ?>
                            <button type="button" class="fl-tag-chip" data-tag="<?= htmlspecialchars($tag) ?>">
                                #<?= htmlspecialchars($tag) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Row 3: Bar Status & Reset -->
            <div class="fl-status-bar">
                <span class="fl-result-count" id="flResultCount">Menampilkan <strong><?= count($itemList) ?></strong> paket</span>
                <button type="button" id="flResetBtn" class="fl-reset-btn" style="display:none;">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                    </svg>
                    Reset Filter
                </button>
            </div>

        </div>

        <!-- Grid Tampilan Produk / Paket -->
        <div class="fl-grid" id="flGridContainer">
            <?php foreach ($itemList as $items): ?>
                <?php include "product_card.php"; ?>
            <?php endforeach; ?>
        </div>

        <!-- Empty State ketika filter tidak menemukan hasil -->
        <div class="fl-empty-state" id="flEmptyState" style="display:none;">
            <div class="fl-empty-icon">
                <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    <line x1="8" y1="11" x2="14" y2="11"></line>
                </svg>
            </div>
            <h3>Tidak Ada Paket Ditemukan</h3>
            <p>Coba ubah kata kunci pencarian atau atur ulang filter Anda.</p>
            <button type="button" class="fl-btn-secondary" onclick="document.getElementById('flResetBtn').click()">Reset Filter</button>
        </div>

    </div>
</section>

<style>
    .fl-section {
        padding: 24px 0;
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        color: #0f172a;
    }

    .fl-container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 16px;
    }

    /* Toolbar Layout */
    .fl-toolbar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 20px 24px;
        margin-bottom: 28px;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05);
    }

    .fl-toolbar-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    /* Search Input Box */
    .fl-search-box {
        position: relative;
        flex: 1 1 300px;
        display: flex;
        align-items: center;
    }

    .fl-search-box svg {
        position: absolute;
        left: 14px;
        color: #94a3b8;
        pointer-events: none;
    }

    .fl-search-box input {
        width: 100%;
        padding: 12px 38px 12px 42px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        font-size: 14px;
        font-weight: 500;
        color: #0f172a;
        outline: none;
        transition: all .2s ease;
    }

    .fl-search-box input:focus {
        background: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
    }

    .fl-clear-btn {
        position: absolute;
        right: 12px;
        background: none;
        border: none;
        font-size: 18px;
        color: #94a3b8;
        cursor: pointer;
        padding: 0 4px;
    }

    .fl-clear-btn:hover {
        color: #0f172a;
    }

    /* Filter Actions / Dropdowns */
    .fl-filter-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .fl-select-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .fl-select-wrap select {
        appearance: none;
        -webkit-appearance: none;
        padding: 12px 36px 12px 16px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        font-size: 13.5px;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
        outline: none;
        transition: all .2s ease;
    }

    .fl-select-wrap select:focus,
    .fl-select-wrap select:hover {
        background: #ffffff;
        border-color: #2563eb;
    }

    .fl-select-wrap svg {
        position: absolute;
        right: 12px;
        color: #64748b;
        pointer-events: none;
    }

    /* Tag Chips Bar */
    .fl-tags-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
    }

    .fl-tags-label {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .fl-tags-scroll {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .fl-tags-scroll::-webkit-scrollbar {
        display: none;
    }

    .fl-tag-chip {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #475569;
        padding: 6px 14px;
        border-radius: 99px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: all .2s ease;
    }

    .fl-tag-chip:hover {
        border-color: #2563eb;
        color: #2563eb;
        background: rgba(37, 99, 235, 0.04);
    }

    .fl-tag-chip.is-active {
        background: #2563eb;
        border-color: #2563eb;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    /* Status Bar */
    .fl-status-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 14px;
        font-size: 13px;
        color: #64748b;
    }

    .fl-result-count strong {
        color: #0f172a;
    }

    .fl-reset-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: none;
        background: none;
        color: #ef4444;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity .2s ease;
    }

    .fl-reset-btn:hover {
        opacity: 0.8;
    }

    /* Product Grid */
    .fl-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
    }

    /* Empty State */
    .fl-empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #ffffff;
        border: 1px dashed #cbd5e1;
        border-radius: 20px;
        margin-top: 20px;
    }

    .fl-empty-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #94a3b8;
        margin-bottom: 16px;
    }

    .fl-empty-state h3 {
        margin: 0 0 6px;
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
    }

    .fl-empty-state p {
        margin: 0 0 20px;
        font-size: 14px;
        color: #64748b;
    }

    .fl-btn-secondary {
        padding: 10px 20px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #0f172a;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s ease;
    }

    .fl-btn-secondary:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }

    @media (max-width: 640px) {
        .fl-toolbar {
            padding: 16px;
        }

        .fl-toolbar-top {
            flex-direction: column;
            align-items: stretch;
        }

        .fl-filter-actions {
            flex-direction: column;
        }

        .fl-select-wrap select {
            width: 100%;
        }

        .fl-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var searchInput = document.getElementById('flSearchInput');
        var searchClear = document.getElementById('flSearchClear');
        var priceSelect = document.getElementById('flPriceSelect');
        var sortSelect = document.getElementById('flSortSelect');
        var tagsContainer = document.getElementById('flTagsContainer');
        var gridContainer = document.getElementById('flGridContainer');
        var emptyState = document.getElementById('flEmptyState');
        var resultCount = document.getElementById('flResultCount');
        var resetBtn = document.getElementById('flResetBtn');

        if (!gridContainer) return;

        var cardItems = Array.from(gridContainer.querySelectorAll('.fl-card-item'));
        var activeTag = 'all';

        function filterAndSort() {
            var query = searchInput ? searchInput.value.trim().toLowerCase() : '';
            var priceRange = priceSelect ? priceSelect.value : 'all';
            var sortBy = sortSelect ? sortSelect.value : 'default';
            var visibleCount = 0;

            if (searchClear) searchClear.style.display = query.length > 0 ? 'block' : 'none';

            var matchedItems = cardItems.filter(function(item) {
                var title = item.getAttribute('data-title') || '';
                var tags = item.getAttribute('data-tags') || '';
                var price = parseFloat(item.getAttribute('data-price')) || 0;

                var matchesSearch = !query || title.indexOf(query) !== -1 || tags.indexOf(query) !== -1;
                var matchesTag = (activeTag === 'all') || tags.indexOf(activeTag) !== -1;
                var matchesPrice = true;
                if (priceRange === '0-10m') {
                    matchesPrice = price > 0 && price <= 10000000;
                } else if (priceRange === '10m-50m') {
                    matchesPrice = price > 10000000 && price <= 50000000;
                } else if (priceRange === '50m-100m') {
                    matchesPrice = price > 50000000 && price <= 100000000;
                } else if (priceRange === '100m-above') {
                    matchesPrice = price > 100000000;
                }

                return matchesSearch && matchesTag && matchesPrice;
            });

            matchedItems.sort(function(a, b) {
                if (sortBy === 'price_asc') {
                    return (parseFloat(a.getAttribute('data-price')) || 0) - (parseFloat(b.getAttribute('data-price')) || 0);
                } else if (sortBy === 'price_desc') {
                    return (parseFloat(b.getAttribute('data-price')) || 0) - (parseFloat(a.getAttribute('data-price')) || 0);
                } else if (sortBy === 'popular') {
                    return (parseInt(b.getAttribute('data-visit')) || 0) - (parseInt(a.getAttribute('data-visit')) || 0);
                } else if (sortBy === 'newest') {
                    return (parseInt(b.getAttribute('data-date')) || 0) - (parseInt(a.getAttribute('data-date')) || 0);
                } else if (sortBy === 'title_asc') {
                    return (a.getAttribute('data-title') || '').localeCompare(b.getAttribute('data-title') || '');
                }
                return 0;
            });

            cardItems.forEach(function(item) {
                item.style.display = 'none';
            });

            matchedItems.forEach(function(item) {
                item.style.display = 'block';
                gridContainer.appendChild(item);
            });

            visibleCount = matchedItems.length;

            if (resultCount) {
                resultCount.innerHTML = 'Menampilkan <strong>' + visibleCount + '</strong> dari <strong>' + cardItems.length + '</strong> paket';
            }

            if (emptyState) {
                emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
            }

            var isFiltered = query.length > 0 || activeTag !== 'all' || priceRange !== 'all' || sortBy !== 'default';
            if (resetBtn) {
                resetBtn.style.display = isFiltered ? 'inline-flex' : 'none';
            }

            var currentCurrency = localStorage.getItem('user-currency') || 'IDR';
            if (typeof window.updateCurrencyExchange === 'function') {
                window.updateCurrencyExchange(currentCurrency);
            }
        }

        if (searchInput) searchInput.addEventListener('input', filterAndSort);
        if (searchClear) {
            searchClear.addEventListener('click', function() {
                searchInput.value = '';
                filterAndSort();
            });
        }
        if (priceSelect) priceSelect.addEventListener('change', filterAndSort);
        if (sortSelect) sortSelect.addEventListener('change', filterAndSort);

        if (tagsContainer) {
            tagsContainer.addEventListener('click', function(e) {
                var target = e.target.closest('.fl-tag-chip');
                if (target) {
                    tagsContainer.querySelectorAll('.fl-tag-chip').forEach(function(btn) {
                        btn.classList.remove('is-active');
                    });
                    target.classList.add('is-active');
                    activeTag = target.getAttribute('data-tag') || 'all';
                    filterAndSort();
                }
            });
        }

        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                if (searchInput) searchInput.value = '';
                if (priceSelect) priceSelect.value = 'all';
                if (sortSelect) sortSelect.value = 'default';
                activeTag = 'all';
                if (tagsContainer) {
                    tagsContainer.querySelectorAll('.fl-tag-chip').forEach(function(btn) {
                        btn.classList.toggle('is-active', btn.getAttribute('data-tag') === 'all');
                    });
                }
                filterAndSort();
            });
        }
    });
</script>