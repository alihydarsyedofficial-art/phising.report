<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRICK A4IF - Threat Intelligence Intelligence Web</title>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-main: #030305;
            --bg-panel: rgba(15, 15, 20, 0.7);
            --bg-sidebar: rgba(10, 10, 15, 0.95);
            --accent-cyan: #00ffcc;
            --accent-red: #ff0055;
            --text-main: #e0e0e8;
            --text-muted: #888899;
            --border-glass: rgba(0, 255, 204, 0.15);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            font-family: 'Rajdhani', sans-serif;
            display: flex;
            height: 100vh;
            overflow: hidden;
            background-image: radial-gradient(circle at top right, rgba(0, 255, 204, 0.05), transparent 40%),
                              radial-gradient(circle at bottom left, rgba(255, 0, 85, 0.05), transparent 40%);
        }

        .sidebar { width: 280px; background: var(--bg-sidebar); border-right: 1px solid var(--border-glass); display: flex; flex-direction: column; backdrop-filter: blur(20px); z-index: 100; }
        .brand { padding: 30px 20px; text-align: center; border-bottom: 1px solid var(--border-glass); }
        .brand h2 { font-size: 28px; font-weight: 700; color: var(--accent-cyan); text-shadow: 0 0 15px rgba(0, 255, 204, 0.5); letter-spacing: 2px; }
        .brand p { font-size: 14px; color: var(--text-muted); font-weight: 500; margin-top: 5px; }

        .nav-menu { flex: 1; padding: 20px 0; }
        .nav-item { padding: 15px 30px; display: flex; align-items: center; gap: 15px; color: var(--text-muted); font-size: 18px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; border-left: 3px solid transparent; }
        .nav-item:hover, .nav-item.active { color: var(--accent-cyan); background: rgba(0, 255, 204, 0.05); border-left: 3px solid var(--accent-cyan); box-shadow: inset 20px 0 20px -20px rgba(0, 255, 204, 0.5); }
        .nav-item i { font-size: 20px; width: 25px; text-align: center; }

        .main-content { flex: 1; display: flex; flex-direction: column; overflow-y: auto; padding: 30px; scroll-behavior: smooth; }
        .main-content::-webkit-scrollbar { width: 8px; }
        .main-content::-webkit-scrollbar-thumb { background: rgba(0,255,204,0.3); border-radius: 4px; }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header-title h1 { font-size: 32px; font-weight: 700; }
        .user-profile { display: flex; align-items: center; gap: 10px; background: var(--bg-panel); padding: 10px 20px; border-radius: 30px; border: 1px solid var(--border-glass); font-weight: 600; }
        .user-profile i { color: var(--accent-red); }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: var(--bg-panel); border: 1px solid var(--border-glass); border-radius: 12px; padding: 25px; display: flex; align-items: center; gap: 20px; backdrop-filter: blur(10px); box-shadow: 0 10px 30px rgba(0,0,0,0.5); transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); border-color: var(--accent-cyan); }
        .stat-icon { width: 60px; height: 60px; border-radius: 12px; display: flex; justify-content: center; align-items: center; font-size: 28px; background: rgba(0, 255, 204, 0.1); color: var(--accent-cyan); }
        .stat-card:nth-child(2) .stat-icon { background: rgba(255, 0, 85, 0.1); color: var(--accent-red); }
        .stat-info h3 { font-size: 32px; font-family: 'Fira Code', monospace; }
        .stat-info p { color: var(--text-muted); font-size: 15px; font-weight: 600; text-transform: uppercase; }

        .execution-panel { background: var(--bg-panel); border: 1px solid var(--accent-red); border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 0 20px rgba(255, 0, 85, 0.1); }
        .execution-panel h3 { margin-bottom: 20px; color: var(--accent-red); display: flex; align-items: center; gap: 10px; }
        .input-group { display: flex; gap: 15px; flex-wrap: wrap; }
        input[type="url"], select { flex: 1; min-width: 200px; padding: 16px 20px; background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 8px; font-size: 16px; font-family: 'Fira Code', monospace; outline: none; transition: 0.3s; }
        input[type="url"]:focus, select:focus { border-color: var(--accent-cyan); box-shadow: 0 0 15px rgba(0,255,204,0.2); }
        select { cursor: pointer; appearance: none; }
        .btn-nuke { background: rgba(255, 0, 85, 0.15); border: 1px solid var(--accent-red); color: #fff; padding: 0 40px; font-weight: 700; font-size: 16px; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; text-transform: uppercase; letter-spacing: 1px; }
        .btn-nuke:hover { background: var(--accent-red); box-shadow: 0 0 20px var(--accent-red); }
        .btn-nuke:disabled { opacity: 0.5; cursor: not-allowed; }

        .data-section { display: none; animation: fadeIn 0.5s; }
        .data-section.active { display: block; }
        .table-container { background: var(--bg-panel); border: 1px solid var(--border-glass); border-radius: 12px; padding: 25px; overflow-x: auto; }
        .table-container h3 { margin-bottom: 20px; font-size: 22px; }
        table { width: 100%; border-collapse: collapse; font-family: 'Fira Code', monospace; font-size: 14px; }
        th, td { text-align: left; padding: 16px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        th { color: var(--accent-cyan); font-weight: 600; text-transform: uppercase; letter-spacing: 1px; background: rgba(0,0,0,0.3); }
        tr:hover { background: rgba(255,255,255,0.02); }
        
        .badge { padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; font-family: 'Rajdhani', sans-serif;}
        .badge-cyan { background: rgba(0, 255, 204, 0.15); color: var(--accent-cyan); border: 1px solid var(--accent-cyan); }
        .badge-red { background: rgba(255, 0, 85, 0.15); color: var(--accent-red); border: 1px solid var(--accent-red); }
        .badge-proxy { background: #221100; color: #ffaa00; border: 1px solid #ffaa00; }

        #toast-container { position: fixed; bottom: 30px; right: 30px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; }
        .toast { background: rgba(10, 10, 15, 0.95); border-left: 4px solid var(--accent-cyan); color: #fff; padding: 16px 24px; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); display: flex; align-items: center; gap: 15px; animation: slideInRight 0.3s forwards; font-weight: 600; min-width: 300px; }
        .toast.error { border-left-color: var(--accent-red); }
        .toast i { font-size: 24px; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeOut { to { opacity: 0; transform: translateX(100%); } }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="brand">
        <h2>TRICK A4IF</h2>
        <p>Threat Intelligence Web</p>
    </div>
    <div class="nav-menu">
        <div class="nav-item active" onclick="switchView('dashboard')"><i class="fa-solid fa-chart-line"></i> Command Center</div>
        <div class="nav-item" onclick="switchView('targets'); loadTargets();"><i class="fa-solid fa-crosshairs"></i> Monitored Targets</div>
        <div class="nav-item" onclick="switchView('network'); loadIpHistory();"><i class="fa-solid fa-network-wired"></i> IP Logs</div>
    </div>
</aside>

<main class="main-content">
    <div class="header">
        <div class="header-title"><h1 id="page-title">Command Center</h1></div>
        <div class="user-profile"><i class="fa-solid fa-user-shield"></i> Mohammad Mujahidul Islam</div>
    </div>

    <div id="view-dashboard" class="data-section active">
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-icon"><i class="fa-solid fa-satellite-dish"></i></div><div class="stat-info"><h3 id="stat-reports">0</h3><p>Total Dispatched</p></div></div>
            <div class="stat-card"><div class="stat-icon"><i class="fa-solid fa-shield-virus"></i></div><div class="stat-info"><h3 id="stat-targets">0</h3><p>Active Targets</p></div></div>
            <div class="stat-card"><div class="stat-icon" style="color: #ffaa00; background: rgba(255, 170, 0, 0.1);"><i class="fa-solid fa-route"></i></div><div class="stat-info"><h3 id="stat-changes">0</h3><p>IP Evasions</p></div></div>
        </div>

        <div class="execution-panel">
            <h3><i class="fa-solid fa-fire-flame-curved"></i> Multi-Node Nuke Engine</h3>
            <div class="input-group">
                <input type="url" id="targetUrl" placeholder="https://target-scam-site.com">
                <select id="proxyNode">
                    <option value="DIRECT">🌐 Direct Global Node</option>
                    <option value="US">🇺🇸 USA Encrypted Node</option>
                    <option value="GB">🇬🇧 UK Encrypted Node</option>
                    <option value="DE">🇩🇪 Germany Encrypted Node</option>
                    <option value="SG">🇸🇬 Singapore Encrypted Node</option>
                </select>
                <button class="btn-nuke" id="nukeBtn" onclick="firePayload()"><i class="fa-solid fa-bolt"></i> Execute Report</button>
            </div>
        </div>

        <div class="table-container">
            <h3><i class="fa-solid fa-paper-plane"></i> Live Dispatch Network</h3>
            <table id="reportLogsTable">
                <thead><tr><th>Target Domain</th><th>Host Abuse Desk</th><th>Routing Node</th><th>Status</th><th>Timestamp (UTC)</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div id="view-targets" class="data-section">
        <div class="table-container">
            <h3><i class="fa-solid fa-database"></i> Intelligence Database</h3>
            <table id="targetsTable">
                <thead><tr><th>Domain URL</th><th>Resolved IP</th><th>Infrastructure</th><th>Abuse Contact</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div id="view-network" class="data-section">
        <div class="table-container">
            <h3><i class="fa-solid fa-tower-broadcast"></i> Infrastructure Shift Logs</h3>
            <table id="ipHistoryTable">
                <thead><tr><th>Target Domain</th><th>Previous IP</th><th>New Resolved IP</th><th>Detection Time</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</main>

<div id="toast-container"></div>

<script>
    function switchView(viewName) {
        document.querySelectorAll('.data-section').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
        document.getElementById('view-' + viewName).classList.add('active');
        event.currentTarget.classList.add('active');
        const titles = { 'dashboard': 'Command Center', 'targets': 'Monitored Targets', 'network': 'Network Shift Logs' };
        document.getElementById('page-title').innerText = titles[viewName];
    }

    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        const icon = type === 'success' ? '<i class="fa-solid fa-circle-check" style="color:var(--accent-cyan)"></i>' : '<i class="fa-solid fa-circle-exclamation" style="color:var(--accent-red)"></i>';
        toast.innerHTML = `${icon} <span>${message}</span>`;
        container.appendChild(toast);
        setTimeout(() => { toast.style.animation = 'fadeOut 0.3s forwards'; setTimeout(() => toast.remove(), 300); }, 4000);
    }

    async function firePayload() {
        const url = document.getElementById('targetUrl').value;
        const proxyNode = document.getElementById('proxyNode').value;
        const btn = document.getElementById('nukeBtn');
        
        if(!url) { showToast('Target URL is required for execution.', 'error'); return; }
        
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ROUTING...';
        btn.disabled = true;

        try {
            const formData = new URLSearchParams();
            formData.append('url', url);
            formData.append('proxy_node', proxyNode);

            const response = await fetch('api.php?action=add_target', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            });
            const data = await response.json();
            
            if(data.status === 'success') {
                showToast(`Payload delivered successfully to ${data.abuse_email}`, 'success');
                document.getElementById('targetUrl').value = '';
                loadDashboardData(); 
            } else {
                showToast(data.message, 'error');
            }
        } catch (error) {
            showToast('API Connection Failed. Check server logs.', 'error');
        }
        
        btn.innerHTML = '<i class="fa-solid fa-bolt"></i> Execute Report';
        btn.disabled = false;
    }

    async function loadDashboardData() {
        const resLogs = await fetch('api.php?action=get_report_logs');
        const logsData = await resLogs.json();
        document.getElementById('stat-reports').innerText = logsData.length;
        document.querySelector('#reportLogsTable tbody').innerHTML = logsData.slice(0, 15).map(l => `
            <tr>
                <td style="color:var(--accent-cyan);">${l.domain}</td>
                <td>${l.recipient_email}</td>
                <td><span class="badge badge-proxy"><i class="fa-solid fa-globe"></i> ${l.proxy_used}</span></td>
                <td><span class="badge ${l.delivery_status.includes('Dispatched') ? 'badge-cyan' : 'badge-red'}">${l.delivery_status}</span></td>
                <td style="color:var(--text-muted);">${l.sent_at}</td>
            </tr>
        `).join('') || '<tr><td colspan="5" style="text-align:center; padding: 30px;">No network activity logged yet.</td></tr>';

        const resTargets = await fetch('api.php?action=get_targets');
        document.getElementById('stat-targets').innerText = (await resTargets.json()).length;

        const resHistory = await fetch('api.php?action=get_ip_history');
        document.getElementById('stat-changes').innerText = (await resHistory.json()).length;
    }

    async function loadTargets() {
        const res = await fetch('api.php?action=get_targets');
        document.querySelector('#targetsTable tbody').innerHTML = (await res.json()).map(t => `
            <tr>
                <td style="font-weight:600;">${t.domain}</td>
                <td style="color:var(--accent-cyan); font-family: 'Fira Code', monospace;">${t.current_ip}</td>
                <td><span class="badge badge-cyan">${t.hosting_provider}</span></td>
                <td>${t.abuse_email}</td>
            </tr>
        `).join('') || '<tr><td colspan="4" style="text-align:center;">Database is empty.</td></tr>';
    }

    async function loadIpHistory() {
        const res = await fetch('api.php?action=get_ip_history');
        document.querySelector('#ipHistoryTable tbody').innerHTML = (await res.json()).map(h => `
            <tr>
                <td>${h.domain}</td>
                <td style="color:var(--accent-red); text-decoration: line-through;">${h.old_ip}</td>
                <td style="color:var(--accent-cyan); font-weight: bold;">${h.new_ip} <i class="fa-solid fa-arrow-trend-up"></i></td>
                <td style="color:var(--text-muted);">${h.detected_at}</td>
            </tr>
        `).join('') || '<tr><td colspan="4" style="text-align:center;">No infrastructure modifications detected.</td></tr>';
    }

    window.onload = loadDashboardData;
</script>
</body>
</html>
