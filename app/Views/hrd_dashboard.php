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
                    <th>Tanggal Tes</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="answersTableBody">
                <tr><td colspan="8" class="text-center py-4 text-muted">Memuat data...</td></tr>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const dashboardDataUrl = "<?= site_url('dashboard-hrd/data') ?>";
    const initialDashboard = {
        summary: <?= json_encode($summary) ?>,
        sessions: <?= json_encode($sessions) ?>,
        recentViolations: <?= json_encode($recentViolations) ?>,
        answersReport: <?= json_encode($answersReport ?? []) ?>,
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
                                ${renderChoice(a.jawaban_dipilih)}
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
