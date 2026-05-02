<?php
$headerTitle = 'Dashboard Pegawai';
$headerSubtitle = 'Selamat datang di panel interview';
?>
<?= $this->extend('layout/main') ?>

<?= $this->section('styles') ?>
<style>
    .hero-card {
        background: linear-gradient(135deg, var(--green-1), var(--green-2));
        color: #fff;
        border: 0;
        padding: 32px;
    }
    .hero-card h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 800;
    }
    .hero-card p {
        margin: 12px 0 0;
        opacity: 0.9;
        font-size: 15px;
        line-height: 1.6;
    }
    .info-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--muted);
        font-weight: 800;
        margin-bottom: 4px;
    }
    .info-value {
        font-size: 15px;
        font-weight: 700;
        color: var(--ink);
    }
    .card-header-custom {
        padding: 0 0 16px 0;
        border-bottom: 1px solid var(--line);
        margin-bottom: 20px;
        font-weight: 800;
        color: var(--ink);
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card hero-card mb-4">
    <h2>Selamat datang, <?= esc($authUser['name'] ?? 'Pegawai') ?></h2>
    <p>Pastikan profil Anda sudah benar, lalu lanjutkan ke halaman tes interview ketika sudah siap.</p>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header-custom">
                <i class="bi bi-person-badge"></i> Profil Pegawai
            </div>
            <div class="grid gap-4">
                <div class="mb-4">
                    <div class="info-label">ID User</div>
                    <div class="info-value"><?= esc($pegawaiProfile['id_user'] ?? ($authUser['id_user'] ?? '-')) ?></div>
                </div>
                <div class="mb-4">
                    <div class="info-label">Nama Lengkap</div>
                    <div class="info-value"><?= esc($pegawaiProfile['name'] ?? ($authUser['name'] ?? '-')) ?></div>
                </div>
                <div class="mb-4">
                    <div class="info-label">Username</div>
                    <div class="info-value"><?= esc($pegawaiProfile['username'] ?? ($authUser['username'] ?? '-')) ?></div>
                </div>
                <div class="mb-4">
                    <div class="info-label">Posisi Dilamar</div>
                    <div class="info-value"><?= esc($pegawaiProfile['positionName'] ?? '-') ?></div>
                </div>
                <div class="mb-0">
                    <div class="info-label">Alamat</div>
                    <div class="info-value"><?= esc($pegawaiProfile['alamat'] ?? '-') ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header-custom">
                <i class="bi bi-lightning-charge"></i> Akses Cepat
            </div>
            <p class="text-secondary mb-4">Navigasi utama untuk memulai sesi interview Anda hari ini.</p>
            <div class="d-grid gap-3">
                <a class="btn btn-primary w-100" href="<?= site_url('tes-interview') ?>">
                    <i class="bi bi-journal-text"></i> Buka Halaman Tes
                </a>
                <a class="btn btn-secondary w-100" href="<?= site_url('logout') ?>">
                    <i class="bi bi-box-arrow-right"></i> Keluar Akun
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
