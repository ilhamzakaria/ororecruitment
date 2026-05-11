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

    /* Modal Styling */
    .modal-content { border-radius: 16px; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
    .modal-header { border-bottom: 1px solid var(--line); padding: 24px; }
    .modal-body { padding: 0; }
    .modal-footer { border-top: 1px solid var(--line); padding: 20px; }
    
    .detail-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 16px;
        padding: 24px;
        background: var(--soft);
        border-bottom: 1px solid var(--line);
    }
    .detail-summary-item { display: flex; flex-direction: column; }
    .detail-summary-label { font-size: 11px; font-weight: 800; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .detail-summary-value { font-size: 16px; font-weight: 800; color: var(--ink); margin-top: 4px; }

    .answer-list { padding: 24px; }
    .answer-card {
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
        transition: all 0.2s ease;
    }
    .answer-card:hover { border-color: var(--green-3); background: #fafdfc; }
    .answer-card.correct { border-left: 4px solid var(--green-1); }
    .answer-card.wrong { border-left: 4px solid var(--danger); }
    
    .answer-num { width: 32px; height: 32px; border-radius: 8px; background: var(--soft); display: grid; place-items: center; font-weight: 800; font-size: 13px; color: var(--muted); }
    .answer-status-badge { font-size: 10px; font-weight: 800; text-transform: uppercase; padding: 4px 10px; border-radius: 6px; }
    .badge-success-alt { background: #e6f6ef; color: #1f8f5e; }
    .badge-danger-alt { background: #fff1f1; color: #c64747; }
    
    .img-answer { max-width: 200px; border-radius: 8px; border: 1px solid var(--line); margin-top: 10px; }
    .img-question { max-width: 100%; border-radius: 12px; margin-bottom: 15px; border: 1px solid var(--line); }
    
    .scrollable-modal-body { max-height: 70vh; overflow-y: auto; }

    /* Custom Alert Overlay */
    .custom-alert-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(4px);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .custom-alert-overlay.active {
        display: flex;
        opacity: 1;
    }

    .custom-alert-box {
        background: #ffffff;
        border-radius: 16px;
        padding: 28px 24px;
        width: 100%;
        max-width: 340px;
        text-align: center;
        box-shadow: 0 15px 50px rgba(0,0,0,0.15);
        transform: scale(0.9);
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .custom-alert-overlay.active .custom-alert-box {
        transform: scale(1);
    }

    .custom-alert-icon {
        font-size: 54px;
        line-height: 1;
        margin-bottom: 16px;
        color: #f5a623;
    }

    .custom-alert-title {
        font-size: 18px;
        font-weight: 700;
        color: #262626;
        margin: 0 0 10px;
    }

    .custom-alert-message {
        font-size: 14px;
        color: #737373;
        margin: 0 0 24px;
        line-height: 1.4;
    }

    .custom-alert-footer {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .custom-alert-btn {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 8px;
        font-weight: 700;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .btn-alert-primary {
        background: var(--green-1);
        color: #fff;
    }

    .btn-alert-secondary {
        background: #efefef;
        color: #262626;
    }

    .btn-alert-primary:hover { opacity: 0.9; }
    .btn-alert-secondary:hover { background: #e5e5e5; }

    .table-danger-light {
        background-color: #fff5f5 !important;
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
                <thead><tr><th>Peserta</th><th>Status</th><th>Pelanggaran</th><th>Sesi Aktif</th><th>Kontrol Sesi</th><th>Aksi</th></tr></thead>
                <tbody id="sessionsTableBody"></tbody>
            </table>
        </div>
        <div id="sessionsPagination" class="d-flex justify-content-center mt-3 gap-2"></div>
    </section>
    
    <aside class="card">
        <div class="card-title-custom">
            <i class="bi bi-bell"></i> Log Pelanggaran
        </div>
        <div id="recentViolationsList"></div>
        <div id="violationsPagination" class="d-flex justify-content-center mt-3 gap-1"></div>
    </aside>
</div>

<section class="card mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="card-title-custom mb-0">
            <i class="bi bi-clock-history"></i> Log Aktivitas Tes
        </div>
        <div class="d-flex gap-2">
            <input type="text" id="filterActivityPegawai" class="form-control form-control-sm" placeholder="Nama Pegawai..." style="width: 150px;">
            <select id="filterActivitySesi" class="form-select form-select-sm" style="width: 100px;">
                <option value="">Semua Sesi</option>
                <option value="1">Sesi 1</option>
                <option value="2">Sesi 2</option>
                <option value="3">Sesi 3</option>
            </select>
            <input type="date" id="filterActivityTanggal" class="form-control form-control-sm" style="width: 140px;">
            <button class="btn btn-sm btn-primary" onclick="refreshActivityLogs()"><i class="bi bi-search"></i></button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Pegawai</th>
                    <th>Sesi</th>
                    <th>Aktivitas</th>
                    <th>Detail</th>
                    <th>Waktu Lengkap</th>
                </tr>
            </thead>
            <tbody id="activityLogsTableBody">
                <tr><td colspan="5" class="text-center py-4 text-muted">Memuat log aktivitas...</td></tr>
            </tbody>
        </table>
    </div>
    <div id="activityLogsPagination" class="d-flex justify-content-center mt-3 gap-2"></div>
</section>

<section class="card mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="card-title-custom mb-0">
            <i class="bi bi-journal-check"></i> Hasil Jawaban Pegawai
        </div>
        <div class="d-flex gap-2">
            <input type="text" id="filterPegawai" class="form-control form-control-sm" placeholder="Filter Pegawai..." style="width: 150px;">
            <select id="filterStatus" class="form-select form-select-sm" style="width: 120px;">
                <option value="">Semua Status</option>
                <option value="active">Active</option>
                <option value="completed">Completed</option>
                <option value="locked">Locked</option>
            </select>
            <input type="date" id="filterTanggal" class="form-control form-control-sm" style="width: 140px;">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Pegawai</th>
                    <th>Status Tes</th>
                    <th class="text-center">Soal</th>
                    <th class="text-center">Benar</th>
                    <th class="text-center">Salah</th>
                    <th class="text-center">Nilai</th>
                    <th class="text-center">Pelanggaran</th>
                    <th>Tanggal Tes</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="answersTableBody">
                <tr><td colspan="9" class="text-center py-4 text-muted">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</section>

<!-- Modal Detail Jawaban -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-800 mb-0" id="modalEmployeeName">Detail Jawaban</h5>
                    <p class="text-muted small mb-0" id="modalEmployeeId">ID: -</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="detail-summary">
                    <div class="detail-summary-item">
                        <span class="detail-summary-label">Total Soal</span>
                        <span class="detail-summary-value" id="modalTotalSoal">0</span>
                    </div>
                    <div class="detail-summary-item">
                        <span class="detail-summary-label">Dijawab</span>
                        <span class="detail-summary-value" id="modalTotalDijawab">0</span>
                    </div>
                    <div class="detail-summary-item text-success">
                        <span class="detail-summary-label">Benar</span>
                        <span class="detail-summary-value" id="modalBenar">0</span>
                    </div>
                    <div class="detail-summary-item text-danger">
                        <span class="detail-summary-label">Salah</span>
                        <span class="detail-summary-value" id="modalSalah">0</span>
                    </div>
                    <div class="detail-summary-item">
                        <span class="detail-summary-label">Nilai Akhir</span>
                        <span class="detail-summary-value" id="modalNilai">0</span>
                    </div>
                    <div class="detail-summary-item">
                        <span class="detail-summary-label">Status Tes</span>
                        <span class="detail-summary-value" id="modalStatusTes">-</span>
                    </div>
                    <div class="detail-summary-item">
                        <span class="detail-summary-label">Tanggal Tes</span>
                        <span class="detail-summary-value" id="modalTanggalTes">-</span>
                    </div>
                    <div class="detail-summary-item text-danger">
                        <span class="detail-summary-label">Pelanggaran</span>
                        <span class="detail-summary-value" id="modalViolations">0</span>
                    </div>
                </div>
                <div class="px-4 py-3">
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="p-2 bg-light rounded border text-center">
                                <div class="small fw-bold text-muted" style="font-size: 10px;">SESI 1</div>
                                <div class="fw-800" id="modalSesi1Stats" style="font-size: 12px;">-</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded border text-center">
                                <div class="small fw-bold text-muted" style="font-size: 10px;">SESI 2</div>
                                <div class="fw-800" id="modalSesi2Stats" style="font-size: 12px;">-</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded border text-center">
                                <div class="small fw-bold text-muted" style="font-size: 10px;">SESI 3</div>
                                <div class="fw-800" id="modalSesi3Stats" style="font-size: 12px;">-</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="scrollable-modal-body">
                    <div id="modalAnswerList" class="answer-list"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Custom Alert Modal (Instagram Style) -->
<div id="customAlertOverlay" class="custom-alert-overlay">
    <div class="custom-alert-box">
        <div id="customAlertIcon" class="custom-alert-icon">
            <i class="bi bi-exclamation-circle-fill"></i>
        </div>
        <h4 id="customAlertTitle" class="custom-alert-title">Judul Alert</h4>
        <p id="customAlertMessage" class="custom-alert-message">Pesan alert di sini...</p>
        <div class="custom-alert-footer" id="customAlertFooter">
            <button id="customAlertBtnPrimary" class="custom-alert-btn btn-alert-primary">Mengerti</button>
            <button id="customAlertBtnSecondary" class="custom-alert-btn btn-alert-secondary" style="display:none;">Batal</button>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const dashboardDataUrl = "<?= site_url('dashboard-hrd/data') ?>";
    const initialDashboard = {
        summary: <?= json_encode($summary ?? ['totalSessions'=>0,'activeSessions'=>0,'lockedSessions'=>0,'completedSessions'=>0,'totalViolations'=>0,'blockedCandidates'=>0]) ?>,
        sessions: <?= json_encode($sessions ?? []) ?>,
        recentViolations: <?= json_encode($recentViolations ?? []) ?>,
        answersReport: <?= json_encode($answersReport ?? []) ?>,
        sessionControl: <?= json_encode($sessionControl ?? []) ?>,
    };

    const state = {
        sessionsPage: 1,
        violationsPage: 1,
        activityPage: 1,
        sessionControl: initialDashboard.sessionControl || []
    };

    const unblockUrl = "<?= site_url('dashboard-hrd/unblock') ?>";

    const el = {
        totalSessionsMetric: document.getElementById('totalSessionsMetric'),
        activeSessionsMetric: document.getElementById('activeSessionsMetric'),
        lockedSessionsMetric: document.getElementById('lockedSessionsMetric'),
        totalViolationsMetric: document.getElementById('totalViolationsMetric'),
        sessionsTableBody: document.getElementById('sessionsTableBody'),
        recentViolationsList: document.getElementById('recentViolationsList'),
        sessionsPagination: document.getElementById('sessionsPagination'),
        violationsPagination: document.getElementById('violationsPagination'),
    };

    function escapeHtml(v) { return String(v ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
    function formatDate(v) { return v ? new Date(v).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}) : '-'; }
    function initials(n) { return (n || 'U').split(' ').map(x => x[0]).join('').slice(0, 2).toUpperCase(); }

    function showCustomAlert(msg, title = 'Info', options = {}) {
        const overlay = document.getElementById('customAlertOverlay');
        const titleEl = document.getElementById('customAlertTitle');
        const msgEl = document.getElementById('customAlertMessage');
        const iconEl = document.getElementById('customAlertIcon');
        const btnPrimary = document.getElementById('customAlertBtnPrimary');
        const btnSecondary = document.getElementById('customAlertBtnSecondary');

        titleEl.textContent = title;
        msgEl.textContent = msg;
        btnPrimary.textContent = options.primaryText || 'Mengerti';
        
        if (options.icon) iconEl.innerHTML = `<i class="bi ${options.icon}"></i>`;
        if (options.iconColor) iconEl.style.color = options.iconColor;
        else iconEl.style.color = '#f5a623';

        if (options.showSecondary) {
            btnSecondary.style.display = 'block';
            btnSecondary.textContent = options.secondaryText || 'Batal';
        } else {
            btnSecondary.style.display = 'none';
        }

        overlay.style.display = 'flex';
        setTimeout(() => overlay.classList.add('active'), 10);

        btnPrimary.onclick = () => {
            overlay.classList.remove('active');
            setTimeout(() => {
                overlay.style.display = 'none';
                if (options.onPrimary) options.onPrimary();
            }, 300);
        };

        btnSecondary.onclick = () => {
            overlay.classList.remove('active');
            setTimeout(() => {
                overlay.style.display = 'none';
                if (options.onSecondary) options.onSecondary();
            }, 300);
        };
    }

    function calculateTimeLeft(startedAt, durationMinutes) {
        if (!startedAt) return '--:--';
        const start = new Date(startedAt.replace(' ', 'T')).getTime();
        if (isNaN(start)) return '--:--';
        const now = Date.now();
        const elapsed = Math.floor((now - start) / 1000);
        const total = durationMinutes * 60;
        const left = total - elapsed;
        
        if (left <= 0) return '<span class="text-danger fw-bold">00:00</span>';
        const m = Math.floor(left / 60);
        const s = left % 60;
        return `<span class="fw-bold">${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}</span>`;
    }

    function renderSessions(sessions) {
        const pageSize = 5;
        const totalPages = Math.ceil(sessions.length / pageSize) || 1;
        if (state.sessionsPage > totalPages) state.sessionsPage = totalPages;
        
        const start = (state.sessionsPage - 1) * pageSize;
        const paged = sessions.slice(start, start + pageSize);

        if (!sessions.length) {
            el.sessionsTableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada sesi interview hari ini.</td></tr>';
            el.sessionsPagination.innerHTML = '';
            return;
        }

        el.sessionsTableBody.innerHTML = paged.map(s => {
            const isBlocked = s.blockedCandidate || s.status === 'locked';
            const rowClass = isBlocked ? 'table-danger-light' : '';
            
            const currentSession = s.current_session || 1;
            
            // Find control for this employee and session
            const control = state.sessionControl ? state.sessionControl.find(c => c.id_pegawai === s.idUser && parseInt(c.nomor_sesi) === parseInt(currentSession)) : null;
            const controlStatus = control ? control.status_sesi : 'belum_dibuka';

            let controlBtn = '';
            if (controlStatus === 'belum_dibuka') {
                controlBtn = `<button class="btn btn-sm btn-success w-100" onclick="openSession('${s.idUser}', '${escapeHtml(s.candidateName)}', ${currentSession})"><i class="bi bi-play-fill"></i> Mulai Sesi ${currentSession}</button>`;
            } else {
                const statusMap = {
                    'dibuka': '<span class="badge bg-info text-dark w-100">DIBUKA</span>',
                    'berjalan': '<span class="badge bg-primary w-100">SEDANG BERJALAN</span>',
                    'selesai': '<span class="badge bg-secondary w-100">SELESAI</span>'
                };
                controlBtn = statusMap[controlStatus] || `<span class="badge bg-light text-dark w-100">${controlStatus.toUpperCase().replace('_', ' ')}</span>`;
            }

            return `
                <tr class="${rowClass}">
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
                    <td>
                        <span class="fw-bold ${s.violations > 0 ? 'text-danger' : 'text-success'}">
                            <i class="bi ${s.violations > 0 ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill'}"></i>
                            ${s.violations} Event
                        </span>
                    </td>
                    <td class="text-center fw-bold">Sesi ${currentSession}</td>
                    <td style="width: 140px;">${controlBtn}</td>
                    <td>
                        ${isBlocked 
                            ? `<button class="btn btn-sm btn-danger py-1 px-2" style="font-size:12px; min-height:0; background-color: var(--danger) !important; color: white !important;" onclick="unblockSession('${s.sessionId}')"><i class="bi bi-unlock-fill"></i> Buka Blokir</button>`
                            : `<span class="text-muted">-</span>`
                        }
                    </td>
                </tr>
            `;
        }).join('');

        renderPagination(el.sessionsPagination, state.sessionsPage, totalPages, (p) => {
            state.sessionsPage = p;
            renderSessions(sessions);
        });
    }

    function renderPagination(container, current, total, onPage) {
        if (total <= 1) {
            container.innerHTML = '';
            return;
        }
        let html = `
            <button class="btn btn-sm btn-secondary p-1" ${current === 1 ? 'disabled' : ''} onclick="window.pageCallback(${current - 1})"><i class="bi bi-chevron-left"></i></button>
        `;
        for (let i = 1; i <= total; i++) {
            html += `<button class="btn btn-sm ${i === current ? 'btn-primary' : 'btn-secondary'} py-1 px-2" onclick="window.pageCallback(${i})">${i}</button>`;
        }
        html += `
            <button class="btn btn-sm btn-secondary p-1" ${current === total ? 'disabled' : ''} onclick="window.pageCallback(${current + 1})"><i class="bi bi-chevron-right"></i></button>
        `;
        container.innerHTML = html;
        window.pageCallback = onPage;
    }

    async function unblockSession(sessionId) {
        showCustomAlert(
            'Apakah Anda yakin ingin membuka blokir pegawai ini? Pegawai dapat melanjutkan tes kembali setelah blokir dibuka.',
            'Buka Blokir Pegawai',
            {
                showSecondary: true,
                primaryText: 'Buka Blokir',
                secondaryText: 'Batal',
                icon: 'bi-unlock-fill',
                onPrimary: async () => {
                    try {
                        const formData = new FormData();
                        formData.append('sessionId', sessionId);
                        const res = await fetch(unblockUrl, {
                            method: 'POST',
                            body: formData
                        });
                        if (res.ok) {
                            refresh();
                            setTimeout(() => {
                                showCustomAlert('Blokir pegawai berhasil dibuka.', 'Sukses', {
                                    icon: 'bi-check-circle-fill',
                                    iconColor: 'var(--green-1)'
                                });
                            }, 400);
                        }
                    } catch(e) {}
                }
            }
        );
    }

    function renderViolations(violations) {
        const pageSize = 5;
        const totalPages = Math.ceil(violations.length / pageSize) || 1;
        if (state.violationsPage > totalPages) state.violationsPage = totalPages;
        
        const start = (state.violationsPage - 1) * pageSize;
        const paged = violations.slice(start, start + pageSize);

        if (!violations.length) {
            el.recentViolationsList.innerHTML = '<div class="text-center py-4 text-muted small">Tidak ada pelanggaran terdeteksi.</div>';
            el.violationsPagination.innerHTML = '';
            return;
        }

        el.recentViolationsList.innerHTML = paged.map(v => {
            const isBlocked = v.status === 'locked' || v.status === 'blocked';
            const statusBadge = isBlocked ? '<span class="badge bg-danger ms-1" style="font-size:9px">BLOCKED</span>' : '';
            const typeBadge = `<span class="badge ${v.count > 1 ? 'bg-danger' : 'bg-warning'} text-dark ms-1" style="font-size:9px">${v.type.toUpperCase()}</span>`;

            return `
                <div class="violation-item" style="padding: 12px;">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div style="min-width:0">
                            <strong style="font-size:13px; display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${escapeHtml(v.candidateName)}</strong>
                            <div class="small text-muted" style="font-size:10px">${escapeHtml(v.idUser)}</div>
                        </div>
                        <span class="violation-time">${formatDate(v.at)}</span>
                    </div>
                    <div class="mt-1 small text-dark fw-600" style="font-size:11px;">
                        <i class="bi bi-info-circle"></i> ${escapeHtml(v.detail || 'Tidak ada detail')}
                    </div>
                    <div class="d-flex align-items-center mt-2 flex-wrap gap-1">
                        <span class="badge bg-primary" style="font-size:9px">SESI ${v.session}</span>
                        <span class="badge bg-light text-dark border" style="font-size:9px">${v.count}x Total</span>
                        ${typeBadge}
                        ${statusBadge}
                    </div>
                </div>
            `;
        }).join('');

        renderPagination(el.violationsPagination, state.violationsPage, totalPages, (p) => {
            state.violationsPage = p;
            renderViolations(violations);
        });
    }

    function renderAnswers(summaries) {
        const filterPegawai = document.getElementById('filterPegawai').value.toLowerCase();
        // filterStatus and filterTanggal might need adjustment since we now filter summaries
        const filterStatus = document.getElementById('filterStatus').value; // This was for individual answer status, now maybe map to summary?
        const filterTanggal = document.getElementById('filterTanggal').value;
        
        const filtered = summaries.filter(s => {
            const matchesPegawai = s.nama_pegawai.toLowerCase().includes(filterPegawai) || s.id_pegawai.toLowerCase().includes(filterPegawai);
            const matchesStatus = filterStatus === '' || s.status_tes === filterStatus;
            const matchesTanggal = filterTanggal === '' || s.tanggal_tes.startsWith(filterTanggal);
            return matchesPegawai && matchesStatus && matchesTanggal;
        });

        if (!filtered.length) {
            document.getElementById('answersTableBody').innerHTML = `<tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada data jawaban yang cocok.</td></tr>`;
            return;
        }

        document.getElementById('answersTableBody').innerHTML = filtered.map(s => `
            <tr>
                <td>
                    <div class="fw-bold text-dark">${escapeHtml(s.nama_pegawai)}</div>
                    <div class="small text-muted">${escapeHtml(s.id_pegawai)}</div>
                </td>
                <td><span class="status-pill status-${s.status_tes}">${s.status_tes}</span></td>
                <td class="text-center fw-bold text-muted">${s.total_dijawab} / ${s.total_soal}</td>
                <td class="text-center"><span class="badge bg-success">${s.benar}</span></td>
                <td class="text-center"><span class="badge bg-danger">${s.salah}</span></td>
                <td class="text-center"><strong class="text-primary">${s.nilai_akhir}</strong></td>
                <td class="text-center">
                    <span class="badge ${s.violations > 0 ? 'bg-danger' : 'bg-success'}">
                        ${s.violations}
                    </span>
                </td>
                <td class="text-muted small">${formatDate(s.tanggal_tes)}</td>
                <td>
                    <button class="btn btn-sm btn-secondary py-1 px-2" style="font-size:12px; min-height:0" onclick="showDetail('${s.id_pegawai}')">
                        <i class="bi bi-eye"></i> Detail
                    </button>
                </td>
            </tr>
        `).join('');
    }

    function showDetail(idPegawai) {
        const summary = currentAnswersData.find(s => s.id_pegawai === idPegawai);
        if (!summary) return;

        document.getElementById('modalEmployeeName').textContent = summary.nama_pegawai;
        document.getElementById('modalEmployeeId').textContent = 'ID: ' + summary.id_pegawai;
        document.getElementById('modalTotalSoal').textContent = summary.total_soal;
        document.getElementById('modalTotalDijawab').textContent = summary.total_dijawab;
        document.getElementById('modalBenar').textContent = summary.benar;
        document.getElementById('modalSalah').textContent = summary.salah;
        document.getElementById('modalNilai').textContent = summary.nilai_akhir;
        document.getElementById('modalStatusTes').textContent = summary.status_tes;
        document.getElementById('modalTanggalTes').textContent = formatDate(summary.tanggal_tes);
        document.getElementById('modalViolations').textContent = summary.violations;

        // Session Stats
        for (let i = 1; i <= 3; i++) {
            const stats = summary.session_stats['sesi_' + i];
            const el = document.getElementById('modalSesi' + i + 'Stats');
            if (stats && stats.total_soal > 0) {
                el.innerHTML = `<span class="text-success">${stats.benar}</span> / ${stats.total_soal} <small class="text-muted">(${stats.nilai} pts)</small>`;
            } else {
                el.textContent = 'Belum Ada';
            }
        }

        const listEl = document.getElementById('modalAnswerList');
        listEl.innerHTML = summary.detail.map((a, index) => {
            const isCorrect = a.status_jawaban === 'Benar';
            
            // Function to render choice content (text or image)
            const renderChoice = (choiceKey) => {
                if (!choiceKey) return '<span class="text-muted italic">Belum dijawab</span>';
                
                const choiceType = a['tipe_pilihan_' + choiceKey.toLowerCase()] || 'text';
                const choiceText = a['pilihan_' + choiceKey.toLowerCase()];
                const choiceImg = a['gambar_pilihan_' + choiceKey.toLowerCase()];
                
                if (choiceType === 'gambar' && choiceImg) {
                    return `<img src="<?= base_url('uploads/questions/') ?>${choiceImg}" class="img-answer d-block" alt="Option ${choiceKey}">`;
                }
                return `<strong>${choiceKey}.</strong> ${escapeHtml(choiceText || '-')}`;
            };

            if (parseInt(a.sesi) === 1) {
                return `
                    <div class="answer-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="answer-num">S${a.sesi}-Q${a.nomor_pertanyaan}</div>
                                <span class="answer-status-badge bg-primary text-white">DISC Test</span>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="small fw-bold text-muted">Mirip (Most):</div>
                                <div class="p-2 bg-success text-white rounded mt-1 border border-success">
                                    ${renderChoice(a.jawaban_most)}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="small fw-bold text-muted">Tidak Mirip (Least):</div>
                                <div class="p-2 bg-danger text-white rounded mt-1 border border-danger">
                                    ${renderChoice(a.jawaban_least)}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            const statusLabel = a.status_jawaban || 'Belum dijawab';
            const badgeClass = statusLabel === 'Benar' ? 'badge-success-alt' : (statusLabel === 'Salah' ? 'badge-danger-alt' : 'bg-warning text-dark');
            const cardBorderClass = statusLabel === 'Benar' ? 'correct' : (statusLabel === 'Salah' ? 'wrong' : '');

            return `
                <div class="answer-card ${cardBorderClass}">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="answer-num">S${a.sesi}-Q${a.nomor_pertanyaan}</div>
                            <span class="answer-status-badge ${badgeClass}">
                                ${statusLabel}
                            </span>
                        </div>
                        <div class="fw-bold text-muted small">Poin: ${a.nilai || 0}</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="small fw-bold text-muted">Jawaban Pegawai:</div>
                            <div class="p-2 bg-light rounded mt-1 border">
                                ${renderChoice(a.jawaban_pegawai)}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="small fw-bold text-muted">Jawaban Benar:</div>
                            <div class="p-2 mt-1 border rounded" style="background: #e6f6ef; border-color: #c3e6cb !important;">
                                ${renderChoice(a.jawaban_benar)}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
        modal.show();
    }

    let currentAnswersData = [];

    function renderDashboard(data) {
        el.totalSessionsMetric.textContent = data.summary.totalSessions;
        el.activeSessionsMetric.textContent = data.summary.activeSessions;
        el.lockedSessionsMetric.textContent = data.summary.lockedSessions;
        el.totalViolationsMetric.textContent = data.summary.totalViolations;
        
        if (data.sessionControl) {
            state.sessionControl = data.sessionControl;
        }
        
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
            if (res.ok) {
                const data = await res.json();
                state.sessionControl = data.sessionControl || [];
                renderDashboard(data);
            }
        } catch(e) {}
    }

    async function refreshActivityLogs() {
        const nama = document.getElementById('filterActivityPegawai').value;
        const sesi = document.getElementById('filterActivitySesi').value;
        const tanggal = document.getElementById('filterActivityTanggal').value;

        try {
            const res = await fetch(`<?= site_url('dashboard-hrd/activity-logs') ?>?nama=${nama}&sesi=${sesi}&tanggal=${tanggal}`);
            if (res.ok) {
                const data = await res.json();
                renderActivityLogs(data.logs);
            }
        } catch (e) {}
    }

    function renderActivityLogs(logs) {
        const pageSize = 10;
        const totalPages = Math.ceil(logs.length / pageSize) || 1;
        if (state.activityPage > totalPages) state.activityPage = totalPages;

        const start = (state.activityPage - 1) * pageSize;
        const paged = logs.slice(start, start + pageSize);

        if (!logs.length) {
            document.getElementById('activityLogsTableBody').innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada log aktivitas.</td></tr>';
            document.getElementById('activityLogsPagination').innerHTML = '';
            return;
        }

        document.getElementById('activityLogsTableBody').innerHTML = paged.map(l => `
            <tr>
                <td>
                    <div class="fw-bold text-dark">${escapeHtml(l.nama_pegawai)}</div>
                    <div class="small text-muted">${escapeHtml(l.id_pegawai)}</div>
                </td>
                <td class="text-center">Sesi ${l.nomor_sesi}</td>
                <td><span class="badge bg-light text-dark border">${escapeHtml(l.aktivitas)}</span></td>
                <td class="small">${escapeHtml(l.detail_aktivitas || '-')}</td>
                <td class="text-muted small">${l.waktu_lengkap}</td>
            </tr>
        `).join('');

        renderPagination(document.getElementById('activityLogsPagination'), state.activityPage, totalPages, (p) => {
            state.activityPage = p;
            renderActivityLogs(logs);
        });
    }

    async function openSession(idPegawai, namaPegawai, nomorSesi) {
        showCustomAlert(
            `Apakah Anda yakin ingin membuka Sesi ${nomorSesi} untuk ${namaPegawai}?`,
            'Buka Sesi Test',
            {
                showSecondary: true,
                primaryText: 'Buka Sesi',
                secondaryText: 'Batal',
                icon: 'bi-play-fill',
                onPrimary: async () => {
                    try {
                        const formData = new FormData();
                        formData.append('idPegawai', idPegawai);
                        formData.append('namaPegawai', namaPegawai);
                        formData.append('nomorSesi', nomorSesi);
                        
                        const res = await fetch("<?= site_url('dashboard-hrd/open-session') ?>", {
                            method: 'POST',
                            body: formData
                        });
                        const result = await res.json();
                        if (result.ok) {
                            refresh();
                            refreshActivityLogs();
                            setTimeout(() => {
                                showCustomAlert(result.message, 'Sukses', {
                                    icon: 'bi-check-circle-fill',
                                    iconColor: 'var(--green-1)'
                                });
                            }, 400);
                        }
                    } catch(e) {}
                }
            }
        );
    }

    // Initial render
    renderDashboard(initialDashboard);
    refreshActivityLogs();
    
    // Auto refresh every 30 seconds
    setInterval(() => {
        refresh();
        refreshActivityLogs();
    }, 30000);
</script>
<?= $this->endSection() ?>
