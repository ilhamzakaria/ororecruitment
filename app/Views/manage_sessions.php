<?= $this->extend('layout/main') ?>

<?= $this->section('styles') ?>
<style>
    .manage-container {
        padding: 24px;
    }
    .page-title {
        font-size: 34px;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin-bottom: 4px;
        color: var(--ink);
    }
    .page-subtitle {
        margin: 0;
        color: var(--muted);
        font-size: 15px;
        font-weight: 500;
    }
    .session-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .session-card {
        background: linear-gradient(180deg, #ffffff 0%, #fcfefd 100%);
        border: 1px solid var(--line);
        border-radius: 14px;
        box-shadow: 0 8px 20px rgba(25, 70, 46, 0.06);
        padding: 16px;
        min-height: 134px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .session-card:hover {
        transform: translateY(-2px);
        border-color: #cfe5d8;
        box-shadow: 0 14px 28px rgba(25, 70, 46, 0.1);
    }
    .session-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .session-name {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        font-size: 15px;
        font-weight: 800;
        color: #1f3f2f;
    }
    .session-name i {
        color: var(--green-1);
        font-size: 14px;
    }
    .session-badges {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 6px;
    }
    .session-chip {
        display: inline-flex;
        align-items: center;
        min-height: 22px;
        border-radius: 999px;
        padding: 0 9px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        border: 1px solid transparent;
    }
    .chip-open {
        background: #e6f2ff;
        color: #205f9f;
        border-color: #d6e8ff;
    }
    .chip-done {
        background: #e8f8f0;
        color: #1d7c52;
        border-color: #d1eddc;
    }
    .chip-idle {
        background: #f5f7f6;
        color: #8a9690;
        border-color: #e8ecea;
    }
    .session-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 14px;
    }
    .session-btn {
        min-height: 38px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        box-shadow: none !important;
    }
    .session-btn.btn-primary {
        background: linear-gradient(90deg, var(--green-1), var(--green-2));
    }
    .session-btn.btn-outline-danger {
        border: 1px solid #f0c2c9;
        color: #bb4b5e;
        background: #fff;
    }
    .session-btn.btn-outline-danger:hover {
        background: #fff5f7;
        color: #a43a4c;
        border-color: #e9a9b4;
    }
    .action-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        border: 1px solid var(--line);
        box-shadow: var(--shadow);
    }
    .bulk-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }
    .user-table-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid var(--line);
        box-shadow: var(--shadow);
        overflow: hidden;
    }
    .table thead th {
        background: var(--soft);
        color: var(--muted);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 800;
        padding: 16px;
        border: 0;
    }
    .table tbody td {
        padding: 16px;
        vertical-align: middle;
        border-bottom: 1px solid var(--line);
    }
    .status-badge {
        font-size: 10px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 999px;
        text-transform: uppercase;
    }
    .badge-belum_dibuka { background: #f1f3f5; color: #868e96; }
    .badge-dibuka { background: #e7f5ff; color: #228be6; }
    .badge-berjalan { background: #fff4e6; color: #fd7e14; }
    .badge-selesai { background: #ebfbee; color: #40c057; }

    .form-check-input:checked {
        background-color: var(--green-1);
        border-color: var(--green-1);
    }
    @media (max-width: 1100px) {
        .session-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="manage-container">
    <div class="mb-4">
        <h2 class="page-title">Manajemen Sesi Tes</h2>
        <p class="page-subtitle">Kontrol pembukaan dan penutupan sesi interview secara massal.</p>
    </div>
    
    <?php
        $stats = [
            1 => ['belum' => 0, 'aktif' => 0, 'selesai' => 0],
            2 => ['belum' => 0, 'aktif' => 0, 'selesai' => 0],
            3 => ['belum' => 0, 'aktif' => 0, 'selesai' => 0],
        ];
        foreach ($employees as $emp) {
            $id = $emp['id_user'] ?? $emp['id_pegawai'] ?? $emp['id'];
            for ($i = 1; $i <= 3; $i++) {
                $c = array_filter($controls, fn($ctrl) => $ctrl['id_pegawai'] == $id && $ctrl['nomor_sesi'] == $i);
                $s = !empty($c) ? reset($c)['status_sesi'] : 'belum_dibuka';
                if ($s === 'dibuka' || $s === 'berjalan') $stats[$i]['aktif']++;
                elseif ($s === 'selesai') $stats[$i]['selesai']++;
                else $stats[$i]['belum']++;
            }
        }
    ?>

    <div class="session-grid">
        <?php for ($i = 1; $i <= 3; $i++): ?>
            <div class="session-card">
                <div class="session-head">
                    <h6 class="session-name"><i class="bi bi-layers"></i> Sesi <?= $i ?></h6>
                    <div class="session-badges">
                        <?php if ($stats[$i]['aktif'] > 0): ?>
                            <span class="session-chip chip-open">Dibuka: <?= $stats[$i]['aktif'] ?></span>
                        <?php endif; ?>
                        <?php if ($stats[$i]['selesai'] > 0): ?>
                            <span class="session-chip chip-done">Selesai: <?= $stats[$i]['selesai'] ?></span>
                        <?php endif; ?>
                        <?php if ($stats[$i]['aktif'] == 0 && $stats[$i]['selesai'] == 0): ?>
                            <span class="session-chip chip-idle">Belum Dimulai</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="session-actions">
                    <button class="btn btn-primary session-btn" onclick="bulkOpen(<?= $i ?>)">
                        <i class="bi bi-play-fill"></i> Mulai
                    </button>
                    <button class="btn btn-outline-danger session-btn" onclick="bulkClose(<?= $i ?>)">
                        <i class="bi bi-lock-fill"></i> Tutup
                    </button>
                </div>
            </div>
        <?php endfor; ?>
    </div>

    <div class="user-table-card">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;">
                        <input type="checkbox" class="form-check-input" id="selectAll" onclick="toggleSelectAll(this)">
                    </th>
                    <th>Nama Pegawai</th>
                    <th>ID Pegawai</th>
                    <th class="text-center">Status Sesi 1</th>
                    <th class="text-center">Status Sesi 2</th>
                    <th class="text-center">Status Sesi 3</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $emp): 
                    $idPegawai = $emp['id_user'] ?? $emp['id_pegawai'] ?? $emp['id'] ?? null;
                ?>
                    <tr>
                        <td>
                            <?php if ($idPegawai): ?>
                                <input type="checkbox" class="form-check-input user-checkbox" value="<?= esc($idPegawai) ?>">
                            <?php else: ?>
                                <input type="checkbox" class="form-check-input" disabled>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="fw-bold text-dark"><?= esc($emp['name'] ?? $emp['username'] ?? 'User') ?></div>
                        </td>
                        <td><code class="small"><?= esc($idPegawai ?? '-') ?></code></td>
                        <td class="text-center">
                            <?php 
                                $c1 = array_filter($controls, fn($c) => $c['id_pegawai'] == $idPegawai && $c['nomor_sesi'] == 1);
                                $s1 = !empty($c1) ? reset($c1)['status_sesi'] : 'belum_dibuka';
                            ?>
                            <span class="status-badge badge-<?= $s1 ?>"><?= $s1 === 'berjalan' ? 'Sedang Berjalan' : str_replace('_', ' ', $s1) ?></span>
                        </td>
                        <td class="text-center">
                            <?php 
                                $c2 = array_filter($controls, fn($c) => $c['id_pegawai'] == $idPegawai && $c['nomor_sesi'] == 2);
                                $s2 = !empty($c2) ? reset($c2)['status_sesi'] : 'belum_dibuka';
                            ?>
                            <span class="status-badge badge-<?= $s2 ?>"><?= $s2 === 'berjalan' ? 'Sedang Berjalan' : str_replace('_', ' ', $s2) ?></span>
                        </td>
                        <td class="text-center">
                            <?php 
                                $c3 = array_filter($controls, fn($c) => $c['id_pegawai'] == $idPegawai && $c['nomor_sesi'] == 3);
                                $s3 = !empty($c3) ? reset($c3)['status_sesi'] : 'belum_dibuka';
                            ?>
                            <span class="status-badge badge-<?= $s3 ?>"><?= $s3 === 'berjalan' ? 'Sedang Berjalan' : str_replace('_', ' ', $s3) ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($employees)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada data pegawai.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Custom Alert/Confirm Modal -->
<div class="custom-alert-overlay" id="alertOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center; opacity:0; transition: opacity 0.3s ease;">
    <div class="custom-alert-box" style="background:#fff; border-radius:15px; width:90%; max-width:400px; padding:30px; text-align:center; transform: scale(0.9); transition: transform 0.3s ease;">
        <div id="alertIcon" style="font-size: 50px; margin-bottom: 20px; color: var(--green-1);">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <h3 id="alertTitle" style="font-weight:800; margin-bottom:10px;">Sukses</h3>
        <p id="alertMsg" style="color:var(--muted); margin-bottom:25px;"></p>
        <div id="alertButtons" style="display: flex; gap: 10px;">
            <button id="alertCancelBtn" class="btn btn-secondary w-100" style="display:none;" onclick="closeAlert()">Cancel</button>
            <button id="alertOkBtn" class="btn btn-primary w-100" onclick="closeAlert()">Mengerti</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function toggleSelectAll(source) {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(cb => cb.checked = source.checked);
    }

    let alertCloseTimeout = null;

    function showAlert(msg, title = 'Sukses', icon = 'bi-check-circle-fill', color = 'var(--green-1)', okText = 'Mengerti', callback = null) {
        if (alertCloseTimeout) clearTimeout(alertCloseTimeout);
        
        const overlay = document.getElementById('alertOverlay');
        const box = overlay.querySelector('.custom-alert-box');
        const cancelBtn = document.getElementById('alertCancelBtn');
        const okBtn = document.getElementById('alertOkBtn');

        document.getElementById('alertMsg').textContent = msg;
        document.getElementById('alertTitle').textContent = title;
        document.getElementById('alertIcon').innerHTML = `<i class="${icon}"></i>`;
        document.getElementById('alertIcon').style.color = color;
        
        cancelBtn.style.display = 'none';
        okBtn.textContent = okText;
        okBtn.className = 'btn btn-primary w-100';
        okBtn.onclick = () => {
            closeAlert();
            if (callback) callback();
        };

        overlay.style.display = 'flex';
        setTimeout(() => {
            overlay.style.opacity = '1';
            box.style.transform = 'scale(1)';
        }, 10);
    }

    function showConfirm(msg, title = 'Konfirmasi', onConfirm = null) {
        if (alertCloseTimeout) clearTimeout(alertCloseTimeout);

        const overlay = document.getElementById('alertOverlay');
        const box = overlay.querySelector('.custom-alert-box');
        const cancelBtn = document.getElementById('alertCancelBtn');
        const okBtn = document.getElementById('alertOkBtn');

        document.getElementById('alertMsg').textContent = msg;
        document.getElementById('alertTitle').textContent = title;
        document.getElementById('alertIcon').innerHTML = `<i class="bi bi-question-circle-fill"></i>`;
        document.getElementById('alertIcon').style.color = '#3498db';
        
        cancelBtn.style.display = 'block';
        cancelBtn.textContent = 'Cancel';
        
        okBtn.textContent = 'Mengerti';
        okBtn.className = 'btn btn-primary w-100';
        okBtn.onclick = () => {
            closeAlert(false); // don't reload yet
            if (onConfirm) onConfirm();
        };

        overlay.style.display = 'flex';
        setTimeout(() => {
            overlay.style.opacity = '1';
            box.style.transform = 'scale(1)';
        }, 10);
    }

    function closeAlert(shouldReload = true) {
        if (alertCloseTimeout) clearTimeout(alertCloseTimeout);

        const overlay = document.getElementById('alertOverlay');
        const box = overlay.querySelector('.custom-alert-box');
        overlay.style.opacity = '0';
        box.style.transform = 'scale(0.9)';
        
        alertCloseTimeout = setTimeout(() => {
            overlay.style.display = 'none';
            if (shouldReload) {
                location.reload();
            }
        }, 300);
    }




    async function bulkOpen(nomorSesi) {
        const selected = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
        
        if (selected.length === 0) {
            showAlert('Pilih minimal satu pegawai terlebih dahulu.', 'Peringatan', 'bi-exclamation-triangle-fill', '#f5a623');
            return;
        }

        showConfirm(`Apakah Anda yakin ingin membuka Sesi ${nomorSesi} untuk ${selected.length} pegawai terpilih?`, 'Konfirmasi Buka Sesi', async () => {
            const formData = new FormData();
            selected.forEach(id => formData.append('userIds[]', id));
            formData.append('nomorSesi', nomorSesi);

            try {
                const res = await fetch('<?= site_url('dashboard-hrd/bulk-open-session') ?>', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.ok) {
                    showAlert(data.message);
                } else {
                    showAlert(data.message || 'Gagal membuka sesi.', 'Gagal', 'bi-x-circle-fill', '#c64747');
                }
            } catch (e) {
                showAlert('Terjadi kesalahan jaringan.', 'Error', 'bi-x-circle-fill', '#c64747');
            }
        });
    }

    async function bulkClose(nomorSesi) {
        const selected = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
        
        if (selected.length === 0) {
            showAlert('Pilih minimal satu pegawai terlebih dahulu.', 'Peringatan', 'bi-exclamation-triangle-fill', '#f5a623');
            return;
        }

        showConfirm(`Apakah Anda yakin ingin menutup Sesi ${nomorSesi} untuk ${selected.length} pegawai terpilih?`, 'Konfirmasi Tutup Sesi', async () => {
            const formData = new FormData();
            selected.forEach(id => formData.append('userIds[]', id));
            formData.append('nomorSesi', nomorSesi);

            try {
                const res = await fetch('<?= site_url('dashboard-hrd/bulk-close-session') ?>', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.ok) {
                    showAlert(data.message);
                } else {
                    showAlert(data.message || 'Gagal menutup sesi.', 'Gagal', 'bi-x-circle-fill', '#c64747');
                }
            } catch (e) {
                showAlert('Terjadi kesalahan jaringan.', 'Error', 'bi-x-circle-fill', '#c64747');
            }
        });
    }

</script>
<?= $this->endSection() ?>
