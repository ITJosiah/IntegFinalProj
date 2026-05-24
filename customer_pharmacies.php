<?php
// ── 1. Shared session logic ──
require_once 'includes/session_guard.php';

// ── 2. Shared db connection ──
require_once 'api/config/db.php';

// ── 3. Set active nav tab for this page ──
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
    <style>
        /* Sort Toggle Bar */
        .sort-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding: 0.6rem 0.85rem;
            background: #F8FAFC;
            border: 1px solid var(--border);
            border-radius: 0.75rem;
        }
        .sort-bar-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }
        .sort-toggle {
            display: flex;
            background: #E2E8F0;
            border-radius: 0.5rem;
            padding: 0.2rem;
            gap: 0.15rem;
        }
        .sort-toggle-btn {
            padding: 0.4rem 0.85rem;
            border-radius: 0.4rem;
            border: none;
            background: transparent;
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748B;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
        }
        .sort-toggle-btn.active {
            background: white;
            color: var(--primary);
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .sort-toggle-btn:hover:not(.active) {
            color: var(--text-main);
        }

        /* Distance Badge on Pharmacy Cards */
        .pharm-distance {
            display: inline-flex;
            align-items: center;
            font-size: 0.7rem;
            font-weight: 700;
            color: #059669;
            background: #ECFDF5;
            border: 1px solid #A7F3D0;
            padding: 0.2rem 0.6rem;
            border-radius: 2rem;
            margin-top: 0.5rem;
            gap: 0.25rem;
        }

        .location-notice {
            font-size: 0.75rem;
            color: #94A3B8;
            font-style: italic;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
    </style>
</head>
<body>

<!-- Shared navbar -->
<?php require_once 'includes/navbar.php'; ?>

<div class="layout">
    <main class="main-content">

        <!-- ── HEADER SECTION ── -->
        <section class="pharm-header" style="padding: 0.5rem 0 0.85rem;">
            <h1 class="pharm-title">Pharmacy Directory</h1>

        </section>

        <!-- ── MAIN CONTENT GRID ── -->
        <section class="pharm-main">
            <div class="pharm-grid">

                <!-- ── LEFT: PHARMACY LISTINGS ── -->
                <div class="pharm-listings">

                    <!-- Sort Toggle Bar -->
                    <div class="sort-bar" id="sortBar">
                        <span class="sort-bar-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="4" y1="6" x2="16" y2="6"></line>
                                <line x1="4" y1="12" x2="12" y2="12"></line>
                                <line x1="4" y1="18" x2="8" y2="18"></line>
                            </svg>
                            Sort by
                        </span>
                        <div class="sort-toggle">
                            <button class="sort-toggle-btn active" id="sortAlpha" onclick="sortPharmacies('alpha')">Alphabetical</button>
                            <button class="sort-toggle-btn" id="sortNearest" onclick="sortPharmacies('nearest')">Nearest</button>
                        </div>
                    </div>

                    <!-- Location notice (shown when nearest sort is selected but no location) -->
                    <div id="locationNotice" style="display: none; margin-bottom: 0.75rem;">
                        <span class="location-notice">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            Enable location access in your browser to sort by nearest.
                        </span>
                    </div>

                    <div id="pharmacyList">
                    <?php if (empty($pharmacies)): ?>
                        <div class="pharm-card" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            <p style="font-size: 1.1rem; font-weight: 600; margin: 0 0 0.5rem;">No pharmacies registered</p>
                            <p style="font-size: 0.9rem; margin: 0;">Contact the health system administrator to register your node.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pharmacies as $pharm): ?>
                            <div class="pharm-card" id="pharm-card-<?= $pharm['id'] ?>"
                                 data-name="<?= htmlspecialchars($pharm['name']) ?>"
                                 data-lat="<?= $pharm['latitude'] ?>"
                                 data-lng="<?= $pharm['longitude'] ?>">
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

                                        <!-- Distance Badge (hidden until location is available) -->
                                        <div class="pharm-distance-wrap" style="display: none;">
                                            <span class="pharm-distance">
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                                <span class="dist-value">—</span>
                                            </span>
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
                            <span style="font-size:0.75rem; background:var(--primary-light); color:var(--primary); padding:0.25rem 0.6rem; border-radius:1rem; font-weight:700;"><?= count($pharmacies) ?> PHARMACIES</span>
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

<!-- Google Maps Embed Script + Sort + Distance Logic -->
<script>
    let userLat = null;
    let userLng = null;
    let locationReady = false;
    let currentSort = 'nearest';

    // ── Geolocation ──
    if (navigator.geolocation) {
        // Show notice initially since location is loading/requesting
        document.getElementById('locationNotice').style.display = '';

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                userLat = pos.coords.latitude;
                userLng = pos.coords.longitude;
                locationReady = true;
                document.getElementById('locationNotice').style.display = 'none';

                // Send to server
                fetch('api/update_location.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ latitude: userLat, longitude: userLng })
                }).catch(() => {});

                // Calculate and show distance badges
                updateDistanceBadges();

                // Sort by nearest now that location is ready
                if (currentSort === 'nearest') {
                    sortPharmacies('nearest');
                }
            },
            () => {
                // Location access denied/failed
                if (currentSort === 'nearest') {
                    document.getElementById('locationNotice').style.display = '';
                }
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    } else {
        // Geolocation not supported
        if (currentSort === 'nearest') {
            document.getElementById('locationNotice').style.display = '';
        }
    }

    // Haversine
    function haversineKm(lat1, lng1, lat2, lng2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) ** 2 +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLng / 2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function updateDistanceBadges() {
        if (!locationReady) return;
        const cards = document.querySelectorAll('.pharm-card[data-lat]');
        cards.forEach(card => {
            const lat = parseFloat(card.getAttribute('data-lat'));
            const lng = parseFloat(card.getAttribute('data-lng'));
            if (isNaN(lat) || isNaN(lng)) return;

            const dist = haversineKm(userLat, userLng, lat, lng);
            const label = dist < 1 ? `${Math.round(dist * 1000)}m away` : `${dist.toFixed(1)} km away`;

            card.setAttribute('data-distance', dist);
            const badge = card.querySelector('.pharm-distance-wrap');
            const valEl = card.querySelector('.dist-value');
            if (badge && valEl) {
                valEl.textContent = label;
                badge.style.display = '';
            }
        });
    }

    function sortPharmacies(mode) {
        currentSort = mode;

        // Update toggle UI
        document.getElementById('sortAlpha').classList.toggle('active', mode === 'alpha');
        document.getElementById('sortNearest').classList.toggle('active', mode === 'nearest');

        const list = document.getElementById('pharmacyList');
        const cards = Array.from(list.querySelectorAll('.pharm-card[data-lat]'));

        if (mode === 'nearest') {
            if (!locationReady) {
                document.getElementById('locationNotice').style.display = '';
                return;
            }
            document.getElementById('locationNotice').style.display = 'none';

            cards.sort((a, b) => {
                const da = parseFloat(a.getAttribute('data-distance')) || 9999;
                const db = parseFloat(b.getAttribute('data-distance')) || 9999;
                return da - db;
            });
        } else {
            document.getElementById('locationNotice').style.display = 'none';
            cards.sort((a, b) => {
                const na = (a.getAttribute('data-name') || '').toLowerCase();
                const nb = (b.getAttribute('data-name') || '').toLowerCase();
                return na.localeCompare(nb);
            });
        }

        // Re-order DOM
        cards.forEach(card => list.appendChild(card));
    }

    function focusPharmacyOnMap(name) {
        const iframe = document.getElementById('googleMap');
        const query = encodeURIComponent(name + ", Basud, Camarines Norte");
        iframe.src = `https://maps.google.com/maps?q=${query}&t=&z=16&ie=UTF8&iwloc=&output=embed`;
        
        iframe.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
</script>

</body>
</html>