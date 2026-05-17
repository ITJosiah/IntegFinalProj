<?php
// customer_about.php
$activePage = 'about';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About & Technology - PharmaSync</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/customer_about.css">
</head>
<body>

<!-- Shared navbar -->
<?php require_once 'includes/navbar.php'; ?>

<div class="layout">
    <main class="main-content">

        <!-- ── HERO HEADER SECTION ── -->
        <section class="about-hero">
            <p class="about-tagline">PLATFORM TECHNOLOGY & SPECS</p>
            <h1 class="about-title">
                Smart health-system<br>
                <span>decentralized middleware</span>
            </h1>
            <p class="about-subtitle">
                PharmaSync is a guest-friendly lookup portal serving Basud, Camarines Norte. 
                Our platform integrates local POS inventory databases with modern API intelligence in real-time.
            </p>
        </section>

        <!-- ── 4 SPECIFICATION CARDS ── -->
        <section class="about-grid">
            <div class="spec-card">
                <div class="spec-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                </div>
                <h3 class="spec-title">Option B Normalization</h3>
                <p class="spec-desc">
                    Features an advanced relational structure: active ingredients map to the parent entity (`medicines`) while commercial strength options store as child SKUs (`products`), completely eliminating redundancies.
                </p>
            </div>

            <div class="spec-card">
                <div class="spec-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                </div>
                <h3 class="spec-title">Decentralized Webhook Sync</h3>
                <p class="spec-desc">
                    Utilizes REST middleware synchronization: transactional sales and stock counts from independent physical pharmacies trigger instant inventory updates in the master central hub database.
                </p>
            </div>

            <div class="spec-card">
                <div class="spec-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                </div>
                <h3 class="spec-title">openFDA Integrations</h3>
                <p class="spec-desc">
                    Parallel-source search: queries both your local MySQL stock and the official US FDA database simultaneously to deliver robust clinical dosage information, warnings, and usage advice instantly.
                </p>
            </div>

            <div class="spec-card">
                <div class="spec-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                </div>
                <h3 class="spec-title">Official Google Maps Embed</h3>
                <p class="spec-desc">
                    Features official geocoder coordinates targeting the registered business listings directly. Click locate to drop precise pins and display real directions for verified stores along the highway.
                </p>
            </div>
        </section>

        <!-- ── SUGGESTIONS & IMPROVEMENTS SPLIT SECTION ── -->
        <section class="suggestion-section">
            
            <!-- Left Column: Submit form -->
            <div class="glass-card">
                <h2 class="section-title">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    Suggest Improvements
                </h2>
                <p style="color:var(--text-muted); font-size:0.875rem; margin-bottom:1.5rem; line-height:1.5;">
                    Help us improve our community search! Submit features you want added. Submissions are instantly visible on the public feed.
                </p>

                <form id="suggestionForm" onsubmit="submitSuggestion(event)">
                    <div class="form-group">
                        <label for="guestName">YOUR NAME (Optional)</label>
                        <input type="text" id="guestName" name="name" class="form-input" placeholder="Anonymous Resident">
                    </div>

                    <div class="form-group">
                        <label for="guestEmail">EMAIL ADDRESS (Optional)</label>
                        <input type="email" id="guestEmail" name="email" class="form-input" placeholder="your.email@example.com">
                    </div>

                    <div class="form-group">
                        <label for="suggCat">CATEGORY</label>
                        <select id="suggCat" name="category" class="form-input" style="height: 48px;" required>
                            <option value="General">General Suggestion</option>
                            <option value="Feature Request">New Feature Request</option>
                            <option value="UI/UX">UI/UX Improvement</option>
                            <option value="Database">Database Improvement</option>
                            <option value="Payment">Payment Options</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="suggText">YOUR SUGGESTION</label>
                        <textarea id="suggText" name="suggestion" class="form-input" rows="4" placeholder="I would like to see..." required style="resize: none;"></textarea>
                    </div>

                    <button type="submit" class="form-submit-btn">
                        Submit Feature Request
                    </button>
                </form>
            </div>

            <!-- Right Column: Live Board -->
            <div>
                <h2 class="section-title">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    Live Feature Roadmap
                </h2>
                
                <div class="feed-container" id="suggestionsFeed">
                    <!-- Suggestions will be loaded here dynamically -->
                    <div style="text-align:center; padding:3rem; color:var(--text-muted);">
                        Loading community feedback board...
                    </div>
                </div>
            </div>

        </section>

    </main>
</div>

<!-- Shared footer -->
<?php require_once 'includes/footer.php'; ?>

