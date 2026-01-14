-- ============================================
-- FinanceAI Database Setup Script
-- ============================================
-- Jalankan script ini di MySQL untuk membuat database dan tabel yang diperlukan
-- 
-- Buat database (jika belum ada)
CREATE DATABASE IF NOT EXISTS finance_ai_agent CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE finance_ai_agent;
-- ============================================
-- Tabel: users
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    google_id VARCHAR(255) NULL,
    email VARCHAR(255) NOT NULL,
    name VARCHAR(255) NULL,
    avatar VARCHAR(500) NULL,
    phone VARCHAR(20) NULL,
    telegram_user_id VARCHAR(50) NULL,
    api_token VARCHAR(64) NULL,
    profile_completed TINYINT(1) DEFAULT 0,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    UNIQUE INDEX idx_google_id (google_id),
    UNIQUE INDEX idx_email (email),
    UNIQUE INDEX idx_api_token (api_token),
    INDEX idx_telegram_user_id (telegram_user_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ============================================
-- Tabel: categories
-- ============================================
CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    name VARCHAR(100) NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    icon VARCHAR(50) NULL,
    is_default TINYINT(1) DEFAULT 0,
    created_at DATETIME NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_type (type),
    CONSTRAINT fk_categories_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ============================================
-- Tabel: transactions
-- ============================================
CREATE TABLE IF NOT EXISTS transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NULL,
    type ENUM('income', 'expense') NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    description TEXT NULL,
    source VARCHAR(50) DEFAULT 'web',
    attachment_url VARCHAR(500) NULL,
    transaction_date DATE NOT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_category_id (category_id),
    INDEX idx_type (type),
    INDEX idx_transaction_date (transaction_date),
    CONSTRAINT fk_transactions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_transactions_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE
    SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ============================================
-- Insert Kategori Default
-- ============================================
INSERT INTO categories (name, type, icon, is_default, created_at)
VALUES -- Kategori Pemasukan
    ('Gaji', 'income', '💰', 1, NOW()),
    ('Bonus', 'income', '🎁', 1, NOW()),
    ('Investasi', 'income', '📈', 1, NOW()),
    ('Penjualan', 'income', '🛒', 1, NOW()),
    ('Lainnya', 'income', '📥', 1, NOW()),
    -- Kategori Pengeluaran
    ('Makanan & Minuman', 'expense', '🍔', 1, NOW()),
    ('Transportasi', 'expense', '🚗', 1, NOW()),
    ('Belanja', 'expense', '🛍️', 1, NOW()),
    ('Tagihan', 'expense', '📄', 1, NOW()),
    ('Hiburan', 'expense', '🎮', 1, NOW()),
    ('Kesehatan', 'expense', '💊', 1, NOW()),
    ('Pendidikan', 'expense', '📚', 1, NOW()),
    ('Lainnya', 'expense', '📤', 1, NOW());
-- ============================================
-- Selesai!
-- ============================================
SELECT 'Database setup completed successfully!' AS Status;