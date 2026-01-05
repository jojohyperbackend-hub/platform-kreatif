# Platform Kreatif Mata Pelajaran

Platform edukatif berbasis **kompetisi kreatif antar murid** per mata pelajaran.

> **Bukan quiz, bukan LMS.**
> Fokus ke kreativitas murid, dengan **role Admin, Guru, Murid dipisah jelas**.

---

## 🎯 Tujuan Sistem
- Memberi ruang murid untuk **berkarya** (teks / gambar / PDF)
- Guru membuat challenge kreatif
- Admin mengontrol sistem secara penuh
- Murid **tidak bisa mengubah submission** setelah dikirim

---

## 🧱 Tech Stack
- PHP Native (tanpa framework)
- MySQL
- Bootstrap 5 (CDN)
- Framer Motion (CDN)
- Session-based authentication

---

## 👥 Role & Hak Akses

### 👑 Admin
- Login pakai **username + password (plain)**
- Tambah / hapus guru
- Approve challenge guru
- Hapus **semua data** (murid, guru, challenge, submission)
- Lihat statistik

### 👨‍🏫 Guru
- Login pakai **username + password (plain)**
- Buat challenge kreatif
- Lihat submission murid
- Beri nilai & feedback

### 👨‍🎓 Murid
- Login **tanpa password**
- Username bebas
- Lihat challenge aktif
- Submit 1x per challenge
- Lihat nilai & feedback

---

## 🔁 Flow Sistem (ASCII Flowchart)

```
MULAI
  |
  v
LOGIN
  |
  +--> ADMIN -----------------------------+
  |      |                                |
  |      v                                |
  |   Manage Guru                         |
  |   Approve Challenge                   |
  |   Lihat Statistik                     |
  |   Hapus Data                          |
  |                                       |
  +--> GURU -------------------------+    |
  |      |                          |    |
  |      v                          |    |
  |   Buat Challenge (Pending)      |    |
  |      |                          |    |
  |      v                          |    |
  |   Admin Approve <---------------+    |
  |      |                               |
  |      v                               |
  |   Lihat Submission Murid             |
  |   Beri Nilai & Feedback              |
  |                                       |
  +--> MURID -----------------------------+
         |
         v
     Lihat Challenge Aktif
         |
         v
     Submit Karya (1x)
         |
         v
     Lihat Nilai & Feedback

SELESAI
```

---

## 🧩 ERD (Entity Relationship Diagram)

```
ADMIN
- id (PK)
- username
- password

GURU
- id (PK)
- username
- password

MURID
- id (PK)
- username

CHALLENGE
- id (PK)
- guru_id (FK)
- title
- subject
- description
- approved

SUBMISSION
- id (PK)
- challenge_id (FK)
- murid_id (FK)
- text_submission
- file_path
- score
- feedback
```

Relasi:
- Guru → banyak Challenge
- Challenge → banyak Submission
- Murid → banyak Submission

---

## 🗄️ Cara Membuat Database (Manual di phpMyAdmin)

### 1️⃣ Buat Database
```sql
CREATE DATABASE platform_kreatif;
```

### 2️⃣ Pilih Database
```sql
USE platform_kreatif;
```

### 3️⃣ Buat Tabel (SALIN SEMUA SEKALIGUS)
```sql

CREATE TABLE submission (
  id INT AUTO_INCREMENT PRIMARY KEY,
  challenge_id INT,
  murid_id INT,
  text_submission TEXT,
  file_path VARCHAR(255),
  score INT,
  feedback TEXT
);
```

---

## 🔐 Cara Login

### Admin
- Username: admin1
- Password: admin123

### Guru
- Username: guru1
- Password: guru1344

### Murid
- Username jau (default dari developer)
- Tanpa password

---

## 📁 Struktur Folder

```
platform-kreatif/
├── admin/
│   └── admin.php
├── guru/
│   └── guru.php
├── murid/
│   └── murid.php
├── config/
│   └── config.php
├── uploads/
├── middleware.php
├── route.php
├── index.php
├── logout.php
```

---

## ⬇️ Cara Clone Project

```bash
git clone https://github.com/jojohyperbackend-hub/platform-kreatif.git
cd platform-kreatif
```

Letakkan di:
```
C:/xampp/htdocs/
```

Akses di browser:
```
http://localhost/platform-kreatif/
```

---

## ✅ Aturan Sistem (PENTING)
- Murid **tidak bisa edit submission**
- 1 murid = 1 submission per challenge
- Guru **tidak bisa approve challenge sendiri**
- Admin punya **hak penuh**

---

## 🚀 Siap Dikembangkan
- Export nilai (CSV)
- Filter submission
- Statistik per kelas
- Upload validation

---

> Dibuat untuk edukasi & demo sistem PHP Native
> Fokus ke **alur logika**, bukan framework

>untuk tabel scema sql yang lengkap
-- Buat database
CREATE DATABASE IF NOT EXISTS platform_kreatif;
USE platform_kreatif;

-- Tabel admin
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Tabel guru
CREATE TABLE IF NOT EXISTS guru (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Tabel murid
CREATE TABLE IF NOT EXISTS murid (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL
    -- tidak ada password
);

-- Tabel challenge
CREATE TABLE IF NOT EXISTS challenge (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guru_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    subject VARCHAR(50) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    approved TINYINT(1) DEFAULT 0,
    FOREIGN KEY (guru_id) REFERENCES guru(id) ON DELETE CASCADE
);

-- Tabel submission
CREATE TABLE IF NOT EXISTS submission (
    id INT AUTO_INCREMENT PRIMARY KEY,
    challenge_id INT NOT NULL,
    murid_id INT NOT NULL,
    file_path VARCHAR(255) DEFAULT NULL,
    text_submission TEXT DEFAULT NULL,
    score INT DEFAULT NULL,
    feedback TEXT DEFAULT NULL,
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (challenge_id) REFERENCES challenge(id) ON DELETE CASCADE,
    FOREIGN KEY (murid_id) REFERENCES murid(id) ON DELETE CASCADE
);

-- Tabel statistik opsional (bisa dihitung dinamis)
CREATE TABLE IF NOT EXISTS stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    total_challenges INT DEFAULT 0,
    total_submissions INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

>note dari developer

sebelum masuk admin atau guru
di database platform_kreatif

di tabel admin pake query yah
INSERT INTO admin (username, password)
VALUES ('admin1', 'admin123');

di tabel guru
INSERT INTO guru (username, password)
VALUES ('guruipa', '12345');
