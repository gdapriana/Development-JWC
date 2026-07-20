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
Pada area/elemen HTML yang menampilkan harga produk, bungkus harga dengan class `price price-convert` serta sediakan atribut `data-price-idr`:

```html
<div class="price price-convert" data-price-idr="<?= $items->price ?>">
    Rp. <?= $items->price ?>
</div>
```
