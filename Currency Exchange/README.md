# Modul Currency Exchange (Konversi Mata Uang)

Panduan konfigurasi dan integrasi modul penukar mata uang (`currency_exchange.php`) pada tema JWC.

---

## 🛠️ Langkah-Langkah Panduan & Setup

### 1. Penempatan File
Pindahkan file `currency_exchange.php` ke direktori berikut:
```
public_html/theme/front/partials/components/currency_exchange.php
```

---

### 2. Pemanggilan File di Footer
Panggil file `currency_exchange.php` dari bagian footer:

```php
<footer>
    ...
</footer>
<?php include "components/currency_exchange.php"; ?>
```

---

### 3. Pembuatan Dropdown Pemilih Mata Uang
Buat elemen dropdown selector mata uang (pilihan negara/mata uang dapat disesuaikan kebutuhan).

> ⚠️ **PENTING:** Pastikan class `currency-selector`, `currency-dropdown`, dan ID `currency-select` sesuai dengan contoh di bawah ini.

```html
<div class="currency-selector">
    <select id="currency-select" class="currency-dropdown">
        <option value="IDR">IDR (Rp)</option>
        <option value="USD">USD ($)</option>
        <option value="EUR">EUR (€)</option>
        <option value="AUD">AUD (A$)</option>
        <option value="SGD">SGD (S$)</option>
    </select>
</div>
```

---

### 4. Konfigurasi Area Tampilan Harga

#### A. Harga Normal
Elemen yang menampilkan harga asli/normal menggunakan class `price price-convert` dan atribut `data-price-idr`:

```html
<div class="price price-convert" data-price-idr="<?= $items->price ?>">
    Rp. <?= $items->price ?>
</div>
```

#### B. Harga Dicoret (Coretan Diskon 120%)
Gunakan class `price-convert-crossout` untuk menampilkan harga awal yang dicoret (otomatis dihitung **120% dari harga asli**):

```html
<!-- Elemen harga dicoret di atas harga asli -->
<span class="price-convert-crossout" data-price-idr="<?= $items->price ?>" style="text-decoration: line-through; opacity: 0.7;"></span>

<div class="price price-convert" data-price-idr="<?= $items->price ?>">
    Rp. <?= $items->price ?>
</div>
```

---

### 5. Integrasi Tautan WhatsApp (Opsional)
Jika Anda memiliki tombol pemesanan WhatsApp, gunakan class `wa-convert` agar pesan WhatsApp secara otomatis dikonversi ke mata uang yang dipilih pengguna:

```html
<a href="#" 
   class="wa-convert" 
   data-price-idr="<?= $items->price ?>" 
   data-product-name="<?= $items->title ?>" 
   data-phone="628123456789">
   Pesan via WhatsApp
</a>
```
