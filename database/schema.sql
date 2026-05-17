-- PharmaSync Fully Normalized Pure DDL Schema (Healthcare Standard Option B)
-- Hierarchical entities: categories & medicines (generics) -> products (commercial branded strength SKUs)

-- 1. CORE DATABASE (Middleware & Logs)
DROP DATABASE IF EXISTS pharmasync_core;
CREATE DATABASE pharmasync_core;
USE pharmasync_core;

CREATE TABLE IF NOT EXISTS pharmacies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
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


-- 2. LAURENTS PHARMACY DATABASE
DROP DATABASE IF EXISTS pharmacy_laurents;
CREATE DATABASE pharmacy_laurents;
USE pharmacy_laurents;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    generic_name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand_name VARCHAR(100) NOT NULL,
    strength VARCHAR(50) NOT NULL,
    medicine_id INT NOT NULL,
    category_id INT NOT NULL,
    manufacturer VARCHAR(100),
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);


-- 3. JRMP DOCTORS PHARMACY DATABASE
DROP DATABASE IF EXISTS pharmacy_jrmp;
CREATE DATABASE pharmacy_jrmp;
USE pharmacy_jrmp;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    generic_name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand_name VARCHAR(100) NOT NULL,
    strength VARCHAR(50) NOT NULL,
    medicine_id INT NOT NULL,
    category_id INT NOT NULL,
    manufacturer VARCHAR(100),
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);


-- 4. JAS5 PHARMACY DATABASE
DROP DATABASE IF EXISTS pharmacy_jas5;
CREATE DATABASE pharmacy_jas5;
USE pharmacy_jas5;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    generic_name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand_name VARCHAR(100) NOT NULL,
    strength VARCHAR(50) NOT NULL,
    medicine_id INT NOT NULL,
    category_id INT NOT NULL,
    manufacturer VARCHAR(100),
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);
