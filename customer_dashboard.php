<?php
require_once 'includes/session_guard.php';

/** @var string $greeting Provided by includes/session_guard.php */

$activePage = 'profile';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Portal - PharmaDirect</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/customer_dashboard.css">
</head>
<body>

<!-- Shared navbar -->
<?php require_once 'includes/navbar.php'; ?>

<div class="layout">
    <main class="main-content">

        <div class="hero-section">
            <h1 class="hero-title">
                <?php echo $greeting; ?>, <?php echo htmlspecialchars($_SESSION['user_name']); ?>.
            </h1>
            <p class="hero-subtitle">
                Your prescriptions are tracked and synchronized with local Basud pharmacies.<br>
                Everything is on schedule.
            </p>
        </div>

        <div class="dashboard-grid">

            <div class="card">
                <div class="card-header">
                    <span class="card-title">Active Medications</span>
                    <a href="#" class="card-link">View All</a>
                </div>

                <div class="med-item">
                    <div class="med-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                    </div>
                    <div class="med-info">
                        <p class="med-name">Lisinopril 10mg</p>
                        <p class="med-dose">1 tablet daily for blood pressure</p>
                        <div class="med-meta">
                            <span>Last Refilled: 12 Oct</span>
                            <span class="dot"></span>
                            <span>Next Refill: Nov 10</span>
                        </div>
                    </div>
                    <span class="med-badge active">Active</span>
                </div>

                <div class="med-item">
                    <div class="med-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                    </div>
                    <div class="med-info">
                        <p class="med-name">Metformin 500mg</p>
                        <p class="med-dose">2 tablets daily with meals</p>
                        <div class="med-meta">
                            <span>Last Refilled: 05 Oct</span>
                            <span class="dot"></span>
                            <span>Next Refill: Nov 05</span>
                        </div>
                    </div>
                    <span class="med-badge active">Active</span>
                </div>
            </div>

            <!-- Quick Actions Panel -->
            <div class="quick-actions">
                <p class="card-title">Quick Actions</p>
                <div class="action-btns-row">
                    <a href="#" class="action-btn primary">
                        Refill Request
                        <span class="action-btn-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2.5">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </span>
                    </a>
                    <a href="#" class="action-btn light">
                        Find Pharmacy
                        <span class="action-btn-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                </svg>
                            </span>
                    </a>
                    <a href="#" class="action-btn ghost">
                        Chat with Pharmacist
                        <span class="action-btn-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                </svg>
                            </span>
                    </a>
                </div>
            </div>

        </div>

        <!-- MEDICATION SEARCH SECTION -->
        <div style="margin-top: 2rem;">
            <div class="hero-section" style="margin-bottom: 1.25rem;">
                <h2 style="font-size:1.25rem;font-weight:700;letter-spacing:-0.02em;margin:0 0 0.25rem;">
                    Find your medication
                </h2>
                <p style="font-size:0.875rem;color:var(--text-muted);margin:0;">
                    Search across global databases and local stock in Basud effortlessly.
                </p>
            </div>

            <div class="search-container">
                <div class="search-box">
                    <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input
                            type="text"
                            id="searchInput"
                            class="search-input"
                            placeholder="Search Medicines Globally & Locally"
                            autocomplete="off"
                    >
                    <button class="search-btn" onclick="searchMedication()">Search</button>
                </div>
                <div class="source-badges">
                    <button class="badge badge-global" id="globalBadge">GLOBAL: OPENFDA</button>
                    <button class="badge badge-local" id="localBadge">LOCAL: MYSQL DB</button>
                </div>
            </div>

            <div id="resultsSection" class="results-section"></div>
        </div>

        <!-- BOTTOM WIDGETS -->
        <div class="bottom-widgets">

            <!-- Nearby Pharmacies -->
            <div class="pharmacy-card">
                <div class="pharmacy-map">
                    <div class="map-placeholder">
                        <svg class="map-grid" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <pattern id="grid" width="15" height="15" patternUnits="userSpaceOnUse">
                                    <path d="M 15 0 L 0 0 0 15" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1"/>
                                </pattern>
                            </defs>
                            <rect width="100%" height="100%" fill="url(#grid)" />
                            <path d="M-10 50 L150 150 M50 -10 L150 200 M20 180 L200 40"
                                  stroke="rgba(255,255,255,0.5)" stroke-width="3" fill="none"/>
                        </svg>
                        <div class="map-pin"></div>
                    </div>
                </div>
                <div class="pharmacy-info">
                    <h3 class="widget-title">Nearby Pharmacies</h3>
                    <div class="pharmacy-list">
                        <div class="pharmacy-item">
                            <div class="pharm-details">
                                <h4>Basud Pharma Central</h4>
                                <p>2.4 km away • Purok 2, Basud</p>
                            </div>
                            <span class="status-badge open">OPEN<br>NOW</span>
                        </div>
                        <div class="pharmacy-item">
                            <div class="pharm-details">
                                <h4>Cam Norte Health Drug</h4>
                                <p>4.1 km away • San Pablo St.</p>
                            </div>
                            <span class="status-badge closed">CLOSES<br>6PM</span>
                        </div>
                    </div>
                    <a href="#" class="browse-link">Browse All 12 Locations</a>
                </div>
            </div>

            <!-- Health Insight -->
            <div class="insight-card">
                <div class="insight-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 18h6"></path>
                        <path d="M10 22h4"></path>
                        <path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1.45.62 2.84 1.5 3.5.76.76 1.23 1.52 1.41 2.5"></path>
                    </svg>
                </div>
                <h3 class="widget-title">Health Insight</h3>
                <p class="insight-text">
                    Consistency is key. Your Lisinopril works best when taken at the same time every day.
                    Try setting an alarm for 8:00 AM.
                </p>
                <div class="adherence-section">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 88%;"></div>
                    </div>
                    <span class="adherence-text">MEDICATION ADHERENCE: 88%</span>
                </div>
            </div>

        </div>

    </main>
