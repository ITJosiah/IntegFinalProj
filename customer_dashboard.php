<?php
require_once 'includes/session_guard.php';

/** @var string $greeting Provided by includes/session_guard.php */

$activePage = 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Medication - PharmaSync</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/customer_dashboard.css">
</head>
<body>

<?php require_once 'includes/navbar.php'; ?>

<div class="layout">
    <main class="main-content" style="padding: 0;">

        <!-- ── HERO SEARCH ZONE ── -->
        <div class="lookup-hero">
            <h1 class="lookup-title">Find your medication</h1>
            <p class="lookup-subtitle">Search across global databases and local stock in Basud effortlessly.</p>

            <div class="lookup-search-box">
                <svg class="lookup-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path>
                </svg>
                <input type="text" id="searchInput" class="lookup-input" placeholder="e.g., paracetamol" autocomplete="off">
                <button class="lookup-btn" id="searchBtn" onclick="searchMedications()">Search</button>
            </div>

            <div class="lookup-badges">
                <span class="lookup-badge">GLOBAL: OPENFDA</span>
                <span class="lookup-badge">LOCAL: MYSQL DB</span>
            </div>
        </div>

        <!-- ── RESULTS AREA ── -->
        <div class="lookup-results-wrapper">

            <!-- Empty / default state -->
            <div id="emptyState" class="lookup-empty">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #CBD5E1; margin-bottom: 0.75rem;">
                    <circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path>
                </svg>
                <p style="font-size: 0.9rem; font-weight: 600; color: #94A3B8; margin: 0 0 0.25rem;">Ready to search</p>
                <p style="font-size: 0.8rem; color: #CBD5E1; margin: 0;">Enter a generic or brand name above to query all databases.</p>
            </div>

            <!-- Active results (hidden until search) -->
            <div id="resultsArea" style="display: none;">
                <div class="results-topbar">
                    <span id="resultsLabel" class="results-label"></span>
                    <button class="clear-btn" onclick="clearSearch()">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 0.3rem; vertical-align: -1px;"><path d="M18 6 6 18M6 6l12 12"></path></svg>
                        Clear Results
                    </button>
                </div>

                <div class="results-columns">
                    <!-- Left: Global openFDA -->
                    <div class="results-col">
                        <div class="col-header">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                            <span class="col-title">Global Database</span>
                            <span class="col-meta">Source: openFDA</span>
                        </div>
                        <div id="globalResults"></div>
                    </div>

                    <!-- Vertical divider -->
                    <div class="col-divider"></div>

                    <!-- Right: Local Stock -->
                    <div class="results-col">
                        <div class="col-header">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            <span class="col-title">Local availability</span>
                            <span class="col-meta">Location: Basud, CN</span>
                        </div>
                        <div id="localResults"></div>
                    </div>
                </div>
            </div>

        </div>

    </main>
</div>

<?php require_once 'includes/footer.php'; ?>

