# 🎯 Personal Work OS

Sebuah *Productivity Dashboard* dan *Timeboxing Engine* yang dirancang khusus untuk menjembatani kesenjangan antara eksekusi teknis (Developer) dan pemikiran strategis (Project Management). 

Aplikasi ini bukan sekadar *To-Do List*, melainkan sistem operasi kerja yang melacak **Akurasi Perencanaan**, **Distribusi Waktu**, dan **Jurnal Refleksi** dalam satu antarmuka terpusat.

---

## 📖 Latar Belakang

Sistem *To-Do List* konvensional memiliki kelemahan mendasar: mereka hanya melacak apakah sebuah tugas **Selesai** atau **Belum**. Mereka tidak melacak *Berapa lama waktu yang dihabiskan?*, *Seberapa akurat estimasi awalnya?*, dan *Mengapa waktu pengerjaannya membengkak?*.

Bagi seorang profesional yang berorientasi pada manajerial dan *continuous improvement*, data historis sangatlah penting. Aplikasi **Personal Work OS** ini diciptakan untuk menyelesaikan masalah tersebut dengan memperkenalkan konsep:

1. **Pemisahan Estimasi & Alokasi:** Membedakan antara *Total Estimated Time* (misal: 4 jam untuk satu fitur) dengan *Daily Timebox Allocation* (misal: fokus 15 menit hari ini).
2. **Drill-down Analytics:** Mengubah metrik pasif menjadi investigasi aktif untuk melihat *variance* (selisih) antara waktu yang direncanakan vs. aktual pada setiap proyek.
3. **Penyatuan Konteks:** Menggabungkan pelacakan tugas, pelaporan *bug* (Root Cause & Solution), dan jurnal harian (*Daily Win, Oops Moment*) agar tidak tercecer di berbagai aplikasi yang berbeda.

---

## 🛠️ Tools & Tech Stack

Aplikasi ini dibangun menggunakan arsitektur modern yang ringan namun tangguh:

*   **Framework Backend:** Laravel 11 (PHP 8.3)
*   **Database:** MySQL / MariaDB
*   **Frontend UI/UX:** Vanilla JavaScript (ES6+), CSS3, Blade Templating
*   **Visualisasi Data:** Chart.js & DOM Manipulation
*   **Interaksi UI:** SweetAlert2 (untuk *Pop-up Timebox* & Konfirmasi)
*   **Build Tools:** Vite & Node.js
*   **Environment & Deployment:** Docker Desktop, Laravel Sail, Windows Subsystem for Linux (WSL)

---

## ⚙️ Cara Kerja & Fitur Utama

Bagaimana alur kerja (*workflow*) menggunakan aplikasi ini?

1. **Hierarki Proyek (Project -> Milestone -> Task):** 
   Setiap tugas tidak berdiri sendiri. Pengguna mendefinisikan tugas di bawah payung proyek dan *milestone* agar rapi di panel *Time Analysis*.
2. **Input Estimasi & Timebox:** 
   Pengguna memasukkan tugas, menentukan *Total Estimasi*, dan memberikan **Alokasi Hari Ini** (misal: 15 menit).
3. **Current Focus Player (Timer):** 
   Tugas yang masuk ke *Today's Plan* dapat dieksekusi dengan menekan tombol `▶ Start`. Sistem akan menghitung waktu secara *real-time*. Saat alokasi waktu habis, akan muncul *Pop-up* interaktif untuk menentukan tindakan selanjutnya (Selesai, Lanjut, atau Simpan Sesi).
4. **Analitik Harian & Bulanan:** 
   Waktu aktual dari setiap sesi akan diakumulasikan. Sistem akan menghitung **Akurasi Bulanan** (jika minimal 3 tugas berestimasi telah selesai) dan menampilkan distribusi waktu kerja (*Development, Bug Fix, Meeting, dll*).
5. **Dual Theme Context Switching:** 
   Aplikasi dilengkapi *toggle* **Work/Personal Mode**. Saat digeser ke *Personal Mode*, UI akan bertransformasi dari warna *Slate/Blue* (profesional) menjadi *Sakura Pink Pastel* secara instan, mengubah metrik menjadi aktivitas bersantai/*me-time*.

---

## 🚀 Panduan Menjalankan Aplikasi di Lokal

Aplikasi ini berjalan di atas ekosistem **Docker** menggunakan **Laravel Sail**, sehingga Anda tidak perlu menginstal PHP atau MySQL secara manual di sistem operasi komputer Anda.

### Prasyarat:
Pastikan aplikasi berikut sudah terinstal dan berjalan:
*   [Docker Desktop](https://www.docker.com/products/docker-desktop) (Pastikan integrasi WSL2 aktif jika menggunakan Windows)
*   Git Bash / Terminal WSL

### Langkah-langkah Instalasi:

**1. Clone Repositori**
Unduh kode sumber dari GitHub ke komputer lokal Anda.
```bash
git clone [https://github.com/slmaaanf/work-os.git](https://github.com/slmaaanf/work-os.git)
cd work-os

Setup Konfigurasi (.env)
cp .env.example .env

Install Dependensi PHP (Composer via Docker)
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs

Nyalakan Mesin Docker (Sail)
./vendor/bin/sail up -d

Setup Aplikasi Laravel
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate

Build Frontend Assets (Vite)
./vendor/bin/sail npm install

# Gunakan perintah ini untuk mengompilasi file satu kali (untuk production/testing)
./vendor/bin/sail npm run build

# ATAU, gunakan perintah ini jika Anda ingin mengedit kode dan melihat perubahannya secara live di browser:
./vendor/bin/sail npm run dev

Akses Aplikasi! 🎉
Buka browser favorit Anda dan navigasikan ke:
👉 http://localhost

Bersihakan databse 
./vendor/bin/sail artisan migrate:fresh

Deploy 
https://work-os-chi-six.vercel.app/