</div>

<!-- ── 6. Shared footer component ── -->
<?php require_once 'includes/footer.php'; ?>

<script>
    /* ── SOURCE BADGE TOGGLE ── */
    let searchSource = 'both';

    document.getElementById('globalBadge').addEventListener('click', () => toggleSource('global'));
    document.getElementById('localBadge').addEventListener('click',  () => toggleSource('local'));

    function toggleSource(source) {
        const gb = document.getElementById('globalBadge');
        const lb = document.getElementById('localBadge');

        if (searchSource === source) {
            searchSource = 'both';
            gb.className = 'badge badge-global';
            lb.className = 'badge badge-local';
        } else if (searchSource === 'both') {
            searchSource = source;
            if (source === 'global') {
                gb.className = 'badge badge-local';
                lb.className = 'badge badge-global';
            } else {
                gb.className = 'badge badge-global';
                lb.className = 'badge badge-local';
            }
        } else {
            searchSource = 'both';
            gb.className = 'badge badge-global';
            lb.className = 'badge badge-local';
        }
    }

    /* ── SEARCH ── */
    document.getElementById('searchInput').addEventListener('keypress', e => {
        if (e.key === 'Enter') searchMedication();
    });

    function searchMedication() {
        const query = document.getElementById('searchInput').value.trim();
        if (!query) {
            alert('Please enter a medication name to search');
            return;
        }

        const section = document.getElementById('resultsSection');
        section.classList.add('active');
        section.innerHTML = `
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Searching for "<strong>${query}</strong>"…</p>
                </div>`;

        setTimeout(() => fetchResults(query), 900);
    }

    function fetchResults(query) {
        fetch('search_medication.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `query=${encodeURIComponent(query)}&source=${searchSource}`
        })
            .then(r => r.json())
            .then(data => displayResults(data))
            .catch(() => {
                document.getElementById('resultsSection').innerHTML = `
                    <div class="no-results">
                        <p>An error occurred while searching. Please try again.</p>
                    </div>`;
            });
    }

    function displayResults(data) {
        const section = document.getElementById('resultsSection');

        if (!data || data.length === 0) {
            section.innerHTML = `
                    <div class="no-results">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.5"
                             style="color:var(--text-faint);">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <p>No medications found. Try a different search term.</p>
                    </div>`;
            return;
        }

        section.innerHTML = data.map(med => `
                <div class="result-card">
                    <div class="result-header">
                        <h3 class="result-name">${med.name}</h3>
                        <span class="result-badge ${med.stock > 10 ? 'badge-available' : 'badge-limited'}">
                            ${med.stock > 10 ? 'Available' : 'Limited Stock'}
                        </span>
                    </div>
                    <p style="color:var(--text-muted);font-size:0.875rem;margin:0.35rem 0 0;">
                        ${med.description}
                    </p>
                    <div class="result-details">
                        <div class="detail-item">
                            <span class="detail-label">Generic Name</span>
                            <span class="detail-value">${med.generic_name}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Dosage</span>
                            <span class="detail-value">${med.dosage}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Price</span>
                            <span class="detail-value">₱${med.price}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Stock</span>
                            <span class="detail-value">${med.stock} units</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Source</span>
                            <span class="detail-value">${med.source}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Pharmacy</span>
                            <span class="detail-value">${med.pharmacy}</span>
                        </div>
                    </div>
                </div>
            `).join('');
    }
</script>

</body>
</html>