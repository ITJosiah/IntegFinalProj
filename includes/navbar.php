<?php
// includes/navbar.php - Auto-detection version

$navItems = [
        'home'        => ['label' => 'Home',         'href' => 'customer_medications.php'],
        'pharmacies'  => ['label' => 'Pharmacies',  'href' => 'customer_pharmacies.php'],
        //'profile'     => ['label' => 'Profile',     'href' => 'customer_dashboard.php'],
        'support'     => ['label' => 'About',     'href' => 'customer_about.php'],
];

// Get current page filename
$currentFile = basename($_SERVER['PHP_SELF']);

// Map filenames to nav keys
$pageToNavKey = [
        'customer_medications.php' => 'home',
        'customer_pharmacies.php'  => 'pharmacies',
        'customer_dashboard.php'   => 'profile',
        'customer_support.php'     => 'support'
];

// Determine active page
$activeNavKey = isset($pageToNavKey[$currentFile]) ? $pageToNavKey[$currentFile] : '';
?>
<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="brand">Pharma<span class="sync-part" style="color: black;">Sync</span></a>

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
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- If logged in as staff/admin, link back to dashboard -->
                <a href="<?= ($_SESSION['role'] === 'admin') ? 'admin_dashboard.php' : 'pharmacy_dashboard.php' ?>" class="user-icon" title="Go to Dashboard" style="color: inherit;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </a>
                <a href="logout.php" style="margin-left: 15px; color: #EF4444; text-decoration: none; font-size: 0.9rem; font-weight: 600;">Logout</a>
            <?php else: ?>
                <!-- If not logged in, icon links to login page -->
                <a href="index.php" class="user-icon" title="Staff Login" style="color: inherit;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>