# Setup Guide: Telegram Bot + n8n + Gemini AI

Panduan lengkap untuk setup integrasi Telegram Bot dengan n8n dan Gemini AI untuk fitur upload struk.

## 1. Setup Telegram Bot

1. Buka [@BotFather](https://t.me/BotFather) di Telegram
2. Ketik `/newbot` dan ikuti instruksi
3. Simpan **Bot Token** yang diberikan

## 2. Setup Gemini API Key

1. Buka [Google AI Studio](https://aistudio.google.com/apikey)
2. Klik "Create API Key"
3. Simpan **API Key**

## 3. Setup n8n

### 3.1 Import Workflow

1. Buka n8n instance Anda
2. Klik **Settings** > **Import from File**
3. Pilih file `n8n-workflow-telegram-gemini.json`
4. Klik Import

### 3.2 Setup Credentials

#### Telegram Bot Credential:
1. Settings > Credentials > Add Credential
2. Pilih "Telegram"
3. Masukkan Bot Token
4. Simpan

#### Gemini API Credential:
1. Settings > Credentials > Add Credential
2. Pilih "HTTP Query Auth"
3. Name: `key`
4. Value: `YOUR_GEMINI_API_KEY`
5. Simpan

#### incatat.id API Token:
1. Settings > Credentials > Add Credential
2. Pilih "HTTP Header Auth"
3. Name: `Authorization`
4. Value: `Bearer YOUR_USER_API_TOKEN`
5. Simpan

> **Note**: API Token didapat dari Dashboard incatat.id > Settings

### 3.3 Update Credential IDs

Di setiap node, update credential ID yang sesuai:
- `YOUR_TELEGRAM_CREDENTIAL_ID`
- `YOUR_GEMINI_CREDENTIAL_ID`
- `YOUR_API_TOKEN_CREDENTIAL_ID`

### 3.4 Setup Webhook

1. Aktifkan node "Telegram Trigger"
2. Copy Webhook URL yang muncul
3. Set webhook di Telegram:

```bash
curl -X POST "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook" \
     -H "Content-Type: application/json" \
     -d '{"url": "<YOUR_N8N_WEBHOOK_URL>", "allowed_updates": ["message"]}'
```

## 4. API Endpoints Tersedia

### User Endpoints
| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/api/v1/user/verify` | GET | No | Cek user by telegram_id atau email |
| `/api/v1/user/link-telegram` | POST | No | Link telegram ke akun |
| `/api/v1/user/profile` | GET | Bearer | Get user profile |

### Transaction Endpoints
| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/api/v1/transaction` | GET | Bearer | List transaksi |
| `/api/v1/transaction/create` | POST | Bearer | Buat transaksi |
| `/api/v1/transaction/summary` | GET | Bearer | Get summary |
| `/api/v1/transaction/delete/{id}` | DELETE | Bearer | Hapus transaksi |

### Masterdata Endpoints
| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/api/v1/masterdata/categories` | GET | Bearer | List kategori |
| `/api/v1/masterdata/categories/create` | POST | Bearer | Buat kategori custom |

## 5. Contoh Request

### Create Transaction
```json
POST /api/v1/transaction/create
Authorization: Bearer YOUR_API_TOKEN

{
  "type": "expense",
  "amount": 50000,
  "description": "Makan siang",
  "category": "makanan",
  "date": "2025-01-15",
  "source": "telegram"
}
```

### Response
```json
{
  "success": true,
  "message": "Transaksi berhasil dicatat!",
  "transaction_id": 123
}
```

## 6. Alur Kerja

```
User kirim foto struk
    ↓
n8n download foto
    ↓
Gemini Vision extract data
    ↓
Parse JSON response
    ↓
POST ke incatat.id API
    ↓
Reply konfirmasi ke user
```

## 7. Testing

1. Buka bot Telegram Anda
2. Kirim `/start` untuk lihat help
3. Kirim `/tambah 50000 makan siang` untuk test input text
4. Kirim foto struk untuk test AI extraction

## 8. Troubleshooting

- **Bot tidak merespon**: Cek webhook sudah ter-set dengan benar
- **Error 401**: Cek API Token di credential sudah benar
- **Gemini error**: Cek API Key dan pastikan quota tidak habis
- **User not found**: Pastikan akun sudah link Telegram di website
