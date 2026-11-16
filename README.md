
# 🏥 AntriSehat

### Smart Queue & Appointment Management for Primary Clinics

**Sistem antrean dan janji temu klinik yang dirancang untuk membuat
layanan kesehatan lebih teratur, efisien, dan mudah dipantau.**

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Docker](https://img.shields.io/badge/Docker-Container-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com/)
[![Jenkins](https://img.shields.io/badge/Jenkins-CI%2FCD-D24939?style=for-the-badge&logo=jenkins&logoColor=white)](https://www.jenkins.io/)

[Overview](#-overview) • [Features](#-key-features) •
[Architecture](#-system-architecture) • [Getting
Started](#-getting-started) • [CI/CD](#-cicd-pipeline) • [Team](#-team)
:::

------------------------------------------------------------------------

## 📌 Overview

**AntriSehat** adalah aplikasi manajemen nomor antrean dan janji temu
untuk klinik pratama. Pasien dapat mengambil nomor antrean berdasarkan
poli atau membuat janji temu dengan dokter sesuai jadwal praktik.
Petugas klinik dapat memanggil antrean berikutnya, sementara pasien
dapat memantau antrean yang sedang berjalan.

Proyek ini dikembangkan sebagai bagian dari **Tugas Besar 1 mata kuliah
Komputasi Awan, Semester Ganjil 2026/2027 (Kelompok K05)**.

Selain membangun aplikasi, proyek ini berfokus pada penerapan praktik
**DevOps dan Continuous Integration / Continuous Deployment (CI/CD)**.
Aplikasi menjadi media implementasi pipeline otomatis mulai dari
pengambilan source code, pengujian, pembuatan Docker image, publikasi
image, deployment, hingga smoke testing.

### 🎯 Project Goals

-   Mengembangkan sistem antrean dan janji temu klinik.
-   Menerapkan otomatisasi pengujian dan deployment melalui Jenkins.
-   Mengemas aplikasi dalam container menggunakan Docker.
-   Mengelola aplikasi, database, dan scheduler menggunakan Docker
    Compose.
-   Menyiapkan fondasi deployment lanjutan pada Azure App Service untuk
    Tugas Besar 2.

## ✨ Key Features

  -----------------------------------------------------------------------
  Feature                             Description
  ----------------------------------- -----------------------------------
  👨‍⚕️ Doctor Management                CRUD data dokter dan jadwal
                                      praktik.

  🏥 Queue Registration               Pasien dapat mengambil nomor
                                      antrean berdasarkan poli.

  📅 Appointment Booking              Membuat janji temu berdasarkan
                                      jadwal dokter yang tersedia.

  📢 Queue Calling                    Petugas memanggil antrean
                                      berikutnya pada poli.

  📺 Live Queue Display               Menampilkan informasi antrean yang
                                      sedang berjalan.

  💚 Health Check                     Memeriksa status aplikasi dan
                                      koneksi database melalui
                                      `GET /health`.

  ⏰ Daily Queue Scheduler            Memproses antrean yang belum
                                      selesai dan menutup sesi yang telah
                                      lewat.
  -----------------------------------------------------------------------

> **Catatan:** Daftar fitur di atas merupakan cakupan produk berdasarkan
> PRD. Ketersediaan implementasi aktual perlu diverifikasi terhadap
> repository.

## 🧰 Tech Stack

  Technology       Version / Role     Purpose
  ---------------- ------------------ ----------------------------------------
  Laravel          12 (target PRD)    Backend application framework
  PHP              8.3 (target PRD)   Application runtime
  MySQL            8.4                Relational database
  Composer         2.x                PHP dependency management
  Docker           ---                Application containerization
  Docker Compose   ---                Multi-container orchestration
  Jenkins          ---                CI/CD automation
  Docker Hub       ---                Container image registry
  Ngrok            ---                Public access for Jenkins webhook/demo

Versi dan konfigurasi aktual harus mengikuti kondisi repository apabila
berbeda dari rekomendasi PRD.

## 🏗️ System Architecture

AntriSehat menggunakan pendekatan multi-container agar setiap komponen
memiliki tanggung jawab yang jelas.

``` mermaid
flowchart TB
    User["Patient / Clinic Staff"]
    Browser["Web Browser"]
    App["Laravel Application<br/>Apache · Port 80"]
    DB[("MySQL 8.4<br/>Port 3306")]
    Scheduler["Scheduler Container<br/>Laravel schedule:work"]

    User --> Browser
    Browser -->|"localhost:8000"| App
    App -->|"Database connection"| DB
    Scheduler -->|"Scheduled jobs"| DB
```

### Container Responsibilities

  -----------------------------------------------------------------------
  Service                 Responsibility          Network
  ----------------------- ----------------------- -----------------------
  `app`                   Menjalankan aplikasi    `frontend` + `backend`
                          Laravel dan melayani    
                          HTTP request.           

  `db`                    Menyimpan data aplikasi `backend`
                          menggunakan MySQL.      

  `scheduler`             Menjalankan Laravel     `backend`
                          scheduler dan job reset 
                          antrean harian.         
  -----------------------------------------------------------------------

**Network design:** - `frontend` digunakan agar aplikasi dapat diakses
dari host melalui port `8000`. - `backend` menggunakan internal network
untuk komunikasi aplikasi dengan database. - Database tidak mengekspos
port ke publik. - Service scheduler tidak membutuhkan port publik.

### Application Ports

  Component                             Port Access
  --------------------------------- -------- -------------------------
  Laravel Application (container)       `80` Internal
  Laravel Application (host)          `8000` `http://localhost:8000`
  MySQL                               `3306` Internal Docker network
  Jenkins                             `8080` Jenkins host / tunnel

## 📂 Project Structure

Struktur target repository berdasarkan PRD:

``` text
antrisehat-app/
├── app/                    # Laravel application logic
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
├── docker/                 # Supporting Docker configuration
├── public/
├── resources/
├── routes/
├── tests/                  # Automated tests
├── storage/
├── Dockerfile              # Multi-stage Docker build
├── compose.yaml            # Application, database & scheduler
├── Jenkinsfile             # CI/CD pipeline definition
├── .dockerignore
├── .env.example
├── .gitignore
└── README.md
```

> Struktur ini merupakan struktur yang direncanakan dalam PRD. Sesuaikan
> dokumentasi dengan struktur aktual repository.

## 🚀 Getting Started

### Prerequisites

Pastikan perangkat telah memiliki:

-   PHP sesuai versi Laravel yang digunakan.
-   Composer 2.x.
-   MySQL 8.4 (untuk menjalankan tanpa Docker).
-   Docker Engine.
-   Docker Compose.
-   Git.

### 1. Clone Repository

``` bash
git clone https://github.com/Zhacksdev/antrisehat-app.git
cd antrisehat-app
```

### 2. Configure Environment

Salin file environment example:

``` bash
cp .env.example .env
```

Sesuaikan konfigurasi `.env` dengan environment lokal. Contoh
konfigurasi database untuk Docker Compose:

``` env
APP_NAME=AntriSehat
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta
LOG_CHANNEL=stderr

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=antrisehat
DB_USERNAME=antrisehat
DB_PASSWORD=change-this-password
DB_ROOT_PASSWORD=change-this-root-password

DOCKERHUB_USER=your-dockerhub-username
IMAGE_TAG=latest
```

> Gunakan secret yang kuat untuk deployment. Jangan commit file `.env`,
> password, APP_KEY, atau credential ke repository.

### 3. Build and Run Containers

``` bash
docker compose up -d --build
```

Periksa status container:

``` bash
docker compose ps
```

Lihat log aplikasi:

``` bash
docker compose logs -f app
```

### 4. Run Database Migration

Jalankan migrasi setelah database siap:

``` bash
docker compose run --rm app php artisan migrate --force
```

Jika seeder tersedia:

``` bash
docker compose run --rm app php artisan db:seed --force
```

### 5. Access the Application

Buka aplikasi melalui browser:

``` text
http://localhost:8000
```

Periksa health endpoint:

``` bash
curl -i http://localhost:8000/health
```

Contoh respons ketika aplikasi dan database tersedia:

``` json
{
  "status": "ok",
  "database": "connected",
  "timestamp": "2026-11-17T08:00:00+07:00"
}
```

Kontrak endpoint mengharapkan HTTP `200` ketika database terhubung dan
HTTP `503` ketika database tidak dapat dijangkau.

## ⚙️ Environment Variables

  -----------------------------------------------------------------------
  Variable                            Description
  ----------------------------------- -----------------------------------
  `APP_NAME`                          Nama aplikasi.

  `APP_ENV`                           Environment aplikasi.

  `APP_KEY`                           Encryption key Laravel.

  `APP_DEBUG`                         Mengaktifkan atau menonaktifkan
                                      debug mode.

  `APP_URL`                           Base URL aplikasi.

  `APP_TIMEZONE`                      Zona waktu aplikasi,
                                      direkomendasikan `Asia/Jakarta`.

  `LOG_CHANNEL`                       Tujuan output log, direkomendasikan
                                      `stderr` untuk Docker.

  `DB_CONNECTION`                     Driver database (`mysql`).

  `DB_HOST`                           Nama service database pada Compose
                                      (`db`).

  `DB_PORT`                           Port database (`3306`).

  `DB_DATABASE`                       Nama database (`antrisehat`).

  `DB_USERNAME`                       Username database.

  `DB_PASSWORD`                       Password database.

  `DB_ROOT_PASSWORD`                  Password root MySQL.

  `DOCKERHUB_USER`                    Username Docker Hub untuk image.

  `IMAGE_TAG`                         Tag Docker image yang digunakan.
  -----------------------------------------------------------------------

Nilai di atas adalah panduan konfigurasi dari PRD. Pastikan nama
variabel konsisten dengan `compose.yaml`, Dockerfile, dan konfigurasi
Laravel aktual.

## 🗃️ Database Design

Model data awal yang direncanakan:

  -----------------------------------------------------------------------
  Table                   Main Attributes         Purpose
  ----------------------- ----------------------- -----------------------
  `polis`                 `id`, `nama`, `kode`    Data poli klinik.

  `dokters`               `id`, `nama`, `poli_id` Data dokter.

  `jadwal_dokters`        `dokter_id`, `hari`,    Jadwal praktik dokter.
                          `jam_mulai`,            
                          `jam_selesai`,          
                          `maks_pasien`           

  `pasiens`               `id`, `nama`, `no_hp`   Data pasien.

  `antreans`              `poli_id`, `pasien_id`, Data nomor antrean.
                          `tanggal`, `nomor`,     
                          `status`                

  `janji_temus`           `dokter_id`,            Data janji temu.
                          `jadwal_dokter_id`,     
                          `pasien_id`, `tanggal`, 
                          `jam_mulai`,            
                          `jam_selesai`, `status` 
  -----------------------------------------------------------------------

### Business Rules

Sistem dirancang mengikuti aturan bisnis berikut:

1.  **Sequential Queue Number** --- Nomor antrean berurutan untuk setiap
    poli dan dimulai kembali dari nomor `1` pada hari berikutnya.
2.  **Valid Appointment Schedule** --- Janji temu hanya dapat dibuat
    pada slot jadwal praktik dokter.
3.  **No Appointment Conflict** --- Pasien tidak dapat membuat janji
    temu yang bentrok pada dokter dan slot waktu yang sama.
4.  **Session Capacity** --- Maksimal 30 pasien dalam satu sesi dokter.
5.  **Daily Queue Cleanup** --- Antrean hari sebelumnya yang masih
    berstatus `MENUNGGU` diproses menjadi `KEDALUWARSA`.

Untuk menjaga konsistensi nomor antrean, PRD merekomendasikan unique
constraint pada kombinasi `(poli_id, tanggal, nomor)` dan transaksi
database dengan locking saat menentukan nomor berikutnya.

## 🔌 Main Endpoints

Endpoint yang direncanakan dalam PRD:

  -----------------------------------------------------------------------
  Method                  Endpoint                Description
  ----------------------- ----------------------- -----------------------
  `GET`                   `/health`               Memeriksa kesehatan
                                                  aplikasi dan koneksi
                                                  database.

  `CRUD`                  `/dokters`              Mengelola data dokter.

  `CRUD`                  `/jadwal-dokters`       Mengelola jadwal
                                                  praktik dokter.

  `POST`                  `/antrean`              Mengambil nomor antrean
                                                  berdasarkan poli.

  `POST`                  `/antrean/panggil`      Memanggil antrean
                                                  berikutnya.

  `GET`                   `/antrean/berjalan`     Menampilkan antrean
                                                  yang sedang berjalan.

  `POST`                  `/janji-temu`           Membuat janji temu
                                                  dokter.
  -----------------------------------------------------------------------

> Endpoint di atas merupakan rancangan awal, bukan jaminan bahwa seluruh
> route sudah tersedia. Periksa `routes/web.php` dan `routes/api.php`
> untuk implementasi aktual.

## 🧪 Testing

Pengujian otomatis menjadi bagian penting dari pipeline CI/CD untuk
memastikan aplikasi tetap berjalan sesuai kebutuhan.

### Minimum Test Scenarios

  ------------------------------------------------------------------------
                           No. Scenario              Expected Result
  ---------------------------- --------------------- ---------------------
                             1 Health endpoint       HTTP `200` saat
                               memeriksa koneksi     database terhubung.
                               database.             

                             2 Pasien mengambil      Nomor antrean
                               antrean pada poli     berurutan.
                               yang sama.            

                             3 Antrean dibuat pada   Nomor dimulai kembali
                               hari berikutnya.      dari `1`.

                             4 Janji temu dibuat di  Permintaan ditolak.
                               luar jadwal dokter.   

                             5 Janji temu bentrok    Permintaan ditolak.
                               pada dokter dan slot  
                               yang sama.            

                             6 Pasien ke-31          Permintaan ditolak.
                               mendaftar pada satu   
                               sesi dokter.          

                             7 Petugas memanggil     Status antrean
                               antrean berikutnya.   diperbarui.

                             8 Command reset         Antrean yang memenuhi
                               memproses antrean     kondisi ditandai
                               lama.                 kedaluwarsa.
  ------------------------------------------------------------------------

Jalankan test Laravel:

``` bash
php artisan test
```

Untuk menjalankan test melalui Docker sesuai rancangan multi-stage
build:

``` bash
docker build --target test -t antrisehat:test .
```

Gunakan database pengujian terpisah atau SQLite in-memory. Untuk
pengujian yang bergantung pada locking dan unique constraint, gunakan
MySQL agar perilakunya mendekati production.

## 🔄 CI/CD Pipeline

AntriSehat dirancang menggunakan Jenkins untuk mengotomatisasi proses
integrasi dan deployment aplikasi.

### Pipeline Flow

``` mermaid
flowchart LR
    A["GitHub Push / Webhook"] --> B["Checkout"]
    B --> C["Install Dependencies"]
    C --> D["Automated Tests"]
    D --> E["Build Docker Image"]
    E --> F["Push to Docker Hub"]
    F --> G["Deploy with Docker Compose"]
    G --> H["Database Migration"]
    H --> I["Smoke Test"]
```

### Pipeline Stages

  -----------------------------------------------------------------------
  Stage                               Responsibility
  ----------------------------------- -----------------------------------
  Checkout                            Mengambil source code dari GitHub.

  Dependencies                        Menyiapkan dependency aplikasi.

  Test                                Menjalankan automated tests.

  Build                               Membuat Docker image aplikasi.

  Push                                Mengirim image ke Docker Hub.

  Deploy                              Menjalankan atau memperbarui
                                      service melalui Docker Compose.

  Migration                           Menjalankan migrasi database
                                      setelah database siap.

  Smoke Test                          Memastikan aplikasi merespons
                                      melalui `/health`.
  -----------------------------------------------------------------------

**Scheduled Jenkins Build**

Selain webhook GitHub, PRD menetapkan trigger terjadwal pada Jenkins:

``` groovy
triggers {
    cron('H 1 * * *')
}
```

Trigger ini menjalankan pipeline secara terjadwal. Tanggung jawabnya
berbeda dengan scheduler aplikasi.

### Daily Queue Scheduler

Container `scheduler` menjalankan:

``` bash
php artisan schedule:work
```

Command reset antrean:

``` bash
php artisan antrean:reset
```

Jadwal yang direncanakan pada Laravel:

``` php
Schedule::command('antrean:reset')
    ->dailyAt('00:00')
    ->withoutOverlapping();
```

Periksa log scheduler untuk bukti eksekusi:

``` bash
docker compose logs -f scheduler
```

> Jenkins cron bertanggung jawab atas pipeline CI/CD terjadwal,
> sedangkan Laravel scheduler bertanggung jawab atas pemrosesan antrean
> harian. Keduanya merupakan mekanisme yang berbeda.

## 👥 Team & Responsibilities

Project dikerjakan oleh Kelompok K05 dengan pembagian tanggung jawab
berikut:

  -----------------------------------------------------------------------
  Member                  Role                    Responsibilities
  ----------------------- ----------------------- -----------------------
  Jeki                    Tech Lead / Backend     Arsitektur aplikasi,
                                                  fitur backend,
                                                  database, endpoint, dan
                                                  aturan bisnis.

  Devina                  DevOps Engineer         Jenkins, CI/CD
                                                  pipeline, webhook,
                                                  Ngrok, credentials, dan
                                                  deployment workflow.

  Aura                    Container Engineer      Dockerfile, Docker
                                                  Compose, container,
                                                  network, database
                                                  service, dan scheduler.

  Alysa                   QA & Tech Writer        Automated testing,
                                                  skenario pengujian,
                                                  dokumentasi, diagram,
                                                  dan laporan.
  -----------------------------------------------------------------------

## 🗺️ Project Timeline

  -----------------------------------------------------------------------
  Week                                Focus
  ----------------------------------- -----------------------------------
  Week 9                              Kickoff, repository, Laravel
                                      foundation, health endpoint,
                                      Dockerfile, dan Compose lokal.

  Week 10                             Jenkins, Ngrok, GitHub webhook, dan
                                      pipeline CI/CD.

  Week 11                             Implementasi fitur, business tests,
                                      scheduler, dan pengujian recovery.

  Week 12                             Final report, validasi demo, dan
                                      presentasi.
  -----------------------------------------------------------------------

## 🔐 Security Notes

-   Jangan menyimpan `.env` atau credential di Git.
-   Gunakan `.env.example` hanya untuk mendokumentasikan nama variabel
    dan placeholder.
-   Pastikan `APP_DEBUG=false` pada production.
-   Gunakan password database yang kuat.
-   Jangan mengekspos port database ke jaringan publik.
-   Simpan credential Docker Hub dan deployment di Jenkins Credentials.
-   Batasi akses Jenkins dan endpoint deployment sesuai kebutuhan.

## 📚 Project Documentation

Dokumen acuan utama pengembangan:

-   **Product Requirements Document (PRD):** PRD AntriSehat -- Tugas
    Besar 1 DevOps (K05).
-   **Repository:**
    [github.com/Zhacksdev/antrisehat-app](https://github.com/Zhacksdev/antrisehat-app)
-   **Framework Documentation:** [Laravel
    Documentation](https://laravel.com/docs)
-   **Container Documentation:** [Docker
    Documentation](https://docs.docker.com/)
-   **CI/CD Documentation:** [Jenkins
    Documentation](https://www.jenkins.io/doc/)

## 🚧 Project Status

AntriSehat merupakan proyek akademik yang dikembangkan secara bertahap.
README ini mendokumentasikan ruang lingkup produk dan rancangan teknis
berdasarkan PRD awal. Status implementasi, versi dependency, command,
endpoint, serta konfigurasi aktual perlu diselaraskan dengan kondisi
repository.

------------------------------------------------------------------------


**Built with ❤️ by Team K05**

*AntriSehat --- Better Queue, Better Care.*
