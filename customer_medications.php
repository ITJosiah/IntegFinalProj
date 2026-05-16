<?php
// ── 1. Shared session logic ──
require_once 'includes/session_guard.php';

/** @var string $greeting Provided by includes/session_guard.php */

// ── 2. Set active nav tab for this page ──
$activePage = 'drugs';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Medications - PharmaDirect</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/customer_medications.css">
</head>
<body>

<!-- Shared navbar -->
<?php require_once 'includes/navbar.php'; ?>

<div class="layout">
    <main class="main-content">

        <!-- ── HERO HEADER ── -->
        <section class="med-hero">
            <h1 class="med-hero-title">Find your medication</h1>
            <p class="med-hero-subtitle">
                Search across global databases and local stock in Basud effortlessly.
            </p>

            <div class="med-search-wrapper">
                <div class="med-search-box">
                    <svg class="med-search-icon" width="20" height="20" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input
                            type="text"
                            id="medSearchInput"
                            class="med-search-input"
                            placeholder="Search Medicines Globally & Locally"
                            autocomplete="off"
                    >
                    <button class="med-search-btn" onclick="searchMedication()">Search</button>
                </div>

                <div class="med-source-badges">
                    <button class="med-badge med-badge-global" id="medGlobalBadge">
                        GLOBAL: OPENFDA
                    </button>
                    <button class="med-badge med-badge-local" id="medLocalBadge">
                        LOCAL: MYSQL DB
                    </button>
                </div>
            </div>
        </section>

        <!-- ── COMPARISON SECTION ── -->
        <section class="med-comparison">
            <div class="med-comparison-grid">

                <!-- ── GLOBAL DATABASE COLUMN ── -->
                <div class="med-column">
                    <div class="med-column-header">
                        <div class="med-column-title">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="2" y1="12" x2="22" y2="12"></line>
                                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                            </svg>
                            <h2>Global Database</h2>
                        </div>
                        <span class="med-column-source">Source: openFDA</span>
                    </div>

                    <div class="med-global-card">
                        <div class="med-global-head">
                            <div>
                                <h3 class="med-global-name">Amoxicillin</h3>
                                <p class="med-global-category">Antibacterial agent</p>
                            </div>
                            <span class="med-status-pill approved">FDA APPROVED</span>
                        </div>
                        <p class="med-global-dosage">
                            <span class="med-dose-strong">500mg</span> Capsule
                        </p>
                        <p class="med-global-desc">
                            Used for treatment of infections caused by susceptible isolates of
                            Streptococcus species, Staphylococcus species, and H. influenzae.
                        </p>
                    </div>

                    <div class="med-global-card">
                        <div class="med-global-head">
                            <div>
                                <h3 class="med-global-name">Lisinopril</h3>
                                <p class="med-global-category">ACE Inhibitor</p>
                            </div>
                            <span class="med-status-pill approved">FDA APPROVED</span>
                        </div>
                        <p class="med-global-dosage">
                            <span class="med-dose-strong">10mg</span> Tablet
                        </p>
                        <p class="med-global-desc">
                            Indicated for the treatment of hypertension in adults and children 6 years
                            of age and older to lower blood pressure.
                        </p>
                    </div>

                    <div class="med-global-card">
                        <div class="med-global-head">
                            <div>
                                <h3 class="med-global-name">Atorvastatin</h3>
                                <p class="med-global-category">Statin</p>
                            </div>
                            <span class="med-status-pill approved">FDA APPROVED</span>
                        </div>
                        <p class="med-global-dosage">
                            <span class="med-dose-strong">20mg</span> Oral Tablet
                        </p>
                        <p class="med-global-desc">
                            Lipid-lowering agent indicated as an adjunct to diet to reduce elevated
                            total cholesterol.
                        </p>
                    </div>
                </div>

                <!-- ── LOCAL AVAILABILITY COLUMN ── -->
                <div class="med-column">
                    <div class="med-column-header">
                        <div class="med-column-title">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <h2>Local availability</h2>
                        </div>
                        <span class="med-column-source">Location: Basud, CN</span>
                    </div>

                    <!-- In Stock — High -->
                    <div class="med-local-card in-stock">
                        <div class="med-local-head">
                            <div>
                                <h3 class="med-local-name">Amoxicillin (Generic)</h3>
                                <p class="med-local-pharmacy">Basud Community Pharmacy</p>
                            </div>
                            <div class="med-price-block">
                                <span class="med-price">₱12.50</span>
                                <span class="med-price-unit">PER PIECE</span>
                            </div>
                        </div>
                        <div class="med-stock-bar">
                            <div class="med-stock-fill" style="width: 85%;"></div>
                        </div>
                        <p class="med-stock-text">85 in stock</p>
                        <button class="med-reserve-btn">Reserve for Pickup</button>
                    </div>

                    <!-- In Stock — Low -->
                    <div class="med-local-card in-stock">
                        <div class="med-local-head">
                            <div>
                                <h3 class="med-local-name">Lisinopril (Zestril)</h3>
                                <p class="med-local-pharmacy">HealthMart Basud</p>
                            </div>
                            <div class="med-price-block">
                                <span class="med-price">₱45.00</span>
                                <span class="med-price-unit">PER PIECE</span>
                            </div>
                        </div>
                        <div class="med-stock-bar">
                            <div class="med-stock-fill" style="width: 25%;"></div>
                        </div>
                        <p class="med-stock-text">12 in stock</p>
                        <button class="med-reserve-btn">Reserve for Pickup</button>
                    </div>

                    <!-- Out of Stock -->
                    <div class="med-local-card out-of-stock">
                        <div class="med-local-head">
                            <div>
                                <h3 class="med-local-name">Atorvastatin (Lipitor)</h3>
                                <p class="med-local-pharmacy">Central Pharmacy</p>
                            </div>
                            <div class="med-price-block">
                                <span class="med-price">₱38.00</span>
                                <span class="med-price-unit">PER PIECE</span>
                            </div>
                        </div>
                        <div class="med-stock-bar empty">
                            <div class="med-stock-fill" style="width: 0%;"></div>
                        </div>
                        <p class="med-stock-text out">Out of Stock</p>
                        <button class="med-reserve-btn disabled" disabled>Notify when available</button>
                    </div>
                </div>

            </div>
        </section>

            <!-- ── CTA SECTION — Refill + Support ── -->
            <section class="med-cta">
                <div class="med-cta-grid">

                    <!-- Refill Card -->
                    <div class="med-refill-card">
                        <h2 class="med-refill-title">Need a refill?</h2>
                        <p class="med-refill-text">
                            Our integrated system allows you to request refills directly
                            from Basud pharmacies with just one click if your
                            prescription is on file.
                        </p>
                        <div class="med-refill-actions">
                            <a href="customer_refills.php" class="med-refill-btn primary">
                                Fast Refill Request
                            </a>
                            <a href="#" class="med-refill-btn link">How it works</a>
                        </div>
                    </div>

                    <!-- Local Support Card -->
                    <div class="med-support-card">
                        <div class="med-support-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 18v-6a9 9 0 0 1 18 0v6"></path>
                                <path d="M21 19a2 2 0 0 1-2 2h-1v-6h3zM3 19a2 2 0 0 0 2 2h1v-6H3z"></path>
                            </svg>
                        </div>
                        <h3 class="med-support-title">Local Support</h3>
                        <p class="med-support-text">
                            Have questions about drug interactions or local availability?
                        </p>
                        <a href="customer_support.php" class="med-support-link">
                            Chat with Pharmacist
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2.5"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>

                </div>
            </section>

        </main>
    </div>

    </main>
