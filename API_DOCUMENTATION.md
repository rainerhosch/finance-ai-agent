# API Documentation - incatat.id

Dokumentasi lengkap untuk REST API incatat.id untuk integrasi dengan Telegram Bot dan aplikasi eksternal.

## Base URL

```
https://your-domain.com/api/v1
```

## Autentikasi

Semua endpoint (kecuali `/verify-user` dan `/link-telegram`) memerlukan header autentikasi:

```
Authorization: Bearer {API_TOKEN}
```

API Token dapat diperoleh dari **Dashboard > Settings**.

---

## Transaction Endpoints

### 1. Daftar Transaksi

Mendapatkan daftar transaksi user.

```
GET /api/v1/transaction
```

**Query Parameters:**

| Parameter | Tipe | Wajib | Deskripsi |
|-----------|------|-------|-----------|
| `type` | string | Tidak | Filter berdasarkan tipe: `income` atau `expense` |
| `from` | string | Tidak | Tanggal awal (format: `YYYY-MM-DD`) |
| `to` | string | Tidak | Tanggal akhir (format: `YYYY-MM-DD`) |
| `limit` | integer | Tidak | Jumlah data per halaman (default: 20) |
| `offset` | integer | Tidak | Offset untuk pagination (default: 0) |

**Contoh Request:**

```bash
curl -X GET "https://your-domain.com/api/v1/transaction?type=expense&from=2026-01-01&to=2026-01-31&limit=10" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

**Contoh Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "type": "expense",
      "transaction_date": "2026-01-15",
      "store_name": "Warung Makan",
      "total_amount": "50000",
      "notes": "Makan siang",
      "source": "telegram",
      "items": [
        {
          "id": 1,
          "name": "Nasi Goreng",
          "qty": 1,
          "price": "25000",
          "category_name": "Makanan",
          "category_icon": "utensils"
        }
      ]
    }
  ]
}
```

---

### 2. Buat Transaksi Baru

Membuat transaksi baru (pemasukan atau pengeluaran).

```
POST /api/v1/transaction/create
```

**Request Body (Format Sederhana):**

```json
{
  "type": "expense",
  "amount": 50000,
  "description": "Makan siang",
  "category": "Makanan",
  "date": "2026-01-15",
  "source": "telegram"
}
```

**Request Body (Format dengan Items):**

```json
{
  "type": "expense",
  "store_name": "Alfamart",
  "date": "2026-01-15",
  "source": "telegram",
  "items": [
    {
      "name": "Indomie Goreng",
      "qty": 2,
      "price": 4000,
      "category": "Makanan"
    },
    {
      "name": "Aqua 600ml",
      "qty": 1,
      "price": 5000,
      "category": "Minuman"
    }
  ]
}
```

**Parameter Body:**

| Parameter | Tipe | Wajib | Deskripsi |
|-----------|------|-------|-----------|
| `type` | string | Ya | Tipe transaksi: `income` atau `expense` |
| `amount` | number | Ya* | Total nominal (wajib jika tidak ada items) |
| `items` | array | Ya* | Daftar item (wajib jika tidak ada amount) |
| `description` | string | Tidak | Deskripsi transaksi |
| `category` | string | Tidak | Nama kategori (akan di-resolve otomatis) |
| `category_id` | integer | Tidak | ID kategori langsung |
| `store_name` | string | Tidak | Nama toko/merchant |
| `date` | string | Tidak | Tanggal transaksi (default: hari ini) |
| `source` | string | Tidak | Sumber transaksi: `web`, `telegram` (default: telegram) |
| `notes` | string | Tidak | Catatan tambahan |

**Contoh Response:**

```json
{
  "success": true,
  "message": "Transaksi berhasil dicatat!",
  "transaction_id": 123,
  "data": {
    "id": 123,
    "type": "expense",
    "transaction_date": "2026-01-15",
    "total_amount": "13000",
    "items": [...]
  }
}
```

---

### 3. Detail Transaksi

Mendapatkan detail satu transaksi beserta item-itemnya.

```
GET /api/v1/transaction/{id}
```

