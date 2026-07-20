# Modul Filter & Sorting (Sistem Penyaringan Data)

Panduan integrasi sistem pencarian, penyaringan tag, filter harga, dan pengurutan (*sorting*) real-time berbasis data `$data->result` pada tema JWC.

---

## 📂 Struktur File Modul

```
Filter/
├── product_filter.php    # Komponen utama (Toolbar, Search, Tag Chips, Script JS & Layout Grid)
├── product_card.php      # Komponen template terpisah untuk 1 unit item (menerima $items)
└── README.md     # Panduan dokumentasi setup modul Filter
```

---

## 🛠️ Cara Kerja Pemanggilan

Pada `product_filter.php`, setiap unit produk/paket dipanggil secara modular menggunakan `include "product_card.php";` di dalam loop `foreach`:

```php
<div class="fl-grid" id="flGridContainer">
    <?php foreach ($itemList as $items): ?>
        <?php include "product_card.php"; ?>
    <?php endforeach; ?>
</div>
```

Elemen `card.php` menerima objek data per item dalam variabel `$items`.

---

## 🛠️ Fitur Sistem Filter

1. **🔍 Real-Time Search**: Pencarian instan kata kunci pada Judul paket, Isi Konten, dan Tag tanpa reload halaman.
2. **🏷️ Auto Tag Extraction (Chips)**: Menampilkan tombol chip kategori/tag yang secara otomatis disintesis dari atribut `tags` pada seluruh data di `$data->result`.
3. **💰 Filter Rentang Harga**: Menyarikan paket berdasarkan preset harga (Di bawah Rp 10 Juta, 10 - 50 Juta, 50 - 100 Juta, dan di atas 100 Juta).
4. **📊 Pengurutan (*Sorting*)**:
   * Termurah (`price_asc`)
   * Termahal (`price_desc`)
   * Terpopuler (`popular` - berdasarkan `visit`)
   * Terbaru (`newest` - berdasarkan `created_at`)
   * Abjad A - Z (`title_asc`)
5. **💱 Terintegrasi dengan Currency Exchange**: Menggunakan class `.price-convert` dan `data-price-idr` sehingga harga otomatis dikonversi jika modul *Currency Exchange* diaktifkan.
6. **🚫 Empty State & Reset**: Tampilan ramah pengguna saat filter tidak menemukan hasil, beserta tombol 1-klik untuk mereset filter.
