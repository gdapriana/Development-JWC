<style>
    /* Wrapper penampung input dan dropdown */
    .pg-search-wrapper {
        position: relative;
        width: 100%;
        max-width: 500px;
        margin: 15px 0;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    /* Input search unik */
    .pg-search-input {
        width: 100%;
        padding: 12px 18px;
        font-size: 15px;
        color: #1f2937;
        background-color: #ffffff;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        outline: none;
        box-sizing: border-box;
        transition: all 0.25s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }
    .pg-search-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12), 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    /* Dropdown pencarian dengan efek glassmorphism */
    .pg-search-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        max-height: 380px;
        overflow-y: auto;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(229, 231, 235, 0.7);
        border-radius: 14px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        z-index: 999;
        opacity: 0;
        transform: translateY(-8px) scale(0.99);
        pointer-events: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    /* Tampilkan dropdown */
    .pg-search-dropdown.pg-show {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }
    /* Item hasil pencarian */
    .pg-search-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 16px;
        text-decoration: none;
        color: inherit;
        border-bottom: 1px solid #f3f4f6;
        transition: background-color 0.15s ease;
    }
    .pg-search-item:last-child {
        border-bottom: none;
    }
    /* Hover & Navigasi Keyboard Active */
    .pg-search-item:hover,
    .pg-search-item.pg-active {
        background-color: #eff6ff; /* Warna biru muda lembut */
        outline: none;
    }
    /* Gambar produk */
    .pg-search-img {
        width: 52px;
        height: 52px;
        border-radius: 8px;
        object-fit: cover;
        background-color: #f3f4f6;
        flex-shrink: 0;
    }
    /* Informasi produk */
    .pg-search-details {
        flex: 1;
        min-width: 0;
    }
    .pg-search-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 8px;
        margin-bottom: 4px;
    }
    .pg-search-title {
        font-size: 14.5px;
        font-weight: 600;
        color: #111827;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 1;
    }
    .pg-search-price {
        font-size: 13px;
        font-weight: 700;
        color: #2563eb;
        white-space: nowrap;
    }
    /* Deskripsi dengan line clamp 2 */
    .pg-search-desc {
        font-size: 12.5px;
        color: #6b7280;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.4;
    }
    /* Desain pesan data kosong */
    .pg-search-empty {
        padding: 24px;
        text-align: center;
        color: #9ca3af;
        font-size: 14px;
    }
    /* Kustomisasi scrollbar */
    .pg-search-dropdown::-webkit-scrollbar {
        width: 6px;
    }
    .pg-search-dropdown::-webkit-scrollbar-track {
        background: transparent;
    }
    .pg-search-dropdown::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }
    .pg-search-dropdown::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
</style>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Data produk dari PHP array
        const productsData = <?php echo json_encode($data->data ?? []); ?>;
        
        const liveSearchInputs = document.querySelectorAll('.livesearch');
        // Format mata uang IDR
        const formatRupiah = (price) => {
            if (!price || parseInt(price) === 0) return 'Free';
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(price);
        };
        // Menghapus tag HTML dari konten deskripsi
        const stripHtml = (html) => {
            if (!html) return '';
            return html.replace(/<\/?[^>]+(>|$)/g, "");
        };
        liveSearchInputs.forEach(input => {
            // Mengambil wrapper produk (.pg-search-wrapper)
            const wrapper = input.closest('.pg-search-wrapper') || input.parentElement;
            
            // Buat element dropdown pencarian jika belum ada
            let dropdown = wrapper.querySelector('.pg-search-dropdown');
            if (!dropdown) {
                dropdown = document.createElement('div');
                dropdown.className = 'pg-search-dropdown';
                wrapper.appendChild(dropdown);
            }
            let currentIndex = -1;
            const performSearch = (query) => {
                query = query.toLowerCase().trim();
                currentIndex = -1; 
                if (query === '') {
                    dropdown.classList.remove('pg-show');
                    dropdown.innerHTML = '';
                    return;
                }
                // Filter data berdasarkan judul / tag
                const filtered = productsData.filter(item => {
                    const matchTitle = item.title && item.title.toLowerCase().includes(query);
                    const matchTags = item.tags && item.tags.toLowerCase().includes(query);
                    return matchTitle || matchTags;
                });
                if (filtered.length === 0) {
                    dropdown.innerHTML = '<div class="pg-search-empty">Product tidak ditemukan</div>';
                } else {
                    dropdown.innerHTML = filtered.map(item => {
                        const image = item.img_thumb_url || item.img_cover_url || 'https://via.placeholder.com/150?text=No+Image';
                        const cleanDesc = stripHtml(item.content);
                        const priceHtml = (item.hide_price == 0) 
                            ? `<span class="pg-search-price">${formatRupiah(item.price)}</span>` 
                            : '';
                        
                        const detailUrl = `/${item.slug}`;
                        return `
                            <a href="${detailUrl}" class="pg-search-item">
                                <img src="${image}" alt="${item.title}" class="pg-search-img" onerror="this.src='https://via.placeholder.com/150?text=No+Image'">
                                <div class="pg-search-details">
                                    <div class="pg-search-row">
                                        <h4 class="pg-search-title">${item.title}</h4>
                                        ${priceHtml}
                                    </div>
                                    <p class="pg-search-desc">${cleanDesc}</p>
                                </div>
                            </a>
                        `;
                    }).join('');
                }
                dropdown.classList.add('pg-show');
            };
            // Event handler
            input.addEventListener('input', (e) => {
                performSearch(e.target.value);
            });
            input.addEventListener('focus', (e) => {
                if (e.target.value.trim() !== '') {
                    performSearch(e.target.value);
                }
            });
            // Navigasi Keyboard
            input.addEventListener('keydown', (e) => {
                const items = dropdown.querySelectorAll('.pg-search-item');
                if (!items.length) return;
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    currentIndex = (currentIndex + 1) % items.length;
                    updateActiveItem(items);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    currentIndex = (currentIndex - 1 + items.length) % items.length;
                    updateActiveItem(items);
                } else if (e.key === 'Enter') {
                    if (currentIndex > -1 && items[currentIndex]) {
                        e.preventDefault();
                        items[currentIndex].click();
                    }
                } else if (e.key === 'Escape') {
                    dropdown.classList.remove('pg-show');
                    input.blur();
                }
            });
            const updateActiveItem = (items) => {
                items.forEach((item, index) => {
                    if (index === currentIndex) {
                        item.classList.add('pg-active');
                        item.scrollIntoView({ block: 'nearest' });
                    } else {
                        item.classList.remove('pg-active');
                    }
                });
            };
            // Tutup dropdown jika klik di luar area komponen
            document.addEventListener('click', (e) => {
                if (!wrapper.contains(e.target)) {
                    dropdown.classList.remove('pg-show');
                }
            });
        });
    });
</script>