**Path Parameters:**

| Parameter | Tipe | Wajib | Deskripsi |
|-----------|------|-------|-----------|
| `id` | integer | Ya | ID transaksi |

**Contoh Request:**

```bash
curl -X GET "https://your-domain.com/api/v1/transaction/123" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

**Contoh Response:**

```json
{
  "success": true,
  "data": {
    "id": 123,
    "type": "expense",
    "transaction_date": "2026-01-15",
    "store_name": "Alfamart",
    "total_amount": "13000",
    "notes": null,
    "source": "telegram",
    "items": [
      {
        "id": 1,
        "name": "Indomie Goreng",
        "qty": 2,
        "price": "4000",
        "subtotal": "8000",
        "category_name": "Makanan"
      }
    ]
  }
}
```

---

### 4. Hapus Transaksi

Menghapus transaksi berdasarkan ID.

```
DELETE /api/v1/transaction/delete/{id}
```

**Path Parameters:**

| Parameter | Tipe | Wajib | Deskripsi |
|-----------|------|-------|-----------|
| `id` | integer | Ya | ID transaksi |

**Contoh Response:**

```json
{
  "success": true,
  "message": "Transaksi berhasil dihapus"
}
```

---

### 5. Ringkasan Transaksi (Summary)

Mendapatkan ringkasan total pemasukan dan pengeluaran.

```
GET /api/v1/transaction/summary
```

**Query Parameters:**

| Parameter | Tipe | Wajib | Deskripsi |
|-----------|------|-------|-----------|
| `period` | string | Tidak | Periode: `today`, `week`, `month`, `year` (default: month) |

**Contoh Request:**

```bash
curl -X GET "https://your-domain.com/api/v1/transaction/summary?period=month" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

**Contoh Response:**

```json
{
  "success": true,
  "period": "month",
  "data": {
    "income": 5000000,
    "expense": 3200000,
    "income_count": 10,
    "expense_count": 45,
    "balance": 1800000
  }
}
```

---

### 6. Cek Saldo (Balance) ⭐ NEW

Mendapatkan saldo (total pemasukan - pengeluaran) dalam rentang waktu tertentu.

```
GET /api/v1/transaction/balance
```

**Query Parameters:**

| Parameter | Tipe | Wajib | Deskripsi |
|-----------|------|-------|-----------|
| `period` | string | Tidak | Periode: `month` atau `year` (default: month) |
| `from` | string | Tidak | Tanggal awal kustom (format: `YYYY-MM-DD`) |
| `to` | string | Tidak | Tanggal akhir kustom (format: `YYYY-MM-DD`) |

> **Note:** Jika `from` atau `to` disediakan, maka periode kustom akan digunakan dan parameter `period` diabaikan.

**Contoh Request - Saldo Bulan Ini:**

```bash
curl -X GET "https://your-domain.com/api/v1/transaction/balance?period=month" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

**Contoh Request - Saldo Tahun Ini:**

```bash
curl -X GET "https://your-domain.com/api/v1/transaction/balance?period=year" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

**Contoh Request - Saldo Rentang Waktu Kustom:**

```bash
curl -X GET "https://your-domain.com/api/v1/transaction/balance?from=2026-01-01&to=2026-01-31" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

**Contoh Response (Periode):**

```json
{
  "success": true,
  "period": {
    "type": "month"
  },
  "data": {
    "total_income": 5000000,
    "total_expense": 3200000,
    "balance": 1800000,
    "income_count": 10,
    "expense_count": 45
  }
}
```

**Contoh Response (Rentang Waktu Kustom):**

```json
{
  "success": true,
  "period": {
    "type": "custom",
    "from": "2026-01-01",
    "to": "2026-01-31"
  },
  "data": {
    "total_income": 5000000,
    "total_expense": 3200000,
    "balance": 1800000,
    "income_count": 10,
    "expense_count": 45
  }
}
```

---

### 7. Laporan Transaksi (Report) ⭐ NEW

Mendapatkan laporan transaksi lengkap dengan filter waktu dan tipe.

```
GET /api/v1/transaction/report
```

**Query Parameters:**

| Parameter | Tipe | Wajib | Deskripsi |
|-----------|------|-------|-----------|
| `from` | string | **Ya** | Tanggal awal (format: `YYYY-MM-DD`) |
| `to` | string | **Ya** | Tanggal akhir (format: `YYYY-MM-DD`) |
| `type` | string | Tidak | Filter tipe: `income` atau `expense` (default: semua) |
| `category_id` | integer | Tidak | Filter berdasarkan ID kategori |
| `limit` | integer | Tidak | Jumlah data per halaman (default: 50) |
| `offset` | integer | Tidak | Offset untuk pagination (default: 0) |

**Contoh Request - Semua Transaksi Januari 2026:**

```bash
curl -X GET "https://your-domain.com/api/v1/transaction/report?from=2026-01-01&to=2026-01-31" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

