<?php
// includes/navbar.php - Auto-detection version with customer/guest awareness

$navItems = [
        'home'        => ['label' => 'Home',         'href' => 'customer_dashboard.php'],
        'pharmacies'  => ['label' => 'Pharmacies',  'href' => 'customer_pharmacies.php'],
        'support'     => ['label' => 'About',     'href' => 'customer_about.php'],
];

// Get current page filename
$currentFile = basename($_SERVER['PHP_SELF']);

// Map filenames to nav keys
$pageToNavKey = [
        'customer_dashboard.php'   => 'home',
        'customer_pharmacies.php'  => 'pharmacies',
        'customer_about.php'       => 'support'
];

// Determine active page
$activeNavKey = isset($pageToNavKey[$currentFile]) ? $pageToNavKey[$currentFile] : '';

// Map pharmacy codes to names
$pharmaCode = isset($_SESSION['pharma_code']) ? $_SESSION['pharma_code'] : '';
$pharmaNames = [
        'laurents' => "Laurent's Pharmacy",
        'jrm'      => "JRM DOCTORS Pharmacy",
        'riteaid'  => "D' Rite Aid Generics Pharmacy",
];
$pharmaName = $pharmaNames[$pharmaCode] ?? "Pharmacy Portal";
?>
<nav class="navbar">
    <div class="nav-container">
        <a href="customer_dashboard.php" class="brand" style="display:flex; align-items:center; gap:0.35rem;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="12" y1="8" x2="12" y2="16"></line>
                <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
            <span>Pharma<span class="sync-part" style="color: black;">Sync</span></span>
        </a>

        <ul class="nav-tabs">
            <?php foreach ($navItems as $key => $item): ?>
                <li>
                    <a href="<?= htmlspecialchars($item['href']) ?>"
                       class="<?= ($activeNavKey === $key) ? 'active' : '' ?>">
                        <?= htmlspecialchars($item['label']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="nav-user" style="display: flex; align-items: center; gap: 0.5rem;">
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <!-- Admin portal badge + logout -->
                <a href="admin_dashboard.php" class="portal-badge" title="Go to Admin Dashboard">
                    ADMIN PORTAL: <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>
                </a>
                <a href="logout.php" class="btn-switch-role">Logout</a>

            <?php elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'pharmacy'): ?>
                <!-- Pharmacy portal badge + logout -->
                <a href="pharmacy_dashboard.php?pharma=<?= urlencode($pharmaCode) ?>" class="portal-badge" title="Go to Pharmacy Dashboard">
                    PORTAL: <?= htmlspecialchars($pharmaName) ?>
                </a>
                <a href="logout.php" class="btn-switch-role">Logout</a>

            <?php elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer'): ?>
                <!-- Logged-in customer: show name + logout -->
                <span class="portal-badge static" style="background: #D1FAE5; color: #065F46; border-color: #A7F3D0;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 0.2rem; vertical-align: -1px;">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <?= htmlspecialchars($_SESSION['user_name']) ?>
                </span>
                <a href="logout.php" class="btn-switch-role">Logout</a>

            <?php else: ?>
                <!-- Guest: show login + register -->
                <a href="index.php" class="btn-switch-role" style="background: var(--primary); color: white; border-color: var(--primary);">Sign In</a>
                <a href="register.php" class="btn-switch-role">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>