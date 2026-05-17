<?php
// ── 1. Shared db connection ──
require_once 'api/config/db.php';

// ── 2. Set active nav tab for this page ──
$activePage = 'pharmacies';

// Fetch dynamic pharmacy directory from core database
$coreDB = getDBConnection('pharmasync_core');
$stmt = $coreDB->query("SELECT id, name, code, address, contact_number, email, latitude, longitude, is_open FROM pharmacies ORDER BY id ASC");
$pharmacies = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Directory - PharmaSync</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/customer_pharmacies.css">
</head>
<body>

<!-- Shared navbar -->
<?php require_once 'includes/navbar.php'; ?>

<div class="layout">
    <main class="main-content">

        <!-- ── HEADER SECTION ── -->
        <section class="pharm-header" style="padding: 2rem 0 1.5rem;">
            <h1 class="pharm-title">Pharmacy Directory</h1>
            <p class="pharm-subtitle">
                Find registered, authorized pharmacies in Basud, Camarines Norte. Check operational status, email, contact info, and locate them instantly.
            </p>
        </section>

        <!-- ── MAIN CONTENT GRID ── -->
        <section class="pharm-main">
            <div class="pharm-grid">

                <!-- ── LEFT: PHARMACY LISTINGS ── -->
                <div class="pharm-listings">
                    <?php if (empty($pharmacies)): ?>
                        <div class="pharm-card" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            <p style="font-size: 1.1rem; font-weight: 600; margin: 0 0 0.5rem;">No pharmacies registered</p>
                            <p style="font-size: 0.9rem; margin: 0;">Contact the health system administrator to register your node.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pharmacies as $pharm): ?>
                            <div class="pharm-card" id="pharm-card-<?= $pharm['id'] ?>">
                                <div class="pharm-card-header">
                                    <div class="pharm-info">
                                        <h3 class="pharm-name"><?= htmlspecialchars($pharm['name']) ?></h3>
                                        
                                        <!-- Address -->
                                        <div class="pharm-address" style="margin-bottom: 0.75rem;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="2.2"
                                                 stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                            <span><?= htmlspecialchars($pharm['address']) ?></span>
                                        </div>

                                        <!-- Contact Number -->
                                        <div class="pharm-address" style="margin-bottom: 0.5rem;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="2.2"
                                                 stroke-linecap="round" stroke-linejoin="round" style="color: #10B981;">
                                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                            </svg>
                                            <strong style="color: var(--text-main); margin-right: 0.25rem;">Phone:</strong>
                                            <span><?= htmlspecialchars($pharm['contact_number']) ?></span>
                                        </div>

                                        <!-- Email Address -->
                                        <div class="pharm-address">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="2.2"
                                                 stroke-linecap="round" stroke-linejoin="round" style="color: #3B82F6;">
                                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                                <polyline points="22,6 12,13 2,6"></polyline>
                                            </svg>
                                            <strong style="color: var(--text-main); margin-right: 0.25rem;">Email:</strong>
                                            <span><?= htmlspecialchars($pharm['email'] ?? '—') ?></span>
                                        </div>
                                    </div>
                                    <span class="pharm-status <?= $pharm['is_open'] ? 'open' : 'closed' ?>">
                                        <?= $pharm['is_open'] ? 'OPEN' : 'CLOSED' ?>
                                    </span>
                                </div>
                                <div class="pharm-actions" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed var(--border);">
                                    <button class="pharm-btn primary" onclick="focusPharmacyOnMap('<?= addslashes($pharm['name']) ?>')" style="width: 100%;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 0.2rem;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                        Locate on Map
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- ── RIGHT: MAP + EMERGENCY CARD ── -->
                <div class="pharm-sidebar">

                    <!-- Live Location Map -->
                    <div class="pharm-map-card">
                        <div class="pharm-map-header" style="margin-bottom: 0.75rem;">
                            <h3 style="display:flex; align-items:center; gap:0.4rem; color:var(--text-main); font-weight:700; font-size:0.95rem; margin:0;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color:var(--primary);"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                Live Location Map
                            </h3>
                            <span style="font-size:0.75rem; background:var(--primary-light); color:var(--primary); padding:0.25rem 0.6rem; border-radius:1rem; font-weight:700;">3 PHARMACIES</span>
                        </div>

                        <!-- Google Maps Embed Container -->
                        <iframe id="googleMap" src="https://maps.google.com/maps?q=Laurent's%20Pharmacy,%20Basud,%20Camarines%20Norte&t=&z=16&ie=UTF8&iwloc=&output=embed" style="width: 100%; height: 500px; border-radius: 8px; border: 1px solid var(--border); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 0.5rem; z-index: 1;" allowfullscreen="" loading="lazy"></iframe>
                    </div>


                </div>

            </div>
        </section>

    </main>
</div>

<!-- Shared footer -->
<?php require_once 'includes/footer.php'; ?>

<!-- Google Maps Embed Script -->
<script>
    function focusPharmacyOnMap(name) {
        const iframe = document.getElementById('googleMap');
        // Query Google Maps using the exact official registered business name + city for 100% accurate pinning
        const query = encodeURIComponent(name + ", Basud, Camarines Norte");
        iframe.src = `https://maps.google.com/maps?q=${query}&t=&z=16&ie=UTF8&iwloc=&output=embed`;
        
        // Scroll map gracefully on smaller screens
        iframe.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
</script>

</body>
</html>