**Contoh Request - Hanya Pengeluaran Januari 2026:**

```bash
curl -X GET "https://your-domain.com/api/v1/transaction/report?from=2026-01-01&to=2026-01-31&type=expense" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

**Contoh Request - Hanya Pemasukan Januari 2026:**

```bash
curl -X GET "https://your-domain.com/api/v1/transaction/report?from=2026-01-01&to=2026-01-31&type=income" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

**Contoh Request - Filter dengan Pagination:**

```bash
curl -X GET "https://your-domain.com/api/v1/transaction/report?from=2026-01-01&to=2026-01-31&type=expense&limit=10&offset=0" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

**Contoh Response:**

```json
{
  "success": true,
  "period": {
    "from": "2026-01-01",
    "to": "2026-01-31"
  },
  "filter": {
    "type": "expense",
    "category_id": null
  },
  "summary": {
    "total_income": 0,
    "total_expense": 3200000,
    "balance": -3200000,
    "transaction_count": 45
  },
  "data": [
    {
      "id": 123,
      "type": "expense",
      "transaction_date": "2026-01-15",
      "store_name": "Warung Makan",
      "total_amount": "50000",
      "notes": "Makan siang",
      "source": "telegram",
      "created_by_name": "John Doe",
      "category_name": "Makanan",
      "category_icon": "utensils",
      "items": [
        {
          "id": 1,
          "name": "Nasi Goreng",
          "qty": 1,
          "price": "25000",
          "subtotal": "25000",
          "category_name": "Makanan"
        }
      ]
    }
  ],
  "pagination": {
    "limit": 50,
    "offset": 0,
    "count": 45
  }
}
```

---

## Error Responses

Semua endpoint mengembalikan format error yang konsisten:

**401 Unauthorized:**
```json
{
  "error": "API key tidak valid"
}
```

**400 Bad Request:**
```json
{
  "error": "Rentang waktu (from dan to) diperlukan"
}
```

**404 Not Found:**
```json
{
  "error": "Transaksi tidak ditemukan"
}
```

**500 Internal Server Error:**
```json
{
  "error": "Gagal menyimpan transaksi"
}
```

---

## Contoh Penggunaan di n8n

### Cek Saldo Bulan Ini

```javascript
// HTTP Request node
Method: GET
URL: https://your-domain.com/api/v1/transaction/balance?period=month
Headers:
  - Authorization: Bearer {{$node["API Token"].data.token}}
```

### Laporan Pengeluaran Bulan Ini

```javascript
// HTTP Request node
Method: GET
URL: https://your-domain.com/api/v1/transaction/report?from={{$today.minus({months: 1}).format('yyyy-MM-dd')}}&to={{$today.format('yyyy-MM-dd')}}&type=expense
Headers:
  - Authorization: Bearer {{$node["API Token"].data.token}}
```

---

## Rate Limiting

API memiliki batasan request berdasarkan konfigurasi server:
- **RPM (Requests Per Minute)**: Maksimal requests per menit
- **RPD (Requests Per Day)**: Maksimal requests per hari

Jika limit tercapai, API akan mengembalikan error:
```json
{
  "error": "Rate limit exceeded. Please try again later."
}
```
