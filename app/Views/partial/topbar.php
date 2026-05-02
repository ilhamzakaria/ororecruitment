<header class="topbar">
    <div class="topbar-left">
        <h2><?= $headerTitle ?? 'Dashboard' ?></h2>
        <span class="date-text"><?= $headerSubtitle ?? date('d F Y') ?></span>
    </div>

    <div class="topbar-right">
        <div class="dropdown">
            <a class="profile-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                <div class="profile-text">
                    <strong><?= esc($authUser['name'] ?? 'User') ?></strong>
                    <span><?= ucfirst($authUser['role'] ?? 'guest') ?></span>
                </div>
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($authUser['name'] ?? 'U') ?>&background=f3faf6&color=1f8f5e&bold=true" alt="Profile">
            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li><a class="dropdown-item" href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Keluar</a></li>
            </ul>
        </div>
    </div>
</header>

<style>
    .topbar {
        position: sticky;
        top: 0;
        z-index: 50;
        background: #fff;
        border-bottom: 1px solid var(--line);
        padding: 16px 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .topbar-left h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: var(--ink);
    }

    .date-text {
        display: block;
        margin-top: 2px;
        color: var(--muted);
        font-size: 12px;
        font-weight: 600;
    }

    .profile-link {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: var(--ink);
    }

    .profile-text {
        text-align: right;
        line-height: 1.2;
    }

    .profile-text strong {
        display: block;
        font-size: 13px;
        font-weight: 700;
    }

    .profile-text span {
        font-size: 11px;
        color: var(--muted);
        font-weight: 600;
    }

    .profile-link img {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: 2px solid #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    @media (max-width: 860px) {
        .topbar { padding: 16px 20px; }
        .profile-text { display: none; }
    }
</style>
