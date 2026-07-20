# Modul Popup Banner

Panduan konfigurasi dan integrasi modul popup / modal banner (`popup.php` & `sidebar_popup.php`) pada tema JWC.

---

## 🛠️ Langkah-Langkah Panduan & Setup

### 1. Penempatan File
* Pindahkan file `popup.php` ke:
  ```
  public_html/theme/front/sidebar/type/popup.php
  ```
* Pindahkan file `sidebar_popup.php` ke:
  ```
  public_html/theme/front/sidebar/sidebar_popup.php
  ```

---

### 2. Modifikasi File `post.php`
Tambahkan logika kondisional berikut pada file `public_html/theme/front/sidebar/type/post.php`:

```php
...
<?php if ($opsi == 'popup'): ?>
    <?php include "popup.php"; ?>
<?php endif; ?>
...
```

---

### 3. Register Sidebar pada Config Tema
Tambahkan `Sidebar_Popup` ke dalam array sidebar di file `public_html/jwc_theme_config.php`:

```php
$data['sidebar'] = array(
    ...
    'Sidebar_Popup' => 'sidebar_popup',
    ...
);
```

---

### 4. Pemanggilan Sidebar di Footer
Tambahkan baris berikut pada file footer aplikasi/tema:

```php
...
<?php if ($data->site_position == 'home'): ?>
    <?php $func->sidebar('popup'); ?>
<?php endif; ?>
...
```

---

### 5. Pengaturan Sidebar & Tag di cPanel / Admin
1. Tambahkan data sidebar baru pada cPanel/Admin Dashboard dengan memilih tipe `Sidebar_Popup`.
2. Tambahkan tag produk/postingan yang sesuai dengan kriteria tag pada sidebar yang telah diinputkan.
3. Produk/post yang memiliki tag ini secara otomatis akan ditampilkan di dalam popup modal.
