# Modul Popup (Product & Event)

Kumpulan modul popup dialog / modal banner kustom untuk tema JWC yang terbagi berdasarkan tipe konten.

---

## 📂 Sub-Modul Popup

| Tipe Popup | Deskripsi Konten | Path Dokumentasi |
| :--- | :--- | :--- |
| 🛍️ **[Product Popup](Product)** | Popup promo untuk menampilkan katalog produk, harga, tombol pemesanan & link WhatsApp. | [Product/README.md](Product/README.md) |
| 📅 **[Event Popup](Event)** | Popup event untuk menampilkan agenda event, tanggal pelaksanaan, jam, lokasi & executor. | [Event/README.md](Event/README.md) |

---

## 💡 Perbedaan Utama Data & Layout

* **Product Popup**: Menampilkan `img_cover_url`, `price` (Rp), dan tombol pemesanan/booking (`ROUTE_PRODUCT_VIEW`).
* **Event Popup**: Menampilkan `img_cover`, `date_start` & `date_finish`, `start_at` (jam), `location` (lokasi), `executor` (penyelenggara), serta tombol detail event (`ROUTE_EVENT_VIEW`).
