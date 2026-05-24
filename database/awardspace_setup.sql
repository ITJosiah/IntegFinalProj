-- PharmaSync Unified Schema & Master Seed for AwardSpace Deployment
-- Hierarchical entities: categories & medicines (generics) -> products (commercial branded strength SKUs)
-- This file does NOT contain CREATE DATABASE or USE statements, making it safe for free hosting environments.

-- ==========================================
-- 1. CORE TABLES (Middleware & Logs)
-- ==========================================
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

-- ==========================================
-- 2. LAURENTS PHARMACY TABLES
-- ==========================================
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

-- ==========================================
-- 3. JRM DOCTORS PHARMACY TABLES
-- ==========================================
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

-- ==========================================
-- 4. D' RITE AID GENERICS PHARMACY TABLES
-- ==========================================
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


-- ==========================================
-- 5. MASTER SEED DATA
-- ==========================================

-- CORE PHARMACY NODES
INSERT IGNORE INTO pharmacies (id, name, username, password_hash, code, address, contact_number, email, latitude, longitude, is_open) VALUES
(1, 'Laurent\'s Pharmacy', 'laurents', '$2y$10$BPb1YJOCljVGvy9mJzaWS.KsS1M9aLn7ihKeCKisLauVDjPviz7z6', 'laurents', 'Maharlika Highway, Poblacion, Basud, Camarines Norte, 4608', '0917-111-2222', 'laurents.pharmacy@gmail.com', 14.0700, 122.9645, 1),
(2, 'JRM DOCTORS Pharmacy', 'jrm', '$2y$10$lxVa34BT93CrS36lyGbqyeIVNbVbdwTlGiLDZOPewmwWqtvgTm5P.', 'jrm', 'Maharlika Highway, Poblacion, Basud, Camarines Norte, 4608', '0918-333-4444', 'jrm.doctors@gmail.com', 14.0712, 122.9642, 1),
(3, 'D\' Rite Aid Generics Pharmacy', 'riteaid', '$2y$10$A9K7oTZaZKArH3l.ELo5YOiwFDTJRNILaGE86MIuglTG6JQH1w7pG', 'riteaid', 'Maharlika Highway, Poblacion, Basud, Camarines Norte, 4608', '0919-555-6666', 'riteaid.pharmacy@gmail.com', 14.0725, 122.9639, 1);

-- ADMIN USER
INSERT IGNORE INTO admins (id, username, password_hash, name) VALUES
(1, 'admin', '$2y$10$cw8Nz4vza4.JnGyTuCWrFuPNs9/FZBq2nNGSqs2osq1vxYJotYo.W', 'Josiah Luke (Admin)');

-- LAURENTS PHARMACY DATA
INSERT IGNORE INTO laurents_categories (id, name) VALUES
(1, 'Fever & Pain Relief'),
(2, 'Antibiotics'),
(3, 'Allergy & Antihistamine'),
(4, 'Cough & Cold'),
(5, 'Antacids & Gastro'),
(6, 'Vitamins & Supplements');

INSERT IGNORE INTO laurents_medicines (id, generic_name) VALUES
(1, 'Paracetamol'),
(2, 'Amoxicillin'),
(3, 'Cetirizine'),
(4, 'Ibuprofen'),
(5, 'Phenylephrine + Paracetamol'),
(6, 'Dextromethorphan + Guaifenesin'),
(7, 'Famotidine + Antacids'),
(8, 'Paracetamol + Chlorphenamine'),
(9, 'Ibuprofen + Paracetamol'),
(10, 'Metoclopramide'),
(11, 'Diphenhydramine');

INSERT IGNORE INTO laurents_products (id, brand_name, strength, medicine_id, category_id, manufacturer, price, stock, prescription_required) VALUES
(1, 'Biogesic', '500mg', 1, 1, 'Unilab', 5.00, 150, 0),
(2, 'Amoxil', '500mg', 2, 2, 'GSK', 12.50, 85, 1),
(3, 'Virlix', '10mg', 3, 3, 'Sanofi', 22.00, 40, 0),
(4, 'Advil', '200mg', 4, 1, 'Pfizer', 8.50, 0, 0),
(5, 'Neozep', 'Forte', 5, 4, 'Unilab', 6.50, 250, 0),
(6, 'Tuseran', 'Forte', 6, 4, 'Unilab', 8.00, 140, 0),
(7, 'Kremil-S', 'Advance', 7, 5, 'Unilab', 9.50, 80, 0),
(8, 'Decolgen', 'Forte', 8, 4, 'Unilab', 6.00, 110, 0),
(9, 'Alaxan', 'FR', 9, 1, 'Unilab', 9.50, 180, 0),
(10, 'Plasil', '10mg', 10, 5, 'Sanofi', 14.25, 50, 1),
(11, 'Benadryl AH', '25mg', 11, 3, 'J&J', 12.00, 95, 0);

