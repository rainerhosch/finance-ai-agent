# incatat.id - Pencatatan Keuangan Cerdas

Website pencatatan keuangan cerdas berbasis AI dengan integrasi Telegram Bot.

## 🚀 Tech Stack

- **Backend**: CodeIgniter 3.1.x
- **Frontend**: Tailwind CSS (via CDN)
- **Database**: MySQL 5.7+
- **Authentication**: Google OAuth 2.0

## 📋 Fitur

- ✅ Landing page modern dan responsif
- ✅ Login dengan Google SSO
- ✅ Dashboard dengan ringkasan keuangan
- ✅ Pencatatan transaksi (pemasukan/pengeluaran)
- ✅ Chart visualisasi keuangan
- ✅ API REST untuk integrasi Telegram Bot
- ✅ Manajemen profil pengguna

## 🔧 Instalasi

### 1. Clone dan Setup

```bash
# Clone repository
git clone <repository-url>
cd finance-ai-agent

# Install dependencies (jika menggunakan composer)
composer install
```

### 2. Konfigurasi Environment

1. Copy file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```

2. Edit file `.env` dengan konfigurasi Anda:
```env
# Database Configuration
DB_HOST=localhost
DB_USER=root
DB_PASS=your_password
DB_NAME=finance_ai_agent

# Google OAuth 2.0
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret

# Application Environment
CI_ENV=development
```

### 3. Konfigurasi Database

1. Buat database MySQL baru:
```sql
CREATE DATABASE finance_ai_agent CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Jalankan migrasi database:
   - **Via Browser**: Akses `http://localhost/finance-ai-agent/migrate`
   - **Via SQL**: Import file `database_setup.sql`

### 4. Konfigurasi Google OAuth

1. Buat project di [Google Cloud Console](https://console.cloud.google.com/)
2. Enable Google+ API
3. Buat OAuth 2.0 credentials
4. Set Authorized redirect URI: `http://localhost/finance-ai-agent/auth/callback`
5. Copy Client ID dan Client Secret ke file `.env`

### 5. Jalankan Server

```bash
# Menggunakan PHP built-in server
php -S localhost:8000

# Atau gunakan XAMPP/WAMP/MAMP
# Letakkan folder di htdocs dan akses via http://localhost/finance-ai-agent
```

## 📁 Struktur Folder

```
application/
├── config/
│   ├── auth.php          # Konfigurasi Google OAuth
│   ├── database.php      # Konfigurasi database
│   ├── routes.php        # URL routing
│   └── session.php       # Konfigurasi session
├── controllers/
│   ├── api/
│   │   └── V1.php        # REST API untuk Telegram Bot
│   ├── Auth.php          # Login/Logout controller
│   ├── Dashboard.php     # Dashboard controller
│   ├── Home.php          # Landing page
│   └── Migrate.php       # Database migration
├── core/
│   └── MY_Controller.php # Base controller
├── libraries/
│   └── Google_auth.php   # Google OAuth library
├── models/
│   ├── Category_model.php
│   ├── Transaction_model.php
│   └── User_model.php
└── views/
    ├── dashboard/        # Dashboard views
    ├── home/             # Landing page
    └── layout/           # Layout templates
```

## 🔌 API Endpoints

API untuk integrasi dengan Telegram Bot:

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/verify-user?telegram_id=xxx` | Verifikasi user |
| POST | `/api/v1/link-telegram` | Hubungkan Telegram |
| POST | `/api/v1/transactions/create` | Buat transaksi baru |
| GET | `/api/v1/transactions` | List transaksi |
| GET | `/api/v1/summary` | Ringkasan keuangan |

### Autentikasi API

Semua endpoint (kecuali verify-user dan link-telegram) memerlukan header:
```
Authorization: Bearer {API_TOKEN}
```

API Token bisa didapat dari Dashboard > Settings.

## 🤖 Integrasi Telegram Bot (n8n)

Bot Telegram dapat menggunakan API endpoints di atas untuk:
1. Verifikasi apakah user sudah terdaftar
2. Mencatat transaksi via chat atau upload gambar
3. Menampilkan ringkasan keuangan

### Contoh Request untuk n8n:

```json
// Create transaction
POST /api/v1/transactions/create
Headers: { "Authorization": "Bearer xxx", "Content-Type": "application/json" }
Body: {
    "type": "expense",
    "amount": 50000,
    "description": "Makan siang",
    "category_id": 6
}
```

## 📄 License

MIT License
