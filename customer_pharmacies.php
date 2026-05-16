<?php
// ── 1. Shared session logic ──
require_once 'includes/session_guard.php';

/** @var string $greeting Provided by includes/session_guard.php */

// ── 2. Set active nav tab for this page ──
$activePage = 'pharmacies';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Directory - PharmaDirect</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/customer_pharmacies.css">
</head>
<body>

<!-- Shared navbar -->
<?php require_once 'includes/navbar.php'; ?>

<div class="layout">
    <main class="main-content">

        <!-- ── HEADER SECTION ── -->
        <section class="pharm-header">
            <h1 class="pharm-title">Pharmacy Directory</h1>
            <p class="pharm-subtitle">
                Find authorized pharmacies in Basud, Camarines Norte. Check availability, operational
                status, and connect instantly with healthcare providers.
            </p>
        </section>

        <!-- ── MAIN CONTENT GRID ── -->
        <section class="pharm-main">
            <div class="pharm-grid">

                <!-- ── LEFT: PHARMACY LISTINGS ── -->
                <div class="pharm-listings">

                    <!-- St. Jude Generic Pharmacy -->
                    <div class="pharm-card">
                        <div class="pharm-card-header">
                            <div class="pharm-info">
                                <h3 class="pharm-name">St. Jude Generic Pharmacy</h3>
                                <div class="pharm-address">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                    Maharlika Highway, Poblacion, Basud, Camarines Norte
                                </div>
                            </div>
                            <span class="pharm-status open">OPEN</span>
                        </div>
                        <div class="pharm-actions">
                            <button class="pharm-btn phone">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </button>
                            <button class="pharm-btn info">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </button>
                            <button class="pharm-btn primary">View Stock</button>
                        </div>
                    </div>

                    <!-- Basud Community Drugstore -->
                    <div class="pharm-card">
                        <div class="pharm-card-header">
                            <div class="pharm-info">
                                <h3 class="pharm-name">Basud Community Drugstore</h3>
                                <div class="pharm-address">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                    San Vicente St., Basud, Camarines Norte
                                </div>
                            </div>
                            <span class="pharm-status open">OPEN</span>
                        </div>
                        <div class="pharm-actions">
                            <button class="pharm-btn phone">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </button>
                            <button class="pharm-btn info">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </button>
                            <button class="pharm-btn primary">View Stock</button>
                        </div>
                    </div>

                    <!-- Bicol Health Solutions (Closed) -->
                    <div class="pharm-card">
                        <div class="pharm-card-header">
                            <div class="pharm-info">
                                <h3 class="pharm-name">Bicol Health Solutions</h3>
                                <div class="pharm-address">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                    Purok 4, Tuaca, Basud, Camarines Norte
                                </div>
                            </div>
                            <span class="pharm-status closed">CLOSED</span>
                        </div>
                        <div class="pharm-actions">
                            <button class="pharm-btn phone">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </button>
                            <button class="pharm-btn info">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </button>
                            <button class="pharm-btn secondary">Reopens 8AM</button>
                        </div>
                    </div>

                </div>

                <!-- ── RIGHT: MAP + EMERGENCY CARD ── -->
                <div class="pharm-sidebar">

                    <!-- Live Location Map -->
                    <div class="pharm-map-card">
                        <div class="pharm-map-header">
                            <h3>Live Location Map</h3>
                            <a href="#" class="pharm-nearby">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                3 Nearby
                            </a>
                        </div>

                        <div class="pharm-map">
                            <svg class="pharm-map-grid" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <pattern id="pharmGrid" width="12" height="12" patternUnits="userSpaceOnUse">
                                        <path d="M 12 0 L 0 0 0 12" fill="none" stroke="rgba(37,99,235,0.1)" stroke-width="1"/>
                                    </pattern>
                                </defs>
                                <rect width="100%" height="100%" fill="url(#pharmGrid)" />
                                <!-- Decorative path -->
                                <path d="M-10 100 L200 20 M20 200 L180 60" stroke="rgba(37,99,235,0.15)" stroke-width="2" fill="none"/>
                            </svg>

                            <!-- Map pins -->
                            <div class="pharm-pin" style="top: 30%; left: 45%;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" fill="#DC2626" stroke="white" stroke-width="2"/>
                                    <circle cx="12" cy="10" r="3" fill="white"/>
                                </svg>
                            </div>
                            <div class="pharm-pin" style="top: 60%; left: 70%;">
                                <div class="pharm-pin-circle blue"></div>
                            </div>
                            <div class="pharm-pin" style="top: 50%; left: 25%;">
                                <div class="pharm-pin-circle yellow"></div>
                            </div>
                        </div>

                        <a href="#" class="pharm-expand-link">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="15 3 21 3 21 9"></polyline>
                                <polyline points="9 21 3 21 3 15"></polyline>
                                <line x1="21" y1="3" x2="14" y2="10"></line>
                                <line x1="3" y1="21" x2="10" y2="14"></line>
                            </svg>
                            Expand Full Map View
                        </a>
                    </div>

                    <!-- Emergency Service -->
                    <div class="pharm-emergency-card">
                        <h3 class="pharm-emergency-title">Emergency Service?</h3>
                        <p class="pharm-emergency-text">
                            If you need immediate medical assistance or life-saving medication after hours, contact the
                            regional emergency dispatch.
                        </p>
                        <button class="pharm-emergency-btn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            Call Emergency Help
                        </button>
                    </div>

                </div>

            </div>
        </section>

    </main>
</div>

<!-- Shared footer -->
<?php require_once 'includes/footer.php'; ?>

</body>
</html>