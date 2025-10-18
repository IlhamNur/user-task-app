# 🧩 Backend Technical Test – Task Management System (Laravel 12 + AJAX + DataTables)

Sebuah aplikasi **manajemen task dan user** berbasis web yang dibangun dengan **Laravel 12**, **Bootstrap 5**, dan **AJAX DataTables**.  
Aplikasi ini mendukung fitur **CRUD tanpa reload**, **role-based access control (admin & user)**, serta **notifikasi interaktif dengan SweetAlert2**.

---

## 🚀 Fitur Utama

### 🗂️ Manajemen Task

- CRUD task menggunakan **AJAX + DataTables (server-side)**  
- Status task: `To Do`, `In Progress`, `Done`  
- Validasi input real-time dengan SweetAlert  
- Spinner loading & feedback interaktif  

### 👥 Manajemen Pengguna (Role Admin)

- CRUD user dengan modal form (AJAX)  
- Role `admin` dan `user`  
- Proteksi akses: hanya admin yang bisa mengelola user  
- Tidak bisa menghapus diri sendiri (self-delete protection)  

### 🔒 Keamanan & UX

- Middleware `auth` dan `is_admin`  
- CSRF token otomatis di setiap AJAX request  
- Handling error global (401 / 403)  
- Spinner loader saat aksi berjalan  
- Popup konfirmasi penghapusan (SweetAlert2)

---

## 🧱 Teknologi yang Digunakan

| Komponen | Versi / Teknologi |
|-----------|--------------------|
| **Framework** | Laravel 12.x |
| **PHP** | 8.4+ |
| **Database** | MySQL / MariaDB |
| **Frontend** | Bootstrap 5.3, jQuery 3.7, SweetAlert2 |
| **DataTables** | 1.13.8 + Responsive Plugin |
| **Auth** | Laravel Breeze / Jetstream (bisa disesuaikan) |

---

## ⚙️ Cara Instalasi

### 1️⃣ Clone Repository

```bash
git clone https://github.com/IlhamNur/user-task-app.git
cd user-task-app
```

### 2️⃣ Instal Dependensi

```bash
composer install
npm install && npm run build
```

### 3️⃣ Konfigurasi Environment

Buat file `.env`:

```bash
cp .env.example .env
```

Edit konfigurasi database sesuai lokal kamu:

```
DB_DATABASE=user_task
DB_USERNAME=root
DB_PASSWORD=
```

### 4️⃣ Generate Key & Migrasi Database

```bash
php artisan key:generate
php artisan migrate --seed
```

Seeder akan otomatis membuat akun admin:

```
Email: admin@example.com
Password: password
```

### 5️⃣ Jalankan Server

```bash
composer run dev
```

Akses aplikasi di:  
👉 **<http://127.0.0.1:8000>**

---

## 🧑‍💻 Login Default

| Role | Email | Password |
|------|--------|-----------|
| **Admin** | <admin@example.com> | password |
| **User (optional)** | <user@example.com> | password |

---

## 📂 Struktur Project

```
app/
 ├── Http/
 │    ├── Controllers/
 │    │     ├── TaskController.php
 │    │     └── UserController.php
 │    ├── Middleware/
 │    │     └── IsAdmin.php
 │
 ├── Models/
 │    ├── Task.php
 │    └── User.php

resources/
 ├── views/
 │    ├── layouts/
 │    │     └── app.blade.php
 │    ├── tasks/
 │    │     └── index.blade.php
 │    └── users/
 │          └── index.blade.php

routes/
 └── web.php
```

---

## 💡 Catatan Penggunaan

- Semua **aksi CRUD berjalan tanpa reload (AJAX)**.
- Admin-only page: `/users`
- Semua endpoint task terproteksi oleh middleware `auth`.
- Hapus task atau user akan menampilkan **konfirmasi SweetAlert2**.
- Validasi form menggunakan sistem bawaan Laravel (`$request->validate()`).

---

## 🧰 Endpoint Utama (REST API)

| Endpoint | Method | Deskripsi |
|-----------|---------|-----------|
| `/tasks` | GET | Menampilkan daftar task (DataTables AJAX) |
| `/tasks` | POST | Menambahkan task baru |
| `/tasks/{id}` | PUT | Mengubah task |
| `/tasks/{id}` | DELETE | Menghapus task |
| `/users` | GET | Menampilkan daftar user (admin only) |
| `/users/{id}` | PUT | Update data user |
| `/users/{id}` | DELETE | Hapus user |

---

## 🪄 Fitur Tambahan (Opsional)

✅ Export DataTables ke Excel/PDF  
✅ Filter task berdasarkan status  
✅ Integrasi AI Asisten (opsional di brief)  
✅ Unit Test dasar (`php artisan test`)  

---

## 🧑‍🏫 Pengembang

**Nama:** Ilham Nur  
**Email:** <romdhoninuril@gmail.com>  
**GitHub:** [@ilhamnur](https://github.com/IlhamNur)

---

## 🏁 Lisensi

Aplikasi ini dikembangkan untuk keperluan **technical test backend developer**  
dan dapat digunakan untuk pembelajaran atau pengujian internal.
