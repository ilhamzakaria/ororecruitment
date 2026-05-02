<?php
$headerTitle = 'Manajemen User';
$headerSubtitle = 'Kelola data manager, hrd, dan pegawai';
?>
<?= $this->extend('layout/main') ?>

<?= $this->section('styles') ?>
<style>
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
        font-size: 14px;
    }
    .status-pill {
        display: inline-flex;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }
    .status-aktif { background: #e6f6ef; color: #1f8f5e; }
    .status-nonaktif, .status-eliminasi, .status-gagal { background: #fff1f1; color: #c64747; }
    
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-grid;
        place-items: center;
        border-radius: 8px;
        transition: all 0.2s;
    }

    /* Tabs Styling */
    .custom-tabs {
        display: flex;
        flex-direction: row;
        border-bottom: 2px solid var(--line);
        gap: 8px;
        margin-bottom: 24px;
    }
    .custom-tabs .nav-link {
        border: 0;
        color: var(--muted);
        font-weight: 700;
        font-size: 14px;
        padding: 12px 24px;
        border-radius: 10px 10px 0 0;
        background: transparent;
        transition: all 0.2s ease;
        margin-bottom: -2px;
    }
    .custom-tabs .nav-link:hover {
        color: var(--green-1);
        background: var(--soft);
    }
    .custom-tabs .nav-link.active {
        color: var(--green-1);
        background: #fff;
        border-bottom: 2px solid var(--green-1);
    }

    .search-container {
        position: relative;
        width: 300px;
    }
    .search-container i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted);
    }
    .search-container input {
        padding-left: 40px;
        border-radius: 999px;
    }

    .empty-state {
        padding: 60px 20px;
        text-align: center;
        color: var(--muted);
    }
    .empty-state i {
        font-size: 48px;
        color: var(--line);
        margin-bottom: 16px;
        display: block;
    }
    .empty-state h4 {
        color: var(--ink);
        font-weight: 800;
        font-size: 16px;
        margin-bottom: 8px;
    }
    /* Instagram Style Modal */
    .ig-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        display: none;
        place-items: center;
        backdrop-filter: blur(2px);
        animation: fadeIn 0.2s ease-out;
    }
    .ig-modal {
        background: #fff;
        width: 100%;
        max-width: 400px;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        animation: scaleUp 0.2s ease-out;
        margin: 20px;
    }
    .ig-modal-content {
        padding: 32px 24px;
        text-align: center;
    }
    .ig-modal-title {
        font-weight: 800;
        font-size: 18px;
        margin-bottom: 8px;
        color: var(--ink);
    }
    .ig-modal-text {
        color: var(--muted);
        font-size: 14px;
        line-height: 1.5;
    }
    .ig-modal-footer {
        display: flex;
        flex-direction: column;
        border-top: 1px solid var(--line);
    }
    .ig-modal-btn {
        padding: 14px;
        background: transparent;
        border: 0;
        font-weight: 700;
        font-size: 14px;
        transition: background 0.2s;
        width: 100%;
        border-bottom: 1px solid var(--line);
    }
    .ig-modal-btn:last-child {
        border-bottom: 0;
    }
    .ig-modal-btn:active {
        background: var(--soft);
    }
    .ig-btn-danger { color: #ed4956; }
    .ig-btn-primary { color: var(--green-1); }
    .ig-btn-secondary { color: var(--ink); font-weight: 400; }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes scaleUp {
        from { transform: scale(1.1); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-800 mb-1">Manajemen User</h1>
        <p class="text-muted small mb-0">Kelola akses akun Manager, HRD, dan Calon Pegawai.</p>
    </div>
    <div class="d-flex gap-3 align-items-center">
        <div class="search-container">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" class="form-control" placeholder="Cari nama, ID, atau username...">
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-plus-lg"></i> Tambah User
        </button>
    </div>
</div>

<?php
// Filter data pegawai untuk tab Aktif & Nonaktif
$pegawaiAktif = [];
$pegawaiNonaktif = [];
foreach ($pegawai as $p) {
    if (strtolower($p['status']) === 'aktif') {
        $pegawaiAktif[] = $p;
    } else {
        $pegawaiNonaktif[] = $p;
    }
}
?>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success border-0 shadow-sm mb-4"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger border-0 shadow-sm mb-4"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<ul class="nav nav-tabs custom-tabs" id="userTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pegawai-tab" data-bs-toggle="tab" data-bs-target="#tab-pegawai" type="button" role="tab">Pegawai</button>
    </li>
    <?php if ($authUser['role'] === 'manager') : ?>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="manager-tab" data-bs-toggle="tab" data-bs-target="#tab-manager" type="button" role="tab">Manager</button>
    </li>
    <?php endif; ?>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="hrd-tab" data-bs-toggle="tab" data-bs-target="#tab-hrd" type="button" role="tab">HRD</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link text-warning" id="nonaktif-tab" data-bs-toggle="tab" data-bs-target="#tab-nonaktif" type="button" role="tab">Nonaktif</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link text-danger" id="eliminasi-tab" data-bs-toggle="tab" data-bs-target="#tab-eliminasi" type="button" role="tab">Eliminasi</button>
    </li>
</ul>

<div class="tab-content" id="userTabsContent">
    
    <!-- Tab Pegawai -->
    <div class="tab-pane fade show active" id="tab-pegawai" role="tabpanel">
        <div class="card mb-4">
            <?php if (empty($pegawaiAktif)) : ?>
                <div class="empty-state">
                    <i class="bi bi-people"></i>
                    <h4>Belum ada data pegawai</h4>
                    <p>Pegawai yang aktif akan muncul di sini.</p>
                </div>
            <?php else : ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Posisi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pegawaiAktif as $p) : ?>
                            <tr>
                                <td class="fw-bold text-muted"><?= $p['id_user'] ?></td>
                                <td><strong><?= $p['nama'] ?></strong></td>
                                <td><code><?= $p['username'] ?></code></td>
                                <td><span class="badge bg-light text-dark"><?= $p['posisi'] ?></span></td>
                                <td>
                                    <span class="status-pill status-<?= $p['status'] ?>">
                                        <?= ucfirst($p['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-secondary btn-action" onclick="editUser('pegawai', <?= htmlspecialchars(json_encode($p)) ?>)"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-secondary btn-action <?= $p['status'] === 'aktif' ? 'text-warning' : 'text-success' ?>" onclick="toggleStatus('pegawai', '<?= $p['id_user'] ?>', '<?= $p['status'] === 'aktif' ? 'nonaktif' : 'aktif' ?>')"><i class="bi bi-power"></i></button>
                                        <button class="btn btn-secondary btn-action text-danger" onclick="deleteUser('pegawai', '<?= $p['id_user'] ?>')"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab Manager -->
    <?php if ($authUser['role'] === 'manager') : ?>
    <div class="tab-pane fade" id="tab-manager" role="tabpanel">
        <div class="card mb-4">
            <?php if (empty($manager)) : ?>
                <div class="empty-state">
                    <i class="bi bi-shield-lock"></i>
                    <h4>Belum ada data manager</h4>
                </div>
            <?php else : ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($manager as $m) : ?>
                            <tr>
                                <td class="fw-bold text-muted"><?= $m['id_manager'] ?></td>
                                <td><strong><?= $m['nama'] ?></strong></td>
                                <td><code><?= $m['username'] ?></code></td>
                                <td>
                                    <span class="status-pill status-<?= $m['status'] ?>">
                                        <?= ucfirst($m['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <?php if ($m['id_manager'] === $authUser['id_user']) : ?>
                                            <span class="badge bg-light text-muted">Anda</span>
                                        <?php else : ?>
                                            <button class="btn btn-secondary btn-action" onclick="editUser('manager', <?= htmlspecialchars(json_encode($m)) ?>)"><i class="bi bi-pencil"></i></button>
                                            <button class="btn btn-secondary btn-action <?= $m['status'] === 'aktif' ? 'text-warning' : 'text-success' ?>" onclick="toggleStatus('manager', '<?= $m['id_manager'] ?>', '<?= $m['status'] === 'aktif' ? 'nonaktif' : 'aktif' ?>')"><i class="bi bi-power"></i></button>
                                            <button class="btn btn-secondary btn-action text-danger" onclick="deleteUser('manager', '<?= $m['id_manager'] ?>')"><i class="bi bi-trash"></i></button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tab HRD -->
    <div class="tab-pane fade" id="tab-hrd" role="tabpanel">
        <div class="card mb-4">
            <?php if (empty($hrd)) : ?>
                <div class="empty-state">
                    <i class="bi bi-person-gear"></i>
                    <h4>Belum ada data HRD</h4>
                </div>
            <?php else : ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hrd as $h) : ?>
                            <tr>
                                <td class="fw-bold text-muted"><?= $h['id_hrd'] ?></td>
                                <td><strong><?= $h['nama'] ?></strong></td>
                                <td><code><?= $h['username'] ?></code></td>
                                <td>
                                    <span class="status-pill status-<?= $h['status'] ?>">
                                        <?= ucfirst($h['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <?php if ($authUser['role'] === 'manager') : ?>
                                            <button class="btn btn-secondary btn-action" onclick="editUser('hrd', <?= htmlspecialchars(json_encode($h)) ?>)"><i class="bi bi-pencil"></i></button>
                                            <button class="btn btn-secondary btn-action <?= $h['status'] === 'aktif' ? 'text-warning' : 'text-success' ?>" onclick="toggleStatus('hrd', '<?= $h['id_hrd'] ?>', '<?= $h['status'] === 'aktif' ? 'nonaktif' : 'aktif' ?>')"><i class="bi bi-power"></i></button>
                                            <button class="btn btn-secondary btn-action text-danger" onclick="deleteUser('hrd', '<?= $h['id_hrd'] ?>')"><i class="bi bi-trash"></i></button>
                                        <?php elseif ($h['id_hrd'] === $authUser['id_user']) : ?>
                                            <span class="badge bg-light text-muted">Anda</span>
                                        <?php else : ?>
                                            <span class="text-muted small">No Access</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab Nonaktif -->
    <div class="tab-pane fade" id="tab-nonaktif" role="tabpanel">
        <div class="card mb-4 border-warning border-opacity-25">
            <?php if (empty($pegawaiNonaktif)) : ?>
                <div class="empty-state">
                    <i class="bi bi-person-dash text-warning"></i>
                    <h4>Tidak ada pegawai yang dinonaktifkan</h4>
                </div>
            <?php else : ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Posisi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pegawaiNonaktif as $p) : ?>
                            <tr>
                                <td class="fw-bold text-muted"><?= $p['id_user'] ?></td>
                                <td><strong><?= $p['nama'] ?></strong></td>
                                <td><code><?= $p['username'] ?></code></td>
                                <td><span class="badge bg-light text-dark"><?= $p['posisi'] ?></span></td>
                                <td>
                                    <span class="status-pill status-<?= $p['status'] ?>">
                                        <?= ucfirst($p['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-secondary btn-action text-success" title="Aktifkan Kembali" onclick="toggleStatus('pegawai', '<?= $p['id_user'] ?>', 'aktif')"><i class="bi bi-power"></i></button>
                                        <button class="btn btn-secondary btn-action text-danger" onclick="deleteUser('pegawai', '<?= $p['id_user'] ?>')"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab Eliminasi -->
    <div class="tab-pane fade" id="tab-eliminasi" role="tabpanel">
        <div class="card mb-4 border-danger border-opacity-25">
            <?php if (empty($eliminasi)) : ?>
                <div class="empty-state">
                    <i class="bi bi-shield-check text-success"></i>
                    <h4>Tidak ada data di eliminasi</h4>
                    <p>User yang dieliminasi akan muncul di sini.</p>
                </div>
            <?php else : ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Dieliminasi Oleh</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($eliminasi as $p) : ?>
                            <?php 
                                $id = $p['id_user'] ?? $p['id_hrd'] ?? $p['id_manager'];
                            ?>
                            <tr>
                                <td class="fw-bold text-muted"><?= $id ?></td>
                                <td><strong><?= $p['nama'] ?></strong></td>
                                <td><code><?= $p['username'] ?></code></td>
                                <td><span class="badge bg-light text-dark"><?= ucfirst($p['role'] ?? 'pegawai') ?></span></td>
                                <td><span class="text-muted small"><?= $p['dieliminasi_oleh'] ?: '-' ?></span></td>
                                <td><span class="text-muted small"><?= $p['tanggal_eliminasi'] ?: '-' ?></span></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-secondary btn-action text-success" title="Pulihkan Akses" onclick="restoreUser('<?= $id ?>')"><i class="bi bi-arrow-counterclockwise"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form id="userForm" method="post">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title fw-800" id="modalTitle">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="userId">
                    <input type="hidden" name="role" id="userRole">
                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" id="userName" required>
                    </div>
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" id="userUsername" required>
                    </div>
                    <div class="mb-3">
                        <label>Password <small class="text-muted">(Kosongkan jika tidak diubah)</small></label>
                        <input type="password" name="password">
                    </div>
                    <div id="pegawaiFields">
                        <div class="mb-3">
                            <label>Posisi Dilamar</label>
                            <input type="text" name="posisi" id="userPosisi">
                        </div>
                        <div class="mb-3">
                            <label>Alamat</label>
                            <textarea name="alamat" id="userAlamat" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="<?= site_url('manage-users/add') ?>" method="post">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title fw-800">Tambah User Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label>Role / Akses</label>
                        <select name="role" onchange="toggleAddFields(this.value)">
                            <option value="pegawai">Pegawai</option>
                            <?php if ($authUser['role'] === 'manager') : ?>
                                <option value="hrd">HRD</option>
                                <option value="manager">Manager</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" required placeholder="Andi Saputra">
                    </div>
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" required placeholder="andisaputra">
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <div id="addPegawaiFields">
                        <div class="mb-3">
                            <label>Posisi</label>
                            <input type="text" name="posisi" placeholder="Staff Administrasi">
                        </div>
                        <div class="mb-3">
                            <label>Alamat</label>
                            <textarea name="alamat" rows="3" placeholder="Jl. Contoh No. 123"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Daftarkan User</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Instagram Style Alert Modal -->
<div id="igAlertOverlay" class="ig-overlay">
    <div class="ig-modal">
        <div class="ig-modal-content">
            <div id="igAlertTitle" class="ig-modal-title">Konfirmasi</div>
            <div id="igAlertText" class="ig-modal-text">Apakah Anda yakin?</div>
        </div>
        <div class="ig-modal-footer">
            <button id="igAlertConfirmBtn" class="ig-modal-btn">Konfirmasi</button>
            <button onclick="closeIgAlert()" class="ig-modal-btn ig-btn-secondary">Batal</button>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const userModal = new bootstrap.Modal(document.getElementById('userModal'));

    function editUser(role, data) {
        document.getElementById('modalTitle').textContent = 'Edit ' + role.charAt(0).toUpperCase() + role.slice(1);
        document.getElementById('userForm').action = '<?= site_url('manage-users/update') ?>';
        document.getElementById('userId').value = data.id_user || data.id_hrd || data.id_manager;
        document.getElementById('userRole').value = role;
        document.getElementById('userName').value = data.nama || data.name || '';
        document.getElementById('userUsername').value = data.username;
        
        const pegawaiFields = document.getElementById('pegawaiFields');
        if (role === 'pegawai') {
            pegawaiFields.style.display = 'block';
            document.getElementById('userPosisi').value = data.posisi || '';
            document.getElementById('userAlamat').value = data.alamat || '';
        } else {
            pegawaiFields.style.display = 'none';
        }
        
        userModal.show();
    }

    function toggleAddFields(role) {
        document.getElementById('addPegawaiFields').style.display = (role === 'pegawai') ? 'block' : 'none';
    }

    async function toggleStatus(role, id, status) {
        showIgAlert(
            'Ubah Status',
            'Apakah Anda yakin ingin mengubah status user ini?',
            status === 'aktif' ? 'Aktifkan' : 'Nonaktifkan',
            status === 'aktif' ? 'primary' : 'danger',
            async () => {
                const response = await fetch('<?= site_url('manage-users/toggle-status') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `role=${role}&id=${id}&status=${status}`
                });
                const result = await response.json();
                if (result.ok) location.reload();
                else alert('Gagal mengubah status: ' + (result.message || 'Error'));
            }
        );
    }

    // Instagram Style Alert System
    const igOverlay = document.getElementById('igAlertOverlay');
    const igTitle = document.getElementById('igAlertTitle');
    const igText = document.getElementById('igAlertText');
    const igConfirmBtn = document.getElementById('igAlertConfirmBtn');

    function showIgAlert(title, text, confirmText, type, onConfirm) {
        igTitle.innerText = title;
        igText.innerText = text;
        igConfirmBtn.innerText = confirmText;
        
        // Style based on type
        igConfirmBtn.className = 'ig-modal-btn ' + (type === 'danger' ? 'ig-btn-danger' : 'ig-btn-primary');
        
        igOverlay.style.display = 'grid';
        
        igConfirmBtn.onclick = async () => {
            igConfirmBtn.disabled = true;
            igConfirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            await onConfirm();
            closeIgAlert();
            igConfirmBtn.disabled = false;
        };
    }

    function closeIgAlert() {
        igOverlay.style.display = 'none';
    }

    async function deleteUser(role, id) {
        showIgAlert(
            'Eliminasi User',
            'Apakah Anda yakin ingin mengeliminasi user ini? Data akan dipindahkan ke tab Eliminasi.',
            'Eliminasi',
            'danger',
            async () => {
                const response = await fetch('<?= site_url('manage-users/delete') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `role=${role}&id=${id}`
                });
                const result = await response.json();
                if (result.ok) location.reload();
                else alert('Gagal mengeliminasi user: ' + (result.message || 'Error'));
            }
        );
    }

    async function restoreUser(id) {
        showIgAlert(
            'Pulihkan User',
            'Apakah Anda yakin ingin memulihkan user ini? Data akan dikembalikan ke tab Pegawai.',
            'Pulihkan',
            'primary',
            async () => {
                const response = await fetch('<?= site_url('manage-users/restore') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}`
                });
                const result = await response.json();
                if (result.ok) location.reload();
                else alert('Gagal memulihkan user: ' + (result.message || 'Error'));
            }
        );
    }

    // Client-side search logic
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const activeTab = document.querySelector('.tab-pane.active');
        if (!activeTab) return;
        
        const rows = activeTab.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    // Re-run search when tab changes
    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', () => {
            searchInput.dispatchEvent(new Event('keyup'));
        });
    });
</script>
<?= $this->endSection() ?>
