<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php?error=unauthorized');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Middleware Console - PharmaSync</title>
    <link rel="stylesheet" href="assets/css/index.css">
    <style>
        .nodes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .node-card {
            background: white;
            padding: 1.75rem;
            border-radius: 1rem;
            border: 1px solid #E2E8F0;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .node-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, #3B82F6 100%);
        }

        .node-card.offline::before {
            background: linear-gradient(90deg, var(--danger) 0%, #EF4444 100%);
        }

        .node-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08);
            border-color: #CBD5E1;
        }

        .node-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .node-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .badge-code {
            background: #EFF6FF;
            color: var(--primary);
            padding: 0.25rem 0.6rem;
            font-size: 0.75rem;
            border-radius: 0.5rem;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        .node-status-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.25rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px dashed #E2E8F0;
        }

        .status-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-dot.active {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            }

            70% {
                box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        .status-dot.danger-pulse {
            animation: pulse-danger 2s infinite;
        }

        @keyframes pulse-danger {
            0% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
            }

            70% {
                box-shadow: 0 0 0 8px rgba(239, 68, 68, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        .node-details {
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            margin-bottom: 1.5rem;
        }

        .node-detail-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-size: 0.875rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .node-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .icon-muted {
            color: #94A3B8;
            flex-shrink: 0;
        }

        .node-card-footer {
            font-size: 0.75rem;
            font-weight: 700;
            color: #94A3B8;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding-top: 0.75rem;
            border-top: 1px solid #F1F5F9;
            margin-top: auto;
        }

        .metric-card {
            background: #F0F7FF; /* Premium soft light blue */
            padding: 1.75rem;
            border-radius: 1rem;
            border: 1px solid #D1E7FF; /* Soft blue border */
            box-shadow: var(--card-shadow);
        }

        .log-stream {
            background: #0F172A;
            color: #38BDF8;
            font-family: monospace;
            padding: 1.25rem;
            border-radius: 0.5rem;
            height: 380px;
            overflow-y: auto;
            font-size: 0.85rem;
            line-height: 1.5;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.5);
            border: 1px solid #1E293B;
        }

        .log-stream::-webkit-scrollbar {
            width: 8px;
        }

        .log-stream::-webkit-scrollbar-track {
            background: #1E293B;
            border-radius: 4px;
        }

        .log-stream::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 4px;
        }

        /* Modal Styles matching Customer Dashboard */
        .fda-modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            align-items: flex-start;
            justify-content: center;
            padding: 4rem 1.5rem 1.5rem 1.5rem;
        }

        .fda-modal-content {
            background: white;
            border-radius: 1.25rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid #E2E8F0;
            max-width: 600px;
            width: 100%;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: fadeUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .fda-modal-header {
            padding: 1.5rem 1.75rem;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            background: #FAFBFD;
        }

        .fda-modal-body {
            padding: 1.5rem;
            overflow-y: auto;
            flex: 1;
            background: #F8FAFC;
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="brand" style="display:flex; align-items:center; gap:0.35rem;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <span><span style="color: var(--primary);">Pharma</span><span>Sync</span></span>
            </a>
            <div class="nav-links">
                <span onclick="openEditProfileModal()" class="portal-badge"
                    style="background: #EFF6FF; color: var(--primary); font-weight: 700; font-size: 0.8rem; padding: 0.4rem 1rem; border-radius: 2rem; text-transform: uppercase; display: inline-flex; align-items: center; margin-right: 0.75rem; letter-spacing: 0.05em; cursor: pointer; transition: all 0.2s ease;">PORTAL:
                    <span id="portalUserName"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span></span>
                <a href="logout.php" class="btn-switch-role">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container animate-fade" style="margin-top:2rem; padding: 0 2rem;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 style="font-size: 2.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem;">System
                Admin Console</h1>
        </div>


        <!-- System Activity Row -->
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem; margin-bottom:3rem;">
            <!-- Left: Active Middleware Node Status -->
            <div class="metric-card" style="display: flex; flex-direction: column;">
                <h3 style="margin-bottom:1rem; font-weight:800; display:flex; align-items:center; gap:0.5rem; color: var(--text-main);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                        <rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect>
                        <rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect>
                        <line x1="6" y1="6" x2="6.01" y2="6"></line>
                        <line x1="6" y1="18" x2="6.01" y2="18"></line>
                    </svg>
                    Active Middleware Node Status
                </h3>
                <div style="flex: 1; padding: 1.5rem; background: white; border-radius: 0.75rem; border: 1px solid #D1E7FF; overflow-y: auto; max-height: 400px; padding-right: 0.5rem;">
                    <div class="nodes-grid" id="nodeContainer" style="margin-bottom: 0; display: grid; grid-template-columns: 1fr; gap: 1rem;">
                        <!-- Dynamically populated node cards -->
                        <div style="text-align: center; padding: 3rem; color: var(--text-muted); background: #F8FAFC; border-radius: 0.5rem; border: 1px dashed #CBD5E1;">
                            Loading active node connections...
                        </div>
                    </div>
                </div>
                <div style="text-align: right; margin-top: 1rem; border-top: 1px solid #E2E8F0; padding-top: 1rem;">
                    <button class="btn btn-primary" onclick="openAddPharmacyModal()" style="font-weight: 700; display: inline-flex; align-items: center; gap: 0.5rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Register Pharmacy
                    </button>
                </div>
            </div>

            <!-- Right: Resident Inquiries Inbox -->
            <div class="metric-card" style="display: flex; flex-direction: column;">
                <h3 style="margin-bottom:1.25rem; font-weight:800; display:flex; align-items:center; gap:0.5rem; color: var(--text-main);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    Resident Inquiries Inbox
                </h3>
                <div style="flex: 1; overflow-y: auto; max-height: 380px; padding-right: 0.5rem;" id="suggestionInboxBox">
                    <div style="text-align:center; padding:3rem; color:var(--text-muted); font-size: 0.9rem;">
                        Loading suggestions inbox...
                    </div>
                </div>
            </div>
        </div>


    </main>

    <!-- Edit Profile Modal -->
    <div id="editProfileModal" class="fda-modal">
        <div class="fda-modal-content" style="max-width: 450px;">
            <div class="fda-modal-header">
                <div>
                    <h3 style="margin:0; font-weight:800; color:var(--text-main); font-size: 1.4rem;">
                        Edit Profile
                    </h3>
                    <p style="color: var(--primary); font-weight: 600; font-size: 0.9rem; margin: 0.25rem 0 0 0;">Update your administrator details</p>
                </div>
                <button onclick="closeEditProfileModal()" style="background:none; border:none; font-size:1.75rem; color:#94A3B8; cursor:pointer; line-height:1;">&times;</button>
            </div>
            <div class="fda-modal-body">
                <form id="editProfileForm" onsubmit="submitEditProfile(event)">
                        <div id="editProfileAlert" class="alert-banner" style="display:none; margin-bottom:1rem;"></div>
                        
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label class="form-label">Name</label>
                            <input type="text" id="editName" name="name" class="form-input" required>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label class="form-label">Username</label>
                            <input type="text" id="editUsername" name="username" class="form-input" required>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 1.5rem;">
                            <label class="form-label">New Password <span style="color:#94A3B8; font-size:0.75rem; font-weight:normal;">(Leave blank to keep current)</span></label>
                            <input type="password" name="password" class="form-input" minlength="6" placeholder="Enter new password">
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Pharmacy Modal -->
    <div id="addPharmacyModal" class="fda-modal">
        <div class="fda-modal-content">
            <div class="fda-modal-header">
                <div>
                    <h3 style="margin:0; font-weight:800; color:var(--text-main); font-size: 1.4rem;">
                        Register New Pharmacy
                    </h3>
                    <p style="color: var(--primary); font-weight: 600; font-size: 0.9rem; margin: 0.25rem 0 0 0;">Add a node to the network</p>
                </div>
                <button onclick="closeAddPharmacyModal()" style="background:none; border:none; font-size:1.75rem; color:#94A3B8; cursor:pointer; line-height:1;">&times;</button>
            </div>
            <div class="fda-modal-body">
                <form id="addPharmacyForm" onsubmit="submitAddPharmacy(event)">
                        <div id="addPharmacyAlert" class="alert-banner" style="display:none; margin-bottom:1rem;"></div>
                        
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label class="form-label">Pharmacy Name</label>
                            <input type="text" id="addName" name="name" class="form-input" required oninput="generateUniqueCode()">
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label class="form-label">Unique Code (Auto-generated/Editable)</label>
                            <input type="text" id="addCode" name="code" class="form-input" required style="text-transform: uppercase;">
                        </div>

                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-input" required>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">Latitude</label>
                                <input type="number" step="any" name="latitude" class="form-input" value="14.0702" required>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">Longitude</label>
                                <input type="number" step="any" name="longitude" class="form-input" value="122.9610" required>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">Contact Number (Optional)</label>
                                <input type="text" name="contact_number" class="form-input">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">Email (Optional)</label>
                                <input type="email" name="email" class="form-input">
                            </div>
                        </div>

                        <h4 style="margin: 1.5rem 0 1rem; font-size: 0.9rem; color: var(--text-muted); border-bottom: 1px solid #E2E8F0; padding-bottom: 0.5rem;">Login Credentials</h4>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-input" required>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-input" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-weight: 700;">
                            Register Pharmacy Node
                        </button>
                    </form>
                </div>
            </div>
        </div>

    <script>
        function openAddPharmacyModal() {
            document.getElementById('addPharmacyModal').style.display = 'flex';
        }

        function closeAddPharmacyModal() {
            document.getElementById('addPharmacyModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('addPharmacyModal');
            if (event.target === modal) {
                closeAddPharmacyModal();
            }
        }

        function escapeHTML(str) {
            if (!str) return '';
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


        function deleteSuggestion(id) {
            if (confirm('Are you sure you want to dismiss this resident inquiry?')) {
                fetch('api/submit_suggestion.php?action=delete&id=' + id)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            refreshAdminConsole();
                        } else {
                            alert(data.message);
                        }
                    });
            }
        }

        function refreshAdminConsole() {
            fetch('api/admin_metrics.php')
                .then(res => res.json())
                .then(data => {
                    if (data.error) return;

                    // Update Nodes Grid with dynamic metadata and dual status trackings
                    let totalPharmacies = 0;
                    let onlineDatabases = 0;
                    let nodeHtml = '';
                    if (!data.pharmacies || data.pharmacies.length === 0) {
                        nodeHtml = `<div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: var(--text-muted); background: white; border-radius: 1rem; border: 1px solid #D1E7FF;">No active partner nodes registered.</div>`;
                    } else {
                        totalPharmacies = data.pharmacies.length;
                        data.pharmacies.forEach(pharmacy => {
                            const key = pharmacy.code;
                            const dbStatus = data.nodes[key] || "OFFLINE";
                            const isDbOnline = dbStatus === "ONLINE";
                            const isStoreOpen = parseInt(pharmacy.is_open) === 1;

                            if (isDbOnline) onlineDatabases++;

                            const dbDotPulse = isDbOnline ? "active" : "danger-pulse";
                            const dbIndicatorColor = isDbOnline ? "var(--success)" : "var(--danger)";
                            const dbText = isDbOnline ? "DATABASE ONLINE" : "DATABASE OFFLINE";

                            const storeDotPulse = isStoreOpen ? "active" : "";
                            const storeIndicatorColor = isStoreOpen ? "var(--success)" : "#94A3B8";
                            const storeText = isStoreOpen ? "STORE OPEN" : "STORE CLOSED";

                            const cardClass = isDbOnline ? "node-card" : "node-card offline";

                            nodeHtml += `
                                <div class="${cardClass}">
                                    <div>
                                        <div class="node-card-header">
                                            <h3 class="node-title">${pharmacy.name}</h3>
                                        </div>
                                        
                                        <div class="node-status-row">
                                            <div class="status-indicator">
                                                <span class="status-dot ${dbDotPulse}" style="background-color: ${dbIndicatorColor};"></span>
                                                <span style="color: ${dbIndicatorColor}; font-size: 0.75rem; letter-spacing: 0.05em; font-weight: 700;">${dbText}</span>
                                            </div>
                                            <div class="status-indicator">
                                                <span class="status-dot ${storeDotPulse}" style="background-color: ${storeIndicatorColor};"></span>
                                                <span style="color: ${storeIndicatorColor}; font-size: 0.75rem; letter-spacing: 0.05em; font-weight: 700;">${storeText}</span>
                                            </div>
                                        </div>

                                        <div class="node-details">
                                            <div class="node-detail-item">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="icon-muted"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                                <span class="node-text" title="${pharmacy.address}">${pharmacy.address}</span>
                                            </div>
                                            <div class="node-detail-item">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="icon-muted"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                                <span class="node-text">${pharmacy.contact_number}</span>
                                            </div>
                                            <div class="node-detail-item">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="icon-muted"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                                <span class="node-text" title="${pharmacy.email}">${pharmacy.email}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                    document.getElementById('nodeContainer').innerHTML = nodeHtml;

                    // (Audit Trails removed)

                    // Update Suggestions Inbox
                    const inboxBox = document.getElementById('suggestionInboxBox');
                    if (!data.suggestions || data.suggestions.length === 0) {
                        inboxBox.innerHTML = `<div style="text-align:center; padding:3rem; color:var(--text-muted); font-size:0.9rem;">No feedback or inquiries received yet.</div>`;
                    } else {
                        inboxBox.innerHTML = data.suggestions.map(item => {
                            const date = new Date(item.created_at);
                            const formattedDate = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) + ' | ' + date.toLocaleDateString([], { month: 'short', day: 'numeric' });
                            return `
                                <div style="background: white; border: 1px solid #D1E7FF; padding: 1.25rem; border-radius: 0.75rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.02); transition: var(--transition);">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem; gap: 1rem;">
                                        <div style="flex-shrink: 0;">
                                            <strong style="color: var(--text-main); font-size: 0.9rem; display: block;">${escapeHTML(item.name)}</strong>
                                            <span style="font-size: 0.75rem; color: var(--text-muted);">${escapeHTML(item.email)}</span>
                                        </div>
                                        <span style="background: #EFF6FF; color: var(--primary); font-size: 0.7rem; padding: 0.25rem 0.6rem; border-radius: 0.35rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; text-align: right; word-break: break-word; line-height: 1.3;">${escapeHTML(item.category)}</span>
                                    </div>
                                    <p style="color: #475569; font-size: 0.85rem; line-height: 1.5; margin: 0.5rem 0 0.85rem; background: white; padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #F1F5F9; font-style: italic;">
                                        "${escapeHTML(item.suggestion)}"
                                    </p>
                                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #E2E8F0; padding-top: 0.65rem; font-size: 0.75rem; color: var(--text-muted);">
                                        <span>${formattedDate}</span>
                                        <button onclick="deleteSuggestion(${item.id})" style="background: none; border: none; color: var(--danger); font-weight: 700; cursor: pointer; padding: 0; font-size:0.75rem;">
                                            🗑️ Delete
                                        </button>
                                    </div>
                                </div>
                            `;
                        }).join('');
                    }
                    // Update Quick Stats
                    document.getElementById('statTotalPharmacies').innerText = totalPharmacies;
                    document.getElementById('statOnlineDatabases').innerText = onlineDatabases;
                    document.getElementById('statTotalInquiries').innerText = data.suggestions ? data.suggestions.length : 0;
                });
        }

        // Auto refresh setup (checks every 3 seconds)
        setInterval(refreshAdminConsole, 3000);
        refreshAdminConsole();

        // Add Pharmacy JavaScript Logic
        function generateUniqueCode() {
            const name = document.getElementById('addName').value;
            const codeInput = document.getElementById('addCode');
            // Remove non-alphanumeric, uppercase, up to 20 chars
            const generated = name.replace(/[^a-zA-Z0-9]/g, '').toUpperCase().substring(0, 20);
            codeInput.value = generated;
        }

        async function submitAddPharmacy(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const alertBox = document.getElementById('addPharmacyAlert');
            
            try {
                const response = await fetch('api/add_pharmacy.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    alertBox.style.display = 'block';
                    alertBox.className = 'alert-banner alert-banner-success';
                    alertBox.innerHTML = `<strong>Success!</strong> ${data.message}`;
                    form.reset();
                    // trigger auto-refresh for nodes grid
                    refreshAdminConsole();
                    
                    // Hide alert after 5 seconds and close modal
                    setTimeout(() => { 
                        alertBox.style.display = 'none'; 
                        closeAddPharmacyModal();
                    }, 2000);
                } else {
                    alertBox.style.display = 'block';
                    alertBox.className = 'alert-banner alert-banner-error';
                    alertBox.innerHTML = `<strong>Error!</strong> ${data.message}`;
                }
            } catch(err) {
                alertBox.style.display = 'block';
                alertBox.className = 'alert-banner alert-banner-error';
                alertBox.innerHTML = `<strong>Error!</strong> Failed to communicate with server.`;
            }
        }

        // Edit Profile JS
        async function openEditProfileModal() {
            document.getElementById('editProfileModal').style.display = 'flex';
            document.getElementById('editProfileAlert').style.display = 'none';
            document.getElementById('editProfileForm').reset();
            try {
                const res = await fetch('api/update_profile.php');
                const result = await res.json();
                if (result.status === 'success') {
                    document.getElementById('editName').value = result.data.name;
                    document.getElementById('editUsername').value = result.data.username;
                }
            } catch(err) {
                console.error("Error fetching profile", err);
            }
        }

        function closeEditProfileModal() {
            document.getElementById('editProfileModal').style.display = 'none';
        }

        async function submitEditProfile(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const alertBox = document.getElementById('editProfileAlert');
            
            try {
                const response = await fetch('api/update_profile.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.status === 'success') {
                    alertBox.style.display = 'block';
                    alertBox.className = 'alert-banner alert-banner-success';
                    alertBox.innerHTML = `<strong>Success!</strong> ${data.message}`;
                    
                    document.getElementById('portalUserName').innerText = document.getElementById('editUsername').value;
                    
                    setTimeout(() => { 
                        alertBox.style.display = 'none'; 
                        closeEditProfileModal();
                    }, 2000);
                } else {
                    alertBox.style.display = 'block';
                    alertBox.className = 'alert-banner alert-banner-error';
                    alertBox.innerHTML = `<strong>Error!</strong> ${data.message}`;
                }
            } catch(err) {
                alertBox.style.display = 'block';
                alertBox.className = 'alert-banner alert-banner-error';
                alertBox.innerHTML = `<strong>Error!</strong> Failed to update profile.`;
            }
        }
    </script>
</body>

</html>