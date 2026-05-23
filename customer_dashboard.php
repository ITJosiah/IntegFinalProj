<?php
// ── 1. Shared session logic ──
require_once 'includes/session_guard.php';

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
    let activeFdaResults = [];

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

                        const safePharmaName = med.pharmacy_name ? med.pharmacy_name.replace(/"/g, '&quot;') : '';
                        const safeAddress = med.pharmacy_address ? med.pharmacy_address.replace(/"/g, '&quot;') : '';
                        const safeContact = med.contact_number ? med.contact_number.replace(/"/g, '&quot;') : '';

                        html += `
                        <div class="local-card" data-name="${safePharmaName}" data-address="${safeAddress}" data-contact="${safeContact}" data-open="${med.is_open}" onclick="showPharmaDetails(this)">
                            <div class="local-card-top">
                                <div class="local-card-info">
                                    <p class="local-drug-name">
                                        ${med.brand_name} 
                                        <span class="local-generic">(${med.generic_name})</span>
                                        <span class="drug-badge ${parseInt(med.prescription_required) === 1 ? 'rx' : 'otc'}">${parseInt(med.prescription_required) === 1 ? 'Rx' : 'OTC'}</span>
                                    </p>
                                    <span class="local-pharmacy-link">${med.pharmacy_name}</span>
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
                activeFdaResults = list;
                if (list.length > 0) {
                    let html = '';
                    list.forEach((drug, index) => {
                        const brand   = drug.openfda?.brand_name?.[0] ?? 'Generic Reference';
                        const generic = drug.openfda?.generic_name?.[0] ?? query;
                        const mfr     = drug.openfda?.manufacturer_name?.[0] ?? '—';
                        const use     = drug.indications_and_usage?.[0] ? (drug.indications_and_usage[0].slice(0, 180) + '...') : 'No indication on file.';

                        html += `
                            <div class="fda-card" onclick="showFdaDetails(${index})">
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

    function formatClinicalText(text) {
        if (!text) return 'No data';
        
        // Check if there are raw bullet characters or typical list markers
        const hasBullets = /[\*•■]/.test(text) || text.includes('\n- ') || text.includes('\n* ');
        
        if (hasBullets) {
            // Replace various bullet characters with ||| to split cleanly
            let cleanedText = text
                .replace(/[\*•■]/g, '|||')
                .replace(/\n- /g, '|||')
                .replace(/\n\* /g, '|||');
                
            const parts = cleanedText.split('|||').map(p => p.trim()).filter(p => p.length > 0);
            
            if (parts.length > 1) {
                let html = `<p class="modal-intro-text">${parts[0]}</p>`;
                html += `<ul class="modal-bullet-list">`;
                for (let i = 1; i < parts.length; i++) {
                    html += `<li>${parts[i]}</li>`;
                }
                html += `</ul>`;
                return html;
            }
        }
        
        if (text.includes('\n')) {
            const lines = text.split('\n').map(l => l.trim()).filter(l => l.length > 0);
            return lines.map(line => `<p class="modal-para-text">${line}</p>`).join('');
        }
        
        return `<p class="modal-para-text">${text}</p>`;
    }

    function showFdaDetails(index) {
        const drug = activeFdaResults[index];
        if (!drug) return;

        const brand = drug.openfda?.brand_name?.[0] ?? 'Generic Reference';
        const generic = drug.openfda?.generic_name?.[0] ?? 'No Generic Name';
        const mfr = drug.openfda?.manufacturer_name?.[0] ?? '—';
        const use = drug.indications_and_usage?.[0] ?? 'No clinical indications on file.';
        const dosage = drug.dosage_and_administration?.[0] ?? '';
        const warnings = drug.warnings?.[0] ?? drug.warnings_and_precautions?.[0] ?? '';
        const active = drug.active_ingredient?.[0] ?? 'No active ingredient listed.';

        document.getElementById('modalBrandName').innerText = cap(brand);
        document.getElementById('modalGenericName').innerText = cap(generic);
        document.getElementById('modalIndication').innerHTML = formatClinicalText(use);
        document.getElementById('modalActive').innerHTML = formatClinicalText(active);
        document.getElementById('modalMfr').innerHTML = formatClinicalText(mfr);

        const dosageSec = document.getElementById('modalDosageSection');
        if (dosage) {
            document.getElementById('modalDosage').innerHTML = formatClinicalText(dosage);
            dosageSec.style.display = 'flex';
        } else {
            dosageSec.style.display = 'none';
        }

        const warnSec = document.getElementById('modalWarningsSection');
        if (warnings) {
            document.getElementById('modalWarnings').innerHTML = formatClinicalText(warnings);
            warnSec.style.display = 'flex';
        } else {
            warnSec.style.display = 'none';
        }

        const modal = document.getElementById('fdaModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeFdaModal() {
        const modal = document.getElementById('fdaModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    function showPharmaDetails(element) {
        const name = element.getAttribute('data-name');
        const address = element.getAttribute('data-address');
        const contact = element.getAttribute('data-contact');
        const isOpen = element.getAttribute('data-open') === '1' || element.getAttribute('data-open') === 'true';

        document.getElementById('pharmaModalName').innerText = name;
        
        const statusEl = document.getElementById('pharmaModalStatus');
        if (isOpen) {
            statusEl.innerText = 'OPEN';
            statusEl.className = 'pharm-status open';
        } else {
            statusEl.innerText = 'CLOSED';
            statusEl.className = 'pharm-status closed';
        }

        document.getElementById('pharmaModalAddress').innerText = address || 'No address listed';
        document.getElementById('pharmaModalPhone').innerText = contact || 'No contact number listed';

        const mapQuery = encodeURIComponent(name + ", Basud, Camarines Norte");
        document.getElementById('pharmaModalMap').src = `https://maps.google.com/maps?q=${mapQuery}&t=&z=16&ie=UTF8&iwloc=&output=embed`;

        const modal = document.getElementById('pharmacyModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closePharmaModal() {
        const modal = document.getElementById('pharmacyModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
        document.getElementById('pharmaModalMap').src = '';
    }

    window.addEventListener('click', e => {
        const fdaModal = document.getElementById('fdaModal');
        const pharmaModal = document.getElementById('pharmacyModal');
        if (e.target === fdaModal) {
            closeFdaModal();
        }
        if (e.target === pharmaModal) {
            closePharmaModal();
        }
    });
</script>

<!-- ── FDA CLINICAL DETAIL MODAL ── -->
<div id="fdaModal" class="fda-modal">
    <div class="fda-modal-content">
        <div class="fda-modal-header">
            <div>
                <h2 id="modalBrandName" class="modal-brand">Brand Name</h2>
                <p id="modalGenericName" class="modal-generic">Generic Name</p>
            </div>
            <span class="modal-close" onclick="closeFdaModal()">&times;</span>
        </div>
        <div class="fda-modal-body">
            <div class="modal-section">
                <h4 class="section-subtitle">Clinical Indications (Uses)</h4>
                <div id="modalIndication" class="modal-text">...</div>
            </div>
            
            <div id="modalDosageSection" class="modal-section">
                <h4 class="section-subtitle">Dosage & Administration</h4>
                <div id="modalDosage" class="modal-text">...</div>
            </div>

            <div id="modalWarningsSection" class="modal-section">
                <h4 class="section-subtitle">Warnings & Precautions</h4>
                <div id="modalWarnings" class="modal-text">...</div>
            </div>

            <div class="modal-section-grid">
                <div class="modal-section">
                    <h4 class="section-subtitle">Active Ingredients</h4>
                    <div id="modalActive" class="modal-text">...</div>
                </div>
                <div class="modal-section">
                    <h4 class="section-subtitle">Manufacturer / Lab</h4>
                    <div id="modalMfr" class="modal-text">...</div>
                </div>
            </div>
        </div>
        <div class="fda-modal-footer" style="justify-content: center;">
            <span class="fda-badge">✓ FDA APPROVED REFERENCE</span>
        </div>
    </div>
</div>

<!-- ── PHARMACY LOCATION DETAIL MODAL ── -->
<div id="pharmacyModal" class="fda-modal">
    <div class="fda-modal-content">
        <div class="fda-modal-header">
            <div>
                <h2 id="pharmaModalName" class="modal-brand">Pharmacy Name</h2>
                <div style="margin-top: 0.5rem; display: flex; align-items: center;">
                    <span id="pharmaModalStatus" class="pharm-status">OPEN</span>
                </div>
            </div>
            <span class="modal-close" onclick="closePharmaModal()">&times;</span>
        </div>
        <div class="fda-modal-body">
            <div class="modal-section-grid">
                <div class="modal-section">
                    <h4 class="section-subtitle">Address</h4>
                    <div id="pharmaModalAddress" class="modal-text">...</div>
                </div>
                <div class="modal-section">
                    <h4 class="section-subtitle">Contact Number</h4>
                    <div id="pharmaModalPhone" class="modal-text">...</div>
                </div>
            </div>
            
            <div class="modal-section">
                <h4 class="section-subtitle">Location Map</h4>
                <!-- Google Maps Embed Container -->
                <iframe id="pharmaModalMap" src="" style="width: 100%; height: 280px; border-radius: 8px; border: 1px solid var(--border); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); z-index: 1;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
        <div class="fda-modal-footer" style="justify-content: center;">
            <span class="fda-badge" id="pharmaModalBadge">✓ REGISTERED PHARMACY</span>
        </div>
    </div>
</div>

</body>
</html>