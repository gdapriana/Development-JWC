# Modul Header Components

Panduan konfigurasi dan penyesuaian komponen header kustom pada tema JWC.

---

## 🛠️ Langkah-Langkah Panduan & Setup

### 1. Hapus Tag Header Lama
Hapus elemen `<header>` bawaan sebelumnya di file header utama:
```
public_html/theme/front/partials/header.php
```

---

### 2. Penempatan File Header Component
Pilih variasi `header_components.php` yang diinginkan dari koleksi templat header, lalu tempatkan pada lokasi:
```
public_html/theme/front/partials/header_component.php
```

---

### 3. Pemanggilan Header Component
Panggil file `header_component.php` menggunakan perintah `include` pada `header.php`.

#### Contoh Implementasi:
```php
<body>
    <div class="body">
        <?php include "header_component.php"; ?> 
        <div role="main" class="main" style="background: #fff;">
        ...
```

---

### 4. Kustomisasi
Sesuaikan komponen menu, logo, navigasi, dan gaya visual (CSS) pada file `header_component.php` sesuai kebutuhan brand/klien.