-- JRM DOCTORS PHARMACY DATA
INSERT IGNORE INTO jrm_categories (id, name) VALUES
(1, 'Fever & Pain Relief'),
(2, 'Antibiotics'),
(3, 'Allergy & Antihistamine'),
(4, 'Cough & Cold'),
(5, 'Antacids & Gastro'),
(6, 'Vitamins & Supplements'),
(7, 'Hypertension & Heart');

INSERT IGNORE INTO jrm_medicines (id, generic_name) VALUES
(1, 'Paracetamol'),
(2, 'Ibuprofen'),
(3, 'Loperamide'),
(4, 'Ascorbic Acid'),
(5, 'Paracetamol + Chlorphenamine'),
(6, 'Hyoscine Butylbromide'),
(7, 'Ascorbic Acid Syrup'),
(8, 'Nifedipine'),
(9, 'Aspirin'),
(10, 'Dextromethorphan');

INSERT IGNORE INTO jrm_products (id, brand_name, strength, medicine_id, category_id, manufacturer, price, stock, prescription_required) VALUES
(1, 'Tempra', '500mg', 1, 1, 'Taisho', 4.50, 200, 0),
(2, 'Medicol', 'Advance 400mg', 2, 1, 'Unilab', 9.00, 120, 0),
(3, 'Diatabs', '2mg', 3, 5, 'Unilab', 6.75, 95, 0),
(4, 'Poten-Cee', '500mg', 4, 6, 'Pascual Lab', 7.00, 300, 0),
(5, 'Symdex-D', 'Standard', 5, 4, 'Rhea Generics', 4.50, 300, 0),
(6, 'Buscopan Venus', 'Standard', 6, 5, 'Sanofi', 24.50, 75, 0),
(7, 'Imodium', '2mg', 3, 5, 'J&J', 11.25, 150, 0),
(8, 'Ceelin Syrup', '120ml', 7, 6, 'Unilab', 120.00, 45, 0),
(9, 'Calcibloc', '5mg', 8, 7, 'Therapharma', 28.50, 110, 1),
(10, 'Aspilet', '81mg', 9, 7, 'Unilab', 3.50, 400, 0),
(11, 'Robitussin DM', 'Standard', 10, 4, 'Pfizer', 135.00, 35, 0);

-- D' RITE AID GENERICS PHARMACY DATA
INSERT IGNORE INTO riteaid_categories (id, name) VALUES
(1, 'Fever & Pain Relief'),
(2, 'Antibiotics'),
(3, 'Allergy & Antihistamine'),
(4, 'Cough & Cold'),
(5, 'Antacids & Gastro'),
(6, 'Vitamins & Supplements');

INSERT IGNORE INTO riteaid_medicines (id, generic_name) VALUES
(1, 'Cetirizine'),
(2, 'Mefenamic Acid'),
(3, 'Carbocisteine'),
(4, 'Paracetamol'),
(5, 'Butamirate Citrate'),
(6, 'Bacillus Clausii'),
(7, 'Naproxen Sodium'),
(8, 'Co-trimoxazole');

INSERT IGNORE INTO riteaid_products (id, brand_name, strength, medicine_id, category_id, manufacturer, price, stock, prescription_required) VALUES
(1, 'Alnix', '10mg', 1, 3, 'Unilab', 20.00, 110, 0),
(2, 'Dolfenal', '500mg', 2, 1, 'Unilab', 25.00, 60, 1),
(3, 'Solmux', '500mg', 3, 4, 'Unilab', 11.25, 180, 0),
(4, 'Biogesic', '500mg', 4, 1, 'Unilab', 5.00, 50, 0),
(5, 'Skelan', '500mg', 2, 1, 'Unilab', 18.00, 210, 0),
(6, 'Sinecod', 'Forte 50mg', 5, 4, 'Haleon', 32.00, 60, 0),
(7, 'Erceflora', '5ml', 6, 5, 'Sanofi', 45.00, 90, 0),
(8, 'Flanax', '275mg', 7, 1, 'Bayer', 26.50, 40, 0),
(9, 'Bactrim', '400mg', 8, 2, 'Roche', 15.00, 85, 1),
(10, 'Gardan', '500mg', 2, 1, 'Sanofi', 22.50, 70, 1),
(11, 'Ponstan', '500mg', 2, 1, 'Pfizer', 31.00, 130, 1);
