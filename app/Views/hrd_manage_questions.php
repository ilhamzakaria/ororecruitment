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

    .option-img-preview {
        max-width: 40px;
        max-height: 40px;
        object-fit: contain;
        border-radius: 4px;
        border: 1px solid var(--line);
        background: #f8f9fa;
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

    .option-type-toggle {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .input-group-text-custom {
        background: var(--soft);
        font-weight: 800;
        font-size: 12px;
        width: 40px;
        justify-content: center;
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
                        <th width="40">No</th>
                        <th>Tipe</th>
                        <th>Pertanyaan</th>
                        <th>Pilihan Jawaban</th>
                        <th>Jawaban Benar</th>
                        <th>Status</th>
                        <th width="100">Urutan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($questions as $q) : ?>
                        <tr>
                            <td class="text-center fw-800">
                                <?= $q['urutan_pertanyaan'] ?>
                            </td>
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
                                <?php 
                                    $opts = ['a', 'b', 'c', 'd', 'e'];
                                    foreach ($opts as $o) : 
                                        $tipe = $q['tipe_pilihan_'.$o] ?? 'text';
                                        $val = $q['pilihan_'.$o];
                                        $img = $q['gambar_pilihan_'.$o];
                                ?>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-soft text-primary fw-800" style="width: 20px; font-size: 10px;"><?= strtoupper($o) ?></span>
                                        <?php if ($tipe === 'gambar' && $img) : ?>
                                            <img src="<?= base_url($img) ?>" class="option-img-preview" alt="Opt <?= $o ?>">
                                        <?php else : ?>
                                            <small class="text-truncate" style="max-width: 150px;"><?= esc(substr($val, 0, 30)) ?><?= strlen($val) > 30 ? '...' : '' ?></small>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
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
                                <div class="d-flex gap-1 justify-content-center">
                                    <button class="btn btn-light btn-sm p-0" style="width: 26px; height: 26px;" onclick="reorder('<?= $q['id_pertanyaan'] ?>', 'up')" title="Naik"><i class="bi bi-chevron-up"></i></button>
                                    <button class="btn btn-light btn-sm p-0" style="width: 26px; height: 26px;" onclick="reorder('<?= $q['id_pertanyaan'] ?>', 'down')" title="Turun"><i class="bi bi-chevron-down"></i></button>
                                    <button class="btn btn-light btn-sm p-0" style="width: 26px; height: 26px;" onclick="reorder('<?= $q['id_pertanyaan'] ?>', 'move')" title="Pindah ke nomor..."><i class="bi bi-hash"></i></button>
                                </div>
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
                                <input type="file" name="gambar_pertanyaan" class="form-control" accept="image/*" onchange="previewFile(this, 'add_gambar_preview')">
                                <div id="add_gambar_preview" class="mt-2"></div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-800 mt-4 mb-3 border-bottom pb-2">Pilihan Jawaban (A-E)</h6>
                    <div class="row">
                        <?php foreach (['a', 'b', 'c', 'd', 'e'] as $o) : ?>
                        <div class="col-md-6 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-700 mb-0">Pilihan <?= strtoupper($o) ?></label>
                                <div class="btn-group btn-group-sm" role="group">
                                    <input type="radio" class="btn-check" name="tipe_pilihan_<?= $o ?>" id="type_<?= $o ?>_text" value="text" checked onchange="toggleOptionInput('<?= $o ?>', 'add')">
                                    <label class="btn btn-outline-secondary" for="type_<?= $o ?>_text">Text</label>
                                    
                                    <input type="radio" class="btn-check" name="tipe_pilihan_<?= $o ?>" id="type_<?= $o ?>_gambar" value="gambar" onchange="toggleOptionInput('<?= $o ?>', 'add')">
                                    <label class="btn btn-outline-secondary" for="type_<?= $o ?>_gambar">Gambar</label>
                                </div>
                            </div>
                            
                            <div id="wrapper_<?= $o ?>_text_add">
                                <input type="text" name="pilihan_<?= $o ?>" class="form-control" placeholder="Teks pilihan <?= $o ?>">
                            </div>
                            <div id="wrapper_<?= $o ?>_gambar_add" style="display: none;">
                                <input type="file" name="gambar_pilihan_<?= $o ?>" class="form-control" accept="image/*" onchange="previewFile(this, 'add_preview_<?= $o ?>')">
                                <div id="add_preview_<?= $o ?>" class="mt-2"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label fw-700">Jawaban Benar</label>
                            <select name="jawaban_benar" class="form-select" required>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="E">E</option>
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
                                <label class="form-label fw-700">Update Gambar <small class="text-muted">(Kosongkan jika tidak diubah)</small></label>
                                <input type="file" name="gambar_pertanyaan" class="form-control" accept="image/*" onchange="previewFile(this, 'edit_gambar_preview')">
                                <div id="edit_gambar_preview" class="mt-2"></div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-800 mt-4 mb-3 border-bottom pb-2">Pilihan Jawaban (A-E)</h6>
                    <div class="row">
                        <?php foreach (['a', 'b', 'c', 'd', 'e'] as $o) : ?>
                        <div class="col-md-6 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-700 mb-0">Pilihan <?= strtoupper($o) ?></label>
                                <div class="btn-group btn-group-sm" role="group">
                                    <input type="radio" class="btn-check" name="tipe_pilihan_<?= $o ?>" id="edit_type_<?= $o ?>_text" value="text" onchange="toggleOptionInput('<?= $o ?>', 'edit')">
                                    <label class="btn btn-outline-secondary" for="edit_type_<?= $o ?>_text">Text</label>
                                    
                                    <input type="radio" class="btn-check" name="tipe_pilihan_<?= $o ?>" id="edit_type_<?= $o ?>_gambar" value="gambar" onchange="toggleOptionInput('<?= $o ?>', 'edit')">
                                    <label class="btn btn-outline-secondary" for="edit_type_<?= $o ?>_gambar">Gambar</label>
                                </div>
                            </div>
                            
                            <div id="wrapper_<?= $o ?>_text_edit">
                                <input type="text" name="pilihan_<?= $o ?>" id="edit_pilihan_<?= $o ?>" class="form-control">
                            </div>
                            <div id="wrapper_<?= $o ?>_gambar_edit" style="display: none;">
                                <input type="file" name="gambar_pilihan_<?= $o ?>" class="form-control" accept="image/*" onchange="previewFile(this, 'edit_preview_<?= $o ?>')">
                                <div id="edit_preview_<?= $o ?>" class="mt-2"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label fw-700">Jawaban Benar</label>
                            <select name="jawaban_benar" id="edit_jawaban_benar" class="form-select" required>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="E">E</option>
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

    function toggleOptionInput(opt, mode) {
        const modalId = mode === 'add' ? 'addQuestionModal' : 'editQuestionModal';
        const modal = document.getElementById(modalId);
        const type = modal.querySelector(`input[name="tipe_pilihan_${opt}"]:checked`)?.value || 'text';
        const textWrap = document.getElementById(`wrapper_${opt}_text_${mode}`);
        const imgWrap = document.getElementById(`wrapper_${opt}_gambar_${mode}`);
        const textInput = textWrap.querySelector('input');
        const imgInput = imgWrap.querySelector('input');
        
        if (type === 'gambar') {
            textWrap.style.display = 'none';
            imgWrap.style.display = 'block';
            textInput.required = false;
            
            if (mode === 'add') {
                imgInput.required = true;
            } else {
                const preview = document.getElementById(`edit_preview_${opt}`);
                const hasExisting = preview && preview.querySelector('img') !== null;
                imgInput.required = !hasExisting;
            }
        } else {
            textWrap.style.display = 'block';
            imgWrap.style.display = 'none';
            textInput.required = true;
            imgInput.required = false;
        }
    }

    function previewFile(input, targetId) {
        const preview = document.getElementById(targetId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" class="option-img-preview" style="width: 80px; height: 80px; object-fit: contain;">`;
                if (input.required) input.required = false;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function editQuestion(data) {
        document.getElementById('edit_id_pertanyaan').value = data.id_pertanyaan;
        document.getElementById('edit_isi_pertanyaan').value = data.isi_pertanyaan;
        document.getElementById('edit_tipe_pertanyaan').value = data.tipe_pertanyaan;
        
        const opts = ['a', 'b', 'c', 'd', 'e'];
        opts.forEach(o => {
            const tipe = data[`tipe_pilihan_${o}`] || 'text';
            const val = data[`pilihan_${o}`];
            const img = data[`gambar_pilihan_${o}`];
            
            // Set radio
            document.getElementById(`edit_type_${o}_${tipe}`).checked = true;
            
            // Set text value
            document.getElementById(`edit_pilihan_${o}`).value = val || '';
            
            // Set preview
            const preview = document.getElementById(`edit_preview_${o}`);
            if (img) {
                preview.innerHTML = `<img src="<?= base_url() ?>${img}" class="option-img-preview" style="width: 60px; height: 60px;">`;
            } else {
                preview.innerHTML = '';
            }
            
            // Toggle visibility
            toggleOptionInput(o, 'edit');
        });

        document.getElementById('edit_jawaban_benar').value = data.jawaban_benar;
        document.getElementById('edit_status_pertanyaan').value = data.status_pertanyaan;
        
        const mainPreview = document.getElementById('edit_gambar_preview');
        if (data.gambar_pertanyaan) {
            mainPreview.innerHTML = `<img src="<?= base_url() ?>${data.gambar_pertanyaan}" class="question-img-preview" style="max-width: 100px; max-height: 100px;">`;
        } else {
            mainPreview.innerHTML = '';
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

    async function reorder(id, direction) {
        if (direction === 'move') {
            const newPos = prompt('Pindah ke urutan nomor berapa?');
            if (newPos === null || newPos === '' || isNaN(newPos)) return;
            direction = newPos;
        }

        const response = await fetch('<?= site_url('manage-questions/reorder') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&direction=${direction}`
        });
        const result = await response.json();
        if (result.ok) location.reload();
        else alert('Gagal mengubah urutan: ' + (result.message || 'Error unknown'));
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
