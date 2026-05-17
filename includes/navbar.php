<?php
// includes/navbar.php - Auto-detection version

$navItems = [
        'home'        => ['label' => 'Home',         'href' => 'customer_dashboard.php'],
        'pharmacies'  => ['label' => 'Pharmacies',  'href' => 'customer_pharmacies.php'],
        'support'     => ['label' => 'About',       'href' => 'customer_support.php'],
];

// Get current page filename
$currentFile = basename($_SERVER['PHP_SELF']);

// Map filenames to nav keys
$pageToNavKey = [
        'customer_dashboard.php'   => 'home',
        'customer_pharmacies.php'  => 'pharmacies',
        'customer_support.php'     => 'support'
];

// Determine active page
$activeNavKey = isset($pageToNavKey[$currentFile]) ? $pageToNavKey[$currentFile] : '';
?>
<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="brand" style="display:flex; align-items:center; gap:0.35rem; text-decoration:none;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
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

        <div class="nav-user" style="display:flex; align-items:center;">
            <span style="background: #EFF6FF; color: var(--primary); font-weight: 700; font-size: 0.8rem; padding: 0.4rem 1rem; border-radius: 2rem; text-transform: uppercase; display: inline-flex; align-items: center; margin-right: 0.75rem; letter-spacing: 0.05em;">PORTAL: CUSTOMER</span>
            <a href="index.php" class="btn-switch-role">Logout</a>
        </div>
    </div>
</nav>

<style>
.btn-switch-role {
    font-size: 0.875rem;
    padding: 0.4rem 0.875rem;
    border-radius: 6px;
    border: 1.5px solid var(--primary-mid);
    background: var(--primary-light);
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.btn-switch-role:hover {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
    transform: translateY(-1px);
}
</style>