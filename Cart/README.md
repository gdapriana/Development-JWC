# Modul Cart (Keranjang Belanja)

Panduan konfigurasi dan integrasi modul keranjang belanja (`cart.php`) pada tema JWC.

---

## 🛠️ Langkah-Langkah Panduan & Setup

### 1. Penempatan File
Pindahkan file `cart.php` ke direktori berikut:
```
public_html/theme/front/partials/components/cart.php
```

---

### 2. Pemanggilan File di Footer
Panggil file `cart.php` di bagian `footer.php` menggunakan pernyataan `include`:

```php
<footer>
    ...
</footer>
<?php include "components/cart.php"; ?>
```

---

### 3. Konfigurasi Tombol Cart pada Header
Pada komponen header, tambahkan tombol pembuka modal keranjang belanja dengan atribut `id="cartButtonModal_"`:

```html
<!-- Style tombol dapat disesuaikan dengan desain tema -->
<button id="cartButtonModal_" class="btn-cart">
    <i class="fa fa-shopping-cart"></i> Cart
</button>
```

---

### 4. Konfigurasi Tombol "Add to Cart" pada Card Produk
Pada setiap elemen card produk, pastikan tombol tambah ke keranjang memiliki:
* Class: `addProductToCartButton_`
* Atribut Data: `data-image="..."`, `data-title="..."`, dan `data-price="..."`

#### Contoh Implementasi:
```html
<div class="col-md-4 col-12 p-md-2 p-3">
    <div href="<?= $func->link(ROUTE_PRODUCT_VIEW, $items->slug) ?>" class="card">
        <button class="addProductToCartButton_" 
                data-image="<?= $items->img_cover_url ?>" 
                data-title="<?= $items->title ?>" 
                data-price="<?= $items->price ?>">
            Add to Cart
        </button>
        <div class="card-image" style="background-image: url('<?= $items->img_cover_url; ?>');">
            <div class="floating-elements">
                <div class="floating-circle"></div>
                <div class="floating-circle"></div>
            </div>
            ...
        </div>
    </div>
</div>
```
*(Catatan: Style dan struktur tombol dapat disesuaikan kebutuhan design)*
