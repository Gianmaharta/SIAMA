/**
 * ==============================================================
 * SIAMEDA - Centralized SweetAlert2 Notification & Action System
 * Consistent, Modern, and Professional UI Notifications
 * ==============================================================
 */

(function (window, document) {
    'use strict';

    if (typeof Swal === 'undefined') {
        console.warn('SweetAlert2 library is not loaded. SiamaSwal fallback mode.');
        window.SiamaSwal = {
            success: (t, m) => alert(t + (m ? '\n' + m : '')),
            error: (t, m) => alert(t + (m ? '\n' + m : '')),
            warning: (t, m) => alert(t + (m ? '\n' + m : '')),
            info: (t, m) => alert(t + (m ? '\n' + m : '')),
            confirm: (opt) => Promise.resolve({ isConfirmed: confirm(opt.text || opt.title || 'Lanjutkan?') }),
            confirmDelete: (opt) => Promise.resolve({ isConfirmed: confirm(opt.text || 'Apakah Anda yakin ingin menghapus data ini?') }),
            loading: () => {},
            close: () => {}
        };
        return;
    }

    // Custom Mixin configured for SIAMEDA visual identity
    const baseCustomClass = {
        container: 'siama-swal-container',
        popup: 'siama-swal-popup',
        header: 'siama-swal-header',
        title: 'siama-swal-title',
        closeButton: 'siama-swal-close-btn',
        icon: 'siama-swal-icon',
        htmlContainer: 'siama-swal-html',
        actions: 'siama-swal-actions',
        confirmButton: 'siama-swal-btn siama-swal-btn-primary',
        denyButton: 'siama-swal-btn siama-swal-btn-deny',
        cancelButton: 'siama-swal-btn siama-swal-btn-cancel',
        timerProgressBar: 'siama-swal-progress-bar'
    };

    function getCustomClass(overrides = {}) {
        return Object.assign({}, baseCustomClass, overrides);
    }

    const SwalSIAMA = Swal.mixin({
        customClass: baseCustomClass,
        buttonsStyling: false,
        reverseButtons: true,
        focusConfirm: false,
        showDenyButton: false,
        didOpen: (popup) => {
            // Hilangkan tombol Deny / No agar selalu hanya ada 2 tombol (Batal & Konfirmasi)
            const denyBtn = popup.querySelector('.swal2-deny');
            if (denyBtn) {
                denyBtn.style.setProperty('display', 'none', 'important');
                denyBtn.remove();
            }

            const actions = popup.querySelector('.swal2-actions');
            if (actions) {
                actions.style.setProperty('display', 'flex', 'important');
                actions.style.setProperty('gap', '16px', 'important');
                actions.style.setProperty('justify-content', 'center', 'important');
                actions.style.setProperty('align-items', 'center', 'important');
                actions.style.setProperty('margin-top', '26px', 'important');

                const btns = actions.querySelectorAll('.swal2-confirm, .swal2-cancel');
                btns.forEach((b) => {
                    b.style.setProperty('margin', '0 8px', 'important');
                    b.style.setProperty('border-radius', '14px', 'important');
                });
            }
        }
    });

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    /**
     * Mengubah tombol konfirmasi pada SweetAlert aktif menjadi loading state
     * tanpa membuka popup/modal baru.
     */
    function setSwalButtonLoading(loadingText = 'Memproses...') {
        if (typeof Swal !== 'undefined') {
            const confirmBtn = Swal.getConfirmButton();
            if (confirmBtn) {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="width: 13px; height: 13px; border-width: 2px;"></span> ${loadingText}`;
            }
            const cancelBtn = Swal.getCancelButton();
            if (cancelBtn) {
                cancelBtn.disabled = true;
            }
            const closeBtn = Swal.getCloseButton();
            if (closeBtn) {
                closeBtn.disabled = true;
            }
        }
    }

    const SiamaSwal = {
        /**
         * Notifikasi Berhasil (Success)
         * - Auto dismiss dengan timer (default 3000ms / 3 detik)
         * - Tanpa tombol konfirmasi/batal/deny untuk menghindari kebingungan
         * - Menghilangkan instance Swal sebelumnya agar tidak bertumpuk
         * - Digunakan untuk tambah, edit, hapus, verifikasi, dsb.
         */
        success: function (title, message = null, timer = 3000) {
            let titleText = title || 'Berhasil';
            let htmlText = message || '';

            // Jika hanya 1 argumen dan panjang, jadikan message dengan title "Berhasil"
            if (!message && title && title.length > 25) {
                titleText = 'Berhasil';
                htmlText = title;
            }

            // Tutup dialog sebelumnya jika masih aktif agar tidak terjadi popup ganda
            if (typeof Swal !== 'undefined' && Swal.isVisible()) {
                Swal.close();
            }

            return SwalSIAMA.fire({
                icon: 'success',
                title: titleText,
                html: htmlText,
                timer: timer,
                timerProgressBar: true,
                showConfirmButton: false,
                showCancelButton: false,
                showDenyButton: false,
                allowOutsideClick: true
            });
        },

        /**
         * Notifikasi Gagal / Kesalahan (Error)
         * - Tidak otomatis menghilang, tetap tampil sampai pengguna menutupnya
         * - Digunakan untuk validasi gagal, gagal query, dsb.
         */
        error: function (title, message = null) {
            let titleText = title || 'Terjadi Kesalahan';
            let htmlText = message || '';

            if (!message && title && title.length > 30) {
                titleText = 'Terjadi Kesalahan';
                htmlText = title;
            }

            return SwalSIAMA.fire({
                icon: 'error',
                title: titleText,
                html: htmlText,
                showConfirmButton: true,
                confirmButtonText: 'Tutup',
                customClass: getCustomClass({
                    confirmButton: 'siama-swal-btn siama-swal-btn-primary'
                }),
                allowOutsideClick: false
            });
        },

        /**
         * Notifikasi Peringatan (Warning)
         * - Tidak otomatis menghilang
         */
        warning: function (title, message = null) {
            return SwalSIAMA.fire({
                icon: 'warning',
                title: title || 'Perhatian',
                html: message || '',
                showConfirmButton: true,
                confirmButtonText: 'Mengerti',
                customClass: getCustomClass({
                    confirmButton: 'siama-swal-btn siama-swal-btn-primary'
                })
            });
        },

        /**
         * Notifikasi Informasi (Info)
         */
        info: function (title, message = null) {
            return SwalSIAMA.fire({
                icon: 'info',
                title: title || 'Pemberitahuan',
                html: message || '',
                showConfirmButton: true,
                confirmButtonText: 'Tutup',
                customClass: getCustomClass({
                    confirmButton: 'siama-swal-btn siama-swal-btn-primary'
                })
            });
        },

        /**
         * Notifikasi Konfirmasi Tindakan Penting (Confirmation)
         * - Tidak otomatis menghilang
         * - Sediakan tombol konfirmasi dan batal
         * - Mendukung state loading untuk mencegah double action
         */
        confirm: function (options = {}) {
            const defaults = {
                title: 'Apakah Anda yakin?',
                text: 'Tindakan ini tidak dapat dibatalkan.',
                icon: 'warning',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                isDanger: false,
                showLoaderOnConfirm: true
            };
            const config = Object.assign({}, defaults, options);

            return SwalSIAMA.fire({
                icon: config.icon,
                title: config.title,
                html: config.html || config.text,
                showCancelButton: true,
                showDenyButton: false,
                confirmButtonText: config.confirmButtonText,
                cancelButtonText: config.cancelButtonText,
                customClass: getCustomClass({
                    confirmButton: config.isDanger 
                        ? 'siama-swal-btn siama-swal-btn-danger' 
                        : 'siama-swal-btn siama-swal-btn-primary',
                    cancelButton: 'siama-swal-btn siama-swal-btn-cancel'
                }),
                showLoaderOnConfirm: config.showLoaderOnConfirm,
                allowOutsideClick: () => !Swal.isLoading()
            });
        },

        /**
         * Notifikasi Khusus Konfirmasi Penghapusan Data (Delete Confirmation)
         * Sesuai ketentuan:
         * - Judul: "Apakah Anda yakin?"
         * - Deskripsi: "Data yang dihapus tidak dapat dikembalikan."
         * - Tombol "Ya, Hapus" dan "Batal"
         * - Loading state saat proses
         */
        confirmDelete: function (options = {}) {
            const defaults = {
                title: 'Apakah Anda yakin?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                itemName: null,
                itemSub: null,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            };
            const config = Object.assign({}, defaults, options);

            let bodyHtml = `<div class="mb-2 text-secondary" style="font-size: 13.5px;">${config.text}</div>`;
            if (config.itemName) {
                bodyHtml += `
                    <div class="siama-swal-detail-box">
                        <div class="siama-swal-detail-title">Data yang dipilih:</div>
                        <div class="siama-swal-detail-value">${escapeHtml(config.itemName)}</div>
                        ${config.itemSub ? `<div class="siama-swal-detail-sub mt-1">${escapeHtml(config.itemSub)}</div>` : ''}
                    </div>
                `;
            }

            return SwalSIAMA.fire({
                icon: 'warning',
                title: config.title,
                html: bodyHtml,
                showCancelButton: true,
                showDenyButton: false,
                confirmButtonText: config.confirmButtonText,
                cancelButtonText: config.cancelButtonText,
                customClass: getCustomClass({
                    confirmButton: 'siama-swal-btn siama-swal-btn-danger',
                    cancelButton: 'siama-swal-btn siama-swal-btn-cancel'
                }),
                allowOutsideClick: false
            });
        },

        /**
         * State Loading / Menunggu Respon Server
         */
        loading: function (title = 'Memproses...', text = '') {
            return SwalSIAMA.fire({
                title: title,
                html: text,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                showCancelButton: false,
                showDenyButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },

        close: function () {
            Swal.close();
        }
    };

    window.SiamaSwal = SiamaSwal;

    /**
     * Inisialisasi Otomatis saat Halaman Selesai Dimuat
     */
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Eksekusi Notifikasi dari Flashdata Session CodeIgniter 4
        const flashEl = document.getElementById('siama-flash-data');
        if (flashEl) {
            const successMsg = flashEl.getAttribute('data-success');
            const errorMsg = flashEl.getAttribute('data-error');
            const errorsMsg = flashEl.getAttribute('data-errors');
            const warningMsg = flashEl.getAttribute('data-warning');
            const infoMsg = flashEl.getAttribute('data-info');

            if (successMsg && successMsg.trim() !== '') {
                SiamaSwal.success('Berhasil!', successMsg.trim(), 3000);
            } else if (errorMsg && errorMsg.trim() !== '') {
                SiamaSwal.error('Terjadi Kesalahan', errorMsg.trim());
            } else if (errorsMsg && errorsMsg.trim() !== '') {
                SiamaSwal.error('Validasi Gagal', errorsMsg.trim());
            } else if (warningMsg && warningMsg.trim() !== '') {
                SiamaSwal.warning('Perhatian', warningMsg.trim());
            } else if (infoMsg && infoMsg.trim() !== '') {
                SiamaSwal.info('Pemberitahuan', infoMsg.trim());
            }
        }

        // 2. Cegah Pengiriman Ganda (Double-Submit Prevention) pada Seluruh Form
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!form || form.nodeName !== 'FORM') return;

            // Form konfirmasi via atribut data-swal-confirm
            const customConfirm = form.getAttribute('data-swal-confirm');
            if (customConfirm && !form.dataset.swalApproved) {
                e.preventDefault();
                const isDanger = form.getAttribute('data-swal-danger') === 'true' || 
                                 form.action.includes('delete') || 
                                 form.action.includes('cleanse') ||
                                 form.action.includes('reset');
                const title = form.getAttribute('data-swal-title') || 'Apakah Anda yakin?';
                const btnText = form.getAttribute('data-swal-btn') || (isDanger ? 'Ya, Lanjutkan' : 'Ya, Simpan');

                SiamaSwal.confirm({
                    title: title,
                    html: customConfirm,
                    isDanger: isDanger,
                    confirmButtonText: btnText
                }).then((res) => {
                    if (res.isConfirmed) {
                        form.dataset.swalApproved = 'true';
                        setSwalButtonLoading('Memproses...');
                        form.submit();
                    }
                });
                return;
            }

            // Anti double click untuk form normal
            if (form.dataset.submitting === 'true') {
                e.preventDefault();
                return;
            }

            if (!form.checkValidity || form.checkValidity()) {
                form.dataset.submitting = 'true';
                const submitBtns = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                submitBtns.forEach(btn => {
                    setTimeout(() => { btn.disabled = true; }, 20);
                });
            }
        });

        // 3. Global Event Delegation untuk Tombol Konfirmasi Hapus & Aksi Penting
        document.addEventListener('click', function (e) {
            // Hindari trigger ganda jika dialog SweetAlert sedang aktif / loading
            if (typeof Swal !== 'undefined' && (Swal.isVisible() || Swal.isLoading())) {
                return;
            }

            // A. Tombol Hapus Pengguna (.btn-delete-user)
            const btnDeleteUser = e.target.closest('.btn-delete-user');
            if (btnDeleteUser) {
                e.preventDefault();
                e.stopPropagation();
                const id = btnDeleteUser.getAttribute('data-id');
                const nama = btnDeleteUser.getAttribute('data-nama') || 'Pengguna';
                const email = btnDeleteUser.getAttribute('data-email') || '';
                const opd = btnDeleteUser.getAttribute('data-opd') || '';
                const baseDelete = (typeof baseDeleteUrl !== 'undefined') ? baseDeleteUrl : (window.location.origin + '/users/delete');

                SiamaSwal.confirmDelete({
                    title: 'Hapus Akun Pengguna?',
                    text: 'Apakah Anda yakin ingin menghapus akun pengguna ini? Tindakan ini tidak dapat dibatalkan.',
                    itemName: nama,
                    itemSub: email + (opd ? ' • ' + opd : '')
                }).then((result) => {
                    if (result.isConfirmed) {
                        setSwalButtonLoading('Menghapus...');
                        window.location.href = baseDelete.replace(/\/$/, '') + '/' + id;
                    }
                });
                return;
            }

            // B. Tombol Hapus OPD (.btn-delete-opd)
            const btnDeleteOpd = e.target.closest('.btn-delete-opd');
            if (btnDeleteOpd) {
                e.preventDefault();
                e.stopPropagation();
                const id = btnDeleteOpd.getAttribute('data-id');
                const nama = btnDeleteOpd.getAttribute('data-nama') || 'OPD';
                const kode = btnDeleteOpd.getAttribute('data-kode') || '';
                const baseDelete = (typeof baseDeleteUrl !== 'undefined') ? baseDeleteUrl : (window.location.origin + '/opd/delete');

                SiamaSwal.confirmDelete({
                    title: 'Hapus Data OPD?',
                    text: 'Apakah Anda yakin ingin menghapus data OPD ini? Data yang dihapus tidak dapat dikembalikan.',
                    itemName: nama,
                    itemSub: kode ? 'Kode: ' + kode : null
                }).then((result) => {
                    if (result.isConfirmed) {
                        setSwalButtonLoading('Menghapus...');
                        window.location.href = baseDelete.replace(/\/$/, '') + '/' + id;
                    }
                });
                return;
            }

            // C. Tombol Hapus Bidang (.btn-delete-bidang)
            const btnDeleteBidang = e.target.closest('.btn-delete-bidang');
            if (btnDeleteBidang) {
                e.preventDefault();
                e.stopPropagation();
                const id = btnDeleteBidang.getAttribute('data-id');
                const bidang = btnDeleteBidang.getAttribute('data-bidang') || 'Bidang';
                const opd = btnDeleteBidang.getAttribute('data-opd') || '';
                const baseDelete = (typeof baseDeleteUrl !== 'undefined') ? baseDeleteUrl : (window.location.origin + '/bidang/delete');

                SiamaSwal.confirmDelete({
                    title: 'Hapus Data Bidang?',
                    text: 'Apakah Anda yakin ingin menghapus data bidang ini? Data yang dihapus tidak dapat dikembalikan.',
                    itemName: bidang,
                    itemSub: opd ? 'OPD: ' + opd : null
                }).then((result) => {
                    if (result.isConfirmed) {
                        setSwalButtonLoading('Menghapus...');
                        window.location.href = baseDelete.replace(/\/$/, '') + '/' + id;
                    }
                });
                return;
            }

            // D. Tombol Hapus JRA (.jra-action-btn.btn-action-delete)
            const btnDeleteJra = e.target.closest('.jra-action-btn.btn-action-delete');
            if (btnDeleteJra) {
                e.preventDefault();
                e.stopPropagation();
                const id = btnDeleteJra.getAttribute('data-id');
                const kode = btnDeleteJra.getAttribute('data-kode') || 'JRA';
                const ket = btnDeleteJra.getAttribute('data-keterangan') || '';

                if (ket.toLowerCase().includes('musnah')) {
                    SiamaSwal.warning('Tidak Dapat Dihapus', 'Data JRA dengan status retensi Musnah tidak dapat dihapus.');
                    return;
                }

                const baseDelete = (typeof baseDeleteUrl !== 'undefined') ? baseDeleteUrl : (window.location.origin + '/jra/delete');

                SiamaSwal.confirmDelete({
                    title: 'Hapus Data JRA?',
                    text: 'Apakah Anda yakin ingin menghapus jadwal retensi arsip ini? Data yang dihapus tidak dapat dikembalikan.',
                    itemName: kode,
                    itemSub: ket ? 'Nasib Akhir: ' + ket : null
                }).then((result) => {
                    if (result.isConfirmed) {
                        setSwalButtonLoading('Menghapus...');
                        window.location.href = baseDelete.replace(/\/$/, '') + '/' + id;
                    }
                });
                return;
            }

            // E. Tombol Tautan dengan data-swal-confirm
            const customLinkConfirm = e.target.closest('a[data-swal-confirm], button[data-swal-confirm]');
            if (customLinkConfirm && !customLinkConfirm.dataset.swalApproved) {
                e.preventDefault();
                const msg = customLinkConfirm.getAttribute('data-swal-confirm');
                const title = customLinkConfirm.getAttribute('data-swal-title') || 'Apakah Anda yakin?';
                const isDanger = customLinkConfirm.getAttribute('data-swal-danger') === 'true';
                const href = customLinkConfirm.getAttribute('href');

                SiamaSwal.confirm({
                    title: title,
                    html: msg,
                    isDanger: isDanger,
                    confirmButtonText: isDanger ? 'Ya, Lanjutkan' : 'Ya'
                }).then((res) => {
                    if (res.isConfirmed) {
                        if (href && href !== '#' && !href.startsWith('javascript:')) {
                            setSwalButtonLoading('Memproses...');
                            window.location.href = href;
                        } else {
                            customLinkConfirm.dataset.swalApproved = 'true';
                            customLinkConfirm.click();
                        }
                    }
                });
                return;
            }
        });
    });

})(window, document);
