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
        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .metric-card {
            background: white;
            padding: 1.75rem;
            border-radius: 0.75rem;
            border: 1px solid #E2E8F0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .metric-num {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--primary);
            margin-top: 0.5rem;
        }
        .node-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem;
            background: #F8FAFC;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            border: 1px solid #E2E8F0;
        }
        .status-pill {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 700;
        }
        .status-online { background: #D1FAE5; color: #065F46; }
        .status-offline { background: #FEE2E2; color: #991B1B; }
        .log-stream {
            background: #0F172A;
            color: #38BDF8;
            font-family: monospace;
            padding: 1rem;
            border-radius: 0.5rem;
            max-height: 250px;
            overflow-y: auto;
            font-size: 0.85rem;
            line-height: 1.4;
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
                <span class="badge" style="background:#EFF6FF; color:var(--primary); font-weight:700; padding:0.5rem 1rem; border-radius:0.5rem;">Role: Middleware Admin</span>
                <a href="index.php" class="btn btn-outline">Switch Account</a>
            </div>
        </div>
    </nav>

    <main class="container animate-fade" style="margin-top:2rem; padding: 0 2rem;">
        <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.25rem;">System Admin Console</h1>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Real-time node coordination and middleware health statistics.</p>

        <div class="grid-3">
            <div class="metric-card">
                <div style="font-weight:700; color:var(--text-muted);">Unified Stock Volume</div>
                <div class="metric-num" id="metricStock">...</div>
            </div>
            <div class="metric-card">
                <div style="font-weight:700; color:var(--text-muted);">Active Distributed SKUs</div>
                <div class="metric-num" id="metricSkus">...</div>
            </div>
            <div class="metric-card">
                <div style="font-weight:700; color:var(--text-muted);">Registered Customer Accounts</div>
                <div class="metric-num" id="metricCustomers">...</div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1.5fr; gap:1.5rem; margin-bottom:2rem;">
            <div class="metric-card">
                <h3 style="margin-bottom:1rem; font-weight:700;">Node Connectivity Monitor</h3>
                <div id="nodeContainer">
                    <div class="node-row"><span>Laurent's Pharmacy</span><span class="status-pill status-offline">LOADING</span></div>
                    <div class="node-row"><span>JRMP Doctors Pharmacy</span><span class="status-pill status-offline">LOADING</span></div>
                    <div class="node-row"><span>JAS5 Pharmacy</span><span class="status-pill status-offline">LOADING</span></div>
                </div>
            </div>

            <div class="metric-card">
                <h3 style="margin-bottom:1rem; font-weight:700;">Audit Trails</h3>
                <div class="table-container" style="box-shadow:none; border:1px solid #E2E8F0;">
                    <table>
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Node</th>
                                <th>Action Details</th>
                            </tr>
                        </thead>
                        <tbody id="auditTableBody">
                            <tr><td colspan="3" style="text-align:center; color:var(--text-muted);">Monitoring transactions...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="metric-card" style="margin-bottom:3rem;">
            <h3 style="margin-bottom:0.5rem; font-weight:700;">Live Webhook Connection Logs</h3>
            <p style="color:var(--text-muted); font-size:0.85rem; margin-bottom:1rem;">Real-time JSON synchronization payloads arriving from active nodes.</p>
            <div class="log-stream" id="logStreamBox">
                [SYSTEM] Ready and waiting for middleware streams...
            </div>
        </div>
    </main>

    <script>
        function refreshAdminConsole() {
            fetch('api/admin_metrics.php')
                .then(res => res.json())
                .then(data => {
                    if(data.error) return;

                    // Update Metrics
                    document.getElementById('metricStock').textContent = data.metrics.total_stock.toLocaleString();
                    document.getElementById('metricSkus').textContent = data.metrics.total_skus.toLocaleString();
                    document.getElementById('metricCustomers').textContent = data.metrics.total_customers.toLocaleString();

                    // Update Nodes
                    const names = { laurents: "Laurent's Pharmacy", jrmp: "JRMP Doctors Pharmacy", jas5: "JAS5 Pharmacy" };
                    let nodeHtml = '';
                    for (let key in data.nodes) {
                        const status = data.nodes[key];
                        const pillClass = status === "ONLINE" ? "status-online" : "status-offline";
                        nodeHtml += `
                            <div class="node-row">
                                <span style="font-weight:600;">${names[key]}</span>
                                <span class="status-pill ${pillClass}">${status}</span>
                            </div>`;
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
                            return `[${log.timestamp || 'LOGGED'}] INCOMING FROM ${log.pharmacy_code.toUpperCase()}:\n"${log.payload}"\n----------------------------------------`;
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