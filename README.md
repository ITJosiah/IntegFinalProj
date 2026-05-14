# PharmaSync — Multi-Pharmacy Integration & ERP Console

A decentralized middleware web application designed to interconnect rural community pharmacies in Basud, Camarines Norte. Built for IT-111 Integrative Programming.

---

## 🚀 Quick Setup Guide for Classmates & Evaluators

### 1. Prerequisites
- **XAMPP** (with Apache & MySQL enabled) OR any standard PHP/MySQL environment.

### 2. Database Initialization
This project utilizes 4 isolated databases (1 core server + 3 local pharmacy nodes) to demonstrate live synchronization and distributed queries.

1. Open **phpMyAdmin** (usually at `http://localhost/phpmyadmin`).
2. Go to the **Import** tab.
3. Import `database/schema.sql`. This script cleanly sets up all 4 required database structures (`pharmasync_core`, `pharmacy_laurents`, `pharmacy_jrmp`, `pharmacy_jas5`).
4. Next, import `database/basud_medicines_seed.sql`. This script populates the databases with a comprehensive catalog of rural Philippine medicines (Biogesic, Amoxil, Neozep, Calcibloc, Alaxan FR, Plasil, etc.) fully normalized across 3NF tables (`categories` -> `brands` -> `medicines`).

### 3. Running the Web Application

#### Option A: Using XAMPP (Recommended)
1. Copy or move this entire project folder into your XAMPP web directory: `C:\xampp\htdocs\IntegFinalProj`.
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open your browser and navigate to:
   ```
   http://localhost/IntegFinalProj/index.php
   ```

#### Option B: Using PHP Built-In CLI Server
1. Open terminal/Command Prompt inside this folder.
2. Run the development server:
   ```bash
   php -S localhost:8000
   ```
3. Open your browser and navigate to:
   ```
   http://localhost:8000/index.php
   ```

---

## 🌟 System Portals & Features

### 1. Landing Page (`index.php`)
- Minimalist entry portal allowing seamless role switching between **Admin**, **Customer**, and **Pharmacies**.

### 2. Customer Search Portal (`customer_dashboard.php`)
- Live, debounced search querying all 3 pharmacy databases simultaneously.
- Displays real-time stock availability badges (**In Stock**, **Low Stock**, **Out of Stock**) and store operational status.

### 3. Pharmacy ERP Console (`pharmacy_dashboard.php`)
- Enterprise-grade ERP console featuring 3 dedicated Glassmorphism tabs (**Medicines**, **Brands**, **Categories**).
- Full inline editable inventory tables and dedicated addition modals.
- Real-time search bars positioned perfectly next to Add buttons.
- Updating inventory automatically triggers a live JSON webhook synchronization to the core server.

### 4. System Admin Console (`admin_dashboard.php`)
- Executive oversight console displaying real-time webhook connection logs, aggregate SKU counters, and node connectivity status.
