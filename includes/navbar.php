<?php
// includes/navbar.php - Auto-detection version

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

        <div class="nav-user" style="display: flex; align-items: center;">
            <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'pharmacy')): ?>
                <!-- If logged in as staff/admin, show portal badge and logout -->
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <a href="admin_dashboard.php" class="portal-badge" title="Go to Admin Dashboard">
                        ADMIN PORTAL: <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>
                    </a>
                <?php else: ?>
                    <a href="pharmacy_dashboard.php?pharma=<?= urlencode($pharmaCode) ?>" class="portal-badge" title="Go to Pharmacy Dashboard">
                        PORTAL: <?= htmlspecialchars($pharmaName) ?>
                    </a>
                <?php endif; ?>
                <a href="logout.php" class="btn-switch-role">Logout</a>
            <?php else: ?>
                <!-- If guest customer, show Portal: Customer and Logout -->
                <span class="portal-badge static">
                    PORTAL: CUSTOMER
                </span>
                <a href="logout.php" class="btn-switch-role">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</nav>