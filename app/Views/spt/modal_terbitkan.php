<link rel="stylesheet" href="<?= base_url('css/spt-master.css?v=' . (file_exists(FCPATH . 'css/spt-master.css') ? filemtime(FCPATH . 'css/spt-master.css') : time())) ?>">

<div class="modal fade" id="modalTerbitkanSpt" tabindex="-1" aria-labelledby="modalTerbitkanSptLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background-color: #FFFFFF;">
            <!-- Header Modal -->
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold text-dark fs-5" id="modalTerbitkanSptLabel" style="letter-spacing: -0.3px;">
                    Terbitkan spt
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <!-- Form Terbitkan SPT -->
            <form action="<?= base_url('/spt/store') ?>" method="POST" enctype="multipart/form-data" id="formTerbitkanSpt">
                <?= csrf_field() ?>

                <div class="modal-body px-4 pt-3 pb-2">
                    <!-- 1. Nomor SPT -->
                    <div class="mb-3">
                        <label for="nomor_spt" class="spt-modal-label">
                            NOMOR SPT <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nomor_spt" 
                            id="nomor_spt" 
                            class="form-control spt-modal-input" 
                            placeholder="Contoh: SPT/2026/DISKOMINFO/001" 
                            required
                            autocomplete="off"
                        >
                    </div>

                    <!-- 2. Tanggal Terbit SPT -->
                    <div class="mb-3">
                        <label for="tanggal_spt" class="spt-modal-label">
                            TANGGAL TERBIT SPT <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="tanggal_spt" 
                            id="tanggal_spt" 
                            class="form-control spt-modal-input" 
                            value="<?= date('Y-m-d') ?>" 
                            required
                        >
                    </div>

                    <!-- 3. Perihal / Keterangan Penugasan -->
                    <div class="mb-3">
                        <label for="perihal" class="spt-modal-label">
                            PERIHAL / KETERANGAN PENUGASAN <span class="text-danger">*</span>
                        </label>
                        <textarea 
                            name="perihal" 
                            id="perihal" 
                            class="form-control spt-modal-input" 
                            rows="3" 
                            placeholder="Tuliskan perihal atau keterangan penugasan.." 
                            required
                        ></textarea>
                    </div>

                    <!-- 4. Tanggal Mulai & Selesai Pelaksanaan (2 Kolom) -->
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label for="tanggal_mulai" class="spt-modal-label">
                                TANGGAL MULAI PELAKSANAAN <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="tanggal_mulai" 
                                id="tanggal_mulai" 
                                class="form-control spt-modal-input" 
                                value="<?= date('Y-m-d') ?>" 
                                required
                            >
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="tanggal_selesai" class="spt-modal-label">
                                TANGGAL SELESAI PELAKSANAAN <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="tanggal_selesai" 
                                id="tanggal_selesai" 
                                class="form-control spt-modal-input" 
                                value="<?= date('Y-m-d', strtotime('+7 days')) ?>" 
                                required
                            >
                        </div>
                    </div>

                    <!-- 5. Upload Dokumen SPT (PDF) -->
                    <div class="mb-3">
                        <label class="spt-modal-label">
                            Unggah Dokumen SPT (PDF) <span class="text-danger">*</span>
                        </label>
                        
                        <!-- Box Drag & Drop Upload -->
                        <div class="spt-dropzone-box" id="sptDropzoneBox">
                            <input 
                                type="file" 
                                name="file_spt" 
                                id="file_spt" 
                                class="d-none" 
                                accept=".pdf,application/pdf" 
                                required
                            >
                            
                            <!-- State Awal / Belum Ada File -->
                            <div class="spt-dropzone-empty" id="sptDropzoneEmpty">
                                <div class="spt-dropzone-icon mb-2">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                </div>
                                <div class="spt-dropzone-text">
                                    Klik atau seret file PDF ke sini
                                </div>
                                <div class="spt-dropzone-hint">
                                    Format yang didukung hanya PDF. Maksimal 5MB.
                                </div>
                            </div>

                            <!-- State Ketika File Dipilih -->
                            <div class="spt-dropzone-selected d-none" id="sptDropzoneSelected">
                                <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-white border">
                                    <div class="d-flex align-items-center gap-2 overflow-hidden text-start">
                                        <i class="bi bi-file-earmark-pdf-fill text-danger fs-4 flex-shrink-0"></i>
                                        <div class="overflow-hidden">
                                            <div class="fw-semibold text-dark text-truncate" id="sptSelectedFileName" style="font-size: 13px;">
                                                nama_file.pdf
                                            </div>
                                            <div class="text-secondary small" id="sptSelectedFileSize" style="font-size: 11px;">
                                                0 KB
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm text-danger border-0 p-1" id="btnRemoveSptFile" title="Hapus file">
                                        <i class="bi bi-x-circle-fill fs-5"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Tombol Aksi (Kiri Sesuai Mockup) -->
                <div class="modal-footer border-0 px-4 pt-1 pb-4 justify-content-start gap-2">
                    <button type="submit" class="spt-btn-modal-submit" id="btnSubmitTerbitkanSpt">
                        Terbitkan SPT
                    </button>
                    <button type="button" class="spt-btn-modal-cancel" data-bs-dismiss="modal">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('modalTerbitkanSpt');
    if (!modalEl) return;

    // Pindahkan modal ke <body> untuk mencegah stacking context z-index dari .app-layout
    if (modalEl.parentElement !== document.body) {
        document.body.appendChild(modalEl);
    }

    // Drag and Drop & File Upload Logic
    const dropzone = document.getElementById('sptDropzoneBox');
    const fileInput = document.getElementById('file_spt');
    const emptyState = document.getElementById('sptDropzoneEmpty');
    const selectedState = document.getElementById('sptDropzoneSelected');
    const fileNameEl = document.getElementById('sptSelectedFileName');
    const fileSizeEl = document.getElementById('sptSelectedFileSize');
    const removeBtn = document.getElementById('btnRemoveSptFile');

    if (dropzone && fileInput) {
        dropzone.addEventListener('click', function(e) {
            if (e.target.closest('#btnRemoveSptFile')) return;
            fileInput.click();
        });

        fileInput.addEventListener('change', function() {
            handleFile(this.files[0]);
        });

        // Drag & drop handlers
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('spt-dropzone-dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('spt-dropzone-dragover');
            }, false);
        });

        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFile(files[0]);
            }
        });

        if (removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                fileInput.value = '';
                emptyState.classList.remove('d-none');
                selectedState.classList.add('d-none');
            });
        }
    }

    function handleFile(file) {
        if (!file) return;

        if (!file.name.toLowerCase().endsWith('.pdf') && file.type !== 'application/pdf') {
            alert('Format file tidak didukung! Harap unggah file berformat PDF.');
            fileInput.value = '';
            return;
        }

        // Cek ukuran max 5MB
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file melebihi 5MB! Harap unggah file yang lebih kecil.');
            fileInput.value = '';
            return;
        }

        if (fileNameEl) fileNameEl.textContent = file.name;
        if (fileSizeEl) fileSizeEl.textContent = formatBytes(file.size);

        if (emptyState) emptyState.classList.add('d-none');
        if (selectedState) selectedState.classList.remove('d-none');
    }

    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    // Buka popup kalender saat klik di border atau area manapun pada input tanggal modal
    modalEl.querySelectorAll('input[type="date"]').forEach(function(inp) {
        inp.addEventListener('click', function() {
            if (typeof this.showPicker === 'function') {
                try {
                    this.showPicker();
                } catch (e) {}
            }
        });
    });

    // Reset saat modal ditutup
    modalEl.addEventListener('hidden.bs.modal', function() {
        const form = document.getElementById('formTerbitkanSpt');
        if (form) form.reset();
        if (fileInput) fileInput.value = '';
        if (emptyState) emptyState.classList.remove('d-none');
        if (selectedState) selectedState.classList.add('d-none');
    });
});
</script>
