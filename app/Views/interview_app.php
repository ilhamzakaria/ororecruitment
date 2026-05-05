<?php
$headerTitle = 'Halaman Tes';
$headerSubtitle = 'Sesi Tes Aptitude';
?>
<?= $this->extend('layout/main') ?>

<?= $this->section('styles') ?>
<style>
    .workspace {
        width: 100%;
        display: flex;
        flex-direction: column;
        min-height: 100%;
        background: transparent;
        border: 0;
        box-shadow: none;
    }

    .state {
        display: none;
        animation: fadeUp 0.35s ease;
        height: 100%;
    }

    .state.active {
        display: flex;
        flex-direction: column;
    }

    .intro-card,
    .question-card,
    .summary-card,
    .lock-card {
        margin: 20px auto;
        width: 100%;
        max-width: 1000px;
        padding: 32px;
        border-radius: 12px;
        background: #fff;
        border: 1px solid var(--line);
        box-shadow: var(--shadow);
        color: var(--ink);
    }

    .intro-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(280px, 0.9fr);
        gap: 24px;
        margin-top: 24px;
    }

    .intro-block,
    .prompt-block,
    .summary-block,
    .option-panel {
        padding: 20px;
        border-radius: 10px;
        border: 1px solid var(--line);
        background: var(--soft);
    }

    .intro-block h3,
    .prompt-block h3,
    .summary-block h3,
    .option-panel h3 {
        margin: 0 0 12px;
        font-size: 16px;
        font-weight: 800;
        color: var(--green-1);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        margin-top: 16px;
    }

    .field-hint {
        display: block;
        margin-top: 8px;
        font-size: 12px;
        color: var(--muted);
    }

    .actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 24px;
    }

    .question-head {
        display: flex;
        justify-content: space-between;
        gap: 24px;
        align-items: flex-start;
        margin-bottom: 24px;
    }

    .question-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }

    .pill {
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        border-radius: 999px;
        padding: 0 12px;
        font-size: 11px;
        color: var(--muted);
        border: 1px solid var(--line);
        background: #fff;
        font-weight: 800;
        text-transform: uppercase;
    }

    .pill.alert {
        color: var(--danger);
        border-color: rgba(198, 71, 71, 0.2);
        background: #fff5f5;
    }

    .question-body {
        display: grid;
        gap: 16px;
        margin-bottom: 20px;
    }

    .question-prompt {
        margin: 0;
        font-size: 18px;
        line-height: 1.6;
        color: var(--ink);
        font-weight: 700;
    }

    .prompt-image-wrap {
        display: none;
        justify-content: center;
        padding: 18px;
        border-radius: 12px;
        border: 1px solid var(--line);
        background: #fff;
    }

    .prompt-image-wrap.active {
        display: flex;
    }

    .prompt-image {
        max-width: 100%;
        height: auto;
        object-fit: contain;
    }

    .answer-options {
        display: grid;
        gap: 14px;
    }

    .option-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 12px 20px;
        border-radius: 12px;
        border: 1.5px solid var(--line);
        background: #fff;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        margin-bottom: 4px;
        position: relative;
        overflow: hidden;
    }

    .option-card:hover {
        border-color: var(--green-2);
        background: var(--soft);
        transform: translateX(4px);
    }

    .option-card.active {
        border-color: var(--green-1);
        background: #f0fdf4;
        box-shadow: 0 4px 12px rgba(37, 116, 71, 0.1);
    }

    .option-card input[type="radio"] {
        width: 20px;
        height: 20px;
        margin: 0;
        accent-color: var(--green-1);
        flex: 0 0 auto;
        cursor: pointer;
    }

    .choice-chip {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 800;
        color: var(--green-1);
        background: #e7f3ed;
        flex: 0 0 auto;
        border: 1px solid rgba(37, 116, 71, 0.1);
        transition: all 0.2s ease;
    }

    .option-card.active .choice-chip {
        background: var(--green-1);
        color: #fff;
        border-color: var(--green-1);
    }

    .option-content {
        display: grid;
        gap: 10px;
        flex: 1;
        min-width: 0;
    }

    .option-text {
        color: var(--ink);
        font-weight: 600;
        line-height: 1.5;
    }

    .option-text.compact {
        color: var(--muted);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .option-image {
        max-width: 100%;
        max-height: 140px;
        object-fit: contain;
        display: block;
    }

    .summary-list {
        margin: 20px 0 0;
        padding: 0;
        list-style: none;
        display: grid;
        gap: 12px;
    }

    .summary-list li {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 16px;
        border-radius: 10px;
        border: 1px solid var(--line);
        background: var(--soft);
    }

    .summary-list strong {
        font-size: 14px;
        color: var(--ink);
    }

    .violation-panel {
        position: fixed;
        inset: 0;
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(12, 32, 24, 0.4);
        backdrop-filter: blur(4px);
    }

    .violation-panel.active {
        display: flex;
    }

    .violation-card {
        width: min(100%, 540px);
        padding: 32px;
        border-radius: 12px;
        background: #fff;
        border: 1px solid var(--line);
        box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        text-align: center;
    }

    .violation-card i {
        font-size: 48px;
        color: var(--danger);
        margin-bottom: 16px;
        display: block;
    }

    .status-inline {
        margin-top: 16px;
        color: var(--muted);
        font-size: 12px;
        font-weight: 600;
    }

    .stats-container {
        max-width: 1000px;
        margin: 0 auto;
        width: 100%;
    }

    .metric-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }

    .metric-box {
        padding: 20px;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 12px;
        box-shadow: var(--shadow);
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .metric-box .label {
        font-size: 11px;
        font-weight: 800;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .metric-box strong {
        font-size: 24px;
        font-weight: 800;
        color: var(--ink);
    }

    .progress-card {
        padding: 20px;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 12px;
        box-shadow: var(--shadow);
        margin-bottom: 32px;
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .progress-header h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 800;
        color: var(--ink);
    }

    .progress-bar-container {
        width: 100%;
        height: 8px;
        background: var(--soft);
        border-radius: 999px;
        overflow: hidden;
        border: 1px solid var(--line);
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--green-1), var(--green-3));
        width: 0;
        transition: width 0.5s ease;
    }

    .progress-footer {
        margin-top: 8px;
        font-size: 12px;
        color: var(--muted);
        font-weight: 600;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 860px) {
        .intro-layout,
        .form-grid,
        .metric-row {
            grid-template-columns: 1fr;
        }

        .question-head {
            flex-direction: column;
        }
    }

    .custom-alert-overlay {
        position: fixed;
        inset: 0;
        z-index: 2000;
        background: rgba(0, 0, 0, 0.45);
        backdrop-filter: blur(2px);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
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
        max-width: 320px;
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

    .custom-alert-btn {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 8px;
        background: var(--green-1);
        color: #fff;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: opacity 0.2s;
    }

    .custom-alert-btn:hover {
        opacity: 0.85;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="workspace">
    <div class="stats-container">
        <div class="metric-row">
            <div class="metric-box">
                <span class="label">Durasi Sesi</span>
                <strong id="durationMetric"><?= esc($durationMinutes) ?>m</strong>
            </div>
            <div class="metric-box">
                <span class="label">Pelanggaran</span>
                <strong id="violationMetric" class="text-danger">0</strong>
            </div>
            <div class="metric-box">
                <span class="label">Progress</span>
                <strong id="progressMetric">0%</strong>
            </div>
            <div class="metric-box">
                <span class="label">Total Soal</span>
                <strong id="questionCountMetric">0</strong>
            </div>
        </div>

        <div class="progress-card">
            <div class="progress-header">
                <h4>Status Kelengkapan Jawaban</h4>
                <span class="badge bg-light text-success fw-bold" id="progressMetricBadge">0%</span>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" id="progressFill"></div>
            </div>
            <div class="progress-footer" id="answeredMetric">
                0 dari 0 soal telah dijawab
            </div>
        </div>
    </div>

    <section class="state intro-state active" id="introState">
        <div class="intro-card">
            <span class="pill alert mb-3">Sesi Aman</span>
            <h2>Siapkan peserta sebelum memulai tes aptitude</h2>
            <p>Aplikasi ini menampilkan 60 soal aptitude pilihan ganda secara berurutan. Setelah dimulai, peserta akan diarahkan ke mode fullscreen dan aktivitas keluar halaman tetap dipantau.</p>

            <div class="intro-layout">
                <div class="intro-block">
                    <h3>Data Calon Pegawai</h3>
                    <div class="form-grid">
                        <div>
                            <label>Nama Lengkap</label>
                            <input id="candidateName" type="text" placeholder="Andi Saputra">
                        </div>
                        <div>
                            <label>Posisi Dilamar</label>
                            <input id="positionName" type="text" placeholder="Staff Administrasi">
                        </div>
                    </div>
                    <div class="form-grid">
                        <div>
                            <label>Nama HRD</label>
                            <input id="hrdName" type="text" placeholder="Ibu Rina">
                        </div>
                        <div>
                            <label>Kode Sesi</label>
                            <input id="sessionCode" type="text" placeholder="HRD-2026-01">
                        </div>
                    </div>
                </div>

                <div class="intro-block">
                    <h3>Briefing Sesi</h3>
                    <p class="small text-muted mb-2">Peserta harus fokus pada satu layar, tidak membuka tab atau aplikasi lain, dan memilih satu jawaban terbaik untuk setiap soal.</p>
                    <p class="small text-muted">Jika sesi dikunci karena pelanggaran, akses akan diblokir otomatis untuk mencegah kecurangan.</p>
                </div>
            </div>

            <div class="actions">
                <button class="btn btn-primary" id="startBtn">
                    <i class="bi bi-fullscreen"></i> Mulai Tes Fullscreen
                </button>
                <?php if (in_array($authUser['role'] ?? '', ['hrd', 'manager'], true)) : ?>
                    <button class="btn btn-secondary" id="previewBtn">
                        <i class="bi bi-eye"></i> Lihat Soal
                    </button>
                <?php endif; ?>
            </div>
            <div class="status-inline" id="introStatus">Mode aman belum aktif.</div>
        </div>
    </section>

    <section class="state question-state" id="questionState">
        <div class="question-card">
            <div class="question-head">
                <div>
                    <span class="pill mb-2" id="questionCategory">Tes Aptitude - Sesi <?= $currentSession ?></span>
                    <h2 id="questionTitle" class="mb-3">Soal Aptitude</h2>
                    <div class="question-meta">
                        <span class="pill" id="questionIndex">Soal 1 / 60</span>
                        <span class="pill" id="candidateBadge">Peserta belum diisi</span>
                        <span class="pill alert" id="securityBadge">Mode aman aktif</span>
                    </div>
                </div>
                <div class="prompt-block">
                    <h3>Petunjuk</h3>
                    <p class="small mb-0" id="questionInstruction">Pilih satu jawaban yang paling benar, lalu lanjut ke soal berikutnya.</p>
                </div>
            </div>

            <div class="summary-block mb-4">
                <h3 class="mb-2">Pertanyaan</h3>
                <div class="question-body">
                    <p id="questionText" class="question-prompt"></p>
                    <div id="questionPromptImageWrap" class="prompt-image-wrap">
                        <img id="questionPromptImage" class="prompt-image" alt="Ilustrasi soal aptitude">
                    </div>
                </div>
            </div>

            <div class="option-panel">
                <h3>Pilihan Jawaban</h3>
                <div id="answerOptions" class="answer-options"></div>
                <span class="field-hint">Gunakan tombol Sebelumnya dan Simpan &amp; Lanjut untuk berpindah soal. Jawaban akan tersimpan otomatis saat pilihan berubah.</span>
            </div>

            <div class="actions">
                <button class="btn btn-secondary" id="prevBtn">
                    <i class="bi bi-chevron-left"></i> Sebelumnya
                </button>
                <button class="btn btn-primary" id="nextBtn">
                    Simpan &amp; Lanjut <i class="bi bi-chevron-right ms-2"></i>
                </button>
                <button class="btn btn-secondary" id="finishBtn">
                    <i class="bi bi-check-all"></i> Selesaikan Tes
                </button>
            </div>
        </div>
    </section>

    <section class="state summary-state" id="summaryState">
        <div class="summary-card">
            <h2>Sesi Tes Selesai</h2>
            <p>Terima kasih telah menyelesaikan sesi tes aptitude. Berikut adalah ringkasan sesi Anda.</p>

            <div class="summary-block mt-4">
                <h3>Detail Sesi</h3>
                <ul class="summary-list">
                    <li><strong>Total Soal</strong> <span id="summaryTotal">0</span></li>
                    <li><strong>Terjawab</strong> <span id="summaryAnswered">0</span></li>
                    <li><strong>Durasi Total</strong> <span id="summaryDuration">0m</span></li>
                    <li><strong>Total Pelanggaran</strong> <span id="summaryViolations" class="fw-bold">0</span></li>
                </ul>
            </div>

            <div class="actions">
                <button class="btn btn-primary" onclick="location.reload()">
                    <i class="bi bi-arrow-repeat"></i> Mulai Sesi Baru
                </button>
                <a href="<?= esc($dashboardUrl) ?>" class="btn btn-secondary">
                    <i class="bi bi-grid"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </section>

    <section class="state lock-state" id="lockState">
        <div class="lock-card text-center">
            <i class="bi bi-shield-slash text-danger" style="font-size: 64px; display: block; margin-bottom: 20px;"></i>
            <h2 class="text-danger">Sesi Dikunci Otomatis</h2>
            <p>Terjadi pelanggaran keamanan serius (keluar dari mode fullscreen atau berpindah tab terlalu sering). Sesi ini telah dihentikan secara permanen.</p>

            <div class="alert alert-danger border-0 mt-4">
                <strong>ID Peserta:</strong> <span id="lockCandidateID"></span><br>
                <strong>Status:</strong> Diblokir Sementara
            </div>

            <div class="actions justify-content-center">
                <a href="<?= esc($logoutUrl) ?>" class="btn btn-primary">Keluar Aplikasi</a>
            </div>
        </div>
    </section>
</div>

<div class="violation-panel" id="violationPanel">
    <div class="violation-card">
        <i class="bi bi-exclamation-triangle"></i>
        <h2>Peringatan Keamanan</h2>
        <p id="violationMsg">Harap kembali ke mode fullscreen untuk melanjutkan sesi.</p>
        <div class="d-grid">
            <button class="btn btn-primary" id="returnBtn">Kembali ke Sesi</button>
        </div>
    </div>
</div>

<div class="custom-alert-overlay" id="customAlertOverlay">
    <div class="custom-alert-box">
        <div class="custom-alert-icon" id="customAlertIcon">
            <i class="bi bi-exclamation-circle-fill"></i>
        </div>
        <h3 class="custom-alert-title" id="customAlertTitle">Akses Ditolak</h3>
        <p class="custom-alert-message" id="customAlertMessage">Pesan error di sini.</p>
        <button class="custom-alert-btn" id="customAlertBtn">Mengerti</button>
    </div>
</div>

<div class="modal fade" id="rulesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-800">Aturan Tes Aptitude</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <ol class="rules">
                    <li>Peserta wajib dalam mode <strong>Fullscreen</strong> selama tes berlangsung.</li>
                    <li>Dilarang membuka tab atau aplikasi lain selama pengerjaan.</li>
                    <li>Setiap aktivitas keluar halaman akan dicatat sebagai <strong>pelanggaran</strong>.</li>
                    <li>Jika pelanggaran melebihi batas, sesi akan <strong>dikunci otomatis</strong>.</li>
                    <li>Jawaban akan tersimpan otomatis setiap kali Anda memilih opsi atau menekan tombol Lanjut.</li>
                </ol>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Saya Mengerti</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const interviewData = <?= json_encode($questions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const interviewState = {
        current: 0,
        answers: {},
        startTime: Date.now(),
        violations: 0,
        maxViolations: <?= $maxViolations ?>,
        tabSwitches: 0,
        tabSwitchLimit: <?= $tabSwitchLimit ?>,
        isLocked: false,
        candidateName: '',
        positionName: '',
        hrdName: '',
        sessionCode: '',
        isStarted: false,
        currentSession: <?= $currentSession ?>,
    };

    const existingSession = <?= json_encode($existingSession ?? null, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    if (existingSession) {
        interviewState.violations = parseInt(existingSession.violations_count || 0, 10);
        interviewState.tabSwitches = parseInt(existingSession.tab_switches || 0, 10);
        interviewState.isLocked = existingSession.is_blocked == "1" || existingSession.is_blocked === true;
        if (existingSession.candidate_name) interviewState.candidateName = existingSession.candidate_name;
        if (existingSession.position_name) interviewState.positionName = existingSession.position_name;
        if (existingSession.hrd_name) interviewState.hrdName = existingSession.hrd_name;
        if (existingSession.session_code) interviewState.sessionCode = existingSession.session_code;
        if (existingSession.current_question) {
            interviewState.current = parseInt(existingSession.current_question || 0, 10);
        }
        if (existingSession.answers) {
            try {
                interviewState.answers = JSON.parse(existingSession.answers) || {};
            } catch (e) {}
        }
    }

    interviewState.current = Math.min(
        Math.max(interviewState.current, 0),
        Math.max(interviewData.length - 1, 0)
    );

    const el = {
        introState: document.getElementById('introState'),
        questionState: document.getElementById('questionState'),
        summaryState: document.getElementById('summaryState'),
        lockState: document.getElementById('lockState'),
        violationPanel: document.getElementById('violationPanel'),
        returnBtn: document.getElementById('returnBtn'),
        startBtn: document.getElementById('startBtn'),
        previewBtn: document.getElementById('previewBtn'),
        nextBtn: document.getElementById('nextBtn'),
        prevBtn: document.getElementById('prevBtn'),
        finishBtn: document.getElementById('finishBtn'),
        violationMsg: document.getElementById('violationMsg'),
        questionTitle: document.getElementById('questionTitle'),
        questionText: document.getElementById('questionText'),
        questionCategory: document.getElementById('questionCategory'),
        questionInstruction: document.getElementById('questionInstruction'),
        questionIndex: document.getElementById('questionIndex'),
        questionPromptImageWrap: document.getElementById('questionPromptImageWrap'),
        questionPromptImage: document.getElementById('questionPromptImage'),
        answerOptions: document.getElementById('answerOptions'),
        candidateBadge: document.getElementById('candidateBadge'),
        progressFill: document.getElementById('progressFill'),
        progressMetric: document.getElementById('progressMetric'),
        progressMetricBadge: document.getElementById('progressMetricBadge'),
        answeredMetric: document.getElementById('answeredMetric'),
        violationMetric: document.getElementById('violationMetric'),
        durationMetric: document.getElementById('durationMetric'),
        questionCountMetric: document.getElementById('questionCountMetric'),
        lockCandidateID: document.getElementById('lockCandidateID'),
        candidateNameInp: document.getElementById('candidateName'),
        positionNameInp: document.getElementById('positionName'),
        hrdNameInp: document.getElementById('hrdName'),
        sessionCodeInp: document.getElementById('sessionCode'),
    };

    function showCustomAlert(msg, title = 'Akses Ditolak', icon = 'bi-exclamation-circle-fill', btnText = 'Mengerti', callback = null) {
        const overlay = document.getElementById('customAlertOverlay');
        const titleEl = document.getElementById('customAlertTitle');
        const msgEl = document.getElementById('customAlertMessage');
        const btnEl = document.getElementById('customAlertBtn');
        const iconEl = document.getElementById('customAlertIcon');

        titleEl.textContent = title;
        msgEl.textContent = msg;
        btnEl.textContent = btnText;
        iconEl.innerHTML = `<i class="bi ${icon}"></i>`;
        
        // Change icon color based on title or icon type if needed
        if (title.toLowerCase().includes('selesai') || title.toLowerCase().includes('sukses')) {
            iconEl.style.color = 'var(--green-1)';
        } else {
            iconEl.style.color = '#f5a623'; // Default orange
        }

        overlay.style.display = 'flex';

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                overlay.classList.add('active');
            });
        });

        btnEl.onclick = () => {
            overlay.classList.remove('active');
            setTimeout(() => {
                overlay.style.display = 'none';
                if (callback) callback();
            }, 300);
        };
    }

    function updateMetrics() {
        const total = interviewData.length;
        const answered = Object.keys(interviewState.answers).length;
        const pct = total > 0 ? Math.round((answered / total) * 100) : 0;

        if (el.progressFill) el.progressFill.style.width = pct + '%';
        if (el.progressMetric) el.progressMetric.textContent = pct + '%';
        if (el.progressMetricBadge) el.progressMetricBadge.textContent = pct + '%';
        if (el.answeredMetric) el.answeredMetric.textContent = `${answered} dari ${total} soal telah dijawab`;
        if (el.questionCountMetric) el.questionCountMetric.textContent = total;

        const violationText = interviewState.tabSwitches > 0
            ? interviewState.violations + ' / ' + interviewState.maxViolations + ' | Tab ' + interviewState.tabSwitches + ' / ' + interviewState.tabSwitchLimit
            : interviewState.violations + ' / ' + interviewState.maxViolations;

        if (el.violationMetric) el.violationMetric.textContent = violationText;
    }

    function renderOptions(question) {
        const selected = interviewState.answers[question.id] || '';
        el.answerOptions.innerHTML = '';

        question.options.forEach((option) => {
            const wrapper = document.createElement('label');
            wrapper.className = 'option-card';

            const input = document.createElement('input');
            input.type = 'radio';
            input.name = 'answerOption';
            input.value = option.value;
            input.checked = selected === option.value;

            const badge = document.createElement('span');
            badge.className = 'choice-chip';
            badge.textContent = option.label || option.value.toUpperCase();

            const content = document.createElement('div');
            content.className = 'option-content';

            if (option.imageUrl) {
                const image = document.createElement('img');
                image.src = option.imageUrl;
                image.alt = `Pilihan ${option.label || option.value.toUpperCase()}`;
                image.className = 'option-image';
                content.appendChild(image);
            }

            const text = document.createElement('span');
            text.className = 'option-text';
            if (option.text) {
                text.textContent = option.text;
            } else if (!option.imageUrl) {
                text.textContent = `Pilih opsi ${option.label || option.value.toUpperCase()}`;
                text.classList.add('compact');
            }

            if (text.textContent !== '') {
                content.appendChild(text);
            }

            wrapper.appendChild(badge);
            wrapper.appendChild(input);
            wrapper.appendChild(content);

            if (input.checked) {
                wrapper.classList.add('active');
            }

            input.addEventListener('change', () => {
                el.answerOptions.querySelectorAll('.option-card').forEach((card) => card.classList.remove('active'));
                wrapper.classList.add('active');
                saveCurrentAnswer();
            });

            el.answerOptions.appendChild(wrapper);
        });
    }

    function renderQuestion() {
        const question = interviewData[interviewState.current];
        if (!question) {
            return;
        }

        el.questionTitle.textContent = `Soal ${question.number}`;
        el.questionText.textContent = question.prompt;
        el.questionCategory.textContent = 'Tes Aptitude';
        el.questionInstruction.textContent = `Pilih satu jawaban yang paling tepat. Progress soal ${question.number} dari ${interviewData.length}.`;
        el.questionIndex.textContent = `Soal ${question.number} / ${interviewData.length}`;

        if (question.promptImageUrl) {
            el.questionPromptImage.src = question.promptImageUrl;
            el.questionPromptImageWrap.classList.add('active');
        } else {
            el.questionPromptImage.removeAttribute('src');
            el.questionPromptImageWrap.classList.remove('active');
        }

        renderOptions(question);

        el.prevBtn.style.display = interviewState.current === 0 ? 'none' : 'block';
        el.nextBtn.style.display = interviewState.current === interviewData.length - 1 ? 'none' : 'block';
        el.finishBtn.style.display = interviewState.current === interviewData.length - 1 ? 'block' : 'none';
        updateMetrics();
    }

    function saveCurrentAnswer() {
        const question = interviewData[interviewState.current];
        if (!question) {
            return;
        }

        const checked = document.querySelector('input[name="answerOption"]:checked');
        if (checked) {
            interviewState.answers[question.id] = checked.value;
        } else {
            delete interviewState.answers[question.id];
        }

        updateMetrics();
        syncSession('answer_updated');
    }

    async function syncSession(eventType, msg = '') {
        const sessionIdVal = el.sessionCodeInp.value.trim() || 'SESI-<?= esc($authUser['id_user'] ?? uniqid()) ?>';

        try {
            await fetch("<?= esc($monitorEventUrl) ?>", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    eventType: eventType,
                    sessionId: sessionIdVal,
                    idUser: '<?= esc($authUser['id_user'] ?? '') ?>',
                    candidateName: interviewState.candidateName || el.candidateNameInp.value.trim(),
                    positionName: interviewState.positionName || el.positionNameInp.value.trim(),
                    hrdName: interviewState.hrdName || el.hrdNameInp.value.trim(),
                    sessionCode: interviewState.sessionCode || el.sessionCodeInp.value.trim(),
                    current_question: interviewState.current,
                    questionsTotal: interviewData.length,
                    violations: interviewState.violations,
                    tabSwitches: interviewState.tabSwitches,
                    blockedCandidate: interviewState.isLocked,
                    message: msg,
                    violationType: eventType === 'violation_detected' ? 'security_breach' : '',
                    answers: interviewState.answers,
                    currentSession: interviewState.currentSession
                })
            });
        } catch (e) {}
    }

    async function registerSecurityViolation(type, msg) {
        if (interviewState.isLocked || !interviewState.isStarted) return;

        if (type === 'tab') interviewState.tabSwitches++;
        else interviewState.violations++;

        updateMetrics();

        if (interviewState.violations >= interviewState.maxViolations || interviewState.tabSwitches >= interviewState.tabSwitchLimit) {
            lockSesi();
            await syncSession('session_locked', msg);
        } else {
            el.violationMsg.textContent = msg;
            el.violationPanel.classList.add('active');
            await syncSession('violation_detected', msg);
        }
    }

    function lockSesi() {
        interviewState.isLocked = true;
        el.violationPanel.classList.remove('active');
        document.querySelectorAll('.state').forEach((state) => state.classList.remove('active'));
        el.lockState.classList.add('active');
        el.lockCandidateID.textContent = interviewState.candidateName || el.candidateNameInp.value.trim() || 'Unknown';
        if (document.fullscreenElement) document.exitFullscreen();
    }

    async function startInterview() {
        const name = el.candidateNameInp.value.trim();
        const pos = el.positionNameInp.value.trim();
        const sessionCode = el.sessionCodeInp.value.trim();
        if (!name || !pos) return showCustomAlert('Mohon isi nama dan posisi terlebih dahulu.', 'Data Belum Lengkap');

        el.startBtn.disabled = true;
        const originalBtnText = el.startBtn.innerHTML;
        el.startBtn.innerHTML = 'Memeriksa...';

        try {
            const checkRes = await fetch('<?= site_url('tes-interview/check') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ candidateName: name, sessionCode: sessionCode })
            });
            const checkData = await checkRes.json();
            if (checkData.blocked) {
                showCustomAlert('Peserta ini sudah terblokir karena pelanggaran dan tidak dapat mengikuti tes kembali. Silakan konfirmasi ke Manager/HRD.');
                el.candidateNameInp.disabled = true;
                el.positionNameInp.disabled = true;
                el.hrdNameInp.disabled = true;
                el.sessionCodeInp.disabled = true;
                el.startBtn.innerHTML = '<i class=\"bi bi-fullscreen\"></i> Mulai Tes Fullscreen';
                document.getElementById('introStatus').innerHTML = '<span class=\"text-danger fw-bold\">Peserta ini sudah terblokir karena pelanggaran. Silakan konfirmasi ke Manager/HRD.</span>';
                return;
            }
        } catch (e) {
            console.error('Status check error:', e);
        }

        el.startBtn.disabled = false;
        el.startBtn.innerHTML = originalBtnText;

        interviewState.candidateName = name;
        interviewState.positionName = pos;
        interviewState.hrdName = el.hrdNameInp.value.trim();
        interviewState.sessionCode = sessionCode;
        interviewState.isStarted = true;

        el.candidateBadge.textContent = name;
        el.introState.classList.remove('active');
        el.questionState.classList.add('active');
        renderQuestion();

        syncSession('session_started');

        document.documentElement.requestFullscreen().catch(() => {
            showCustomAlert('Gagal masuk mode fullscreen. Mohon gunakan browser terbaru dan berikan izin.', 'Fullscreen Gagal');
        });
    }

    el.startBtn.addEventListener('click', startInterview);

    if (el.previewBtn) {
        el.previewBtn.addEventListener('click', () => {
            interviewState.isStarted = false;
            el.introState.classList.remove('active');
            el.questionState.classList.add('active');
            renderQuestion();
        });
    }

    el.nextBtn.addEventListener('click', () => {
        saveCurrentAnswer();
        interviewState.current++;
        renderQuestion();
    });

    el.prevBtn.addEventListener('click', () => {
        saveCurrentAnswer();
        interviewState.current--;
        renderQuestion();
    });

    el.finishBtn.addEventListener('click', async () => {
        saveCurrentAnswer();
        
        el.finishBtn.disabled = true;
        el.finishBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';

        const res = await fetch("<?= esc($completeSessionUrl) ?>", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                sessionId: el.sessionCodeInp.value.trim() || 'SESI-<?= esc($authUser['id_user'] ?? uniqid()) ?>',
                currentSession: interviewState.currentSession
            })
        });
        const result = await res.json();
        
        if (result.ok) {
            if (result.next) {
                // Go to next session
                showCustomAlert(
                    'Anda akan melanjutkan ke Sesi ' + result.session + '. Pastikan Anda siap sebelum melanjutkan.',
                    'Sesi ' + interviewState.currentSession + ' Selesai',
                    'bi-check-circle-fill',
                    'Lanjut ke Sesi ' + result.session,
                    () => {
                        location.reload();
                    }
                );
            } else {
                // All sessions completed
                const total = interviewData.length;
                const answered = Object.keys(interviewState.answers).length;
                const duration = Math.round((Date.now() - interviewState.startTime) / 60000);

                document.getElementById('summaryTotal').textContent = total;
                document.getElementById('summaryAnswered').textContent = answered;
                document.getElementById('summaryDuration').textContent = duration + 'm';
                document.getElementById('summaryViolations').textContent = interviewState.violations;

                el.questionState.classList.remove('active');
                el.summaryState.classList.add('active');
                if (document.fullscreenElement) document.exitFullscreen();

                syncSession('session_completed');
            }
        } else {
            showCustomAlert('Gagal menyelesaikan sesi: ' + (result.message || 'Error unknown'), 'Gagal Menyimpan');
            el.finishBtn.disabled = false;
            el.finishBtn.innerHTML = '<i class="bi bi-check-all"></i> Selesaikan Tes';
        }
    });

    el.returnBtn.addEventListener('click', () => {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        }
        el.violationPanel.classList.remove('active');
    });

    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement && !interviewState.isLocked && interviewState.isStarted) {
            registerSecurityViolation('fullscreen', 'Peserta keluar dari mode fullscreen.');
        }
    });

    window.addEventListener('blur', () => {
        if (!interviewState.isLocked && interviewState.isStarted) {
            registerSecurityViolation('tab', 'Peserta terdeteksi berpindah tab atau aplikasi.');
        }
    });

    updateMetrics();

    if (existingSession) {
        if (existingSession.candidate_name) el.candidateNameInp.value = existingSession.candidate_name;
        if (existingSession.position_name) el.positionNameInp.value = existingSession.position_name;
        if (existingSession.hrd_name) el.hrdNameInp.value = existingSession.hrd_name;
        if (existingSession.session_code) el.sessionCodeInp.value = existingSession.session_code;

        if (interviewState.isLocked) {
            el.candidateNameInp.disabled = true;
            el.positionNameInp.disabled = true;
            el.hrdNameInp.disabled = true;
            el.sessionCodeInp.disabled = true;
            el.startBtn.disabled = true;
            document.getElementById('introStatus').innerHTML = '<span class="text-danger fw-bold">Peserta ini sudah terblokir karena pelanggaran. Silakan konfirmasi ke Manager/HRD.</span>';
        }
    }
</script>
<?= $this->endSection() ?>
