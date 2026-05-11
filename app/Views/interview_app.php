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

    /* DISC Sesi 1 — tabel seperti lembar kertas tes (No | Pasangan | Mirip | Tidak Mirip) */
    .disc-question-block {
        margin-bottom: 28px;
    }

    .disc-question-block:last-child {
        margin-bottom: 0;
    }

    .disc-table-container {
        width: 100%;
        overflow-x: auto;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }

    .disc-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        font-size: 14px;
        color: var(--ink);
    }

    .disc-table thead th {
        background: #e9ecef;
        padding: 12px 14px;
        font-size: 13px;
        font-weight: 700;
        color: #212529;
        text-align: center;
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }

    .disc-table thead th.disc-th-pasangan {
        text-align: left;
    }

    .disc-table td {
        padding: 12px 14px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }

    .disc-table tbody tr:nth-child(even) td {
        background: #fff;
    }

    .disc-table tbody tr:hover td {
        background: #f1f3f5;
    }

    .disc-no-cell {
        font-size: 24px;
        font-weight: 800;
        color: #343a40;
        text-align: center;
        width: 60px;
        min-width: 60px;
        background: #fff !important;
        border-right: 1px solid #dee2e6;
        vertical-align: middle;
    }

    .disc-text-cell {
        font-size: 14px;
        font-weight: 500;
        line-height: 1.5;
        text-align: left;
    }

    .disc-text-cell .disc-option-label {
        display: inline-block;
        min-width: 1.25rem;
        font-weight: 700;
        margin-right: 6px;
        color: #212529;
    }

    .disc-radio-cell {
        text-align: center;
        width: 100px;
        min-width: 88px;
        background: #fff;
    }

    .disc-radio-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        margin: 0 auto;
    }

    .disc-radio-wrapper input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 1px;
        height: 1px;
        margin: 0;
        cursor: pointer;
    }

    .disc-radio-custom {
        height: 20px;
        width: 20px;
        background-color: #fff;
        border: 2px solid #868e96;
        border-radius: 50%;
        transition: border-color 0.15s ease, background-color 0.15s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
    }

    .disc-radio-wrapper:hover .disc-radio-custom {
        border-color: var(--green-1);
    }

    .disc-radio-wrapper input:focus-visible ~ .disc-radio-custom {
        outline: 2px solid var(--green-2);
        outline-offset: 2px;
    }

    .disc-radio-wrapper input:checked ~ .disc-radio-custom {
        background-color: #fff;
        border-color: var(--green-1);
        border-width: 2px;
    }

    .disc-radio-custom:after {
        content: "";
        display: none;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--green-1);
    }

    .disc-radio-wrapper input:checked ~ .disc-radio-custom:after {
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

    /* Rules Modal Custom Styling */
    #rulesModalOverlay .custom-alert-box {
        max-width: 480px;
        text-align: left;
        padding: 32px;
        border-radius: 20px;
    }

    #rulesModalOverlay .rules-list {
        list-style: none;
        padding: 0;
        margin: 0 0 28px 0;
    }

    #rulesModalOverlay .rules-list li {
        position: relative;
        padding-left: 32px;
        margin-bottom: 16px;
        font-size: 14.5px;
        line-height: 1.6;
        color: #333;
        font-weight: 500;
    }

    #rulesModalOverlay .rules-list li::before {
        content: "\F26A"; /* bi-check-circle-fill */
        font-family: "bootstrap-icons";
        position: absolute;
        left: 0;
        top: 2px;
        color: var(--green-1);
        font-size: 20px;
    }

    #rulesModalOverlay .custom-alert-title {
        font-size: 24px;
        margin-bottom: 20px;
        text-align: center;
        color: var(--ink);
        font-weight: 800;
    }

    .session-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
        border-bottom: 2px solid var(--line);
        padding-bottom: 0;
    }

    .session-tab {
        padding: 12px 24px;
        border-radius: 12px 12px 0 0;
        border: 1px solid transparent;
        cursor: pointer;
        font-weight: 800;
        color: var(--muted);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        background: var(--soft);
        margin-bottom: -2px;
        border: 1px solid var(--line);
        border-bottom: 0;
    }

    .session-tab:hover {
        background: #fff;
        color: var(--green-1);
    }

    .session-tab.active {
        background: #fff;
        color: var(--green-1);
        border-top: 3px solid var(--green-1);
        border-bottom: 2px solid #fff;
        z-index: 2;
    }

    .tab-badge {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 999px;
        background: var(--line);
        color: var(--muted);
        font-weight: 800;
    }

    .session-tab.active .tab-badge {
        background: var(--green-1);
        color: #fff;
    }

    .session-tab.locked {
        opacity: 0.6;
        cursor: not-allowed;
        background: #f8f9fa;
        color: #adb5bd;
        border-color: #e9ecef;
    }

    .session-tab.locked:hover {
        background: #f8f9fa;
        color: #adb5bd;
    }

    .session-tab.locked .tab-badge {
        background: #e9ecef;
        color: #adb5bd;
    }

    /* Session Summary Grid */
    .session-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .session-detail-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid var(--line);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
    }

    .session-detail-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        border-color: var(--green-1);
    }

    .session-detail-card.active {
        border-color: var(--green-1);
        background: linear-gradient(135deg, #fff 0%, #f0fdf4 100%);
    }

    .session-detail-card.completed {
        background: #f8f9fa;
        opacity: 0.85;
    }

    .session-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .session-card-header h4 {
        margin: 0;
        font-weight: 800;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .session-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .stat-item .label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--muted);
        font-weight: 700;
    }

    .stat-item .value {
        font-size: 16px;
        font-weight: 800;
        color: var(--dark);
    }

    .session-status-badge {
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .status-belum_dibuka { background: #f1f3f5; color: #868e96; }
    .status-dibuka { background: #e7f5ff; color: #228be6; }
    .status-berjalan { background: #fff4e6; color: #fd7e14; animation: pulse 2s infinite; }
    .status-selesai { background: #ebfbee; color: #40c057; }

    .btn-session-start {
        margin-top: auto;
        width: 100%;
        padding: 12px;
        border-radius: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-session-start:disabled {
        background: #f1f3f5;
        color: #adb5bd;
        border: 1px solid #dee2e6;
        cursor: not-allowed;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
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
            <?php 
                $currentControlStatus = $allSessionControls[$currentSession]['status_sesi'] ?? 'belum_dibuka';
                $isCurrentOpened = ($currentControlStatus === 'dibuka');
            ?>

            <div class="session-summary-grid mt-2">
                <?php for ($i = 1; $i <= 3; $i++): 
                    $qCount = count($allQuestions[$i] ?? []);
                    $aCount = count($allAnswers[$i] ?? []);
                    $status = $allSessionStatuses[$i]['status_sesi'] ?? 'belum_mulai';
                    $control = $allSessionControls[$i]['status_sesi'] ?? 'belum_dibuka';
                    $violations = $sessionViolations[$i] ?? 0;
                    
                    // Logic for button accessibility
                    $canStart = false;
                    $lockMessage = "";
                    
                    if ($control === 'belum_dibuka') {
                        $lockMessage = "Sesi ini belum dibuka oleh Manager/HRD.";
                    } elseif ($status === 'selesai') {
                        $lockMessage = "Sesi ini sudah Anda selesaikan.";
                    } else {
                        // Progression check
                        if ($i === 1) {
                            $canStart = true;
                        } elseif ($i === 2) {
                            $prevStatus = $allSessionStatuses[1]['status_sesi'] ?? 'belum_mulai';
                            if ($prevStatus === 'selesai') $canStart = true;
                            else $lockMessage = "Selesaikan Sesi 1 terlebih dahulu.";
                        } elseif ($i === 3) {
                            $prevStatus = $allSessionStatuses[2]['status_sesi'] ?? 'belum_mulai';
                            if ($prevStatus === 'selesai') $canStart = true;
                            else $lockMessage = "Selesaikan Sesi 2 terlebih dahulu.";
                        }
                    }

                    $isActive = ($currentSession == $i && $canStart);
                ?>
                    <div class="session-detail-card <?= $isActive ? 'active' : '' ?> <?= $status === 'selesai' ? 'completed' : '' ?>">
                        <div class="session-card-header">
                            <h4>
                                <i class="bi bi-journal-text"></i>
                                Sesi <?= $i ?>
                            </h4>
                            <span class="session-status-badge status-<?= $control === 'dibuka' ? $status : $control ?>">
                                <?= str_replace('_', ' ', $control === 'dibuka' ? $status : $control) ?>
                            </span>
                        </div>
                        
                        <div class="session-stats">
                            <div class="stat-item">
                                <span class="label">Total Soal</span>
                                <span class="value"><?= $qCount ?></span>
                            </div>
                            <div class="stat-item">
                                <span class="label">Terjawab</span>
                                <span class="value"><?= $aCount ?></span>
                            </div>
                            <div class="stat-item">
                                <span class="label">Pelanggaran</span>
                                <span class="value text-<?= $violations > 0 ? 'danger' : 'success' ?>"><?= $violations ?></span>
                            </div>
                            <div class="stat-item">
                                <span class="label">Durasi</span>
                                <span class="value"><?= $allSessionStatuses[$i]['durasi_menit'] ?? 15 ?>m</span>
                            </div>
                        </div>

                        <?php if ($status === 'selesai'): ?>
                            <button class="btn btn-session-start disabled" disabled>
                                <i class="bi bi-check-circle-fill text-success"></i> Sesi Selesai
                            </button>
                        <?php elseif ($control === 'belum_dibuka'): ?>
                            <button type="button" class="btn btn-session-start js-session-alert" data-alert-title="Sesi Belum Tersedia" data-alert-message="<?= esc($lockMessage, 'attr') ?>" data-alert-icon="bi-lock-fill">
                                <i class="bi bi-lock-fill"></i> Belum Dibuka
                            </button>
                        <?php elseif (!$canStart): ?>
                            <button type="button" class="btn btn-session-start js-session-alert" data-alert-title="Sesi Terkunci" data-alert-message="<?= esc($lockMessage, 'attr') ?>" data-alert-icon="bi-lock-fill">
                                <i class="bi bi-lock-fill"></i> Terkunci
                            </button>
                        <?php else: ?>
                            <button type="button" onclick="window.startSpecificSession(<?= (int) $i ?>)" class="btn btn-primary btn-session-start" data-start-session="<?= (int) $i ?>">
                                <i class="bi bi-play-fill"></i> <?= ($status === 'berjalan') ? 'Lanjutkan Sesi' : 'Mulai Sesi ' . $i ?>
                            </button>

                        <?php endif; ?>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="intro-layout d-none">
                <div class="intro-block">
                    <h3>Data Calon Pegawai</h3>
                    <div class="form-grid">
                        <div>
                            <label>Nama Lengkap</label>
                            <input id="candidateName" type="text" value="<?= esc($authUser['name'] ?? '') ?>" readonly>
                        </div>
                        <div>
                            <label>Posisi Dilamar</label>
                            <input id="positionName" type="text" value="<?= esc($existingSession['position_name'] ?? 'Peserta') ?>" readonly>
                        </div>
                    </div>
                    <input id="hrdName" type="hidden" value="<?= esc($existingSession['hrd_name'] ?? ($defaultIdentity['hrdName'] ?? 'System')) ?>">
                    <input id="sessionCode" type="hidden" value="<?= esc($existingSession['session_code'] ?? ($defaultIdentity['sessionCode'] ?? ('SESI-' . ($authUser['id_user'] ?? uniqid())))) ?>">
                </div>
            </div>

            <div class="actions mt-4 justify-content-center">
                <button class="btn btn-secondary" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise"></i> Refresh Status
                </button>
                
                <?php if (in_array($authUser['role'] ?? '', ['hrd', 'manager'], true)) : ?>
                    <button class="btn btn-secondary" id="previewBtn">
                        <i class="bi bi-eye"></i> Mode Preview
                    </button>
                <?php endif; ?>

                <a href="<?= esc($logoutUrl) ?>" class="btn btn-outline-danger">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </a>
            </div>
            <div class="status-inline text-center" id="introStatus">Silakan klik tombol mulai pada sesi yang tersedia.</div>
        </div>
    </section>

    <section class="state question-state" id="questionState">
        <div class="question-card">
            <div class="session-tabs">
                <div class="session-tab active" data-session="1" id="tabSesi1">
                    Sesi 1 <span class="tab-badge" id="badgeSesi1">0/0</span>
                </div>
                <div class="session-tab" data-session="2" id="tabSesi2">
                    Sesi 2 <span class="tab-badge" id="badgeSesi2">0/0</span>
                </div>
                <div class="session-tab" data-session="3" id="tabSesi3">
                    Sesi 3 <span class="tab-badge" id="badgeSesi3">0/0</span>
                </div>
            </div>
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

<div class="custom-alert-overlay" id="rulesModalOverlay">
    <div class="custom-alert-box">
        <h3 class="custom-alert-title">Aturan Tes Aptitude</h3>
        <div class="custom-alert-message">
            <ul class="rules-list">
                <li>Peserta wajib dalam mode <strong>Fullscreen</strong> selama tes berlangsung.</li>
                <li>Dilarang membuka tab atau aplikasi lain selama pengerjaan.</li>
                <li>Setiap aktivitas keluar halaman akan dicatat sebagai <strong>pelanggaran</strong>.</li>
                <li>Jika pelanggaran melebihi batas, sesi akan <strong>dikunci otomatis</strong>.</li>
                <li>Jawaban akan tersimpan otomatis setiap kali Anda memilih opsi atau menekan tombol Lanjut.</li>
            </ul>
        </div>
        <div class="rules-modal-actions d-grid gap-2">
            <button type="button" class="custom-alert-btn" id="confirmRulesBtn">Paham, Mulai Tes</button>
            <button type="button" class="custom-alert-btn d-none" id="closeRulesBtn" style="background: var(--soft); color: var(--ink); border: 1px solid var(--line);">Tutup</button>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php
$__jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_INVALID_UTF8_SUBSTITUTE;
$__interviewBoot = [
    'allQuestions' => $allQuestions,
    'allAnswers'   => $allAnswers,
    'allSessionStatuses' => $allSessionStatuses,
    'existingSession'    => $existingSession ?? null,
];
$__interviewBootJson = json_encode($__interviewBoot, $__jsonFlags);
if ($__interviewBootJson === false) {
    $__interviewBootJson = '{"allQuestions":{"1":[],"2":[],"3":[]},"allAnswers":{"1":{},"2":{},"3":{}},"allSessionStatuses":{},"existingSession":null}';
}
$__scalarFlags = JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE;
$__authUserIdJs = json_encode((string) ($authUser['id_user'] ?? ''), $__scalarFlags);
if ($__authUserIdJs === false) {
    $__authUserIdJs = '""';
}
$__defaultSessionCodeJs = json_encode((string) ($defaultIdentity['sessionCode'] ?? ('SESI-' . ($authUser['id_user'] ?? uniqid()))), $__scalarFlags);
if ($__defaultSessionCodeJs === false) {
    $__defaultSessionCodeJs = '""';
}
helper('url');
$__fallbackNameJs = json_encode((string) ($authUser['name'] ?? 'Peserta'), $__scalarFlags);
if ($__fallbackNameJs === false) {
    $__fallbackNameJs = '"Peserta"';
}
$__fallbackPositionJs = json_encode((string) ($existingSession['position_name'] ?? 'Pegawai'), $__scalarFlags);
if ($__fallbackPositionJs === false) {
    $__fallbackPositionJs = '"Pegawai"';
}
$__fallbackSessionIdJs = json_encode((string) ($sessionId ?? ''), $__scalarFlags);
if ($__fallbackSessionIdJs === false) {
    $__fallbackSessionIdJs = '""';
}
$__tesInterviewUrlJs = json_encode(site_url('tes-interview'), $__scalarFlags);
if ($__tesInterviewUrlJs === false) {
    $__tesInterviewUrlJs = '""';
}
$__tesInterviewCheckUrlJs = json_encode(site_url('tes-interview/check'), $__scalarFlags);
if ($__tesInterviewCheckUrlJs === false) {
    $__tesInterviewCheckUrlJs = '""';
}
$__stateCandidateNameJs = json_encode((string) ($authUser['name'] ?? ''), $__scalarFlags);
if ($__stateCandidateNameJs === false) {
    $__stateCandidateNameJs = '""';
}
$__statePositionNameJs = json_encode((string) ($existingSession['position_name'] ?? 'Peserta'), $__scalarFlags);
if ($__statePositionNameJs === false) {
    $__statePositionNameJs = '"Peserta"';
}
$__stateHrdNameJs = json_encode((string) ($existingSession['hrd_name'] ?? ($defaultIdentity['hrdName'] ?? 'System')), $__scalarFlags);
if ($__stateHrdNameJs === false) {
    $__stateHrdNameJs = '"System"';
}
?>
<script type="application/json" id="interview-bootstrap-json"><?= $__interviewBootJson ?></script>
<script>
    (function removeStaleBootstrapModalBackdrop() {
        document.querySelectorAll('.modal-backdrop').forEach((n) => n.remove());
        document.body.classList.remove('modal-open');
        document.body.style.paddingRight = '';
    })();

    var __interviewBootParsed;
    try {
        __interviewBootParsed = JSON.parse(document.getElementById('interview-bootstrap-json').textContent);
    } catch (__e) {
        __interviewBootParsed = { allQuestions: { 1: [], 2: [], 3: [] }, allAnswers: { 1: {}, 2: {}, 3: {} }, allSessionStatuses: {}, existingSession: null };
    }
    const allQuestions = __interviewBootParsed.allQuestions;
    const allAnswers = __interviewBootParsed.allAnswers;

    const authUserId = <?= $__authUserIdJs ?>;
    const defaultSessionCode = <?= $__defaultSessionCodeJs ?>;

    function dismissRulesOverlay() {
        const rulesOverlay = document.getElementById('rulesModalOverlay');
        if (!rulesOverlay) return;
        rulesOverlay.classList.remove('active');
        setTimeout(() => { rulesOverlay.style.display = 'none'; }, 300);
    }

    function showRulesOverlayForStart(name, pos, sessionCode) {
        const rulesOverlay = document.getElementById('rulesModalOverlay');
        const confirmBtn = document.getElementById('confirmRulesBtn');
        const closeBtn = document.getElementById('closeRulesBtn');
        if (!rulesOverlay || !confirmBtn) return;

        if (closeBtn) {
            closeBtn.classList.remove('d-none');
            closeBtn.textContent = 'Batal';
            closeBtn.onclick = () => dismissRulesOverlay();
        }
        confirmBtn.classList.remove('d-none');
        confirmBtn.onclick = () => {
            dismissRulesOverlay();
            window.executeStartInterview(name, pos, sessionCode);
        };

        rulesOverlay.style.display = 'flex';
        requestAnimationFrame(() => {
            requestAnimationFrame(() => rulesOverlay.classList.add('active'));
        });
    }

    function showRulesOverlayReadOnly() {
        const rulesOverlay = document.getElementById('rulesModalOverlay');
        const confirmBtn = document.getElementById('confirmRulesBtn');
        const closeBtn = document.getElementById('closeRulesBtn');
        if (!rulesOverlay || !closeBtn) return;

        if (confirmBtn) confirmBtn.classList.add('d-none');
        closeBtn.classList.remove('d-none');
        closeBtn.textContent = 'Tutup';
        closeBtn.onclick = () => dismissRulesOverlay();

        rulesOverlay.style.display = 'flex';
        requestAnimationFrame(() => {
            requestAnimationFrame(() => rulesOverlay.classList.add('active'));
        });
    }

    const interviewState = {
        current: 0,
        allAnswers: allAnswers,
        startTime: Date.now(),
        violations: 0,
        maxViolations: <?= $maxViolations ?>,
        tabSwitches: 0,
        tabSwitchLimit: <?= $tabSwitchLimit ?>,
        isLocked: false,
        candidateName: <?= $__stateCandidateNameJs ?>,
        positionName: <?= $__statePositionNameJs ?>,
        hrdName: <?= $__stateHrdNameJs ?>,
        sessionCode: defaultSessionCode,
        isStarted: <?= ($sessionStatus && $sessionStatus['status_sesi'] === 'berjalan') ? 'true' : 'false' ?>,
        currentSession: <?= $currentSession ?>,
        timeLeftSeconds: <?= (int)$timeLeftSeconds ?>,
        isSubmittingFinal: false,
        isTestCompleted: <?= $allCompleted ? 'true' : 'false' ?>,
        allSessionStatuses: __interviewBootParsed.allSessionStatuses || {},
        isSubmittingSession: false,
        isTransitioningSession: false,
    };

    const existingSession = __interviewBootParsed.existingSession || null;
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
    }

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
        tabs: document.querySelectorAll('.session-tab'),
    };

    window.startSpecificSession = async function(sessionNum) {
        if (sessionNum !== interviewState.currentSession) {
            location.href = <?= $__tesInterviewUrlJs ?> + '?session=' + encodeURIComponent(String(sessionNum));
            return;
        }
        await window.startInterview();
    };

    window.startInterview = async function() {
        const name = readInputValue(el.candidateNameInp, interviewState.candidateName || <?= $__fallbackNameJs ?>);
        const pos = readInputValue(el.positionNameInp, interviewState.positionName || <?= $__fallbackPositionJs ?>);
        const sessionCode = readInputValue(el.sessionCodeInp, interviewState.sessionCode || <?= $__fallbackSessionIdJs ?>);

        showRulesOverlayForStart(name, pos, sessionCode);

        try {
            const checkRes = await fetch(<?= $__tesInterviewCheckUrlJs ?>, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ candidateName: name, sessionCode: sessionCode })
            });
            if (checkRes.ok) {
                const checkData = await checkRes.json();
                if (checkData.blocked) {
                    dismissRulesOverlay();
                    showCustomAlert('Peserta ini sudah terblokir karena pelanggaran dan tidak dapat mengikuti tes kembali. Silakan konfirmasi ke Manager/HRD.');
                    document.querySelectorAll('.btn-session-start').forEach(btn => { btn.disabled = true; });
                }
            }
        } catch (e) {
            console.warn('Background check failed:', e);
        }
    };

    window.executeStartInterview = function(name, pos, sessionCode) {
        interviewState.candidateName = name;
        interviewState.positionName = pos;
        interviewState.hrdName = readInputValue(el.hrdNameInp, interviewState.hrdName || 'System');
        interviewState.sessionCode = sessionCode;
        interviewState.isStarted = true;

        el.candidateBadge.textContent = name;
        el.introState.classList.remove('active');
        el.questionState.classList.add('active');

        switchSession(interviewState.currentSession);
        syncSession('fullscreen_entered', 'Mulai pengerjaan sesi ' + interviewState.currentSession);
        startTimer();

        document.documentElement.requestFullscreen().catch(() => {
            showCustomAlert('Gagal masuk mode fullscreen. Mohon gunakan browser terbaru dan berikan izin.', 'Fullscreen Gagal');
        });
    };

    function readInputValue(input, fallback = '') {
        return input && typeof input.value === 'string' ? input.value.trim() : fallback;
    }

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
        
        if (title.toLowerCase().includes('selesai') || title.toLowerCase().includes('sukses')) {
            iconEl.style.color = 'var(--green-1)';
        } else {
            iconEl.style.color = '#f5a623';
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

    var sessionSummaryGrid = document.querySelector('.session-summary-grid');
    if (sessionSummaryGrid) {
        sessionSummaryGrid.addEventListener('click', function(e) {
            var alertBtn = e.target.closest('.js-session-alert');
            if (alertBtn) {
                e.preventDefault();
                var msg = alertBtn.getAttribute('data-alert-message') || '';
                var title = alertBtn.getAttribute('data-alert-title') || 'Akses Ditolak';
                var icon = alertBtn.getAttribute('data-alert-icon') || 'bi-lock-fill';
                showCustomAlert(msg, title, icon);
                return;
            }
            var startBtn = e.target.closest('[data-start-session]');
            if (startBtn && !startBtn.disabled) {
                e.preventDefault();
                var n = parseInt(startBtn.getAttribute('data-start-session'), 10);
                if (!Number.isNaN(n)) {
                    window.startSpecificSession(n);
                }
            }
        });
    }

    function startTimer() {
        if (window.timerInterval) clearInterval(window.timerInterval);
        if (interviewState.isTestCompleted) return; // Don't start if completed
        
        window.timerInterval = setInterval(() => {
            if (!interviewState.isStarted || interviewState.isLocked || interviewState.isTestCompleted) {
                clearInterval(window.timerInterval);
                return;
            }

            if (interviewState.timeLeftSeconds <= 0) {
                clearInterval(window.timerInterval);
                handleTimeUp();
                return;
            }

            interviewState.timeLeftSeconds--;
            if (interviewState.timeLeftSeconds % 30 === 0) {
                syncSession('timer_sync');
            }
            updateTimerDisplay();
        }, 1000);
    }

    function updateTimerDisplay() {
        const m = Math.floor(Math.max(0, interviewState.timeLeftSeconds) / 60);
        const s = Math.max(0, interviewState.timeLeftSeconds) % 60;
        const display = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        if (el.durationMetric) el.durationMetric.textContent = display;
    }

    function handleTimeUp() {
        showCustomAlert("Waktu pengerjaan tes telah habis. Sesi Anda akan otomatis tersimpan.", "Waktu Habis", "bi-alarm-fill", "Lihat Hasil", () => {
             el.finishBtn.click();
        });
    }

    function updateMetrics() {
        const currentData = allQuestions[interviewState.currentSession] || [];
        const currentAnswers = interviewState.allAnswers[interviewState.currentSession] || {};
        
        const total = currentData.length;
        const answered = Object.keys(currentAnswers).length;
        const pct = total > 0 ? Math.round((answered / total) * 100) : 0;

        if (el.progressFill) el.progressFill.style.width = pct + '%';
        if (el.progressMetric) el.progressMetric.textContent = pct + '%';
        if (el.progressMetricBadge) el.progressMetricBadge.textContent = pct + '%';
        if (el.answeredMetric) el.answeredMetric.textContent = `${answered} dari ${total} soal telah dijawab`;
        if (el.questionCountMetric) el.questionCountMetric.textContent = total;

        // Validation logic for tabs
        const sessionCompleted = {
            1: (allQuestions[1] && allQuestions[1].length > 0 && Object.keys(interviewState.allAnswers[1] || {}).length === allQuestions[1].length),
            2: (allQuestions[2] && allQuestions[2].length > 0 && Object.keys(interviewState.allAnswers[2] || {}).length === allQuestions[2].length),
            3: (allQuestions[3] && allQuestions[3].length > 0 && Object.keys(interviewState.allAnswers[3] || {}).length === allQuestions[3].length)
        };

        [1, 2, 3].forEach(s => {
            const sTotal = allQuestions[s] ? allQuestions[s].length : 0;
            const sAnswered = interviewState.allAnswers[s] ? Object.keys(interviewState.allAnswers[s]).length : 0;
            const badge = document.getElementById(`badgeSesi${s}`);
            const tab = document.getElementById(`tabSesi${s}`);
            
            const isLocked = (s !== interviewState.currentSession);

            if (badge && tab) {
                if (isLocked) {
                    badge.innerHTML = '<i class="bi bi-lock-fill"></i> Terkunci';
                    tab.classList.add('locked');
                } else {
                    badge.textContent = `${sAnswered}/${sTotal}`;
                    tab.classList.remove('locked');
                }
            }
        });

        const violationText = interviewState.tabSwitches > 0
            ? interviewState.violations + ' / ' + interviewState.maxViolations + ' | Tab ' + interviewState.tabSwitches + ' / ' + interviewState.tabSwitchLimit
            : interviewState.violations + ' / ' + interviewState.maxViolations;

        if (el.violationMetric) el.violationMetric.textContent = violationText;
    }

    function switchSession(session) {
        // Validation removed to allow partial completion

        interviewState.currentSession = session;
        interviewState.current = 0;
        
        // Reset timer for new session if it's a different session and not already started
        const sessionTotalSeconds = (<?= (int)$durationMinutes ?>) * 60; // Default fallback
        // We'll update the timeLeftSeconds from the server if it's the current session
        // For other sessions, we'll fetch from server later or just render.
        // Actually, the page reloads or syncs when switching session via finishBtn.
        
        el.tabs.forEach(tab => {
            if (parseInt(tab.dataset.session) === session) {
                tab.classList.add('active');
            } else {
                tab.classList.remove('active');
            }
        });

        renderQuestion();
    }

    el.tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const session = parseInt(tab.dataset.session);
            // Allow tab switching if not started (Preview Mode) or if the session is unlocked
            const isPreview = !interviewState.isStarted;
            const isUnlocked = !tab.classList.contains('locked');
            
            if (isPreview || isUnlocked) {
                switchSession(session);
            } else {
                showCustomAlert("Anda harus menyelesaikan sesi yang sedang berjalan terlebih dahulu.", "Sesi Terkunci", "bi-lock-fill");
            }
        });
    });

    // renderOptions is now integrated into renderQuestion for multiple questions

    function renderQuestion() {
        const currentData = allQuestions[interviewState.currentSession] || [];
        if (currentData.length === 0) {
            el.questionTitle.textContent = "Tidak ada soal";
            el.questionText.textContent = "Belum ada soal tersedia untuk sesi ini.";
            el.answerOptions.innerHTML = "";
            return;
        }

        if (interviewState.currentSession === 1) {
            // Sesi 1 (DISC) - Hide generic headers, use table for all questions
            if (el.questionTitle) el.questionTitle.style.display = 'none';
            if (el.questionText) el.questionText.style.display = 'none';
            if (el.questionPromptImageWrap) el.questionPromptImageWrap.style.display = 'none';
            el.prevBtn.style.display = 'none';
            el.nextBtn.style.display = 'none';
            el.finishBtn.style.display = 'block';
        } else {
            // Sesi 2 & 3 (Aptitude) - Show generic headers, one-by-one view
            if (el.questionTitle) el.questionTitle.style.display = 'block';
            if (el.questionText) el.questionText.style.display = 'block';
            // el.questionPromptImageWrap will be toggled by renderQuestion logic
            
            el.prevBtn.style.display = interviewState.current > 0 ? 'block' : 'none';
            el.nextBtn.style.display = interviewState.current < (currentData.length - 1) ? 'block' : 'none';
            el.finishBtn.style.display = interviewState.current === (currentData.length - 1) ? 'block' : 'none';
        }
        
        if (el.questionCategory) el.questionCategory.textContent = interviewState.currentSession === 1 ? `Tes Profil Perilaku (DISC) — Sesi ${interviewState.currentSession}` : `Tes Aptitude - Sesi ${interviewState.currentSession}`;
        if (el.questionInstruction) el.questionInstruction.textContent = interviewState.currentSession === 1 ? `Untuk setiap nomor, pilih satu pasangan kata di kolom Mirip (paling menyerupai Anda) dan satu di kolom Tidak Mirip (paling tidak menyerupai). Satu baris tidak boleh dipilih di kedua kolom sekaligus.` : `Pilih satu jawaban yang paling tepat untuk setiap pertanyaan di bawah ini.`;
        if (el.questionIndex) el.questionIndex.textContent = interviewState.currentSession === 1 ? `Total Soal: ${currentData.length}` : `Soal ${interviewState.current + 1} / ${currentData.length}`;

        el.answerOptions.innerHTML = '';
        if (interviewState.currentSession === 1) {
            const currentAnswers = interviewState.allAnswers[interviewState.currentSession] || {};

            const title = document.createElement('h3');
            title.textContent = 'PILIHAN JAWABAN';
            title.style.color = 'var(--green-1)';
            title.style.fontWeight = '800';
            title.style.marginBottom = '1.5rem';
            title.style.fontSize = '1.25rem';
            title.style.letterSpacing = '0.05em';
            el.answerOptions.appendChild(title);

            const wrap = document.createElement('div');
            wrap.className = 'disc-table-container';
            const table = document.createElement('table');
            table.className = 'disc-table';
            table.setAttribute('role', 'grid');

            const thead = document.createElement('thead');
            const thr = document.createElement('tr');
            ['No', 'Pasangan', 'Mirip', 'Tidak Mirip'].forEach((label, colIdx) => {
                const th = document.createElement('th');
                th.textContent = label;
                if (colIdx === 1) th.className = 'disc-th-pasangan';
                thr.appendChild(th);
            });
            thead.appendChild(thr);
            table.appendChild(thead);

            const tbody = document.createElement('tbody');
            
            // Group data by question.number
            const groupedData = {};
            currentData.forEach(q => {
                if (!groupedData[q.number]) groupedData[q.number] = [];
                groupedData[q.number].push(q);
            });

            Object.keys(groupedData).sort((a, b) => a - b).forEach(num => {
                const group = groupedData[num];
                let optionLetterIdx = 0;
                group.forEach((q, qIdx) => {
                    const selected = currentAnswers[q.id] || { pairs: {} };
                    
                    q.options.forEach((option, optIdx) => {
                        const currentLetter = String.fromCharCode(65 + optionLetterIdx);
                        optionLetterIdx++;
                        const tr = document.createElement('tr');
                        
                        // Only show number for the first row of the first question in the group
                        if (qIdx === 0 && optIdx === 0) {
                            const noCell = document.createElement('td');
                            noCell.className = 'disc-no-cell';
                            // Calculate total rows in this group
                            let totalRows = 0;
                            group.forEach(gq => totalRows += gq.options.length);
                            noCell.rowSpan = totalRows;
                            noCell.textContent = String(num);
                            tr.appendChild(noCell);
                        }

                        const textCell = document.createElement('td');
                        textCell.className = 'disc-text-cell';
                        const labelSpan = document.createElement('span');
                        labelSpan.className = 'disc-option-label';
                        labelSpan.innerHTML = `<strong>${currentLetter}.</strong>`;
                        textCell.appendChild(labelSpan);
                        
                        let optText = option.text != null && String(option.text).trim() !== '' ? String(option.text).trim() : '';
                        if (optText === '' && option.label && option.label.length > 1) {
                            optText = option.label; // Fallback if text is inside label
                        }
                        textCell.appendChild(document.createTextNode(optText !== '' ? ' ' + optText : ''));
                        tr.appendChild(textCell);

                        const mostCell = document.createElement('td');
                        mostCell.className = 'disc-radio-cell';
                        const mostWrapper = document.createElement('label');
                        mostWrapper.className = 'disc-radio-wrapper';
                        const mostRadio = document.createElement('input');
                        mostRadio.type = 'radio';
                        const rowKey = String(option.value);
                        mostRadio.name = 'discPick_' + q.id + '_' + rowKey;
                        mostRadio.value = 'most';
                        mostRadio.checked = (selected.pairs && selected.pairs[rowKey]) === 'most';
                        
                        const mostCustom = document.createElement('span');
                        mostCustom.className = 'disc-radio-custom';
                        mostWrapper.appendChild(mostRadio);
                        mostWrapper.appendChild(mostCustom);
                        
                        mostRadio.addEventListener('change', () => {
                            saveCurrentAnswer(q, 'answer_updated');
                        });
                        mostCell.appendChild(mostWrapper);
                        tr.appendChild(mostCell);

                        const leastCell = document.createElement('td');
                        leastCell.className = 'disc-radio-cell';
                        const leastWrapper = document.createElement('label');
                        leastWrapper.className = 'disc-radio-wrapper';
                        const leastRadio = document.createElement('input');
                        leastRadio.type = 'radio';
                        const rowKeyLeast = String(option.value);
                        leastRadio.name = 'discPick_' + q.id + '_' + rowKeyLeast;
                        leastRadio.value = 'least';
                        leastRadio.checked = (selected.pairs && selected.pairs[rowKeyLeast]) === 'least';
                        
                        const leastCustom = document.createElement('span');
                        leastCustom.className = 'disc-radio-custom';
                        leastWrapper.appendChild(leastRadio);
                        leastWrapper.appendChild(leastCustom);
                        
                        leastRadio.addEventListener('change', () => {
                            saveCurrentAnswer(q, 'answer_updated');
                        });
                        leastCell.appendChild(leastWrapper);
                        tr.appendChild(leastCell);
                        
                        tbody.appendChild(tr);
                    });
                });
            });

            table.appendChild(tbody);
            wrap.appendChild(table);
            el.answerOptions.appendChild(wrap);
        } else {
            // Render Aptitude (One-by-one)
            const question = currentData[interviewState.current];
            const selected = (interviewState.allAnswers[interviewState.currentSession] || {})[question.id] || '';

            el.questionTitle.textContent = `Pertanyaan No. ${question.number}`;
            el.questionText.textContent = question.prompt;
            
            if (question.promptImageUrl) {
                el.questionPromptImage.src = question.promptImageUrl;
                el.questionPromptImageWrap.classList.add('active');
            } else {
                el.questionPromptImageWrap.classList.remove('active');
            }

            el.answerOptions.innerHTML = '';
            question.options.forEach((option) => {
                const card = document.createElement('div');
                card.className = `option-card ${selected === option.value ? 'active' : ''}`;
                
                const radio = document.createElement('input');
                radio.type = 'radio';
                radio.name = `opt_${question.id}`;
                radio.value = option.value;
                radio.checked = selected === option.value;
                
                const chip = document.createElement('div');
                chip.className = 'choice-chip';
                chip.textContent = option.label;
                
                const content = document.createElement('div');
                content.className = 'option-content';
                
                if (option.imageUrl) {
                    const img = document.createElement('img');
                    img.src = option.imageUrl;
                    img.className = 'option-image';
                    content.appendChild(img);
                }
                
                if (option.text) {
                    const txt = document.createElement('div');
                    txt.className = 'option-text';
                    txt.textContent = option.text;
                    content.appendChild(txt);
                }
                
                card.appendChild(radio);
                card.appendChild(chip);
                card.appendChild(content);
                
                card.onclick = () => {
                    radio.checked = true;
                    document.querySelectorAll('.option-card').forEach(c => c.classList.remove('active'));
                    card.classList.add('active');
                    
                    if (!interviewState.allAnswers[interviewState.currentSession]) {
                        interviewState.allAnswers[interviewState.currentSession] = {};
                    }
                    interviewState.allAnswers[interviewState.currentSession][question.id] = option.value;
                    
                    saveCurrentAnswer(question, 'answer_updated');
                };
                
                el.answerOptions.appendChild(card);
            });
        }
        el.finishBtn.innerHTML = interviewState.currentSession < 3 ? `Selesaikan Sesi ${interviewState.currentSession} & Lanjut` : '<i class="bi bi-check-all"></i> Selesaikan Tes';
        
        updateMetrics();
        syncSession('question_viewed', 'Membuka soal nomor ' + (interviewState.current + 1));
    }

    async function saveCurrentAnswer(question, eventType = 'answer_updated') {
        if (!question) return;

        let val = null;
        let most = [];
        let least = [];
        let pairs = {};

        if (interviewState.currentSession === 1) {
            const pickedRows = document.querySelectorAll(`input[name^="discPick_${question.id}_"]:checked`);
            pickedRows.forEach((radio) => {
                const name = radio.name || '';
                const parts = name.split('_');
                const optionKey = parts.length >= 3 ? parts.slice(2).join('_') : '';
                const pickType = radio.value === 'most' ? 'most' : 'least';
                if (optionKey) {
                    pairs[optionKey] = pickType;
                }
            });

            most = Object.keys(pairs).filter((k) => pairs[k] === 'most');
            least = Object.keys(pairs).filter((k) => pairs[k] === 'least');

            if (!interviewState.allAnswers[1]) {
                interviewState.allAnswers[1] = {};
            }
            if (Object.keys(pairs).length > 0) {
                interviewState.allAnswers[1][question.id] = { pairs, most, least };
                val = interviewState.allAnswers[1][question.id];
            } else {
                delete interviewState.allAnswers[1][question.id];
            }
        } else {
            const ans = (interviewState.allAnswers[interviewState.currentSession] || {})[question.id];
            val = ans || null;
        }

        updateMetrics();
        
        try {
            const body = {
                session: interviewState.currentSession,
                idPegawai: authUserId,
                namaPegawai: interviewState.candidateName || readInputValue(el.candidateNameInp),
                idPertanyaan: question.id,
                nomorPertanyaan: question.number,
                jawabanPegawai: typeof val === 'string' ? val : JSON.stringify(val || {})
            };

            if (interviewState.currentSession === 1) {
                body.jawabanMost = most.join(',');
                body.jawabanLeast = least.join(',');
                body.jawabanPairs = pairs;
            }

            await fetch("<?= esc($saveAnswerUrl) ?>", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(body)
            });
        } catch (e) {}

        syncSession(eventType);
    }

    async function syncSession(eventType, msg = '') {
        const sessionIdVal = readInputValue(el.sessionCodeInp, interviewState.sessionCode || defaultSessionCode);
        const currentAnswers = interviewState.allAnswers[interviewState.currentSession] || {};

        try {
            await fetch("<?= esc($monitorEventUrl) ?>", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    eventType: eventType,
                    sessionId: sessionIdVal,
                    idUser: authUserId,
                    candidateName: interviewState.candidateName || readInputValue(el.candidateNameInp),
                    positionName: interviewState.positionName || readInputValue(el.positionNameInp),
                    hrdName: interviewState.hrdName || readInputValue(el.hrdNameInp, 'System'),
                    sessionCode: interviewState.sessionCode || readInputValue(el.sessionCodeInp, defaultSessionCode),
                    current_question: interviewState.current,
                    questionsTotal: (allQuestions[interviewState.currentSession] || []).length,
                    violations: interviewState.violations,
                    tabSwitches: interviewState.tabSwitches,
                    blockedCandidate: interviewState.isLocked,
                    message: msg,
                    violationType: eventType === 'violation_detected' ? 'security_breach' : '',
                    answers: currentAnswers,
                    currentSession: interviewState.currentSession,
                    timeLeftSeconds: interviewState.timeLeftSeconds
                })
            });
        } catch (e) {}
    }

    async function registerSecurityViolation(type, msg) {
        if (interviewState.isLocked || !interviewState.isStarted || interviewState.isSubmittingFinal || interviewState.isTestCompleted || interviewState.isSubmittingSession || interviewState.isTransitioningSession) return;
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
        el.lockCandidateID.textContent = interviewState.candidateName || readInputValue(el.candidateNameInp, 'Unknown');
        if (document.fullscreenElement) document.exitFullscreen();
    }


    if (el.startBtn) {
        el.startBtn.addEventListener('click', startInterview);
    }

    if (el.previewBtn) {
        el.previewBtn.addEventListener('click', () => {
            interviewState.isStarted = false;
            el.introState.classList.remove('active');
            el.questionState.classList.add('active');
            switchSession(interviewState.currentSession);
            updateMetrics();
        });
    }

    var openInterviewRulesLink = document.getElementById('openInterviewRulesLink');
    if (openInterviewRulesLink) {
        openInterviewRulesLink.addEventListener('click', function(e) {
            e.preventDefault();
            showRulesOverlayReadOnly();
        });
    }

    el.nextBtn.addEventListener('click', () => {
        const currentData = allQuestions[interviewState.currentSession] || [];
        if (interviewState.current < (currentData.length - 1)) {
            interviewState.current++;
            renderQuestion();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    el.prevBtn.addEventListener('click', () => {
        if (interviewState.current > 0) {
            interviewState.current--;
            renderQuestion();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    el.finishBtn.addEventListener('click', async () => {
        const currentData = allQuestions[interviewState.currentSession] || [];
        const currentAnswers = interviewState.allAnswers[interviewState.currentSession] || {};
        
        // Final validation before session completion
        if (interviewState.currentSession === 1) {
            const missing = [];
            currentData.forEach(q => {
                const ans = currentAnswers[q.id];
                const pairs = ans && ans.pairs ? ans.pairs : {};
                const pickedCount = Object.keys(pairs).length;
                const requiredCount = Array.isArray(q.options) ? q.options.length : 0;
                if (!ans || pickedCount < requiredCount) {
                    missing.push(q.number);
                }
            });
            if (missing.length > 0) {
                const uniqueMissing = [...new Set(missing)];
                showCustomAlert(
                    'Beberapa nomor di Sesi 1 belum lengkap (pilihan Mirip/Tidak Mirip). Apakah Anda yakin ingin mengakhiri sesi ini? Nomor belum lengkap: ' + uniqueMissing.join(', '), 
                    'Jawaban Belum Lengkap', 
                    'bi-question-circle-fill', 
                    'Ya, Selesaikan', 
                    async () => {
                        executeSessionFinish();
                    }
                );
                return;
            }
        } else {

            const unanswered = currentData.filter(q => !currentAnswers[q.id]);
            if (unanswered.length > 0) {
                showCustomAlert('Masih ada ' + unanswered.length + ' soal yang belum dijawab. Apakah Anda yakin ingin mengakhiri sesi ini?', 'Jawaban Belum Lengkap', 'bi-question-circle-fill', 'Ya, Selesaikan', async () => {
                    executeSessionFinish();
                });
                return;
            }
        }

        executeSessionFinish();
    });

    async function executeSessionFinish() {
        showCustomAlert(
            'Apakah Anda yakin ingin menyelesaikan sesi ini? Jawaban Anda akan disimpan dan Anda tidak dapat kembali ke sesi ini.',
            'Konfirmasi Selesai',
            'bi-question-circle-fill',
            'Ya, Selesaikan',
            async () => {
                interviewState.isSubmittingSession = true;
                if (document.fullscreenElement) {
                    await document.exitFullscreen().catch(() => {});
                }

                // In "Show All" mode, we don't need to call saveCurrentAnswer again for a specific question here
                // as it's already saved on every click.
                
                const currentData = allQuestions[interviewState.currentSession] || [];
                const currentAnswers = interviewState.allAnswers[interviewState.currentSession] || {};
        
                el.finishBtn.disabled = true;
                el.finishBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';

                try {
                    const res = await fetch("<?= esc($completeSessionUrl) ?>", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            sessionId: readInputValue(el.sessionCodeInp, interviewState.sessionCode || defaultSessionCode),
                            currentSession: interviewState.currentSession
                        })
                    });
                    const result = await res.json();
                    
                    if (result.ok) {
                        if (result.next) {
                            interviewState.isTransitioningSession = true;
                            showCustomAlert(
                                'Anda akan melanjutkan ke Sesi ' + result.session + '. Silakan beristirahat sejenak.',
                                'Sesi ' + interviewState.currentSession + ' Selesai',
                                'bi-check-circle-fill',
                                'Lanjut ke Lobby Sesi ' + result.session,
                                () => {
                                    location.reload(); 
                                }
                            );
                        } else {
                            interviewState.isSubmittingFinal = true;
                            interviewState.isTestCompleted = true;
                            
                            if (window.timerInterval) clearInterval(window.timerInterval);
                            
                            const summary = result.summary;
                            document.getElementById('summaryTotal').textContent = summary.totalQuestions;
                            document.getElementById('summaryAnswered').textContent = summary.totalAnswered;
                            document.getElementById('summaryDuration').textContent = summary.durationText;
                            document.getElementById('summaryViolations').textContent = summary.violations;
                            
                            el.questionState.classList.remove('active');
                            el.summaryState.classList.add('active');
                            syncSession('session_completed');
                        }
                    } else {
                        interviewState.isSubmittingSession = false;
                        showCustomAlert('Gagal menyelesaikan sesi: ' + (result.message || 'Error unknown'), 'Gagal Menyimpan');
                        el.finishBtn.disabled = false;
                        el.finishBtn.innerHTML = '<i class="bi bi-check-all"></i> Selesaikan Tes';
                    }
                } catch (e) {
                    interviewState.isSubmittingSession = false;
                    showCustomAlert('Terjadi kesalahan jaringan.', 'Error');
                    el.finishBtn.disabled = false;
                    el.finishBtn.innerHTML = '<i class="bi bi-check-all"></i> Selesaikan Tes';
                }
            }
        );
    }

    el.returnBtn.addEventListener('click', () => {
        if (!document.fullscreenElement) document.documentElement.requestFullscreen();
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

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden' && !interviewState.isLocked && interviewState.isStarted) {
            registerSecurityViolation('tab', 'Peserta terdeteksi meninggalkan halaman tes.');
        }
    });

    window.addEventListener('beforeunload', (e) => {
        if (interviewState.isStarted && !interviewState.isTestCompleted && !interviewState.isLocked && !interviewState.isSubmittingSession && !interviewState.isTransitioningSession) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    if (existingSession) {
        if (existingSession.candidate_name && el.candidateNameInp) el.candidateNameInp.value = existingSession.candidate_name;
        if (existingSession.position_name && el.positionNameInp) el.positionNameInp.value = existingSession.position_name;
        if (existingSession.hrd_name && el.hrdNameInp) el.hrdNameInp.value = existingSession.hrd_name;
        if (existingSession.session_code && el.sessionCodeInp) el.sessionCodeInp.value = existingSession.session_code;

        if (interviewState.isLocked) {
            if (el.candidateNameInp) el.candidateNameInp.disabled = true;
            if (el.positionNameInp) el.positionNameInp.disabled = true;
            if (el.hrdNameInp) el.hrdNameInp.disabled = true;
            if (el.sessionCodeInp) el.sessionCodeInp.disabled = true;
            if (el.startBtn) el.startBtn.disabled = true;
            document.getElementById('introStatus').innerHTML = '<span class="text-danger fw-bold">Peserta ini sudah terblokir karena pelanggaran. Silakan konfirmasi ke Manager/HRD.</span>';
        }
    }
    
    updateMetrics();
    updateTimerDisplay();

    if (interviewState.isTestCompleted) {
        el.introState.classList.remove('active');
        el.questionState.classList.remove('active');
        el.summaryState.classList.add('active');
        
        // Fetch final summary from server to be sure
        fetch("<?= esc($summaryUrl) ?>", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                sessionId: readInputValue(el.sessionCodeInp, interviewState.sessionCode || defaultSessionCode)
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.ok && data.summary) {
                const s = data.summary;
                document.getElementById('summaryTotal').textContent = s.totalQuestions;
                document.getElementById('summaryAnswered').textContent = s.totalAnswered;
                document.getElementById('summaryDuration').textContent = s.durationText;
                document.getElementById('summaryViolations').textContent = s.violations;
            }
        });
    }
    // Auto-start logic removed: participants must always click "Mulai Sesi" or "Lanjutkan Sesi" from the lobby.

    // This prevents accidental security violations on page load and ensures a better UX.
    /*
    if (interviewState.isStarted && !interviewState.isLocked) {
        startTimer();
        el.introState.classList.remove('active');
        el.questionState.classList.add('active');
        switchSession(interviewState.currentSession);
    }
    */
    
    // Ensure intro state is visible by default if not completed
    if (!interviewState.isTestCompleted && !interviewState.isLocked) {
        el.introState.classList.add('active');
    }

</script>
<?= $this->endSection() ?>