</div>

<!-- Shared footer -->
<?php require_once 'includes/footer.php'; ?>

<script>
    /* ── SOURCE BADGE TOGGLE ── */
    let medSearchSource = 'both';

    document.getElementById('medGlobalBadge').addEventListener('click', () => toggleMedSource('global'));
    document.getElementById('medLocalBadge').addEventListener('click',  () => toggleMedSource('local'));

    function toggleMedSource(source) {
        const gb = document.getElementById('medGlobalBadge');
        const lb = document.getElementById('medLocalBadge');

        if (medSearchSource === source) {
            medSearchSource = 'both';
            gb.className = 'med-badge med-badge-global';
            lb.className = 'med-badge med-badge-local';
        } else if (medSearchSource === 'both') {
            medSearchSource = source;
            if (source === 'global') {
                gb.className = 'med-badge med-badge-local';
                lb.className = 'med-badge med-badge-global';
            } else {
                gb.className = 'med-badge med-badge-global';
                lb.className = 'med-badge med-badge-local';
            }
        } else {
            medSearchSource = 'both';
            gb.className = 'med-badge med-badge-global';
            lb.className = 'med-badge med-badge-local';
        }
    }

    /* ── SEARCH ── */
    document.getElementById('medSearchInput').addEventListener('keypress', e => {
        if (e.key === 'Enter') searchMedication();
    });

    function searchMedication() {
        const query = document.getElementById('medSearchInput').value.trim();
        if (!query) {
            alert('Please enter a medication name to search');
            return;
        }
        console.log('Searching for:', query, 'in source:', medSearchSource);
    }
</script>

</body>
</html>