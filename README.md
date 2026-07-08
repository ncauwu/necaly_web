# 🐱 necaly

> Website **weekly planner** yang imut-imut buat nyatet **mood, jadwal, to-do, dan catatan** kamu setiap minggu. ✨

necaly adalah aplikasi web sederhana bertema pastel yang bikin ngatur minggu jadi lebih menyenangkan. Tiap bulan punya folder sendiri, dan tiap folder punya 4 minggu yang bisa diisi dengan mood harian, jadwal, to-do list, dan catatan bebas.

> 📚 Dibuat sebagai tugas mata kuliah **Pemrograman Berbasis Web (PBW)**.

---

## ✨ Fitur

- 🔐 **Autentikasi** — daftar & login dengan password yang di-*hash* (aman).
- 📁 **Folder per bulan** — buat, buka, dan hapus folder bulanan (warna acak biru/pink).
- 🗓️ **4 minggu per folder** — pindah antar minggu lewat tombol bintang yang lucu.
- 😊 **Mood tracker** — 7 bulatan hari yang bisa diklik untuk atur mood (😊 😐 🙁).
- ✅ **To-do list** — tambah item, centang, dan coret otomatis. Tekan Enter untuk baris baru.
- 📝 **Schedule & Notes** — area tulis bebas yang tersimpan per minggu.
- 💾 **Auto-save per minggu** — data disimpan otomatis saat pindah minggu atau keluar.

---

## 🛠️ Teknologi

| Bagian | Teknologi |
| --- | --- |
| Backend | PHP (native, tanpa framework) |
| Database | MySQL (mysqli) |
| UI/UX | Figma |
| Frontend | HTML, CSS, JavaScript |
| Server lokal | XAMPP |
| Font | Libre Caslon Text (Google Fonts) |

---

## 📂 Struktur Proyek

~~~
necaly/
├── config/
│   ├── koneksi.php      # Koneksi ke database
│   └── index.php        # Penjaga folder config
├── index.php            # Tes koneksi database
├── register.php         # Halaman daftar akun
├── login.php            # Halaman login
├── logout.php           # Keluar akun
├── dashboard.php        # Halaman utama (daftar folder bulan)
├── note.php             # Isi folder (mood, schedule, to-do, notes)
├── dashboard.css        # Style dashboard
├── note.css             # Style halaman note
└── style.css            # Style login & register
~~~

---

## 🗄️ Struktur Database

Database: **`necaly_db`** — terdiri dari 3 tabel dengan relasi **one-to-many**:

- **`users`** — data akun (id, username, email, password, dibuat_pada)
- **`folders`** — folder bulanan tiap user (id, user_id, bulan, tahun, warna)
- **`catatan`** — isi tiap minggu (id, folder_id, week, schedule, todo, notes, mood)
  - Pakai `UNIQUE (folder_id, week)` supaya 1 minggu tidak pernah dobel.

~~~
users (1) ──< folders (1) ──< catatan
~~~

---

## 🚀 Cara Menjalankan (Lokal)

1. **Install & jalankan [XAMPP](https://www.apachefriends.org/)** — nyalakan **Apache** dan **MySQL**.
2. **Clone repo ini** ke folder `htdocs`:

~~~bash
git clone https://github.com/ncauwu/necaly_web.git
~~~

   Pastikan foldernya ada di `C:\xampp\htdocs\necaly`.
3. **Buat database** lewat [phpMyAdmin](http://localhost/phpmyadmin):
   - Buat database baru bernama `necaly_db`.
   - Import file SQL (atau jalankan query pembuatan tabel `users`, `folders`, `catatan`).
4. **Cek koneksi** — buka `http://localhost/necaly/index.php`. Kalau muncul *"Koneksi ke database necaly_db berhasil! 🎉"*, berarti sudah siap.
5. **Buka aplikasi** — akses `http://localhost/necaly/register.php` untuk mulai daftar akun.

---

## 🔒 Catatan Keamanan

- Password disimpan dalam bentuk **hash** (`password_hash` / `password_verify`).
- Semua query database memakai **prepared statement** (aman dari SQL injection).
- Setiap halaman privat mengecek **session** sebelum bisa diakses.

---

## 👤 Author

Dibuat dengan 🤍 oleh **[ncauwu](https://github.com/ncauwu)** untuk tugas PBW.

---

## TAMPILAN WEBSITE

**TAMPILAN LOGIN**
<img width="958" height="440" alt="image" src="https://github.com/user-attachments/assets/feb8209d-6417-4c90-b83c-e430e45f31ba" />

**TAMPILAN REGISTRASI**
<img width="958" height="441" alt="image" src="https://github.com/user-attachments/assets/823d06d7-8261-4736-8358-f5bb098df9d3" />

**TAMPILAN DASHBOARD**
<img width="959" height="439" alt="image" src="https://github.com/user-attachments/assets/bec7671b-2ee2-4a2f-a421-e57de7d46a83" />

**TAMPILAN ISI FOLDER**
<img width="959" height="438" alt="image" src="https://github.com/user-attachments/assets/641be195-594f-4556-96de-e578fa3dca7f" />
