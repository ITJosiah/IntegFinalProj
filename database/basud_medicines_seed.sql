-- PharmaSync Master Seed Script (Healthcare Standard Option B)
-- Populates core pharmacy nodes and expanded rural Philippine medicine catalogs

-- 1. CORE PHARMACY NODES
USE pharmasync_core;

INSERT IGNORE INTO pharmacies (id, name, code, address, contact_number, email, latitude, longitude, is_open) VALUES
(1, 'Laurent\'s Pharmacy', 'laurents', 'Maharlika Highway, Poblacion, Basud, Camarines Norte, 4608', '0917-111-2222', 'laurents.pharmacy@gmail.com', 14.0700, 122.9645, 1),
(2, 'JRM DOCTORS Pharmacy', 'jrm', 'Maharlika Highway, Poblacion, Basud, Camarines Norte, 4608', '0918-333-4444', 'jrm.doctors@gmail.com', 14.0712, 122.9642, 1),
(3, 'D\' Rite Aid Generics Pharmacy', 'riteaid', 'Maharlika Highway, Poblacion, Basud, Camarines Norte, 4608', '0919-555-6666', 'riteaid.pharmacy@gmail.com', 14.0725, 122.9639, 1);

INSERT IGNORE INTO suggestions (id, name, email, suggestion, category, upvotes) VALUES
(1, 'Maria Santos', 'maria.santos@gmail.com', 'Please integrate a GCash mobile payment option for seamless medicine reservations!', 'Payment', 14),
(2, 'Anonymous Resident', '', 'Add SMS notification alerts when low-stock maintenance medicines (like Losartan or Amlodipine) are replenished.', 'Feature Request', 28),
(3, 'Dr. Hernandez', 'jhernandez@basudhealth.org', 'The real-world Google Maps pin alignments are incredibly accurate! This saves patients so much time when finding prescriptions.', 'Feedback', 9);


-- 2. LAURENTS PHARMACY DATA
USE pharmacy_laurents;

INSERT IGNORE INTO categories (id, name) VALUES
(1, 'Fever & Pain Relief'),
(2, 'Antibiotics'),
(3, 'Allergy & Antihistamine'),
(4, 'Cough & Cold'),
(5, 'Antacids & Gastro'),
(6, 'Vitamins & Supplements');

INSERT IGNORE INTO medicines (id, generic_name) VALUES
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

INSERT IGNORE INTO products (id, brand_name, strength, medicine_id, category_id, manufacturer, price, stock) VALUES
(1, 'Biogesic', '500mg', 1, 1, 'Unilab', 5.00, 150),
(2, 'Amoxil', '500mg', 2, 2, 'GSK', 12.50, 85),
(3, 'Virlix', '10mg', 3, 3, 'Sanofi', 22.00, 40),
(4, 'Advil', '200mg', 4, 1, 'Pfizer', 8.50, 0),
(5, 'Neozep', 'Forte', 5, 4, 'Unilab', 6.50, 250),
(6, 'Tuseran', 'Forte', 6, 4, 'Unilab', 8.00, 140),
(7, 'Kremil-S', 'Advance', 7, 5, 'Unilab', 9.50, 80),
(8, 'Decolgen', 'Forte', 8, 4, 'Unilab', 6.00, 110),
(9, 'Alaxan', 'FR', 9, 1, 'Unilab', 9.50, 180),
(10, 'Plasil', '10mg', 10, 5, 'Sanofi', 14.25, 50),
(11, 'Benadryl AH', '25mg', 11, 3, 'J&J', 12.00, 95);


-- 3. JRM DOCTORS PHARMACY DATA
USE pharmacy_jrm;

INSERT IGNORE INTO categories (id, name) VALUES
(1, 'Fever & Pain Relief'),
(2, 'Antibiotics'),
(3, 'Allergy & Antihistamine'),
(4, 'Cough & Cold'),
(5, 'Antacids & Gastro'),
(6, 'Vitamins & Supplements'),
(7, 'Hypertension & Heart');

INSERT IGNORE INTO medicines (id, generic_name) VALUES
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

INSERT IGNORE INTO products (id, brand_name, strength, medicine_id, category_id, manufacturer, price, stock) VALUES
(1, 'Tempra', '500mg', 1, 1, 'Taisho', 4.50, 200),
(2, 'Medicol', 'Advance 400mg', 2, 1, 'Unilab', 9.00, 120),
(3, 'Diatabs', '2mg', 3, 5, 'Unilab', 6.75, 95),
(4, 'Poten-Cee', '500mg', 4, 6, 'Pascual Lab', 7.00, 300),
(5, 'Symdex-D', 'Standard', 5, 4, 'Rhea Generics', 4.50, 300),
(6, 'Buscopan Venus', 'Standard', 6, 5, 'Sanofi', 24.50, 75),
(7, 'Imodium', '2mg', 3, 5, 'J&J', 11.25, 150),
(8, 'Ceelin Syrup', '120ml', 7, 6, 'Unilab', 120.00, 45),
(9, 'Calcibloc', '5mg', 8, 7, 'Therapharma', 28.50, 110),
(10, 'Aspilet', '81mg', 9, 7, 'Unilab', 3.50, 400),
(11, 'Robitussin DM', 'Standard', 10, 4, 'Pfizer', 135.00, 35);


-- 4. D' RITE AID GENERICS PHARMACY DATA
USE pharmacy_riteaid;

INSERT IGNORE INTO categories (id, name) VALUES
(1, 'Fever & Pain Relief'),
(2, 'Antibiotics'),
(3, 'Allergy & Antihistamine'),
(4, 'Cough & Cold'),
(5, 'Antacids & Gastro'),
(6, 'Vitamins & Supplements');

INSERT IGNORE INTO medicines (id, generic_name) VALUES
(1, 'Cetirizine'),
(2, 'Mefenamic Acid'),
(3, 'Carbocisteine'),
(4, 'Paracetamol'),
(5, 'Butamirate Citrate'),
(6, 'Bacillus Clausii'),
(7, 'Naproxen Sodium'),
(8, 'Co-trimoxazole');

INSERT IGNORE INTO products (id, brand_name, strength, medicine_id, category_id, manufacturer, price, stock) VALUES
(1, 'Alnix', '10mg', 1, 3, 'Unilab', 20.00, 110),
(2, 'Dolfenal', '500mg', 2, 1, 'Unilab', 25.00, 60),
(3, 'Solmux', '500mg', 3, 4, 'Unilab', 11.25, 180),
(4, 'Biogesic', '500mg', 4, 1, 'Unilab', 5.00, 50),
(5, 'Skelan', '500mg', 2, 1, 'Unilab', 18.00, 210),
(6, 'Sinecod', 'Forte 50mg', 5, 4, 'Haleon', 32.00, 60),
(7, 'Erceflora', '5ml', 6, 5, 'Sanofi', 45.00, 90),
(8, 'Flanax', '275mg', 7, 1, 'Bayer', 26.50, 40),
(9, 'Bactrim', '400mg', 8, 2, 'Roche', 15.00, 85),
(10, 'Gardan', '500mg', 2, 1, 'Sanofi', 22.50, 70),
(11, 'Ponstan', '500mg', 2, 1, 'Pfizer', 31.00, 130);
