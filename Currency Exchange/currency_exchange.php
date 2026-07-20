<!-- 
# DOKUMENTASI PENGGUNAAN KOMPONEN CURRENCY EXCHANGE

1. DROPDOWN MENU PILIHAN CURRENCY:
<div class="currency-selector">
  <select id="currency-select" class="currency-dropdown">
    <option value="IDR">IDR (Rp)</option>
    <option value="USD">USD ($)</option>
    <option value="EUR">EUR (€)</option>
    <option value="AUD">AUD (A$)</option>
    <option value="SGD">SGD (S$)</option>
  </select>
</div>

2. TAMPILAN HARGA NORMAL:
<div class="price price-convert" data-price-idr="<?= $items->price ?>">Rp. <?= $items->price ?></div>

3. TAMPILAN HARGA DICORET (120% dari harga asli):
<span class="price-convert-crossout" data-price-idr="<?= $items->price ?>"></span>
<div class="price price-convert" data-price-idr="<?= $items->price ?>">Rp. <?= $items->price ?></div>

4. TAUTAN WHATSAPP OTOMATIS KONVERSI:
<a href="#" class="wa-convert" data-price-idr="<?= $items->price ?>" data-product-name="<?= $items->title ?>" data-phone="628123456789">Pesan via WhatsApp</a>
-->

<script>
document.addEventListener('DOMContentLoaded', () => {
    const currencySelect = document.getElementById('currency-select');
    
    // Fallback rate jika API gagal diakses (Base IDR = 1)
    let exchangeRates = {
        IDR: 1,
        USD: 0.0000625, // Rp 16.000
        EUR: 0.0000575, // Rp 17.400
        AUD: 0.0000945, // Rp 10.600
        SGD: 0.0000840  // Rp 11.900
    };

    // Mapping Locale Negara untuk format penulisan mata uang yang benar
    const localeMap = {
        IDR: 'id-ID',
        USD: 'en-US',
        EUR: 'de-DE',
        AUD: 'en-AU',
        SGD: 'en-SG'
    };

    const globalWaPhone = "<?= isset($data->social->whatsapp) ? $data->social->whatsapp : '' ?>";

    // Mengambil semua kurs sekaligus dari API
    async function fetchExchangeRates() {
        try {
            const response = await fetch('https://api.frankfurter.app/latest?from=IDR&to=USD,EUR,AUD,SGD');
            if (!response.ok) throw new Error('API request failed');
            const data = await response.json();
            
            // Update rates dengan data real-time terbaru
            exchangeRates.USD = data.rates.USD;
            exchangeRates.EUR = data.rates.EUR;
            exchangeRates.AUD = data.rates.AUD;
            exchangeRates.SGD = data.rates.SGD;
            
            // Simpan cache di sessionStorage
            sessionStorage.setItem('cached_multi_exchange_rates', JSON.stringify(data.rates));
        } catch (error) {
            console.warn("Gagal mengambil kurs terbaru, memuat data dari cache...", error);
            const cachedRates = sessionStorage.getItem('cached_multi_exchange_rates');
            if (cachedRates) {
                try {
                    const parsedRates = JSON.parse(cachedRates);
                    exchangeRates.USD = parsedRates.USD || exchangeRates.USD;
                    exchangeRates.EUR = parsedRates.EUR || exchangeRates.EUR;
                    exchangeRates.AUD = parsedRates.AUD || exchangeRates.AUD;
                    exchangeRates.SGD = parsedRates.SGD || exchangeRates.SGD;
                } catch (e) {
                    console.error("Gagal membaca cache rates", e);
                }
            }
        }
    }

    // Format angka ke format mata uang lokal masing-masing negara
    function formatCurrency(amount, currency) {
        const locale = localeMap[currency] || 'en-US';
        return new Intl.NumberFormat(locale, {
            style: 'currency',
            currency: currency,
            minimumFractionDigits: currency === 'IDR' ? 0 : 2
        }).format(amount);
    }

    // Perbarui harga & tautan WhatsApp di halaman
    function updatePrices(targetCurrency) {
        // A. Perbarui Teks Harga Normal
        const priceElements = document.querySelectorAll('.price-convert');
        priceElements.forEach(el => {
            const basePriceIDR = parseFloat(el.getAttribute('data-price-idr'));
            if (!isNaN(basePriceIDR)) {
                const convertedPrice = basePriceIDR * exchangeRates[targetCurrency];
                el.textContent = formatCurrency(convertedPrice, targetCurrency);
            }
        });

        // B. Perbarui Teks Harga Dicoret (120% dari harga asli)
        const crossoutElements = document.querySelectorAll('.price-convert-crossout');
        crossoutElements.forEach(el => {
            let basePriceIDR = parseFloat(el.getAttribute('data-price-idr'));
            
            // Fallback: Jika data-price-idr tidak ada di el sendiri, ambil dari sibling .price-convert
            if (isNaN(basePriceIDR)) {
                const siblingPrice = el.parentElement ? el.parentElement.querySelector('.price-convert') : null;
                if (siblingPrice) {
                    basePriceIDR = parseFloat(siblingPrice.getAttribute('data-price-idr'));
                }
            }

            if (!isNaN(basePriceIDR)) {
                const crossoutPriceIDR = basePriceIDR * 1.20; // 120% dari harga asli
                const convertedPrice = crossoutPriceIDR * exchangeRates[targetCurrency];
                el.textContent = formatCurrency(convertedPrice, targetCurrency);
            }
        });

        // C. Perbarui Link WhatsApp
        const waLinks = document.querySelectorAll('.wa-convert');
        waLinks.forEach(link => {
            const basePriceIDR = parseFloat(link.getAttribute('data-price-idr'));
            const productName = link.getAttribute('data-product-name');
            const waPhone = link.getAttribute('data-phone') || globalWaPhone;
            
            if (!isNaN(basePriceIDR) && productName) {
                const convertedPrice = basePriceIDR * exchangeRates[targetCurrency];
                const formattedPrice = formatCurrency(convertedPrice, targetCurrency);
                
                const message = `Halo, saya ingin memesan "${productName}" dengan harga ${formattedPrice}. Mohon informasi lebih lanjut.`;
                const encodedMessage = encodeURIComponent(message);
                
                link.href = `https://api.whatsapp.com/send?phone=${waPhone}&text=${encodedMessage}`;
            }
        });
    }

    // Ekspor fungsi ke window agar dapat dipanggil dari AJAX/skrip lain jika diperlukan
    window.updateCurrencyExchange = updatePrices;

    // Inisialisasi awal
    async function init() {
        const savedCurrency = localStorage.getItem('user-currency') || 'IDR';
        if (currencySelect) {
            currencySelect.value = savedCurrency;
        }

        // Ambil data kurs
        await fetchExchangeRates();
        
        // Terapkan harga awal
        updatePrices(savedCurrency);

        // Listener saat user merubah dropdown
        if (currencySelect) {
            currencySelect.addEventListener('change', (e) => {
                const selectedCurrency = e.target.value;
                localStorage.setItem('user-currency', selectedCurrency);
                updatePrices(selectedCurrency);
            });
        }
    }

    init();
});
</script>
