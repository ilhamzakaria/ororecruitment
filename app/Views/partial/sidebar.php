<aside class="sidebar">
    <div class="sidebar-brand">
        <img src="<?= base_url('favicon.ico') ?>" alt="PT OROPALSTINDO" class="brand-logo" />
        <span class="brand-name">PT OROPALSTINDO</span>
    </div>

    <div class="account-card">
        <div class="account-avatar">
            <?= esc(substr($authUser['name'] ?? 'U', 0, 1)) ?>
        </div>
        <div class="account-info">
            <strong><?= esc($authUser['name'] ?? 'User') ?></strong>
            <span><?= esc(strtoupper((string) ($authUser['role'] ?? 'USER'))) ?></span>
        </div>
    </div>

    <div class="nav-group">
        <span class="nav-section-label">Menu Utama</span>
        <nav class="nav" aria-label="Menu Utama">
            <?php if (in_array($authUser['role'] ?? '', ['hrd', 'manager'])) : ?>
                <a class="nav-link <?= url_is('dashboard-hrd') ? 'active' : '' ?>" href="<?= site_url('dashboard-hrd') ?>">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
                <a class="nav-link <?= url_is('manage-users') ? 'active' : '' ?>" href="<?= site_url('manage-users') ?>">
                    <i class="bi bi-people"></i> Manajemen User
                </a>
                <a class="nav-link <?= url_is('manage-questions') ? 'active' : '' ?>" href="<?= site_url('manage-questions') ?>">
                    <i class="bi bi-pencil-square"></i> Isi Pertanyaan
                </a>
                <a class="nav-link <?= url_is('manage-sessions') ? 'active' : '' ?>" href="<?= site_url('manage-sessions') ?>">
                    <i class="bi bi-gear-fill"></i> Manajemen Soal
                </a>
            <?php else : ?>
                <a class="nav-link <?= url_is('dashboard-user') ? 'active' : '' ?>" href="<?= site_url('dashboard-user') ?>">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
            <?php endif; ?>

            <!-- <a class="nav-link <?= url_is('tes-interview') ? 'active' : '' ?>" href="<?= site_url('tes-interview') ?>">
                <i class="bi bi-clipboard2-check"></i> Halaman Tes
            </a> -->

            <?php if (url_is('tes-interview')) : ?>
                <a class="nav-link" href="#" id="openInterviewRulesLink">
                    <i class="bi bi-file-earmark-text"></i> Aturan Tes
                </a>
            <?php endif; ?>
            
            <a class="nav-link" href="<?= site_url('logout') ?>">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </a>
        </nav>
    </div>
</aside>

<style>
    .sidebar {
        position: sticky;
        top: 0;
        height: 100vh;
        width: 240px;
        padding: 16px 12px;
        border-right: 1px solid var(--line);
        background: #fff;
        display: flex;
        flex-direction: column;
        gap: 16px;
        overflow-y: auto;
        flex-shrink: 0;
    }

    .sidebar-brand { display: flex; align-items: center; gap: 12px; padding: 4px 8px 16px 8px; border-bottom: 1px solid var(--line); }
    .brand-logo { width: 38px; height: 38px; border-radius: 50%; background: #fff; border: 1px solid var(--line); object-fit: cover; flex-shrink: 0; display: block; padding: 2px; }
    .brand-name { font-size: 14px; font-weight: 800; color: var(--ink); line-height: 1.2; letter-spacing: 0.02em; }

    .account-card { display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 8px; background: var(--soft); border: 1px solid var(--line); }
    .account-avatar { width: 32px; height: 32px; border-radius: 6px; background: var(--green-1); display: grid; place-items: center; color: #fff; font-weight: 800; font-size: 12px; flex-shrink: 0; }
    .account-info { min-width: 0; }
    .account-info strong { display: block; font-size: 12px; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .account-info span { display: block; font-size: 10px; color: var(--muted); text-transform: uppercase; font-weight: 700; }

    .nav-section-label { display: block; font-size: 10px; font-weight: 800; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; padding-left: 8px; opacity: 0.7; }
    .sidebar .nav { display: grid; gap: 2px; }
    .sidebar .nav-link { display: flex; align-items: center; gap: 10px; min-height: 38px; padding: 0 10px; border-radius: 6px; color: var(--muted); font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s ease; }
    .sidebar .nav-link i { font-size: 16px; width: 20px; text-align: center; }
    .sidebar .nav-link:hover { color: var(--green-1); background: #f0f7f4; }
    .sidebar .nav-link.active { color: var(--green-1); background: #e6f6ef; font-weight: 700; position: relative; }
    .sidebar .nav-link.active::after { content: ''; position: absolute; left: 0; top: 8px; bottom: 8px; width: 3px; background: var(--green-1); border-radius: 0 4px 4px 0; }

    @media (max-width: 860px) {
        .sidebar { width: 100%; height: auto; position: static; padding: 20px; border-right: 0; border-bottom: 1px solid var(--line); }
    }
</style>
