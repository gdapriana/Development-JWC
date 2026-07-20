# Modul Popup Event

Panduan konfigurasi dan integrasi modul popup event / upcoming event (`popup.php` & `sidebar_popup.php`) pada tema JWC.

---

## 🛠️ Fitur Tampilan Event
* **Banner Cover Event**: Menampilkan gambar `img_cover`.
* **Badge Rentang Tanggal**: Menampilkan format tanggal `date_start` s/d `date_finish` secara otomatis (misal: `22 - 24 Jul 2026`).
* **Metadata Detail Event**:
  * 🕒 Waktu pelaksanaan (`start_at`)
  * 📍 Lokasi event (`location`)
  * 👤 Penyelenggara/Executor (`executor`)
* **Slider Auto/Manual**: Jika event lebih dari 1, otomatis menjadi slider/carousel yang responsif.

---

## 🛠️ Langkah-Langkah Panduan & Setup

### 1. Penempatan File
* Pindahkan file `popup.php` dari `Popup/Event/eventpopup.php` ke:
  ```
  public_html/theme/front/sidebar/type/eventpopup.php
  ```
* Pindahkan file `sidebar_eventpopup.php` dari `Popup/Event/sidebar_eventpopup.php` ke:
  ```
  public_html/theme/front/sidebar/sidebar_eventpopup.php
  ```

---

### 2. Modifikasi File `post.php`
Pastikan logika pemanggilan pada `public_html/theme/front/sidebar/type/event.php` sudah aktif:

```php
...
<?php if ($opsi == 'eventpopup'): ?>
    <?php include "eventpopup.php"; ?>
<?php endif; ?>
...
```

---

### 3. Register Sidebar pada Config Tema
Tambahkan `Sidebar_Popup` ke dalam array sidebar di file `public_html/jwc_theme_config.php`:

```php
$data['sidebar'] = array(
    ...
    'Sidebar_Eventpopup' => 'sidebar_eventpopup',
    ...
);
```

---

### 4. Pemanggilan Sidebar di Footer
Tambahkan baris berikut pada file footer aplikasi/tema:

```php
...
<?php if ($data->site_position == 'home'): ?>
    <?php $func->sidebar('eventpopup'); ?>
<?php endif; ?>
...
```

---

### 5. Struktur Data Input Event
Data yang dipassing dari controller/backend (`$data->data`) memiliki struktur objek sebagai berikut:

```php
$data->data = [
    (object)[
        'id'          => 25,
        'title'       => 'Event Test 2',
        'img_cover'   => 'https://domain.com/path/to/image.webp',
        'date_start'  => '2026-07-22',
        'date_finish' => '2026-07-24',
        'start_at'    => '08.00 AM',
        'executor'    => 'Test Executor',
        'location'    => 'Test Location',
        'content'     => 'Deskripsi event...',
        'slug'        => 'event-test-2'
    ]
];
```
