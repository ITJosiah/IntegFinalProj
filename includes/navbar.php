<?php
// includes/navbar.php
// Requires: $activePage (string) — set this in the parent page BEFORE including.
// Example:  $activePage = 'dashboard';

$navItems = [
    'dashboard'    => ['label' => 'Home',         'href' => 'customer_dashboard.php'],
    'drugs'        => ['label' => 'Drugs',       'href' => 'customer_dashboard.php'],
    'pharmacies'   => ['label' => 'Pharmacies',  'href' => 'customer_pharmacies.php'],
    'orders'       => ['label' => 'Refills',     'href' => 'customer_orders.php'],
    'appointments' => ['label' => 'Appointments','href' => 'customer_appointment.php'],
    'support'      => ['label' => 'Support',     'href' => 'customer_support.php'],
];
?>
<nav class="navbar">
    <div class="nav-container">
        <a href="../index.php" class="brand">PharmaDirect</a>

        <ul class="nav-tabs">
            <?php foreach ($navItems as $key => $item): ?>
                <li>
                    <a href="<?= htmlspecialchars($item['href']) ?>"
                       class="<?= (isset($activePage) && $activePage === $key) ? 'active' : '' ?>">
                        <?= htmlspecialchars($item['label']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="nav-user">
            <div class="user-icon" title="<?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
        </div>
    </div>
</nav>