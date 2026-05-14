-- PharmaSync Master Seed Script
-- Populates core nodes and expanded rural Philippine medicine catalogs

-- 1. CORE PHARMACY NODES
USE pharmasync_core;

INSERT IGNORE INTO pharmacies (id, name, code, address, contact_number, is_open) VALUES
(1, "Laurent's Pharmacy", 'laurents', 'Poblacion, Basud, Camarines Norte', '0917-111-2222', 1),
(2, 'JRMP Doctors Pharmacy', 'jrmp', 'Maharlika Highway, Basud, Camarines Norte', '0918-333-4444', 1),
(3, 'JAS5 Pharmacy', 'jas5', 'Barangay Mangcamagong, Basud, Camarines Norte', '0919-555-6666', 1);


-- 2. LAURENTS PHARMACY DATA
USE pharmacy_laurents;

INSERT IGNORE INTO categories (id, name) VALUES
(1, 'Fever & Pain Relief'),
(2, 'Antibiotics'),
(3, 'Allergy & Antihistamine'),
(4, 'Cough & Cold'),
(5, 'Antacids & Gastro'),
(6, 'Vitamins & Supplements');

INSERT IGNORE INTO brands (id, name, category_id, manufacturer) VALUES
(1, 'Biogesic 500mg', 1, 'Unilab'),
(2, 'Amoxil 500mg', 2, 'GSK'),
(3, 'Virlix 10mg', 3, 'Sanofi'),
(4, 'Advil 200mg', 1, 'Pfizer'),
(5, 'Neozep Forte', 4, 'Unilab'),
(6, 'Tuseran Forte', 4, 'Unilab'),
(7, 'Kremil-S Advance', 5, 'Unilab'),
(8, 'Decolgen Forte', 4, 'Unilab'),
(9, 'Alaxan FR', 1, 'Unilab'),
(10, 'Plasil 10mg', 5, 'Sanofi'),
(11, 'Benadryl AH 25mg', 3, 'J&J');

INSERT IGNORE INTO medicines (id, generic_name, brand_id, price, stock) VALUES
(1, 'Paracetamol', 1, 5.00, 150),
(2, 'Amoxicillin', 2, 12.50, 85),
(3, 'Cetirizine', 3, 22.00, 40),
(4, 'Ibuprofen', 4, 8.50, 0),
(5, 'Phenylephrine + Paracetamol', 5, 6.50, 250),
(6, 'Dextromethorphan + Guaifenesin', 6, 8.00, 140),
(7, 'Famotidine + Antacids', 7, 9.50, 80),
(8, 'Paracetamol + Chlorphenamine', 8, 6.00, 110),
(9, 'Ibuprofen + Paracetamol', 9, 9.50, 180),
(10, 'Metoclopramide', 10, 14.25, 50),
(11, 'Diphenhydramine', 11, 12.00, 95);


-- 3. JRMP DOCTORS PHARMACY DATA
USE pharmacy_jrmp;

INSERT IGNORE INTO categories (id, name) VALUES
(1, 'Fever & Pain Relief'),
(2, 'Antibiotics'),
(3, 'Allergy & Antihistamine'),
(4, 'Cough & Cold'),
(5, 'Antacids & Gastro'),
(6, 'Vitamins & Supplements'),
(7, 'Hypertension & Heart');

INSERT IGNORE INTO brands (id, name, category_id, manufacturer) VALUES
(1, 'Tempra 500mg', 1, 'Taisho'),
(2, 'Medicol Advance 400mg', 1, 'Unilab'),
(3, 'Diatabs 2mg', 5, 'Unilab'),
(4, 'Poten-Cee 500mg', 6, 'Pascual Lab'),
(5, 'Symdex-D', 4, 'Rhea Generics'),
(6, 'Buscopan Venus', 5, 'Sanofi'),
(7, 'Imodium 2mg', 5, 'J&J'),
(8, 'Ceelin Syrup 120ml', 6, 'Unilab'),
(9, 'Calcibloc 5mg', 7, 'Therapharma'),
(10, 'Aspilet 81mg', 7, 'Unilab'),
(11, 'Robitussin DM', 4, 'Pfizer');

INSERT IGNORE INTO medicines (id, generic_name, brand_id, price, stock) VALUES
(1, 'Paracetamol', 1, 4.50, 200),
(2, 'Ibuprofen', 2, 9.00, 120),
(3, 'Loperamide', 3, 6.75, 95),
(4, 'Ascorbic Acid', 4, 7.00, 300),
(5, 'Paracetamol + Chlorphenamine', 5, 4.50, 300),
(6, 'Hyoscine Butylbromide', 6, 24.50, 75),
(7, 'Loperamide', 7, 11.25, 150),
(8, 'Ascorbic Acid Syrup', 8, 120.00, 45),
(9, 'Nifedipine', 9, 28.50, 110),
(10, 'Aspirin', 10, 3.50, 400),
(11, 'Dextromethorphan', 11, 135.00, 35);


-- 4. JAS5 PHARMACY DATA
USE pharmacy_jas5;

INSERT IGNORE INTO categories (id, name) VALUES
(1, 'Fever & Pain Relief'),
(2, 'Antibiotics'),
(3, 'Allergy & Antihistamine'),
(4, 'Cough & Cold'),
(5, 'Antacids & Gastro'),
(6, 'Vitamins & Supplements');

INSERT IGNORE INTO brands (id, name, category_id, manufacturer) VALUES
(1, 'Alnix 10mg', 3, 'Unilab'),
(2, 'Dolfenal 500mg', 1, 'Unilab'),
(3, 'Solmux 500mg', 4, 'Unilab'),
(4, 'Biogesic 500mg', 1, 'Unilab'),
(5, 'Skelan 500mg', 1, 'Unilab'),
(6, 'Sinecod Forte 50mg', 4, 'Haleon'),
(7, 'Erceflora 5ml', 5, 'Sanofi'),
(8, 'Flanax 275mg', 1, 'Bayer'),
(9, 'Bactrim 400mg', 2, 'Roche'),
(10, 'Gardan 500mg', 1, 'Sanofi'),
(11, 'Ponstan 500mg', 1, 'Pfizer');

INSERT IGNORE INTO medicines (id, generic_name, brand_id, price, stock) VALUES
(1, 'Cetirizine', 1, 20.00, 110),
(2, 'Mefenamic Acid', 2, 25.00, 60),
(3, 'Carbocisteine', 3, 11.25, 180),
(4, 'Paracetamol', 4, 5.00, 50),
(5, 'Mefenamic Acid', 5, 18.00, 210),
(6, 'Butamirate Citrate', 6, 32.00, 60),
(7, 'Bacillus Clausii', 7, 45.00, 90),
(8, 'Naproxen Sodium', 8, 26.50, 40),
(9, 'Co-trimoxazole', 9, 15.00, 85),
(10, 'Mefenamic Acid', 10, 22.50, 70),
(11, 'Mefenamic Acid', 11, 31.00, 130);
