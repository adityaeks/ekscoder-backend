# 🤖 Dokumentasi Integrasi AI Chat (9Router Gateway & Database Text-to-SQL)

Dokumen ini berisi panduan lengkap, arsitektur teknis, panduan keamanan, serta tata cara penggunaan fitur **AI Chat & Analisis Database Real-Time** pada project **Ekscoder Backend**.

---

## 📌 1. Ikhtisar & Arsitektur Utama

Fitur AI pada project ini menghubungkan sistem admin Laravel dengan **9Router Gateway** (OpenAI-Compatible AI Gateway lokal pada `http://localhost:20128/v1`). 

Fitur ini memiliki dua kemampuan utama:
1. **Chat Generatif LLM (Streaming SSE)**: Percakapan interaktif berbasis teks dengan respon *Server-Sent Events (SSE)* secara real-time.
2. **Database Text-to-SQL & Financial Analyst (Two-Pass Agent)**: Kemampuan membaca, menganalisis, dan menyajikan data dari seluruh tabel database MySQL aplikasi (termasuk transaksi kas, project orders, server VPS, blog, dan user).

```
┌─────────────────┐       1. User Message       ┌────────────────────────┐
│   Browser UI    │ ───────────────────────────>│ Admin\AiChatController │
│ (Dark/Light UI) │                             └───────────┬────────────┘
└────────┬────────┘                                         │
         │                                   2. Inspect DB Schema Context
         │                                                  ▼
         │                                      ┌────────────────────────┐
         │                                      │ DatabaseSchemaService  │
         │                                      └───────────┬────────────┘
         │                                                  │
         │         3. Pass 1: Text-to-SQL Request           ▼
         │       ────────────────────────────────> ┌───────────────────┐
         │ <────────────────────────────────────── │ 9Router AI Server │
         │             Returns SELECT Query        └────────┬──────────┘
         │                                                  │
         │                                   4. Execute SELECT Query
         │                                                  ▼
         │                                      ┌────────────────────────┐
         │                                      │ AiDatabaseQueryService │
         │                                      └───────────┬────────────┘
         │                                                  │
         │         5. Pass 2: Stream Data Insight           ▼
         │ <────────────────────────────────────── ┌───────────────────┐
         │          (Server-Sent Events Chunk)     │ 9Router AI Server │
         └──────────────────────────────────────── └───────────────────┘
```

---

## 🗄️ 2. Fitur Akses Database (Text-to-SQL)

AI di dalam project ini **dapat membaca seluruh tabel database MySQL** tanpa terisolasi pada satu tabel saja.

### 📋 Tabel-Tabel yang Dapat Dibaca AI:
- **`financial_transactions`**: Riwayat pemasukan (`income`) & pengeluaran (`expense`), nominal `amount`, kode transaksi, tanggal, dan catatan.
- **`financial_categories`**: Kategori keuangan (Gaji, Operasional, Server, DP Project, dll.).
- **`project_orders`**: Order proyek, nama client, total budget, `paid_amount` (pembayaran), deadline, dan status.
- **`vps_servers` & `vps_metrics_logs`**: Data server VPS, status IP, penggunaan CPU, Memory, & Disk log.
- **`monitored_sites`**: Daftar website yang dipantau sistem.
- **`blog_posts` & `blog_categories`**: Artikel blog, kategori, status publikasi, dan viewer count.
- **`users`**: Daftar admin/staf pengguna sistem.
- **`notes` & `calendar_events`**: Catatan internal dan agenda kalender.

---

## 🛡️ 3. Keamanan & Safety Guardrails (Strict Read-Only)

Untuk memastikan database aplikasi 100% aman dari kerusakan atau kebocoran data, sistem menerapkan 3 lapisan pengamanan:

1. **Filtering Strict `SELECT`**:
   Sistem **hanya mengeksekusi query SQL yang diawali dengan `SELECT`**. Seluruh perintah berbahaya seperti `INSERT`, `UPDATE`, `DELETE`, `DROP`, `ALTER`, `TRUNCATE`, `RENAME`, atau `CREATE` akan **DIBLOKIR OTOMATIS** oleh `AiDatabaseQueryService`.
