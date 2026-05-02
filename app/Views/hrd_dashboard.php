<?php
$headerTitle = 'Dashboard HRD';
$headerSubtitle = 'Ringkasan aktivitas interview hari ini';
?>
<?= $this->extend('layout/main') ?>

<?= $this->section('styles') ?>
<style>
    .hero-band {
        display: grid;
        grid-template-columns: minmax(0, 1.25fr) minmax(280px, 0.75fr);
        gap: 32px;
        align-items: center;
        padding: 32px;
        border-radius: 12px;
        color: #fff;
        background: linear-gradient(135deg, var(--green-1), var(--green-2));
        box-shadow: var(--shadow);
        margin-bottom: 32px;
    }
    .hero-band h2 { margin: 0; font-size: 32px; font-weight: 800; line-height: 1.2; }
    .hero-band p { margin: 12px 0 0; color: rgba(255, 255, 255, 0.9); line-height: 1.6; }
    
    .metric-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 32px; }
    .metric-card { 
        padding: 24px; 
        border-radius: 12px; 
        background: var(--panel); 
        box-shadow: var(--shadow); 
        border: 1px solid var(--line);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .metric-label { display: block; color: var(--muted); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; }
    .metric-card strong { display: block; margin-top: 8px; font-size: 28px; font-weight: 800; color: var(--ink); }
    
    .metric-icon { 
        width: 48px; height: 48px; border-radius: 10px; color: #fff; font-size: 20px;
        display: inline-grid; place-items: center; 
        background: var(--soft);
        color: var(--green-1);
    }
    .icon-active { background: linear-gradient(135deg, var(--green-1), var(--green-3)); color: #fff; }

    .dashboard-grid { display: grid; grid-template-columns: minmax(0, 1fr) 360px; gap: 32px; align-items: start; }
    
    .card-title-custom {
        font-size: 18px;
        font-weight: 800;
        color: var(--ink);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .table thead th {
        background: var(--soft);
        color: var(--muted);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 800;
        border: 0;
        padding: 12px 16px;
    }
    .table tbody td {
        padding: 16px;
        vertical-align: middle;
        border-bottom: 1px solid var(--line);
    }

    .status-pill {
        display: inline-flex;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }
    .status-active { background: #e6f6ef; color: #1f8f5e; }
    .status-locked { background: #fff1f1; color: #c64747; }
    .status-completed { background: #f0f7f4; color: #2ea56f; }

    .violation-item { 
        padding: 16px; 
        border-radius: 10px; 
        border: 1px solid var(--line); 
        background: var(--soft); 
        margin-bottom: 12px; 
    }
    .violation-item strong { display: block; font-size: 14px; color: var(--ink); }
    .violation-item p { margin: 4px 0; font-size: 13px; color: var(--muted); }
    .violation-time { font-size: 11px; font-weight: 700; color: var(--muted); }

    .avatar-sm { 
        width: 36px; height: 36px; border-radius: 8px; 
        background: var(--soft); color: var(--green-1);
        display: grid; place-items: center; font-weight: 800; font-size: 13px;
    }

    @media (max-width: 1100px) {
        .dashboard-grid { grid-template-columns: 1fr; }
        .hero-band { grid-template-columns: 1fr; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="hero-band">
    <div>
        <h2 id="welcomeTitle">Monitoring Interview</h2>
        <p>Pantau setiap sesi interview calon pegawai secara realtime. Anda dapat melihat pelanggaran keamanan dan status progres pengerjaan tes.</p>
    </div>
</section>

<section class="metric-grid">
    <div class="metric-card">
        <div><span class="metric-label">Total Sesi</span><strong id="totalSessionsMetric">0</strong></div>
        <div class="metric-icon"><i class="bi bi-collection"></i></div>
    </div>
    <div class="metric-card">
        <div><span class="metric-label">Sesi Aktif</span><strong id="activeSessionsMetric">0</strong></div>
        <div class="metric-icon icon-active"><i class="bi bi-activity"></i></div>
    </div>
    <div class="metric-card">
        <div><span class="metric-label">Sesi Dikunci</span><strong id="lockedSessionsMetric">0</strong></div>
        <div class="metric-icon"><i class="bi bi-lock"></i></div>
    </div>
    <div class="metric-card">
        <div><span class="metric-label">Pelanggaran</span><strong id="totalViolationsMetric">0</strong></div>
        <div class="metric-icon"><i class="bi bi-exclamation-triangle"></i></div>
    </div>
</section>

<div class="dashboard-grid">
    <section class="card">
        <div class="card-title-custom">
            <i class="bi bi-people"></i> Sesi Terbaru
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Peserta</th><th>Status</th><th>Pelanggaran</th><th>Terakhir Update</th><th>Aksi</th></tr></thead>
                <tbody id="sessionsTableBody"></tbody>
            </table>
        </div>
    </section>
    
    <aside class="card">
        <div class="card-title-custom">
            <i class="bi bi-bell"></i> Log Pelanggaran
        </div>
        <div id="recentViolationsList"></div>
    </aside>
</div>

<section class="card mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="card-title-custom mb-0">
            <i class="bi bi-journal-check"></i> Hasil Jawaban Pegawai
        </div>
        <div class="d-flex gap-2">
            <input type="text" id="filterPegawai" class="form-control form-control-sm" placeholder="Filter Pegawai..." style="width: 150px;">
            <select id="filterStatus" class="form-select form-select-sm" style="width: 120px;">
                <option value="">Semua Status</option>
                <option value="Benar">Benar</option>
                <option value="Salah">Salah</option>
            </select>
            <input type="date" id="filterTanggal" class="form-control form-control-sm" style="width: 140px;">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Pegawai</th>
                    <th>Pertanyaan</th>
                    <th>Jawaban</th>
                    <th>Benar</th>
                    <th>Status</th>
                    <th>Nilai</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody id="answersTableBody">
                <tr><td colspan="7" class="text-center py-4 text-muted">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const dashboardDataUrl = "<?= site_url('dashboard-hrd/data') ?>";
    const initialDashboard = {
        summary: <?= json_encode($summary) ?>,
        sessions: <?= json_encode($sessions) ?>,
        recentViolations: <?= json_encode($recentViolations) ?>,
    };

    const unblockUrl = "<?= site_url('dashboard-hrd/unblock') ?>";

    const el = {
        totalSessionsMetric: document.getElementById('totalSessionsMetric'),
        activeSessionsMetric: document.getElementById('activeSessionsMetric'),
        lockedSessionsMetric: document.getElementById('lockedSessionsMetric'),
        totalViolationsMetric: document.getElementById('totalViolationsMetric'),
        sessionsTableBody: document.getElementById('sessionsTableBody'),
        recentViolationsList: document.getElementById('recentViolationsList'),
    };

    function escapeHtml(v) { return String(v ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
    function formatDate(v) { return v ? new Date(v).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}) : '-'; }
    function initials(n) { return (n || 'U').split(' ').map(x => x[0]).join('').slice(0, 2).toUpperCase(); }

    function renderSessions(sessions) {
        if (!sessions.length) {
            el.sessionsTableBody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">Belum ada sesi interview hari ini.</td></tr>';
            return;
        }
        el.sessionsTableBody.innerHTML = sessions.map(s => `
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-sm">${escapeHtml(initials(s.candidateName))}</div>
                        <div>
                            <div class="fw-bold text-dark">${escapeHtml(s.candidateName)}</div>
                            <div class="small text-muted">${escapeHtml(s.idUser)}</div>
                        </div>
                    </div>
                </td>
                <td><span class="status-pill status-${s.status}">${s.status}</span></td>
                <td><span class="fw-bold ${s.violations > 0 ? 'text-danger' : 'text-success'}">${s.violations} Event</span></td>
                <td class="text-muted fw-semibold">${formatDate(s.updatedAt)}</td>
                <td>
                    ${s.blockedCandidate 
                        ? `<button class="btn btn-sm btn-primary py-1 px-2" style="font-size:12px; min-height:0" onclick="unblockSession('${s.sessionId}')">Buka Blokir</button>`
                        : `<span class="text-muted">-</span>`
                    }
                </td>
            </tr>
        `).join('');
    }

    async function unblockSession(sessionId) {
        if (!confirm('Apakah Anda yakin ingin membuka blokir sesi ini?')) return;
        try {
            const formData = new FormData();
            formData.append('sessionId', sessionId);
            const res = await fetch(unblockUrl, {
                method: 'POST',
                body: formData
            });
            if (res.ok) refresh();
        } catch(e) {}
    }

    function renderViolations(violations) {
        if (!violations.length) {
            el.recentViolationsList.innerHTML = '<div class="text-center py-4 text-muted small">Tidak ada pelanggaran terdeteksi.</div>';
            return;
        }
        el.recentViolationsList.innerHTML = violations.map(v => `
            <div class="violation-item">
                <div class="d-flex justify-content-between align-items-start">
                    <strong>${escapeHtml(v.candidateName)}</strong>
                    <span class="violation-time">${formatDate(v.at)}</span>
                </div>
                <p>${escapeHtml(v.message)}</p>
            </div>
        `).join('');
    }

    function renderAnswers(answers) {
        const filterPegawai = document.getElementById('filterPegawai').value.toLowerCase();
        const filterStatus = document.getElementById('filterStatus').value;
        const filterTanggal = document.getElementById('filterTanggal').value;
        
        const filtered = answers.filter(a => {
            const matchesPegawai = a.nama_pegawai.toLowerCase().includes(filterPegawai) || a.id_pegawai.toLowerCase().includes(filterPegawai);
            const matchesStatus = filterStatus === '' || a.status_jawaban === filterStatus;
            const matchesTanggal = filterTanggal === '' || a.tanggal_menjawab.startsWith(filterTanggal);
            return matchesPegawai && matchesStatus && matchesTanggal;
        });

        if (!filtered.length) {
            document.getElementById('answersTableBody').innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada data jawaban yang cocok.</td></tr>';
            return;
        }

        document.getElementById('answersTableBody').innerHTML = filtered.map(a => `
            <tr>
                <td>
                    <div class="fw-bold">${escapeHtml(a.nama_pegawai)}</div>
                    <div class="small text-muted">${escapeHtml(a.id_pegawai)}</div>
                </td>
                <td style="max-width: 200px;"><div class="text-truncate">ID Soal: ${a.id_pertanyaan}</div></td>
                <td><span class="badge bg-secondary">${a.jawaban_dipilih}</span></td>
                <td><span class="badge bg-primary">${a.jawaban_benar}</span></td>
                <td>
                    <span class="status-pill ${a.status_jawaban === 'Benar' ? 'status-completed' : 'status-locked'}">
                        ${a.status_jawaban}
                    </span>
                </td>
                <td class="fw-bold">${a.nilai}</td>
                <td class="text-muted small">${formatDate(a.tanggal_menjawab)}</td>
            </tr>
        `).join('');
    }

    let currentAnswersData = [];

    function renderDashboard(data) {
        el.totalSessionsMetric.textContent = data.summary.totalSessions;
        el.activeSessionsMetric.textContent = data.summary.activeSessions;
        el.lockedSessionsMetric.textContent = data.summary.lockedSessions;
        el.totalViolationsMetric.textContent = data.summary.totalViolations;
        
        renderSessions(data.sessions);
        renderViolations(data.recentViolations);
        
        if (data.answersReport) {
            currentAnswersData = data.answersReport;
            renderAnswers(currentAnswersData);
        }
    }

    document.getElementById('filterPegawai').addEventListener('input', () => renderAnswers(currentAnswersData));
    document.getElementById('filterStatus').addEventListener('change', () => renderAnswers(currentAnswersData));
    document.getElementById('filterTanggal').addEventListener('change', () => renderAnswers(currentAnswersData));

    async function refresh() {
        try {
            const res = await fetch(dashboardDataUrl);
            if (res.ok) renderDashboard(await res.json());
        } catch(e) {}
    }

    renderDashboard(initialDashboard);
    setInterval(refresh, 8000);
</script>
<?= $this->endSection() ?>
