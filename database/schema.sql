-- PharmaSync Unified DDL Schema (Single Database for Free Hosting)
-- Hierarchical entities: categories & medicines (generics) -> products (commercial branded strength SKUs)

-- LOCALHOST SETUP: Automatically drop and recreate the database
DROP DATABASE IF EXISTS pharmasync_db;
CREATE DATABASE pharmasync_db;
USE pharmasync_db;

-- 1. CORE TABLES (Middleware & Logs)
CREATE TABLE IF NOT EXISTS pharmacies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    address VARCHAR(255) NOT NULL,
    contact_number VARCHAR(50),
    email VARCHAR(100),
    latitude DECIMAL(10, 8) DEFAULT 14.0702,
    longitude DECIMAL(11, 8) DEFAULT 122.9610,
    is_open BOOLEAN DEFAULT TRUE,
    last_sync DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pharmacy_code VARCHAR(20) NOT NULL,
    action VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS suggestions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) DEFAULT 'Anonymous Resident',
    email VARCHAR(100) DEFAULT '',
    suggestion TEXT NOT NULL,
    category VARCHAR(50) DEFAULT 'General',
    upvotes INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- CUSTOMERS TABLE (for customer registration/login)
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    latitude DECIMAL(10, 8) DEFAULT NULL,
    longitude DECIMAL(11, 8) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. LAURENTS PHARMACY TABLES
CREATE TABLE IF NOT EXISTS laurents_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS laurents_medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    generic_name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS laurents_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand_name VARCHAR(100) NOT NULL,
    strength VARCHAR(50) NOT NULL,
    medicine_id INT NOT NULL,
    category_id INT NOT NULL,
    manufacturer VARCHAR(100),
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    prescription_required BOOLEAN DEFAULT FALSE,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (medicine_id) REFERENCES laurents_medicines(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES laurents_categories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS laurents_sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receipt_no VARCHAR(30) NOT NULL UNIQUE,
    total_amount DECIMAL(10, 2) NOT NULL,
    amount_paid DECIMAL(10, 2) NOT NULL,
    change_amount DECIMAL(10, 2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS laurents_sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES laurents_sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES laurents_products(id) ON DELETE CASCADE
);

-- 3. JRM DOCTORS PHARMACY TABLES
CREATE TABLE IF NOT EXISTS jrm_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS jrm_medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    generic_name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS jrm_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand_name VARCHAR(100) NOT NULL,
    strength VARCHAR(50) NOT NULL,
    medicine_id INT NOT NULL,
    category_id INT NOT NULL,
    manufacturer VARCHAR(100),
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    prescription_required BOOLEAN DEFAULT FALSE,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (medicine_id) REFERENCES jrm_medicines(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES jrm_categories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS jrm_sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receipt_no VARCHAR(30) NOT NULL UNIQUE,
    total_amount DECIMAL(10, 2) NOT NULL,
    amount_paid DECIMAL(10, 2) NOT NULL,
    change_amount DECIMAL(10, 2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS jrm_sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES jrm_sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES jrm_products(id) ON DELETE CASCADE
);

-- 4. D' RITE AID GENERICS PHARMACY TABLES
CREATE TABLE IF NOT EXISTS riteaid_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS riteaid_medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    generic_name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS riteaid_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand_name VARCHAR(100) NOT NULL,
    strength VARCHAR(50) NOT NULL,
    medicine_id INT NOT NULL,
    category_id INT NOT NULL,
    manufacturer VARCHAR(100),
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    prescription_required BOOLEAN DEFAULT FALSE,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (medicine_id) REFERENCES riteaid_medicines(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES riteaid_categories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS riteaid_sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receipt_no VARCHAR(30) NOT NULL UNIQUE,
    total_amount DECIMAL(10, 2) NOT NULL,
    amount_paid DECIMAL(10, 2) NOT NULL,
    change_amount DECIMAL(10, 2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS riteaid_sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES riteaid_sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES riteaid_products(id) ON DELETE CASCADE
);
