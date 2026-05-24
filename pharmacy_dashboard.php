<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pharma = isset($_GET['pharma']) ? trim($_GET['pharma']) : (isset($_SESSION['pharma_code']) ? $_SESSION['pharma_code'] : 'laurents');

// Enforce session security checks
if (!isset($_SESSION['user_role']) || 
    ($_SESSION['user_role'] !== 'pharmacy' && $_SESSION['user_role'] !== 'admin') || 
    ($_SESSION['user_role'] === 'pharmacy' && $_SESSION['pharma_code'] !== $pharma)) {
    header('Location: index.php?error=unauthorized');
    exit;
}

$_SESSION['pharma_code'] = $pharma;

$names = [
    'laurents' => "Laurent's Pharmacy",
    'jrm' => "JRM DOCTORS Pharmacy",
    'riteaid' => "D' Rite Aid Generics Pharmacy"
];
$pharmaName = $names[$pharma] ?? "Pharmacy Portal";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pharmaName; ?> Management ERP - PharmaSync</title>
    <link rel="stylesheet" href="assets/css/index.css">
    <style>
        .alert-banner {
            background: #D1FAE5;
            color: #065F46;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: none;
            align-items: center;
            justify-content: space-between;
            font-weight: 500;
            border: 1px solid #A7F3D0;
        }

        /* Custom ERP Tabs */
        .erp-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #E2E8F0;
            align-items: flex-end;
        }

        .erp-tab-btn {
            padding: 0.75rem 1.5rem;
            font-size: 1.05rem;
            font-weight: 600;
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
        }

        .erp-tab-btn:hover {
            color: var(--primary);
        }

        .erp-tab-btn.active {
            color: var(--primary);
            border-bottom: 3px solid var(--primary);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* ── DASHBOARD TAB STYLES ── */
        .dashboard-metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .dashboard-metric-card {
            background: white;
            border: 1.5px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            transition: var(--transition);
        }

        .dashboard-metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            border-color: #CBD5E1;
        }

        .metric-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .metric-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .metric-value {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .dashboard-details-row {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .dashboard-detail-box {
            background: white;
            border: 1.5px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .top-selling-bar-wrapper {
            margin-bottom: 0.75rem;
        }

        .top-selling-item-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.35rem;
        }

        .top-selling-bar-outer {
            height: 6px;
            background: #F1F5F9;
            border-radius: 3px;
            overflow: hidden;
        }

        .top-selling-bar-inner {
            height: 100%;
            background: var(--primary);
            border-radius: 3px;
            transition: width 0.6s ease-in-out;
        }

        .watchlist-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            border: 1.5px solid var(--border-color);
            border-radius: 0.5rem;
            background: #FAFBFD;
        }

        .watchlist-item.critical {
            border-color: #FCA5A5;
            background: #FFF5F5;
        }

        @media (max-width: 900px) {
            .dashboard-details-row {
                grid-template-columns: 1fr;
            }
        }

        /* ── POS SYSTEM STYLES ── */
        .pos-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 1.5rem;
            align-items: start;
        }

        .pos-search-box {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .pos-search-box input {
            width: 100%;
            padding: 0.85rem 1rem 0.85rem 2.75rem;
            border: 1.5px solid var(--border-color);
            border-radius: 0.75rem;
            font-size: 0.95rem;
            background: white;
            outline: none;
            transition: var(--transition);
        }

        .pos-search-box input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .pos-search-box svg {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
        }

        .pos-product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 0.75rem;
            max-height: 520px;
            overflow-y: auto;
            padding-right: 0.25rem;
        }

        .pos-product-grid::-webkit-scrollbar { width: 6px; }
        .pos-product-grid::-webkit-scrollbar-track { background: #F1F5F9; border-radius: 3px; }
        .pos-product-grid::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 3px; }

        .pos-product-card {
            background: white;
            border: 1.5px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .pos-product-card:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.1);
            transform: translateY(-2px);
        }

        .pos-product-card.out-of-stock {
            opacity: 0.45;
            pointer-events: none;
            background: #F8FAFC;
        }

        .pos-prod-name {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.3;
        }

        .pos-prod-generic {
            font-size: 0.72rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .pos-prod-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.35rem;
        }

        .pos-prod-price {
            font-size: 1rem;
            font-weight: 800;
            color: var(--primary);
        }

        .pos-prod-stock {
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.2rem 0.5rem;
            border-radius: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .pos-prod-stock.in { background: #D1FAE5; color: #065F46; }
        .pos-prod-stock.low { background: #FEF3C7; color: #92400E; }
        .pos-prod-stock.out { background: #FEE2E2; color: #991B1B; }

        /* Cart Panel */
        .pos-cart-panel {
            background: white;
            border: 1.5px solid var(--border-color);
            border-radius: 0.75rem;
            display: flex;
            flex-direction: column;
            max-height: 620px;
            position: sticky;
            top: 100px;
        }

        .pos-cart-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pos-cart-header h3 {
            font-size: 1rem;
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pos-cart-count {
            background: var(--primary);
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.15rem 0.5rem;
            border-radius: 1rem;
        }

        .pos-cart-clear {
            background: none;
            border: none;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--danger);
            cursor: pointer;
            transition: var(--transition);
        }

        .pos-cart-clear:hover { opacity: 0.7; }

        .pos-cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 0.5rem 0;
        }

        .pos-cart-items::-webkit-scrollbar { width: 4px; }
        .pos-cart-items::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 2px; }

        .pos-cart-empty {
            text-align: center;
            padding: 2.5rem 1rem;
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .pos-cart-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.65rem 1.25rem;
            border-bottom: 1px solid #F1F5F9;
            transition: background 0.15s;
        }

        .pos-cart-item:hover { background: #F8FAFC; }

        .pos-cart-item-info {
            flex: 1;
            min-width: 0;
        }

        .pos-cart-item-name {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pos-cart-item-price {
            font-size: 0.72rem;
            color: var(--text-muted);
        }

        .pos-cart-qty-controls {
            display: flex;
            align-items: center;
            gap: 0.1rem;
            flex-shrink: 0;
        }

        .pos-cart-qty-btn {
            width: 26px;
            height: 26px;
            border: 1px solid var(--border-color);
            background: white;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            transition: var(--transition);
        }

        .pos-cart-qty-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-light);
        }

        .pos-cart-qty-input {
            width: 38px;
            text-align: center;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 0.2rem;
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--text-main);
            outline: none;
            -moz-appearance: textfield; /* Firefox */
        }

        /* Chrome, Safari, Edge, Opera */
        .pos-cart-qty-input::-webkit-outer-spin-button,
        .pos-cart-qty-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .pos-cart-qty-input:focus {
            border-color: var(--primary);
        }

        .pos-cart-item-subtotal {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-main);
            min-width: 60px;
            text-align: right;
            flex-shrink: 0;
        }

        .pos-cart-item-remove {
            background: none;
            border: none;
            color: #CBD5E1;
            cursor: pointer;
            font-size: 1.1rem;
            line-height: 1;
            padding: 0;
            transition: var(--transition);
            flex-shrink: 0;
        }

        .pos-cart-item-remove:hover { color: var(--danger); }

        /* Checkout Footer */
        .pos-checkout {
            border-top: 2px solid var(--border-color);
            padding: 1rem 1.25rem;
            background: #FAFBFD;
            border-radius: 0 0 0.75rem 0.75rem;
        }

        .pos-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .pos-total-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .pos-total-value {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .pos-change-value {
            font-size: 1rem;
            font-weight: 700;
            color: var(--success);
        }

        .pos-pay-input {
            width: 100%;
            padding: 0.7rem 0.85rem 0.7rem 1.75rem;
            border: 1.5px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 700;
            outline: none;
            transition: var(--transition);
            margin-bottom: 0.75rem;
            position: relative;
        }

        .pos-pay-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .pos-pay-group {
            position: relative;
            margin: 0.75rem 0;
        }

        .pos-pay-group .peso-sign {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            font-weight: 700;
            color: var(--text-muted);
            font-size: 0.95rem;
            pointer-events: none;
        }

        .pos-complete-btn {
            width: 100%;
            padding: 0.8rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .pos-complete-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
        }

        .pos-complete-btn:disabled {
            background: #CBD5E1;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Sales History */
        .pos-history-section {
            margin-top: 1.5rem;
        }

        .pos-history-toggle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: none;
            border: none;
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.5rem 0;
            transition: var(--transition);
            width: 100%;
        }

        .pos-history-toggle:hover { color: var(--primary); }

        .pos-history-list {
            display: none;
            margin-top: 0.75rem;
        }

        .pos-history-list.open { display: block; }

        .pos-sale-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.85rem 1rem;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            transition: var(--transition);
        }

        .pos-sale-row:hover {
            border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.06);
        }

        .pos-sale-receipt {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--primary);
            font-family: monospace;
            min-width: 160px;
        }

        .pos-sale-items {
            flex: 1;
            font-size: 0.78rem;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pos-sale-total {
            font-size: 0.9rem;
            font-weight: 800;
            color: var(--text-main);
            min-width: 80px;
            text-align: right;
        }

        .pos-sale-time {
            font-size: 0.72rem;
            color: var(--text-muted);
            min-width: 110px;
            text-align: right;
        }

        @media (max-width: 900px) {
            .pos-layout {
                grid-template-columns: 1fr;
            }
            .pos-cart-panel {
                position: static;
            }
        }
        /* Modal Styles */
        .fda-modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            align-items: flex-start;
            justify-content: center;
            padding: 4rem 1.5rem 1.5rem 1.5rem;
        }

        .fda-modal-content {
            background: white;
            border-radius: 1.25rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid #E2E8F0;
            max-width: 600px;
            width: 100%;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: fadeUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .fda-modal-header {
            padding: 1.5rem 1.75rem;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            background: #FAFBFD;
        }

        .fda-modal-body {
            padding: 1.5rem;
            overflow-y: auto;
            flex: 1;
            background: #F8FAFC;
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="brand" style="display:flex; align-items:center; gap:0.35rem;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <span><span style="color: var(--primary);">Pharma</span><span style="color: black;">Sync</span></span>
            </a>
            <div class="nav-links">
                <span onclick="openEditProfileModal()" class="portal-badge"
                    style="background: #EFF6FF; color: var(--primary); font-weight: 700; font-size: 0.8rem; padding: 0.4rem 1rem; border-radius: 2rem; text-transform: uppercase; display: inline-flex; align-items: center; margin-right: 0.75rem; letter-spacing: 0.05em; cursor: pointer; transition: all 0.2s ease;">PORTAL:
                    <span id="portalUserName"><?php echo htmlspecialchars($pharmaName); ?></span></span>
                <a href="logout.php" class="btn-switch-role">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Edit Profile Modal -->
    <div id="editProfileModal" class="fda-modal">
        <div class="fda-modal-content" style="max-width: 500px;">
            <div class="fda-modal-header">
                <div>
                    <h3 style="margin:0; font-weight:800; color:var(--text-main); font-size: 1.4rem;">
                        Edit Profile
                    </h3>
                    <p style="color: var(--primary); font-weight: 600; font-size: 0.9rem; margin: 0.25rem 0 0 0;">Update your pharmacy details</p>
                </div>
                <button onclick="closeEditProfileModal()" style="background:none; border:none; font-size:1.75rem; color:#94A3B8; cursor:pointer; line-height:1;">&times;</button>
            </div>
            <div class="fda-modal-body">
                <form id="editProfileForm" onsubmit="submitEditProfile(event)">
                        <div id="editProfileAlert" class="alert-banner" style="display:none; margin-bottom:1rem;"></div>
                        
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label class="form-label">Pharmacy Name</label>
                            <input type="text" id="editName" name="name" class="form-input" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label class="form-label">Username</label>
                            <input type="text" id="editUsername" name="username" class="form-input" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label class="form-label">Address</label>
                            <input type="text" id="editAddress" name="address" class="form-input" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label class="form-label">Contact Number</label>
                            <input type="text" id="editContact" name="contact_number" class="form-input">
                        </div>

                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label class="form-label">Email</label>
                            <input type="email" id="editEmail" name="email" class="form-input">
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 1.5rem;">
                            <label class="form-label">New Password <span style="color:#94A3B8; font-size:0.75rem; font-weight:normal;">(Leave blank to keep current)</span></label>
                            <input type="password" name="password" class="form-input" minlength="6" placeholder="Enter new password">
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <main class="container animate-fade">
        <div id="syncBanner" class="alert-banner animate-fade">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span id="syncBannerText">Inventory Synchronized!</span>
            </div>
            <button onclick="document.getElementById('syncBanner').style.display='none'"
                style="background:none;border:none;cursor:pointer;font-weight:bold;color:#065F46;">✕</button>
        </div>

        <div
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                    <?php echo $pharmaName; ?> ERP
                </h1>
            </div>
            <div>
                <button id="toggleStatusBtn" onclick="toggleStoreStatus()" class="btn btn-outline">Store Status:
                    Loading...</button>
            </div>
        </div>

        <!-- ERP Tabs Navigation -->
        <div class="erp-tabs">
            <button class="erp-tab-btn active" onclick="switchTab('dashboard', event)">Dashboard</button>
            <button class="erp-tab-btn" onclick="switchTab('pos', event)">Point of Sale</button>
            <button class="erp-tab-btn" onclick="switchTab('medicines', event)">Branded Products</button>
            <button class="erp-tab-btn" onclick="switchTab('brands', event)">Generics Dictionary</button>
            <button class="erp-tab-btn" onclick="switchTab('categories', event)">Categories</button>
            <button class="erp-tab-btn" onclick="switchTab('logs', event)">History / Logs</button>
        </div>

        <!-- ================= TAB: DASHBOARD ================= -->
        <div id="tab_dashboard" class="tab-content active animate-fade">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap;">
                <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin: 0;">Performance Overview</h2>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Period:</label>
                    <select id="dashPeriodSelect" onchange="loadDashboardMetrics(this.value)" style="padding: 0.5rem 1.5rem 0.5rem 0.75rem; border: 1.5px solid var(--border-color); border-radius: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-main); outline: none; background: white; cursor: pointer; min-width: 140px;">
                        <option value="today" selected>Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="weekly">This Week</option>
                        <option value="monthly">This Month</option>
                        <option value="yearly">This Year</option>
                    </select>
                </div>
            </div>
            <!-- Analytics Cards Grid -->
            <div class="dashboard-metrics-grid">
                <!-- Revenue Card -->
                <div class="dashboard-metric-card">
                    <div class="metric-icon-wrapper" style="background: #EFF6FF; color: var(--primary);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="metric-label" id="lbl_revenue">Today's Revenue</div>
                        <div class="metric-value" id="metric_revenue">₱0.00</div>
                    </div>
                </div>

                <!-- Transactions Card -->
                <div class="dashboard-metric-card">
                    <div class="metric-icon-wrapper" style="background: #ECFDF5; color: #10B981;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="metric-label" id="lbl_sales_count">Today's Sales</div>
                        <div class="metric-value" id="metric_sales_count">0 Txns</div>
                    </div>
                </div>

                <!-- Registered SKUs Card -->
                <div class="dashboard-metric-card">
                    <div class="metric-icon-wrapper" style="background: #F5F3FF; color: #8B5CF6;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="metric-label">Active SKUs</div>
                        <div class="metric-value" id="metric_total_skus">0</div>
                    </div>
                </div>

                <!-- Stock Alert Card -->
                <div class="dashboard-metric-card">
                    <div class="metric-icon-wrapper" id="stock_alert_icon_wrapper" style="background: #FEF2F2; color: #EF4444;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    <div>
                        <div class="metric-label">Stock Warnings</div>
                        <div class="metric-value" id="metric_stock_warnings" style="font-size: 0.95rem; font-weight: 700; color: #EF4444;">
                            <span id="out_of_stock_badge">0 Out</span> / <span id="low_stock_badge">0 Low</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two-Column Layout for lists -->
            <div class="dashboard-details-row">
                <!-- Left Column: Recent Sales Activity -->
                <div class="dashboard-detail-box" style="display: flex; flex-direction: column;">
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text-main);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                        Recent POS Transactions
                    </h3>
                    <div class="table-container" style="border: 1px solid var(--border-color); flex: 1; min-height: 350px; overflow-y: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 25%;">Time</th>
                                    <th style="width: 30%;">Receipt No</th>
                                    <th style="width: 20%;">Total</th>
                                    <th style="width: 25%;">Items</th>
                                </tr>
                            </thead>
                            <tbody id="dashRecentSalesBody">
                                <tr>
                                    <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2rem;">Loading transactions...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right Column: Top Products & Stock Watchlist -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Top Selling Products -->
                    <div class="dashboard-detail-box" style="flex: 1;">
                        <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text-main);">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                            Top Selling Products
                        </h3>
                        <div id="dashTopProductsList" style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <div style="text-align: center; color: var(--text-muted); padding: 1rem;">Loading...</div>
                        </div>
                    </div>

                    <!-- Low Stock watchlist -->
                    <div class="dashboard-detail-box" style="flex: 1;">
                        <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text-main);">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            Stock Watchlist (Low Stock)
                        </h3>
                        <div id="dashStockWatchlist" style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <div style="text-align: center; color: var(--text-muted); padding: 1rem;">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= TAB 0: POINT OF SALE ================= -->
        <div id="tab_pos" class="tab-content animate-fade">
            <div class="pos-layout">
                <!-- Left: Product Grid -->
                <div>
                    <div class="pos-search-box">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <input type="text" id="posSearchInput" placeholder="Search products..." oninput="filterPosProducts()">
                    </div>
                    <div class="pos-product-grid" id="posProductGrid">
                        <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: var(--text-muted);">
                            Loading products...
                        </div>
                    </div>
                </div>

                <!-- Right: Cart Panel -->
                <div class="pos-cart-panel">
                    <div class="pos-cart-header">
                        <h3>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            Cart
                            <span class="pos-cart-count" id="posCartCount">0</span>
                        </h3>
                        <button class="pos-cart-clear" onclick="clearPosCart()">Clear All</button>
                    </div>
                    <div class="pos-cart-items" id="posCartItems">
                        <div class="pos-cart-empty">Click products to add them to your cart</div>
                    </div>
                    <div class="pos-checkout">
                        <div class="pos-total-row">
                            <span class="pos-total-label">Subtotal</span>
                            <span class="pos-total-value" id="posSubtotal">₱0.00</span>
                        </div>
                        <div class="pos-pay-group">
                            <span class="peso-sign">₱</span>
                            <input type="number" class="pos-pay-input" id="posAmountPaid" placeholder="Amount paid" min="0" step="0.01" oninput="updatePosChange()">
                        </div>
                        <div class="pos-total-row">
                            <span class="pos-total-label">Change</span>
                            <span class="pos-change-value" id="posChange">₱0.00</span>
                        </div>
                        <button class="pos-complete-btn" id="posCompleteBtn" disabled onclick="completeSale()">Complete Sale</button>
                    </div>
                </div>
            </div>

            <!-- Sales History -->
            <div class="pos-history-section">
                <button class="pos-history-toggle" onclick="toggleSalesHistory()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    Recent Sales History
                    <svg id="posHistoryChevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div class="pos-history-list" id="posHistoryList">
                    <div style="text-align:center; padding:1.5rem; color:var(--text-muted); font-size:0.85rem;">Loading sales history...</div>
                </div>
            </div>
        </div>

        <!-- ================= TAB 1: PRODUCTS SKU ================= -->
        <div id="tab_medicines" class="tab-content animate-fade">
            <div style="display: flex; justify-content: flex-start; align-items: center; margin-bottom: 1.25rem; gap: 1rem; flex-wrap: wrap;">
                <div class="pos-search-box" style="margin-bottom: 0; width: 300px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" onkeyup="filterSpecificTable('medTableBody', this.value)" placeholder="Search products...">
                </div>
                <button onclick="openModal('medicineModal')" class="btn btn-primary">Add Product</button>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 25%;">Product SKU</th>
                            <th style="width: 30%;">Classification</th>
                            <th style="width: 30%;">Inventory & Pricing</th>
                            <th style="width: 15%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="medTableBody">
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">Loading
                                products...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================= TAB 2: GENERICS ================= -->
        <div id="tab_brands" class="tab-content animate-fade">
            <div style="display: flex; justify-content: flex-start; align-items: center; margin-bottom: 1.25rem; gap: 1rem; flex-wrap: wrap;">
                <div class="pos-search-box" style="margin-bottom: 0; width: 300px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" onkeyup="filterSpecificTable('brandTableBody', this.value)" placeholder="Search ingredients...">
                </div>
                <button onclick="openModal('brandModal')" class="btn btn-primary">Add Generic</button>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Generic Active Ingredient</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="brandTableBody">
                        <tr>
                            <td colspan="2" style="text-align: center; padding: 2rem; color: var(--text-muted);">Loading
                                generics...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================= TAB 3: CATEGORIES ================= -->
        <!-- ================= TAB 3: CATEGORIES ================= -->
        <div id="tab_categories" class="tab-content animate-fade">
            <div style="display: flex; justify-content: flex-start; align-items: center; margin-bottom: 1.25rem; gap: 1rem; flex-wrap: wrap;">
                <div class="pos-search-box" style="margin-bottom: 0; width: 300px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" onkeyup="filterSpecificTable('catTableBody', this.value)" placeholder="Search categories...">
                </div>
                <button onclick="openModal('categoryModal')" class="btn btn-primary">Add Category</button>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="catTableBody">
                        <tr>
                            <td colspan="2" style="text-align: center; padding: 2rem; color: var(--text-muted);">Loading
                                categories...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================= TAB 4: LOGS ================= -->
        <!-- ================= TAB 4: LOGS ================= -->
        <div id="tab_logs" class="tab-content animate-fade">
            <div style="display: flex; justify-content: flex-start; align-items: center; margin-bottom: 1.25rem; gap: 1rem; flex-wrap: wrap;">
                <div class="pos-search-box" style="margin-bottom: 0; width: 300px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" id="logsSearch" onkeyup="filterSpecificTable('logsTableBody', this.value)" placeholder="Search logs...">
                </div>
                <button onclick="loadLogs()" class="btn btn-outline">Refresh Logs</button>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 180px;">Date & Time</th>
                            <th style="width: 200px;">Action Type</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody id="logsTableBody">
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 2rem; color: var(--text-muted);">Loading logs...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- ================= MODALS ================= -->

    <!-- Add Product SKU Modal -->
    <div id="medicineModal" class="modal-overlay">
        <div class="modal">
            <h2 style="margin-bottom: 1.5rem; font-weight: 700;">Add New Product SKU</h2>
            <form id="medForm" onsubmit="addMedicine(event)">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Brand Name</label>
                        <input type="text" id="medBrandName" class="form-input" placeholder="e.g. Biogesic" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Strength</label>
                        <input type="text" id="medStrength" class="form-input" placeholder="e.g. 500mg" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Active Generic Ingredient</label>
                    <select id="medBrandSelect" class="form-input" style="background: white;" required></select>
                </div>
                <div class="form-group">
                    <label class="form-label">Assign Category</label>
                    <select id="medCatSelect" class="form-input" style="background: white;" required></select>
                </div>
                <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Manufacturer</label>
                        <input type="text" id="medMan" class="form-input" placeholder="e.g. Unilab" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Classification / Type</label>
                        <select id="medRxOtc" class="form-input" style="background: white;" required>
                            <option value="0" selected>Over-the-Counter (OTC)</option>
                            <option value="1">Prescription Required (Rx)</option>
                        </select>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Price (₱)</label>
                        <input type="number" step="0.01" id="medPrice" class="form-input" value="5.00" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Initial Stock</label>
                        <input type="number" id="medStock" class="form-input" value="100" required>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem;">
                    <button type="button" onclick="closeModal('medicineModal')" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save SKU</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Generic Active Ingredient Modal -->
    <div id="brandModal" class="modal-overlay">
        <div class="modal">
            <h2 style="margin-bottom: 1.5rem; font-weight: 700;">Add New Generic Active Ingredient</h2>
            <form id="brandForm" onsubmit="addBrand(event)">
                <div class="form-group">
                    <label class="form-label">Generic Name</label>
                    <input type="text" id="brandNameInput" class="form-input" placeholder="e.g. Paracetamol" required>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem;">
                    <button type="button" onclick="closeModal('brandModal')" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Generic</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div id="categoryModal" class="modal-overlay">
        <div class="modal">
            <h2 style="margin-bottom: 1.5rem; font-weight: 700;">Add New Category</h2>
            <form id="catForm" onsubmit="addCategory(event)">
                <div class="form-group">
                    <label class="form-label">Category Name</label>
                    <input type="text" id="catNameInput" class="form-input" placeholder="e.g. Antibiotics" required>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem;">
                    <button type="button" onclick="closeModal('categoryModal')" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const pharmaCode = "<?php echo $pharma; ?>";
        let currentMedicinesList = [];
        let currentBrandsList = [];
        let currentCategoriesList = [];
        let storeIsOpen = true;

        function switchTab(tabId, e) {
            document.querySelectorAll('.erp-tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            const targetBtn = e ? e.currentTarget : (event ? event.currentTarget || event.target : null);
            if (targetBtn) {
                targetBtn.classList.add('active');
            } else {
                const btn = document.querySelector(`.erp-tab-btn[onclick*="${tabId}"]`);
                if (btn) btn.classList.add('active');
            }

            document.getElementById('tab_' + tabId).classList.add('active');

            if (tabId === 'dashboard') {
                loadDashboardMetrics();
            }
        }

        function filterSpecificTable(tbodyId, query) {
            query = query.toLowerCase();
            const tbody = document.getElementById(tbodyId);
            if (!tbody) return;

            const rows = tbody.querySelectorAll('tr');
            rows.forEach(row => {
                if (row.cells.length === 1 && row.cells[0].colSpan > 1) return;

                let text = '';

                // 1. Gather values from all text inputs
                row.querySelectorAll('input').forEach(input => {
                    if (input.value) {
                        text += ' ' + input.value.toLowerCase();
                    }
                });

                // 2. Gather text only from currently selected options in select dropdowns
                row.querySelectorAll('select').forEach(select => {
                    if (select.selectedIndex >= 0) {
                        const opt = select.options[select.selectedIndex];
                        if (opt) {
                            text += ' ' + opt.text.toLowerCase();
                        }
                    }
                });

                // 3. Fallback for static rows (e.g. initial states or items without inputs/selects)
                if (row.querySelectorAll('input, select').length === 0) {
                    text = row.textContent.toLowerCase();
                }

                row.style.display = text.includes(query) ? '' : 'none';
            });
        }

        function loadData() {
            fetch(`api/inventory.php?action=get&pharma=${pharmaCode}`)
                .then(res => res.json())
                .then(data => {
                    currentMedicinesList = data.data; // Products list
                    currentBrandsList = data.brands;   // Generic active ingredients
                    currentCategoriesList = data.categories; // Categories

                    const statusBtn = document.getElementById('toggleStatusBtn');
                    storeIsOpen = (data.is_open == 1);
                    if (storeIsOpen) {
                        statusBtn.innerHTML = `Store Status: <span style="color:#10B981;font-weight:bold;">OPEN</span>`;
                    } else {
                        statusBtn.innerHTML = `Store Status: <span style="color:#EF4444;font-weight:bold;">CLOSED</span>`;
                    }

                    // Populate Dropdowns
                    const medBrandSel = document.getElementById('medBrandSelect');
                    const brandCatSel = document.getElementById('medCatSelect');
                    medBrandSel.innerHTML = '<option value="">-- Select Active Ingredient --</option>';
                    brandCatSel.innerHTML = '<option value="">-- Select Category --</option>';

                    currentBrandsList.forEach(b => {
                        const opt = document.createElement('option');
                        opt.value = b.id;
                        opt.textContent = b.name;
                        medBrandSel.appendChild(opt);
                    });
                    currentCategoriesList.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.id;
                        opt.textContent = c.name;
                        brandCatSel.appendChild(opt);
                    });

                    // Populate Table 1: Products
                    const medBody = document.getElementById('medTableBody');
                    medBody.innerHTML = '';
                    if (currentMedicinesList.length === 0) {
                        medBody.innerHTML = `<tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--text-muted);">No products found.</td></tr>`;
                    } else {
                        currentMedicinesList.forEach(med => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>
                                    <div style="display:flex; flex-direction:column; gap:0.4rem;">
                                        <input type="text" value="${med.brand_name}" id="brand_name_${med.id}" style="width:100%;padding:0.35rem;border:1px solid var(--border-color);border-radius:0.25rem;font-weight:600;color:var(--text-main);" placeholder="Brand Name">
                                        <input type="text" value="${med.strength}" id="strength_${med.id}" style="width:100%;padding:0.35rem;border:1px solid var(--border-color);border-radius:0.25rem;font-weight:600;" placeholder="Strength (e.g. 500mg)">
                                    </div>
                                </td>
                                <td>
                                    <div style="display:flex; flex-direction:column; gap:0.4rem;">
                                        <select id="med_gen_sel_${med.id}" style="width:100%;padding:0.35rem;border:1px solid var(--border-color);border-radius:0.25rem;font-weight:600;color:var(--text-main);">
                                            ${currentBrandsList.map(b => `<option value="${b.id}" ${b.id == med.medicine_id ? 'selected' : ''}>${b.name}</option>`).join('')}
                                        </select>
                                        <select id="med_cat_sel_${med.id}" style="width:100%;padding:0.35rem;border:1px solid var(--border-color);border-radius:0.25rem;">
                                            ${currentCategoriesList.map(c => `<option value="${c.id}" ${c.id == med.category_id ? 'selected' : ''}>${c.name}</option>`).join('')}
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div style="display:flex; flex-direction:column; gap:0.4rem;">
                                        <div style="display:flex; gap:0.4rem;">
                                            <input type="text" value="${med.manufacturer || ''}" id="manufacturer_${med.id}" style="flex:2;padding:0.35rem;border:1px solid var(--border-color);border-radius:0.25rem;" placeholder="Manufacturer">
                                            <select id="prescription_required_${med.id}" style="flex:1;padding:0.35rem;border:1px solid var(--border-color);border-radius:0.25rem;font-weight:700;color:var(--text-main);background:white;cursor:pointer;">
                                                <option value="0" ${parseInt(med.prescription_required) === 0 ? 'selected' : ''}>OTC</option>
                                                <option value="1" ${parseInt(med.prescription_required) === 1 ? 'selected' : ''}>Rx</option>
                                            </select>
                                        </div>
                                        <div style="display:flex; gap:0.4rem;">
                                            <div style="position:relative; flex:1; display:flex; align-items:center;">
                                                <span style="position:absolute; left:8px; color:var(--text-muted); font-size:0.9rem; font-weight:600;">₱</span>
                                                <input type="number" step="0.01" value="${med.price}" id="price_${med.id}" style="width:100%;padding:0.35rem 0.5rem 0.35rem 1.25rem;border:1px solid var(--border-color);border-radius:0.25rem;font-weight:700;color:var(--primary);" placeholder="Price">
                                            </div>
                                            <input type="number" value="${med.stock}" id="stock_${med.id}" style="flex:1;width:100%;padding:0.35rem 0.5rem;border:1px solid var(--border-color);border-radius:0.25rem;font-weight:600;" placeholder="Stock">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display:flex; flex-direction:column; gap:0.4rem;">
                                        <button onclick="updateMedicine(${med.id})" class="btn btn-secondary" style="width:100%;padding:0.35rem 0.75rem;">Update</button>
                                        <button onclick="deleteMedicine(${med.id})" class="btn btn-danger" style="width:100%;padding:0.35rem 0.75rem;">Delete</button>
                                    </div>
                                </td>
                            `;
                            medBody.appendChild(tr);
                        });
                    }

                    // Populate Table 2: Generics
                    const brandBody = document.getElementById('brandTableBody');
                    brandBody.innerHTML = '';
                    if (currentBrandsList.length === 0) {
                        brandBody.innerHTML = `<tr><td colspan="2" style="text-align:center;padding:2rem;color:var(--text-muted);">No active ingredients found.</td></tr>`;
                    } else {
                        currentBrandsList.forEach(b => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><input type="text" value="${b.name}" id="generic_name_${b.id}" style="width:100%;padding:0.35rem;border:1px solid var(--border-color);border-radius:0.25rem;font-weight:600;"></td>
                                <td style="display:flex;gap:0.5rem;">
                                    <button onclick="updateBrand(${b.id})" class="btn btn-secondary">Update</button>
                                    <button onclick="deleteBrand(${b.id})" class="btn btn-danger">Delete</button>
                                </td>
                            `;
                            brandBody.appendChild(tr);
                        });
                    }

                    // Populate Table 3: Categories
                    const catBody = document.getElementById('catTableBody');
                    catBody.innerHTML = '';
                    if (currentCategoriesList.length === 0) {
                        catBody.innerHTML = `<tr><td colspan="2" style="text-align:center;padding:2rem;color:var(--text-muted);">No categories found.</td></tr>`;
                    } else {
                        currentCategoriesList.forEach(c => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><input type="text" value="${c.name}" id="cat_name_${c.id}" style="width:100%;padding:0.35rem;border:1px solid var(--border-color);border-radius:0.25rem;"></td>
                                <td style="display:flex;gap:0.5rem;">
                                    <button onclick="updateCategory(${c.id})" class="btn btn-secondary">Update</button>
                                    <button onclick="deleteCategory(${c.id})" class="btn btn-danger">Delete</button>
                                </td>
                            `;
                            catBody.appendChild(tr);
                        });
                    }
                    loadDashboardMetrics();
                });
        }

        function loadDashboardMetrics(period) {
            if (!period) {
                const periodSelect = document.getElementById('dashPeriodSelect');
                period = periodSelect ? periodSelect.value : 'today';
            }
            
            fetch(`api/pharmacy_metrics.php?pharma=${pharmaCode}&period=${period}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        const metrics = data.metrics;
                        
                        // Update labels based on period selection
                        let periodLabel = "Today's";
                        if (period === 'yesterday') periodLabel = "Yesterday's";
                        else if (period === 'weekly') periodLabel = "Weekly";
                        else if (period === 'monthly') periodLabel = "Monthly";
                        else if (period === 'yearly') periodLabel = "Yearly";
                        
                        const lblRev = document.getElementById('lbl_revenue');
                        if (lblRev) lblRev.textContent = periodLabel + " Revenue";
                        
                        const lblSales = document.getElementById('lbl_sales_count');
                        if (lblSales) lblSales.textContent = periodLabel + " Sales";
                        
                        // 1. Update metric cards
                        document.getElementById('metric_revenue').textContent = '₱' + parseFloat(metrics.revenue_today).toFixed(2);
                        document.getElementById('metric_sales_count').textContent = metrics.txns_today + ' Txn' + (metrics.txns_today !== 1 ? 's' : '');
                        document.getElementById('metric_total_skus').textContent = metrics.total_skus;
                        
                        document.getElementById('out_of_stock_badge').textContent = metrics.out_of_stock + ' Out';
                        document.getElementById('low_stock_badge').textContent = metrics.low_stock + ' Low';
                        
                        const alertWrapper = document.getElementById('stock_alert_icon_wrapper');
                        const stockWarningsVal = document.getElementById('metric_stock_warnings');
                        if (metrics.out_of_stock > 0) {
                            alertWrapper.style.background = '#FEE2E2';
                            alertWrapper.style.color = '#EF4444';
                            stockWarningsVal.style.color = '#EF4444';
                        } else if (metrics.low_stock > 0) {
                            alertWrapper.style.background = '#FEF3C7';
                            alertWrapper.style.color = '#F59E0B';
                            stockWarningsVal.style.color = '#D97706';
                        } else {
                            alertWrapper.style.background = '#D1FAE5';
                            alertWrapper.style.color = '#10B981';
                            stockWarningsVal.style.color = '#059669';
                        }

                        // 2. Render Recent Sales Table
                        const salesBody = document.getElementById('dashRecentSalesBody');
                        if (data.recent_sales.length === 0) {
                            salesBody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2rem;">No transactions recorded today.</td></tr>';
                        } else {
                            salesBody.innerHTML = data.recent_sales.map(sale => {
                                const date = new Date(sale.created_at);
                                const timeStr = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) + ' | ' + date.toLocaleDateString([], { month: 'short', day: 'numeric' });
                                return `
                                    <tr>
                                        <td style="font-size:0.82rem; color:var(--text-muted);">${timeStr}</td>
                                        <td style="font-family: monospace; font-size:0.82rem; font-weight:700; color:var(--primary);">${sale.receipt_no}</td>
                                        <td style="font-weight:700;">₱${parseFloat(sale.total_amount).toFixed(2)}</td>
                                        <td style="font-size:0.82rem; color:var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;" title="${sale.items_summary || ''}">${sale.items_summary || 'No details'}</td>
                                    </tr>
                                `;
                            }).join('');
                        }

                        // 3. Render Top Selling Products list
                        const topProdContainer = document.getElementById('dashTopProductsList');
                        if (data.top_selling.length === 0) {
                            topProdContainer.innerHTML = '<div style="text-align: center; color: var(--text-muted); padding: 1.5rem; font-size: 0.85rem;">No sales recorded yet.</div>';
                        } else {
                            const maxQty = data.top_selling.reduce((max, item) => Math.max(max, parseInt(item.total_qty)), 1);
                            
                            topProdContainer.innerHTML = data.top_selling.map(item => {
                                const qty = parseInt(item.total_qty);
                                const pct = Math.round((qty / maxQty) * 100);
                                const revenue = parseFloat(item.total_revenue).toFixed(2);
                                return `
                                    <div class="top-selling-bar-wrapper">
                                        <div class="top-selling-item-header">
                                            <span>${item.product_name}</span>
                                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500;">
                                                <strong>${qty} sold</strong> (₱${revenue})
                                            </span>
                                        </div>
                                        <div class="top-selling-bar-outer">
                                            <div class="top-selling-bar-inner" style="width: ${pct}%;"></div>
                                        </div>
                                    </div>
                                `;
                            }).join('');
                        }

                        // 4. Render Stock Watchlist
                        const watchlistContainer = document.getElementById('dashStockWatchlist');
                        if (data.low_stock_list.length === 0) {
                            watchlistContainer.innerHTML = '<div style="text-align: center; color: var(--success); font-weight: 600; padding: 1.5rem; font-size: 0.85rem; background: #D1FAE5; border-radius: 0.5rem;">All products are well stocked!</div>';
                        } else {
                            watchlistContainer.innerHTML = data.low_stock_list.map(item => {
                                const stock = parseInt(item.stock);
                                const isCritical = stock === 0;
                                return `
                                    <div class="watchlist-item ${isCritical ? 'critical' : ''}">
                                        <div style="display:flex; flex-direction:column; gap:0.15rem;">
                                            <strong style="font-size: 0.85rem; color: var(--text-main);">${item.brand_name} ${item.strength}</strong>
                                            <span style="font-size: 0.72rem; color: var(--text-muted);">${item.generic_name}</span>
                                            <span style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: ${isCritical ? 'var(--danger)' : (item.popularity === 'fast' ? '#3B82F6' : (item.popularity === 'normal' ? 'var(--text-muted)' : '#D97706'))}; margin-top: 0.15rem;">
                                                ${isCritical ? 'Out of Stock' : (item.popularity === 'fast' ? '⚡ Fast-Moving (Threshold: 30)' : (item.popularity === 'normal' ? '📦 Normal (Threshold: 10)' : '🐌 Slow-Moving (Threshold: 3)'))}
                                            </span>
                                        </div>
                                        <span class="badge ${isCritical ? 'badge-danger' : 'badge-warning'}" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                                            ${stock} left
                                        </span>
                                    </div>
                                `;
                            }).join('');
                        }
                    }
                })
                .catch(err => {
                    console.error("Dashboard metrics failed to load:", err);
                });
        }

        function toggleStoreStatus() {
            const statusText = storeIsOpen ? 'CLOSE' : 'OPEN';
            if (confirm(`Are you sure you want to change the store status to ${statusText}?`)) {
                fetch(`api/inventory.php?action=toggle_status&pharma=${pharmaCode}`)
                    .then(res => res.json())
                    .then(() => loadData());
            }
        }

        // --- PRODUCT ACTIONS ---
        function updateMedicine(id) {
            const brand_name = document.getElementById(`brand_name_${id}`).value.trim();
            const strength = document.getElementById(`strength_${id}`).value.trim();
            const medicine_id = document.getElementById(`med_gen_sel_${id}`).value;
            const category_id = document.getElementById(`med_cat_sel_${id}`).value;
            const manufacturer = document.getElementById(`manufacturer_${id}`).value.trim();
            const price = document.getElementById(`price_${id}`).value;
            const stock = document.getElementById(`stock_${id}`).value;
            const prescription_required = parseInt(document.getElementById(`prescription_required_${id}`).value);

            // 1. Client-side Input Validations
            if (!brand_name) {
                alert("Error: Brand Name cannot be empty.");
                return;
            }
            if (!strength) {
                alert("Error: Strength cannot be empty.");
                return;
            }
            if (!medicine_id) {
                alert("Error: Active Ingredient must be selected.");
                return;
            }
            if (!category_id) {
                alert("Error: Category must be selected.");
                return;
            }
            if (price === "" || parseFloat(price) < 0) {
                alert("Error: Price must be a positive number.");
                return;
            }
            if (stock === "" || parseInt(stock) < 0) {
                alert("Error: Stock cannot be negative.");
                return;
            }

            // 2. Unchanged Verification
            const original = currentMedicinesList.find(m => m.id == id);
            if (original) {
                const isUnchanged = 
                    original.brand_name === brand_name &&
                    original.strength === strength &&
                    original.medicine_id == medicine_id &&
                    original.category_id == category_id &&
                    original.manufacturer === manufacturer &&
                    parseFloat(original.price) === parseFloat(price) &&
                    parseInt(original.stock) === parseInt(stock) &&
                    parseInt(original.prescription_required) === prescription_required;
                
                if (isUnchanged) {
                    alert("No changes detected. The product details are already up-to-date.");
                    return;
                }
            }

            // 3. API Execution with Error Handling
            fetch(`api/inventory.php?action=update&pharma=${pharmaCode}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, brand_name, strength, medicine_id, category_id, price, stock, manufacturer, prescription_required })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        alert("Error updating product: " + data.message);
                        return;
                    }
                    
                    // Sync to admin console with custom update action details
                    fetch('api/sync.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ 
                            pharmacy_code: pharmaCode, 
                            custom_action: 'PRODUCT_UPDATE',
                            custom_message: `System received an update from ${pharmaCode === 'laurents' ? "Laurent's Pharmacy" : pharmaCode === 'jrm' ? "JRM DOCTORS Pharmacy" : "D' Rite Aid Generics Pharmacy"}: Product SKU details for '${brand_name} ${strength}' were updated. Current stock is at ${stock} units, priced at ₱${price}.`
                        })
                    })
                        .then(() => {
                            document.getElementById('syncBanner').style.display = 'flex';
                            alert("Product SKU details updated successfully!");
                            loadData();
                        })
                        .catch(err => {
                            console.error("Admin sync failed:", err);
                            loadData();
                        });
                })
                .catch(err => {
                    alert("A connection error occurred while updating the product SKU.");
                });
        }
        function deleteMedicine(id) {
            if (!confirm("Delete product SKU?")) return;
            const original = currentMedicinesList.find(m => m.id == id);
            const medName = original ? `${original.brand_name} ${original.strength}` : `SKU #${id}`;

            fetch(`api/inventory.php?action=delete&pharma=${pharmaCode}`, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id }) 
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'error') {
                    alert("Error deleting product SKU: " + data.message);
                } else {
                    // Sync delete event to admin
                    fetch('api/sync.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            pharmacy_code: pharmaCode,
                            custom_action: 'PRODUCT_DELETE',
                            custom_message: `System received an update from ${pharmaCode === 'laurents' ? "Laurent's Pharmacy" : pharmaCode === 'jrm' ? "JRM DOCTORS Pharmacy" : "D' Rite Aid Generics Pharmacy"}: Product SKU '${medName}' was removed from the inventory.`
                        })
                    }).finally(() => {
                        alert("Product SKU deleted successfully!");
                        loadData();
                    });
                }
            })
            .catch(err => {
                alert("A connection error occurred while deleting the product SKU.");
            });
        }
        function addMedicine(e) {
            e.preventDefault();
            const brand_name = document.getElementById('medBrandName').value.trim();
            const strength = document.getElementById('medStrength').value.trim();
            const medicine_id = document.getElementById('medBrandSelect').value;
            const category_id = document.getElementById('medCatSelect').value;
            const manufacturer = document.getElementById('medMan').value.trim();
            const price = document.getElementById('medPrice').value;
            const stock = document.getElementById('medStock').value;
            const prescription_required = parseInt(document.getElementById('medRxOtc').value);

            if (!brand_name) {
                alert("Error: Brand Name is required.");
                return;
            }
            if (!strength) {
                alert("Error: Strength is required.");
                return;
            }
            if (!medicine_id) {
                alert("Error: Active Ingredient must be selected.");
                return;
            }
            if (!category_id) {
                alert("Error: Category must be selected.");
                return;
            }
            if (price === "" || parseFloat(price) < 0) {
                alert("Error: Price must be a positive number.");
                return;
            }
            if (stock === "" || parseInt(stock) < 0) {
                alert("Error: Stock cannot be negative.");
                return;
            }

            fetch(`api/inventory.php?action=add&pharma=${pharmaCode}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ brand_name, strength, medicine_id, category_id, price, stock, manufacturer, prescription_required })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        alert("Error adding product SKU: " + data.message);
                    } else {
                        // Sync add event to admin
                        fetch('api/sync.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                pharmacy_code: pharmaCode,
                                custom_action: 'PRODUCT_ADD',
                                custom_message: `System received an update from ${pharmaCode === 'laurents' ? "Laurent's Pharmacy" : pharmaCode === 'jrm' ? "JRM DOCTORS Pharmacy" : "D' Rite Aid Generics Pharmacy"}: New product SKU '${brand_name} ${strength}' added with ${stock} units in stock, priced at ₱${price}.`
                            })
                        }).finally(() => {
                            alert("Product SKU added successfully!");
                            closeModal('medicineModal');
                            document.getElementById('medForm').reset();
                            loadData();
                        });
                    }
                })
                .catch(err => {
                    alert("A connection error occurred while creating the product SKU.");
                });
        }

        // --- GENERIC ACTIONS ---
        function updateBrand(id) {
            const brand_name = document.getElementById(`generic_name_${id}`).value.trim();
            
            if (!brand_name) {
                alert("Error: Generic active ingredient name cannot be empty.");
                return;
            }

            const original = currentBrandsList.find(b => b.id == id);
            if (original && original.name === brand_name) {
                alert("No changes detected. The active ingredient name is already up-to-date.");
                return;
            }
            const oldName = original ? original.name : `Ingredient #${id}`;

            fetch(`api/inventory.php?action=update_brand&pharma=${pharmaCode}`, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, brand_name }) 
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        alert("Error updating active ingredient: " + data.message);
                    } else {
                        // Sync generic rename event to admin
                        fetch('api/sync.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                pharmacy_code: pharmaCode,
                                custom_action: 'GENERIC_UPDATE',
                                custom_message: `System received an update from ${pharmaCode === 'laurents' ? "Laurent's Pharmacy" : pharmaCode === 'jrm' ? "JRM DOCTORS Pharmacy" : "D' Rite Aid Generics Pharmacy"}: Generic active ingredient renamed from '${oldName}' to '${brand_name}'.`
                            })
                        }).finally(() => {
                            alert("Generic active ingredient updated successfully!");
                            loadData();
                        });
                    }
                })
                .catch(err => {
                    alert("A connection error occurred while updating the active ingredient.");
                });
        }
        function deleteBrand(id) {
            if (!confirm("Delete generic drug? All linked product SKUs will be deleted as well.")) return;
            const original = currentBrandsList.find(b => b.id == id);
            const brandName = original ? original.name : `Ingredient #${id}`;

            fetch(`api/inventory.php?action=delete_brand&pharma=${pharmaCode}`, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id }) 
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        alert("Error deleting generic drug: " + data.message);
                    } else {
                        // Sync generic delete event to admin
                        fetch('api/sync.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                pharmacy_code: pharmaCode,
                                custom_action: 'GENERIC_DELETE',
                                custom_message: `System received an update from ${pharmaCode === 'laurents' ? "Laurent's Pharmacy" : pharmaCode === 'jrm' ? "JRM DOCTORS Pharmacy" : "D' Rite Aid Generics Pharmacy"}: Generic active ingredient '${brandName}' was removed from the dictionary.`
                            })
                        }).finally(() => {
                            alert("Generic drug deleted successfully!");
                            loadData();
                        });
                    }
                })
                .catch(err => {
                    alert("A connection error occurred while deleting the generic drug.");
                });
        }
        function addBrand(e) {
            e.preventDefault();
            const brand_name = document.getElementById('brandNameInput').value.trim();
            if (!brand_name) {
                alert("Error: Generic active ingredient name cannot be empty.");
                return;
            }

            fetch(`api/inventory.php?action=add_brand&pharma=${pharmaCode}`, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ brand_name }) 
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        alert("Error adding generic active ingredient: " + data.message);
                    } else {
                        // Sync generic add event to admin
                        fetch('api/sync.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                pharmacy_code: pharmaCode,
                                custom_action: 'GENERIC_ADD',
                                custom_message: `System received an update from ${pharmaCode === 'laurents' ? "Laurent's Pharmacy" : pharmaCode === 'jrm' ? "JRM DOCTORS Pharmacy" : "D' Rite Aid Generics Pharmacy"}: New generic active ingredient '${brand_name}' added to dictionary.`
                            })
                        }).finally(() => {
                            alert("Generic active ingredient added successfully!");
                            closeModal('brandModal');
                            document.getElementById('brandForm').reset();
                            loadData();
                        });
                    }
                })
                .catch(err => {
                    alert("A connection error occurred while adding the active ingredient.");
                });
        }

        // --- CATEGORY ACTIONS ---
        function updateCategory(id) {
            const category_name = document.getElementById(`cat_name_${id}`).value.trim();
            
            if (!category_name) {
                alert("Error: Category name cannot be empty.");
                return;
            }

            const original = currentCategoriesList.find(c => c.id == id);
            if (original && original.name === category_name) {
                alert("No changes detected. The category name is already up-to-date.");
                return;
            }
            const oldName = original ? original.name : `Category #${id}`;

            fetch(`api/inventory.php?action=update_category&pharma=${pharmaCode}`, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, category_name }) 
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        alert("Error updating category: " + data.message);
                    } else {
                        // Sync category update to admin
                        fetch('api/sync.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                pharmacy_code: pharmaCode,
                                custom_action: 'CATEGORY_UPDATE',
                                custom_message: `System received an update from ${pharmaCode === 'laurents' ? "Laurent's Pharmacy" : pharmaCode === 'jrm' ? "JRM DOCTORS Pharmacy" : "D' Rite Aid Generics Pharmacy"}: Inventory category renamed from '${oldName}' to '${category_name}'.`
                            })
                        }).finally(() => {
                            alert("Category updated successfully!");
                            loadData();
                        });
                    }
                })
                .catch(err => {
                    alert("A connection error occurred while updating the category.");
                });
        }
        function deleteCategory(id) {
            if (!confirm("Delete category?")) return;
            const original = currentCategoriesList.find(c => c.id == id);
            const catName = original ? original.name : `Category #${id}`;

            fetch(`api/inventory.php?action=delete_category&pharma=${pharmaCode}`, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id }) 
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        alert("Error deleting category: " + data.message);
                    } else {
                        // Sync category delete to admin
                        fetch('api/sync.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                pharmacy_code: pharmaCode,
                                custom_action: 'CATEGORY_DELETE',
                                custom_message: `System received an update from ${pharmaCode === 'laurents' ? "Laurent's Pharmacy" : pharmaCode === 'jrm' ? "JRM DOCTORS Pharmacy" : "D' Rite Aid Generics Pharmacy"}: Inventory category '${catName}' was removed.`
                            })
                        }).finally(() => {
                            alert("Category deleted successfully!");
                            loadData();
                        });
                    }
                })
                .catch(err => {
                    alert("A connection error occurred while deleting the category.");
                });
        }
        function addCategory(e) {
            e.preventDefault();
            const category_name = document.getElementById('catNameInput').value.trim();
            if (!category_name) {
                alert("Error: Category name cannot be empty.");
                return;
            }

            fetch(`api/inventory.php?action=add_category&pharma=${pharmaCode}`, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ category_name }) 
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        alert("Error adding category: " + data.message);
                    } else {
                        // Sync category add to admin
                        fetch('api/sync.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                pharmacy_code: pharmaCode,
                                custom_action: 'CATEGORY_ADD',
                                custom_message: `System received an update from ${pharmaCode === 'laurents' ? "Laurent's Pharmacy" : pharmaCode === 'jrm' ? "JRM DOCTORS Pharmacy" : "D' Rite Aid Generics Pharmacy"}: New inventory category '${category_name}' was created.`
                            })
                        }).finally(() => {
                            alert("Category added successfully!");
                            closeModal('categoryModal');
                            document.getElementById('catForm').reset();
                            loadData();
                        });
                    }
                })
                .catch(err => {
                    alert("A connection error occurred while adding the category.");
                });
        }

        function openModal(id) { document.getElementById(id).classList.add('active'); }
        function closeModal(id) { document.getElementById(id).classList.remove('active'); }

        // ═══════════════════════════════════════════════════════
        // POS SYSTEM LOGIC
        // ═══════════════════════════════════════════════════════
        let posProducts = [];
        let posCart = []; // { product_id, name, price, quantity, maxStock }

        function loadPosProducts() {
            fetch(`api/pos.php?action=get_products&pharma=${pharmaCode}`)
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        posProducts = res.data;
                        renderPosGrid();
                    }
                })
                .catch(() => {
                    document.getElementById('posProductGrid').innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--text-muted);">Failed to load products.</div>';
                });
        }

        function renderPosGrid() {
            const grid = document.getElementById('posProductGrid');
            const query = document.getElementById('posSearchInput').value.toLowerCase();

            const filtered = posProducts.filter(p =>
                p.name.toLowerCase().includes(query) ||
                p.generic_name.toLowerCase().includes(query) ||
                p.category.toLowerCase().includes(query)
            );

            if (filtered.length === 0) {
                grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--text-muted);">No products match your search.</div>';
                return;
            }

            grid.innerHTML = filtered.map(p => {
                const stock = parseInt(p.stock);
                const isOut = stock === 0;
                const stockClass = isOut ? 'out' : (stock < 30 ? 'low' : 'in');
                const stockLabel = isOut ? 'Out' : stock + ' left';
                const price = parseFloat(p.price).toFixed(2);

                return `
                    <div class="pos-product-card ${isOut ? 'out-of-stock' : ''}" onclick="addToCart(${p.id}, '${p.name.replace(/'/g, "\\'")}', ${p.price}, ${stock})">
                        <span class="pos-prod-name">${p.name}</span>
                        <span class="pos-prod-generic">${p.generic_name}</span>
                        <div class="pos-prod-bottom">
                            <span class="pos-prod-price">₱${price}</span>
                            <span class="pos-prod-stock ${stockClass}">${stockLabel}</span>
                        </div>
                    </div>`;
            }).join('');
        }

        function filterPosProducts() {
            renderPosGrid();
        }

        function addToCart(productId, name, price, maxStock) {
            const existing = posCart.find(i => i.product_id === productId);
            if (existing) {
                if (existing.quantity >= maxStock) {
                    alert(`Cannot add more. Only ${maxStock} in stock.`);
                    return;
                }
                existing.quantity++;
            } else {
                posCart.push({ product_id: productId, name, price: parseFloat(price), quantity: 1, maxStock });
            }
            renderCart();
        }

        function removeFromCart(productId) {
            posCart = posCart.filter(i => i.product_id !== productId);
            renderCart();
        }

        function updateCartQty(productId, newQty) {
            const item = posCart.find(i => i.product_id === productId);
            if (!item) return;

            newQty = parseInt(newQty);
            if (isNaN(newQty) || newQty < 1) {
                removeFromCart(productId);
                return;
            }
            if (newQty > item.maxStock) {
                alert(`Cannot exceed available stock (${item.maxStock}).`);
                newQty = item.maxStock;
            }
            item.quantity = newQty;
            renderCart();
        }

        function incrementQty(productId) {
            const item = posCart.find(i => i.product_id === productId);
            if (!item) return;
            if (item.quantity >= item.maxStock) {
                alert(`Cannot exceed available stock (${item.maxStock}).`);
                return;
            }
            item.quantity++;
            renderCart();
        }

        function decrementQty(productId) {
            const item = posCart.find(i => i.product_id === productId);
            if (!item) return;
            if (item.quantity <= 1) {
                removeFromCart(productId);
                return;
            }
            item.quantity--;
            renderCart();
        }

        function clearPosCart() {
            posCart = [];
            renderCart();
        }

        function getCartTotal() {
            return posCart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
        }

        function renderCart() {
            const container = document.getElementById('posCartItems');
            const countEl = document.getElementById('posCartCount');
            const subtotalEl = document.getElementById('posSubtotal');

            const totalItems = posCart.reduce((s, i) => s + i.quantity, 0);
            countEl.textContent = totalItems;

            if (posCart.length === 0) {
                container.innerHTML = '<div class="pos-cart-empty">Click products to add them to your cart</div>';
                subtotalEl.textContent = '₱0.00';
                updatePosChange();
                return;
            }

            container.innerHTML = posCart.map(item => {
                const subtotal = (item.price * item.quantity).toFixed(2);
                return `
                    <div class="pos-cart-item">
                        <div class="pos-cart-item-info">
                            <div class="pos-cart-item-name" title="${item.name}">${item.name}</div>
                            <div class="pos-cart-item-price">₱${item.price.toFixed(2)} each</div>
                        </div>
                        <div class="pos-cart-qty-controls">
                            <button class="pos-cart-qty-btn" onclick="decrementQty(${item.product_id})">−</button>
                            <input type="number" class="pos-cart-qty-input" value="${item.quantity}" min="1" max="${item.maxStock}" onchange="updateCartQty(${item.product_id}, this.value)">
                            <button class="pos-cart-qty-btn" onclick="incrementQty(${item.product_id})">+</button>
                        </div>
                        <span class="pos-cart-item-subtotal">₱${subtotal}</span>
                        <button class="pos-cart-item-remove" onclick="removeFromCart(${item.product_id})" title="Remove">&times;</button>
                    </div>`;
            }).join('');

            subtotalEl.textContent = '₱' + getCartTotal().toFixed(2);
            updatePosChange();
        }

        function updatePosChange() {
            const total = getCartTotal();
            const paid = parseFloat(document.getElementById('posAmountPaid').value) || 0;
            const change = paid - total;
            const changeEl = document.getElementById('posChange');
            const btn = document.getElementById('posCompleteBtn');

            if (total === 0 || paid < total) {
                changeEl.textContent = '₱0.00';
                changeEl.style.color = 'var(--text-muted)';
                btn.disabled = true;
            } else {
                changeEl.textContent = '₱' + change.toFixed(2);
                changeEl.style.color = 'var(--success)';
                btn.disabled = false;
            }
        }

        function completeSale() {
            if (posCart.length === 0) {
                alert('Cart is empty.');
                return;
            }

            const total = getCartTotal();
            const paid = parseFloat(document.getElementById('posAmountPaid').value) || 0;

            if (paid < total) {
                alert('Insufficient payment amount.');
                return;
            }

            const btn = document.getElementById('posCompleteBtn');
            btn.disabled = true;
            btn.textContent = 'Processing...';

            const payload = {
                items: posCart.map(i => ({ product_id: i.product_id, quantity: i.quantity })),
                amount_paid: paid
            };

            fetch(`api/pos.php?action=process_sale&pharma=${pharmaCode}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(`Sale completed!\nReceipt: ${data.receipt_no}\nTotal: ₱${parseFloat(data.total).toFixed(2)}\nPaid: ₱${parseFloat(data.paid).toFixed(2)}\nChange: ₱${parseFloat(data.change).toFixed(2)}`);

                        // Reset POS
                        posCart = [];
                        document.getElementById('posAmountPaid').value = '';
                        renderCart();
                        loadPosProducts();
                        loadSalesHistory();
                        loadData(); // Refresh inventory tabs too

                        // Show sync banner
                        document.getElementById('syncBannerText').textContent = `POS Sale ${data.receipt_no} completed & synced!`;
                        document.getElementById('syncBanner').style.display = 'flex';
                    } else {
                        alert('Sale failed: ' + data.message);
                    }
                })
                .catch(err => {
                    alert('Connection error while processing sale.');
                    console.error(err);
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.textContent = 'Complete Sale';
                });
        }

        // ── SALES HISTORY ──
        function loadSalesHistory() {
            fetch(`api/pos.php?action=get_history&pharma=${pharmaCode}`)
                .then(r => r.json())
                .then(res => {
                    const list = document.getElementById('posHistoryList');
                    if (res.status === 'success' && res.data && res.data.length > 0) {
                        list.innerHTML = res.data.map(sale => {
                            const date = new Date(sale.created_at);
                            const timeStr = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) + ' | ' + date.toLocaleDateString([], { month: 'short', day: 'numeric' });
                            const total = parseFloat(sale.total_amount).toFixed(2);

                            return `
                                <div class="pos-sale-row">
                                    <span class="pos-sale-receipt">${sale.receipt_no}</span>
                                    <span class="pos-sale-items">${sale.items_summary || 'No items'}</span>
                                    <span class="pos-sale-total">₱${total}</span>
                                    <span class="pos-sale-time">${timeStr}</span>
                                </div>`;
                        }).join('');
                    } else {
                        list.innerHTML = '<div style="text-align:center; padding:1.5rem; color:var(--text-muted); font-size:0.85rem;">No sales recorded yet.</div>';
                    }
                })
                .catch(() => {
                    document.getElementById('posHistoryList').innerHTML = '<div style="text-align:center; padding:1.5rem; color:var(--text-muted); font-size:0.85rem;">Failed to load sales history.</div>';
                });
        }

        function toggleSalesHistory() {
            const list = document.getElementById('posHistoryList');
            const chevron = document.getElementById('posHistoryChevron');
            list.classList.toggle('open');
            chevron.style.transform = list.classList.contains('open') ? 'rotate(180deg)' : '';
        }

        function loadLogs() {
            fetch(`api/logs.php?pharma=${pharmaCode}`)
                .then(res => res.json())
                .then(res => {
                    const tbody = document.getElementById('logsTableBody');
                    if (res.status === 'success') {
                        if (res.data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 2rem; color: var(--text-muted);">No logs found.</td></tr>';
                            return;
                        }
                        
                        let html = '';
                        res.data.forEach(log => {
                            // Format date nicely
                            const dateObj = new Date(log.created_at);
                            const dateStr = dateObj.toLocaleDateString() + ' ' + dateObj.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                            
                            // Color code actions
                            let badgeClass = 'bg-gray-100 text-gray-800';
                            if (log.action === 'POS_SALE_COMPLETED') badgeClass = 'bg-green-100 text-green-800';
                            else if (log.action === 'INVENTORY_UPDATE') badgeClass = 'bg-blue-100 text-blue-800';
                            else if (log.action === 'STORE_STATUS') badgeClass = 'bg-purple-100 text-purple-800';

                            html += `
                                <tr>
                                    <td style="font-size: 0.85rem; color: var(--text-muted);">${dateStr}</td>
                                    <td>
                                        <span style="display: inline-block; padding: 0.2rem 0.6rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 600; 
                                            ${badgeClass === 'bg-green-100 text-green-800' ? 'background: #d1fae5; color: #065f46;' : 
                                              badgeClass === 'bg-blue-100 text-blue-800' ? 'background: #dbeafe; color: #1e40af;' : 
                                              badgeClass === 'bg-purple-100 text-purple-800' ? 'background: #f3e8ff; color: #6b21a8;' : 
                                              'background: #f3f4f6; color: #374151;'}">
                                            ${log.action.replace(/_/g, ' ')}
                                        </span>
                                    </td>
                                    <td style="font-size: 0.9rem;">${log.message}</td>
                                </tr>
                            `;
                        });
                        tbody.innerHTML = html;
                    } else {
                        tbody.innerHTML = `<tr><td colspan="3" style="color:red; text-align:center;">Failed to load logs: ${res.message}</td></tr>`;
                    }
                })
                .catch(err => {
                    document.getElementById('logsTableBody').innerHTML = '<tr><td colspan="3" style="color:red; text-align:center;">Error connecting to server.</td></tr>';
                });
        }

        loadData();
        loadPosProducts();
        loadSalesHistory();
        loadLogs();

        // Edit Profile JS
        async function openEditProfileModal() {
            document.getElementById('editProfileModal').style.display = 'flex';
            document.getElementById('editProfileAlert').style.display = 'none';
            document.getElementById('editProfileForm').reset();
            try {
                const res = await fetch('api/update_profile.php');
                const result = await res.json();
                if (result.status === 'success') {
                    document.getElementById('editName').value = result.data.name;
                    document.getElementById('editUsername').value = result.data.username;
                    document.getElementById('editAddress').value = result.data.address;
                    document.getElementById('editContact').value = result.data.contact_number;
                    document.getElementById('editEmail').value = result.data.email;
                }
            } catch(err) {
                console.error("Error fetching profile", err);
            }
        }

        function closeEditProfileModal() {
            document.getElementById('editProfileModal').style.display = 'none';
        }

        async function submitEditProfile(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const alertBox = document.getElementById('editProfileAlert');
            
            try {
                const response = await fetch('api/update_profile.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.status === 'success') {
                    alertBox.style.display = 'block';
                    alertBox.className = 'alert-banner alert-banner-success';
                    alertBox.innerHTML = `<strong>Success!</strong> ${data.message}`;
                    
                    document.getElementById('portalUserName').innerText = document.getElementById('editName').value;
                    
                    setTimeout(() => { 
                        alertBox.style.display = 'none'; 
                        closeEditProfileModal();
                    }, 2000);
                } else {
                    alertBox.style.display = 'block';
                    alertBox.className = 'alert-banner alert-banner-error';
                    alertBox.innerHTML = `<strong>Error!</strong> ${data.message}`;
                }
            } catch(err) {
                alertBox.style.display = 'block';
                alertBox.className = 'alert-banner alert-banner-error';
                alertBox.innerHTML = `<strong>Error!</strong> Failed to update profile.`;
            }
        }
    </script>
</body>

</html>