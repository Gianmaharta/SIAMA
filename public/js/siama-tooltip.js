/**
 * Global Bootstrap 5 Tooltip Manager for SIAMA
 * - Menginisialisasi seluruh [data-bs-toggle="tooltip"] dengan container: 'body' agar tidak terpotong overflow tabel.
 * - Mendukung elemen dinamis (AJAX, DataTables, renderTable, pagination) via MutationObserver.
 * - Mencegah duplikasi instance dan membersihkan tooltip yatim saat DOM diperbarui.
 */
(function (window, document) {
    'use strict';

    function initSingleTooltip(el) {
        if (!el || typeof bootstrap === 'undefined' || !bootstrap.Tooltip) return;
        if (bootstrap.Tooltip.getInstance(el)) return;

        // Pastikan placement default adalah 'top'
        if (!el.hasAttribute('data-bs-placement')) {
            el.setAttribute('data-bs-placement', 'top');
        }

        // Jika masih memakai title tanpa data-bs-title, pindahkan ke data-bs-title agar konsisten
        if (!el.hasAttribute('data-bs-title') && el.hasAttribute('title') && el.getAttribute('title').trim() !== '') {
            el.setAttribute('data-bs-title', el.getAttribute('title'));
            if (!el.hasAttribute('aria-label')) {
                el.setAttribute('aria-label', el.getAttribute('title'));
            }
            el.removeAttribute('title');
        }

        new bootstrap.Tooltip(el, {
            container: 'body',
            boundary: 'window',
            placement: el.getAttribute('data-bs-placement') || 'top',
            trigger: 'hover focus'
        });
    }

    function initAllTooltips(root) {
        if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) return;
        const scope = root && root.querySelectorAll ? root : document;

        // Bersihkan tooltip DOM yang tertinggal jika trigger-nya sudah terhapus
        document.querySelectorAll('.tooltip.bs-tooltip-auto, .tooltip.bs-tooltip-top, .tooltip.bs-tooltip-bottom').forEach(function (tipEl) {
            const id = tipEl.getAttribute('id');
            if (id && !document.querySelector('[aria-describedby="' + id + '"]')) {
                tipEl.remove();
            }
        });

        scope.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(initSingleTooltip);
    }

    function hideAllTooltips() {
        if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) return;
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            const inst = bootstrap.Tooltip.getInstance(el);
            if (inst) inst.hide();
        });
    }

    let rafPending = false;
    function scheduleScan() {
        if (rafPending) return;
        rafPending = true;
        window.requestAnimationFrame(function () {
            rafPending = false;
            initAllTooltips(document);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initAllTooltips(document);

        // Sembunyikan tooltip saat tombol diklik agar tidak tertinggal saat modal/halaman dibuka
        document.addEventListener('click', function (e) {
            const trigger = e.target.closest('[data-bs-toggle="tooltip"]');
            if (trigger && typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                const inst = bootstrap.Tooltip.getInstance(trigger);
                if (inst) inst.hide();
            }
        }, true);

        // Pantau perubahan DOM dinamis (AJAX, innerHTML, pagination, DataTables)
        if (typeof MutationObserver !== 'undefined' && document.body) {
            const observer = new MutationObserver(function (mutations) {
                for (let i = 0; i < mutations.length; i++) {
                    if (mutations[i].addedNodes && mutations[i].addedNodes.length > 0) {
                        scheduleScan();
                        break;
                    }
                }
            });
            observer.observe(document.body, { childList: true, subtree: true });
        }
    });

    window.SiamaTooltip = {
        init: initAllTooltips,
        hideAll: hideAllTooltips
    };
})(window, document);
