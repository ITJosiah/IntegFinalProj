<?php
session_start();
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
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        .status-dot.danger-pulse {
            animation: pulse-danger 2s infinite;
        }
        @keyframes pulse-danger {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            70% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
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
            background: white;
            padding: 1.75rem;
            border-radius: 1rem;
            border: 1px solid #E2E8F0;
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
            box-shadow: inset 0 2px 8px rgba(0,0,0,0.5);
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
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="brand" style="display:flex; align-items:center; gap:0.35rem;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                <span><span style="color: var(--primary);">Pharma</span><span>Sync</span></span>
            </a>
            <div class="nav-links">
                <span style="background: #EFF6FF; color: var(--primary); font-weight: 700; font-size: 0.8rem; padding: 0.4rem 1rem; border-radius: 2rem; text-transform: uppercase; display: inline-flex; align-items: center; margin-right: 0.75rem; letter-spacing: 0.05em;">PORTAL: MIDDLEWARE ADMIN</span>
                <a href="index.php" class="btn-switch-role">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container animate-fade" style="margin-top:2rem; padding: 0 2rem;">
        <div style="text-align: center; margin-bottom: 3rem;">
            <h1 style="font-size: 2.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem;">System Admin Console</h1>
            <p style="color: var(--text-muted); font-size: 1.05rem; max-width: 600px; margin: 0 auto; line-height: 1.5;">Real-time node coordination and middleware health statistics.</p>
        </div>

        <!-- Hero Connectivity Grid -->
        <h2 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text-main);">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
            Active Middleware Node Status
        </h2>
        <div class="nodes-grid" id="nodeContainer">
            <!-- Dynamically populated node cards -->
            <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: var(--text-muted); background: white; border-radius: 1rem; border: 1px solid #E2E8F0;">
                Loading active node connections...
            </div>
        </div>

        <!-- System Activity Row -->
        <div style="display:grid; grid-template-columns: 1.3fr 1fr; gap:1.5rem; margin-bottom:3rem;">
            <!-- Left: Audit Trails -->
            <div class="metric-card" style="display: flex; flex-direction: column;">
                <h3 style="margin-bottom:1rem; font-weight:800; display:flex; align-items:center; gap:0.5rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    Executive Audit Ledger
                </h3>
                <div class="table-container" style="box-shadow:none; border:1px solid #E2E8F0; flex: 1;">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 25%;">Timestamp</th>
                                <th style="width: 20%;">Node</th>
                                <th style="width: 55%;">Action Details</th>
                            </tr>
                        </thead>
                        <tbody id="auditTableBody">
                            <tr><td colspan="3" style="text-align:center; color:var(--text-muted);">Monitoring transactions...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right: Webhook Live Terminal -->
            <div class="metric-card">
                <h3 style="margin-bottom:0.25rem; font-weight:800; display:flex; align-items:center; gap:0.5rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><polyline points="4 17 10 11 4 5"></polyline><line x1="12" y1="19" x2="20" y2="19"></line></svg>
                    Live Webhook Payload Terminal
                </h3>
                <p style="color:var(--text-muted); font-size:0.8rem; margin-bottom:1rem;">Real-time JSON streams coordinates from active partner nodes.</p>
                <div class="log-stream" id="logStreamBox">
                    [SYSTEM] Ready and waiting for middleware streams...
                </div>
            </div>
        </div>
    </main>

    <script>
        function refreshAdminConsole() {
            fetch('api/admin_metrics.php')
                .then(res => res.json())
                .then(data => {
                    if(data.error) return;

                    // Update Nodes Grid with dynamic metadata and dual status trackings
                    let nodeHtml = '';
                    if(!data.pharmacies || data.pharmacies.length === 0) {
                        nodeHtml = `<div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: var(--text-muted); background: white; border-radius: 1rem; border: 1px solid #E2E8F0;">No active partner nodes registered.</div>`;
                    } else {
                        data.pharmacies.forEach(pharmacy => {
                            const key = pharmacy.code;
                            const dbStatus = data.nodes[key] || "OFFLINE";
                            const isDbOnline = dbStatus === "ONLINE";
                            const isStoreOpen = parseInt(pharmacy.is_open) === 1;

                            const dbDotPulse = isDbOnline ? "active" : "danger-pulse";
                            const dbIndicatorColor = isDbOnline ? "var(--success)" : "var(--danger)";
                            const dbText = isDbOnline ? "DATABASE ONLINE" : "DATABASE OFFLINE";

                            const storeDotPulse = isStoreOpen ? "active" : "";
                            const storeIndicatorColor = isStoreOpen ? "var(--success)" : "#94A3B8";
                            const storeText = isStoreOpen ? "STORE FRONT OPEN" : "STORE FRONT CLOSED";

                            let formattedSync = 'Never Synchronized';
                            if (pharmacy.last_sync) {
                                const date = new Date(pharmacy.last_sync);
                                formattedSync = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) + ' | ' + date.toLocaleDateString([], { month: 'short', day: 'numeric', year: 'numeric' });
                            }

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

                    // Update Audit Trails
                    const auditBody = document.getElementById('auditTableBody');
                    if(data.audit_trails.length === 0) {
                        auditBody.innerHTML = `<tr><td colspan="3" style="text-align:center; color:var(--text-muted);">No logs documented yet.</td></tr>`;
                    } else {
                        auditBody.innerHTML = data.audit_trails.map(item => `
                            <tr>
                                <td style="font-size:0.85rem; color:var(--text-muted);">${item.timestamp || 'Just Now'}</td>
                                <td><span class="badge" style="background:#F1F5F9; color:#475569; padding:0.25rem 0.5rem; font-size:0.75rem;">${item.pharmacy_code.toUpperCase()}</span></td>
                                <td style="font-weight:500;">${item.message}</td>
                            </tr>
                        `).join('');
                    }

                    // Update Webhook Logs Stream
                    const streamBox = document.getElementById('logStreamBox');
                    if(data.webhook_logs.length > 0) {
                        streamBox.innerHTML = data.webhook_logs.map(log => {
                            return `[${log.timestamp || 'LOGGED'}] INCOMING FROM ${log.pharmacy_code.toUpperCase()}:\n"${log.payload}"\n------------------------------------------------------------`;
                        }).join('\n\n');
                    }
                });
        }

        // Auto refresh setup (checks every 3 seconds)
        setInterval(refreshAdminConsole, 3000);
        refreshAdminConsole();
    </script>
</body>
</html>