<script>
    document.getElementById('searchInput').addEventListener('keypress', e => {
        if (e.key === 'Enter') searchMedications();
    });

    const MAX_STOCK = 500; // for progress bar scaling

    function spinnerHTML() {
        return `<div class="ld-spinner-wrap"><div class="ld-spinner"></div><span>Fetching…</span></div>`;
    }

    function emptyHTML(title, sub) {
        return `<div class="ld-empty">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:#CBD5E1;margin-bottom:0.6rem;"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
            <p class="ld-empty-title">${title}</p>
            <p class="ld-empty-sub">${sub}</p>
        </div>`;
    }

    function searchMedications() {
        const query = document.getElementById('searchInput').value.trim();
        if (!query) { document.getElementById('searchInput').focus(); return; }

        document.getElementById('emptyState').style.display = 'none';
        document.getElementById('resultsArea').style.display = 'block';
        document.getElementById('resultsLabel').innerHTML = `Showing results for &ldquo;<strong>${query}</strong>&rdquo;`;

        document.getElementById('globalResults').innerHTML = spinnerHTML();
        document.getElementById('localResults').innerHTML  = spinnerHTML();

        // ── Local MySQL ──
        fetch(`api/search.php?query=${encodeURIComponent(query)}`)
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success' && res.data && res.data.length > 0) {
                    let html = '';
                    res.data.forEach(med => {
                        const stock    = parseInt(med.stock) || 0;
                        const maxStock = MAX_STOCK;
                        const pct      = Math.min(100, Math.round((stock / maxStock) * 100));
                        const barColor = stock === 0 ? '#EF4444' : (stock < 30 ? '#F59E0B' : '#2563EB');
                        const price    = parseFloat(med.price).toFixed(2);

                        html += `
                        <div class="local-card">
                            <div class="local-card-top">
                                <div class="local-card-info">
                                    <p class="local-drug-name">${med.brand_name} <span class="local-generic">(${med.generic_name})</span></p>
                                    <a class="local-pharmacy-link">${med.pharmacy_name}</a>
                                </div>
                                <div class="local-price-wrap">
                                    <span class="local-price">₱${price}</span>
                                    <span class="local-per">PER PIECE</span>
                                </div>
                            </div>
                            <div class="local-stock-bar-wrap">
                                <div class="local-stock-bar">
                                    <div class="local-stock-fill" style="width:${pct}%; background:${barColor};"></div>
                                </div>
                                <span class="local-stock-label">${stock === 0 ? 'Out of stock' : stock + ' in stock'}</span>
                            </div>
                        </div>`;
                    });
                    document.getElementById('localResults').innerHTML = html;
                } else {
                    document.getElementById('localResults').innerHTML = emptyHTML('No local records', 'No matching stocks found across Basud pharmacies.');
                }
            })
            .catch(() => {
                document.getElementById('localResults').innerHTML = emptyHTML('Query failed', 'Could not reach the local database.');
            });

        // ── openFDA Global ──
        const fdaUrl = `https://api.fda.gov/drug/label.json?search=openfda.generic_name:${encodeURIComponent(query)}*+openfda.brand_name:${encodeURIComponent(query)}*&limit=5`;
        fetch(fdaUrl)
            .then(r => r.ok ? r.json() : { results: [] })
            .then(data => {
                const list = data.results || [];
                if (list.length > 0) {
                    let html = '';
                    list.forEach(drug => {
                        const brand   = drug.openfda?.brand_name?.[0] ?? 'Generic Reference';
                        const generic = drug.openfda?.generic_name?.[0] ?? query;
                        const mfr     = drug.openfda?.manufacturer_name?.[0] ?? '—';
                        const use     = drug.indications_and_usage?.[0]?.slice(0, 200) + '…' ?? 'No indication on file.';

                        html += `
                            <div class="fda-card">
                                <div class="fda-card-header">
                                    <div>
                                        <p class="fda-brand">${cap(brand)}</p>
                                        <p class="fda-generic">${cap(generic)}</p>
                                    </div>
                                    <span class="fda-badge">✓ FDA APPROVED</span>
                                </div>
                                <p class="fda-indication">${use}</p>
                                <p class="fda-mfr"><span class="fda-mfr-label">Lab</span>${cap(mfr)}</p>
                            </div>`;
                    });
                    document.getElementById('globalResults').innerHTML = html;
                } else {
                    document.getElementById('globalResults').innerHTML = emptyHTML('No openFDA Records', 'No globally verified matches found in US FDA database.');
                }
            })
            .catch(() => {
                document.getElementById('globalResults').innerHTML = emptyHTML('openFDA Offline', 'Could not reach the FDA database right now.');
            });
    }

    function clearSearch() {
        document.getElementById('searchInput').value = '';
        document.getElementById('resultsArea').style.display = 'none';
        document.getElementById('emptyState').style.display  = 'flex';
        document.getElementById('searchInput').focus();
    }

    function cap(str) {
        return str.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
    }
</script>

</body>
</html>