2. **Penyembunyian Kolom Sensitif (Masking)**:
   Kolom sensitif seperti `password`, `remember_token`, `api_key`, `secret`, `two_factor_secret` disaring oleh `DatabaseSchemaService` sehingga AI **tidak pernah mengetahui atau membaca isi password/key tersebut**.
3. **Pembatasan Jumlah Baris (Safety Limit)**:
   Setiap query otomatis ditambahkan `LIMIT 100` untuk mencegah *overload* memori server saat membaca tabel berukuran besar.

---

## 🎨 4. Fitur Antarmuka Pengguna (UI/UX)

- **Dark & Light Mode 100% Floating Theme**: Kompatibel sepenuhnya dengan pengubah tema bawaan aplikasi.
- **Instant Draft Mode (0ms)**: Mengklik `+ Percakapan Baru` tidak melakukan server request; percakapan baru di database hanya dibuat ketika pengguna mengirimkan pesan pertama.
- **Daftar Thread Reaktif**: Menghapus percakapan langsung memperbarui tampilan sidebar secara seketika tanpa perlu mereset browser.
- **SVG Vector Iconography**: Antarmuka bersih dari emoji unicode bawaan OS, menggunakan ikon SVG tajam dan modern.
- **Full Viewport Layout Fit**: Halaman chat diatur presisi (`calc(100vh - 92px)`), sehingga area pengetikan pesan selalu tampil di layar bagian bawah tanpa scrollbar outer.

---

## 💡 5. Contoh Pertanyaan yang Bisa Diajukan

Anda dapat bertanya kepada AI menggunakan bahasa Indonesia sehari-hari:

### 💰 Keuangan & Kas:
- *"Berapa total sisa saldo kas aplikasi saat ini?"*
- *"Tampilkan 5 pengeluaran keuangan terbesar bulan ini."*
- *"Berapa total pemasukan dari proyek bulan ini?"*

### 💼 Order Proyek & Client:
- *"Client mana yang belum melunasi sisa pembayaran proyek?"*
- *"Berapa total nilai anggaran proyek yang sedang berjalan (status in_progress)?"*

### 🖥️ Infrastruktur & Blog:
- *"Berapa total server VPS yang terdaftar di sistem?"*
- *"Tampilkan 3 artikel blog terbanyak dibaca."*

---

## 📂 6. Struktur File Terkait AI

| Nama File | Deskripsi |
| :--- | :--- |
| [config/ninerouter.php](file:///c:/laragon/www/2026/ekscoder-backend/config/ninerouter.php) | Konfigurasi default URL 9Router `http://localhost:20128/v1` & API Key. |
| [app/Services/NineRouterService.php](file:///c:/laragon/www/2026/ekscoder-backend/app/Services/NineRouterService.php) | HTTP Client cURL SSE Streaming & discovery model 9Router. |
| [app/Services/DatabaseSchemaService.php](file:///c:/laragon/www/2026/ekscoder-backend/app/Services/DatabaseSchemaService.php) | Ekstraktor skema database otomatis dengan pengaman kolom sensitif. |
| [app/Services/AiDatabaseQueryService.php](file:///c:/laragon/www/2026/ekscoder-backend/app/Services/AiDatabaseQueryService.php) | Eksekutor & validator SQL `SELECT` aman dengan guardrail ketat. |
| [app/Http/Controllers/Admin/AiChatController.php](file:///c:/laragon/www/2026/ekscoder-backend/app/Http/Controllers/Admin/AiChatController.php) | Controller admin alur percakapan, SSE stream, dan Text-to-SQL 2-Pass. |
| [resources/views/admin/ai-chat/index.blade.php](file:///c:/laragon/www/2026/ekscoder-backend/resources/views/admin/ai-chat/index.blade.php) | Tampilan Blade UI AI Chat (Dark/Light mode, marked.js, highlight.js). |

---

## 🛠️ 7. Panduan Konfigurasi 9Router

1. Pastikan **9Router Gateway** berjalan di lokal Anda pada `http://localhost:20128`.
2. Jika 9Router Anda memerlukan API Key, buka dashboard 9Router di `http://localhost:20128/dashboard`, salin API Key Anda.
3. Di halaman **AI Chat** (`http://localhost:8000/admin/ai-chat`), klik tombol **`⚙️`** di samping tombol bersihkan, lalu tempelkan **API Key** Anda dan klik **Simpan Pengaturan**.