<script>
    // Fetch and display suggestions on page load
    function loadSuggestions() {
        fetch('api/get_suggestions.php')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    displaySuggestions(res.data);
                } else {
                    document.getElementById('suggestionsFeed').innerHTML = `
                        <div style="text-align:center; padding:3rem; color:var(--danger);">
                            Failed to load board: ${res.message}
                        </div>`;
                }
            });
    }

    // Display list
    function displaySuggestions(data) {
        const feed = document.getElementById('suggestionsFeed');
        if (data.length === 0) {
            feed.innerHTML = `
                <div style="text-align:center; padding:3rem; color:var(--text-muted);">
                    No suggestions submitted yet. Be the first to share feedback!
                </div>`;
            return;
        }

        // Get already voted IDs from localStorage
        const votedIds = JSON.parse(localStorage.getItem('pharma_upvoted_ids') || '[]');

        feed.innerHTML = data.map(item => {
            const hasVoted = votedIds.includes(item.id.toString());
            const activeClass = hasVoted ? 'active' : '';
            const catClass = getCategoryClass(item.category);

            return `
                <div class="suggestion-card animate-fade" id="sugg-card-${item.id}">
                    <div class="suggestion-content">
                        <div class="suggestion-header">
                            <span class="suggestion-author">${escapeHTML(item.name)}</span>
                            <span class="suggestion-date">${item.formatted_date}</span>
                            <span class="badge-cat ${catClass}">${escapeHTML(item.category)}</span>
                        </div>
                        <div class="suggestion-text">${escapeHTML(item.suggestion)}</div>
                    </div>
                    
                    <div class="upvote-box ${activeClass}" onclick="upvoteSuggestion(${item.id}, this)">
                        <span class="upvote-arrow">▲</span>
                        <span class="upvote-count" id="upvote-count-${item.id}">${item.upvotes}</span>
                    </div>
                </div>
            `;
        }).join('');
    }

    // Map categories to badges
    function getCategoryClass(cat) {
        switch (cat) {
            case 'UI/UX': return 'ui';
            case 'New Feature Request':
            case 'Feature Request': return 'feature';
            case 'Database': return 'database';
            case 'Payment': return 'payment';
            default: return 'general';
        }
    }

    // Handle AJAX Suggestion Submission
    function submitSuggestion(event) {
        event.preventDefault();

        // Anti-Spam Rate Limiter Check
        const lastSubmit = sessionStorage.getItem('last_suggestion_timestamp');
        const now = Date.now();
        if (lastSubmit && (now - lastSubmit < 30000)) {
            alert('Please wait 30 seconds before submitting another suggestion.');
            return;
        }

        const nameInput = document.getElementById('guestName').value.trim();
        const emailInput = document.getElementById('guestEmail').value.trim();
        const categoryInput = document.getElementById('suggCat').value;
        const suggestionInput = document.getElementById('suggText').value.trim();

        const payload = {
            name: nameInput,
            email: emailInput,
            category: categoryInput,
            suggestion: suggestionInput
        };

        fetch('api/submit_suggestion.php?action=submit', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                sessionStorage.setItem('last_suggestion_timestamp', Date.now());
                document.getElementById('suggestionForm').reset();
                
                // Show success notification & reload feed
                alert(res.message);
                loadSuggestions();
            } else {
                alert('Submission error: ' + res.message);
            }
        });
    }

    // Handle LocalStorage cached upvote increment
    function upvoteSuggestion(id, element) {
        const stringId = id.toString();
        let votedIds = JSON.parse(localStorage.getItem('pharma_upvoted_ids') || '[]');

        // Check if already voted
        if (votedIds.includes(stringId)) {
            alert('You have already upvoted this suggestion!');
            return;
        }

        // Send AJAX Upvote Request
        fetch(`api/submit_suggestion.php?action=upvote&id=${id}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    // Update LocalStorage to cache vote
                    votedIds.push(stringId);
                    localStorage.setItem('pharma_upvoted_ids', JSON.stringify(votedIds));

                    // Update UI State instantly
                    element.classList.add('active');
                    const countSpan = document.getElementById(`upvote-count-${id}`);
                    countSpan.textContent = parseInt(countSpan.textContent) + 1;
                } else {
                    alert('Upvote failed: ' + res.message);
                }
            });
    }

    // Escape HTML helper
    function escapeHTML(str) {
        return str.replace(/[&<>'"]/g, 
            tag => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                "'": '&#39;',
                '"': '&quot;'
            }[tag] || tag)
        );
    }

    // Initialize Feed
    loadSuggestions();
</script>

</body>
</html>
