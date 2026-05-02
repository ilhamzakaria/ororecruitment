<?php
$headerTitle = 'Isi Pertanyaan';
$headerSubtitle = 'Kelola pertanyaan tes interview';
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
    .status-Aktif { background: #e6f6ef; color: #1f8f5e; }
    .status-Nonaktif { background: #fff1f1; color: #c64747; }
    
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-grid;
        place-items: center;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .question-img-preview {
        max-width: 60px;
        max-height: 60px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid var(--line);
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
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-800 mb-1">Isi Pertanyaan</h1>
        <p class="text-muted small mb-0">Kelola daftar pertanyaan tes interview untuk calon pegawai.</p>
    </div>
    <div class="d-flex gap-3 align-items-center">
        <div class="search-container">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" class="form-control" placeholder="Cari pertanyaan...">
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
            <i class="bi bi-plus-lg"></i> Tambah Pertanyaan
        </button>
    </div>
</div>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success border-0 shadow-sm mb-4"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger border-0 shadow-sm mb-4"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card mb-4">
    <?php if (empty($questions)) : ?>
        <div class="empty-state">
            <i class="bi bi-patch-question"></i>
            <h4>Belum ada pertanyaan</h4>
            <p>Klik tombol "Tambah Pertanyaan" untuk mulai membuat pertanyaan tes.</p>
        </div>
    <?php else : ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="questionTable">
                <thead>
                    <tr>
                        <th>Tipe</th>
                        <th>Pertanyaan</th>
                        <th>Pilihan Jawaban</th>
                        <th>Jawaban Benar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($questions as $q) : ?>
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark text-uppercase"><?= $q['tipe_pertanyaan'] ?></span>
                            </td>
                            <td style="max-width: 300px;">
                                <div class="d-flex align-items-center gap-2">
                                    <?php if ($q['gambar_pertanyaan']) : ?>
                                        <img src="<?= base_url($q['gambar_pertanyaan']) ?>" class="question-img-preview" alt="Preview">
                                    <?php endif; ?>
                                    <div class="text-truncate" title="<?= esc($q['isi_pertanyaan']) ?>">
                                        <?= esc($q['isi_pertanyaan']) ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <small class="d-block">A: <?= esc(substr($q['pilihan_a'], 0, 20)) ?>...</small>
                                <small class="d-block">B: <?= esc(substr($q['pilihan_b'], 0, 20)) ?>...</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary rounded-circle" style="width: 24px; height: 24px; display: inline-grid; place-items: center;"><?= $q['jawaban_benar'] ?></span>
                            </td>
                            <td>
                                <span class="status-pill status-<?= $q['status_pertanyaan'] ?>">
                                    <?= $q['status_pertanyaan'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-secondary btn-action" onclick="editQuestion(<?= htmlspecialchars(json_encode($q)) ?>)"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-secondary btn-action <?= $q['status_pertanyaan'] === 'Aktif' ? 'text-warning' : 'text-success' ?>" onclick="toggleStatus('<?= $q['id_pertanyaan'] ?>', '<?= $q['status_pertanyaan'] === 'Aktif' ? 'Nonaktif' : 'Aktif' ?>')"><i class="bi bi-power"></i></button>
                                    <button class="btn btn-secondary btn-action text-danger" onclick="deleteQuestion('<?= $q['id_pertanyaan'] ?>')"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="<?= site_url('manage-questions/add') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title fw-800">Tambah Pertanyaan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-700">Isi Pertanyaan</label>
                                <textarea name="isi_pertanyaan" class="form-control" rows="3" required placeholder="Tuliskan pertanyaan di sini..."></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-700">Tipe Pertanyaan</label>
                                <select name="tipe_pertanyaan" class="form-select" required>
                                    <option value="text">Text</option>
                                    <option value="angka">Angka</option>
                                    <option value="gambar">Gambar</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-700">Upload Gambar <small class="text-muted">(Opsional)</small></label>
                                <input type="file" name="gambar_pertanyaan" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-700">Pilihan A</label>
                                <input type="text" name="pilihan_a" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-700">Pilihan B</label>
                                <input type="text" name="pilihan_b" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-700">Pilihan C</label>
                                <input type="text" name="pilihan_c" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-700">Pilihan D</label>
                                <input type="text" name="pilihan_d" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label fw-700">Jawaban Benar</label>
                            <select name="jawaban_benar" class="form-select" required>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-700">Status</label>
                            <select name="status_pertanyaan" class="form-select" required>
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Pertanyaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Question Modal -->
<div class="modal fade" id="editQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="<?= site_url('manage-questions/update') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title fw-800">Edit Pertanyaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="id_pertanyaan" id="edit_id_pertanyaan">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-700">Isi Pertanyaan</label>
                                <textarea name="isi_pertanyaan" id="edit_isi_pertanyaan" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-700">Tipe Pertanyaan</label>
                                <select name="tipe_pertanyaan" id="edit_tipe_pertanyaan" class="form-select" required>
                                    <option value="text">Text</option>
                                    <option value="angka">Angka</option>
                                    <option value="gambar">Gambar</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-700">Update Gambar <small class="text-muted">(Biarkan kosong jika tidak diubah)</small></label>
                                <input type="file" name="gambar_pertanyaan" class="form-control" accept="image/*">
                                <div id="edit_gambar_preview" class="mt-2"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-700">Pilihan A</label>
                                <input type="text" name="pilihan_a" id="edit_pilihan_a" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-700">Pilihan B</label>
                                <input type="text" name="pilihan_b" id="edit_pilihan_b" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-700">Pilihan C</label>
                                <input type="text" name="pilihan_c" id="edit_pilihan_c" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-700">Pilihan D</label>
                                <input type="text" name="pilihan_d" id="edit_pilihan_d" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label fw-700">Jawaban Benar</label>
                            <select name="jawaban_benar" id="edit_jawaban_benar" class="form-select" required>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-700">Status</label>
                            <select name="status_pertanyaan" id="edit_status_pertanyaan" class="form-select" required>
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Pertanyaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const editModal = new bootstrap.Modal(document.getElementById('editQuestionModal'));

    function editQuestion(data) {
        document.getElementById('edit_id_pertanyaan').value = data.id_pertanyaan;
        document.getElementById('edit_isi_pertanyaan').value = data.isi_pertanyaan;
        document.getElementById('edit_tipe_pertanyaan').value = data.tipe_pertanyaan;
        document.getElementById('edit_pilihan_a').value = data.pilihan_a;
        document.getElementById('edit_pilihan_b').value = data.pilihan_b;
        document.getElementById('edit_pilihan_c').value = data.pilihan_c;
        document.getElementById('edit_pilihan_d').value = data.pilihan_d;
        document.getElementById('edit_jawaban_benar').value = data.jawaban_benar;
        document.getElementById('edit_status_pertanyaan').value = data.status_pertanyaan;
        
        const previewDiv = document.getElementById('edit_gambar_preview');
        if (data.gambar_pertanyaan) {
            previewDiv.innerHTML = `<img src="<?= base_url() ?>${data.gambar_pertanyaan}" class="question-img-preview" style="max-width: 100px; max-height: 100px;">`;
        } else {
            previewDiv.innerHTML = '';
        }
        
        editModal.show();
    }

    async function toggleStatus(id, status) {
        if (confirm('Ubah status pertanyaan ini?')) {
            const response = await fetch('<?= site_url('manage-questions/toggle-status') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&status=${status}`
            });
            const result = await response.json();
            if (result.ok) location.reload();
            else alert('Gagal mengubah status');
        }
    }

    async function deleteQuestion(id) {
        if (confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')) {
            const response = await fetch('<?= site_url('manage-questions/delete') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}`
            });
            const result = await response.json();
            if (result.ok) location.reload();
            else alert('Gagal menghapus pertanyaan');
        }
    }

    // Search logic
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#questionTable tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>
<?= $this->endSection() ?>
