/**
 * Global Bootstrap 5 Custom Select Engine for SIAMA
 * - Menggantikan popup native <select> agar lebar menu pilihan SELALU sama persis dengan input pemicunya.
 * - Mempertahankan <select> asli (name, id, value, required, disabled, events, FormData, AJAX options).
 * - Mendukung sinkronisasi programmatic (select.value = ...) dan DOM dinamis via MutationObserver.
 */
(function (window, document) {
    'use strict';

    let activeInstance = null;

    // Hook HTMLSelectElement.prototype.value agar perubahan programmatic (misal di modal Edit) langsung memperbarui UI custom select
    const nativeValueDescriptor = Object.getOwnPropertyDescriptor(HTMLSelectElement.prototype, 'value');
    if (nativeValueDescriptor && nativeValueDescriptor.set && !HTMLSelectElement.prototype._siamaValueHooked) {
        Object.defineProperty(HTMLSelectElement.prototype, 'value', {
            configurable: true,
            enumerable: true,
            get: function () {
                return nativeValueDescriptor.get.call(this);
            },
            set: function (val) {
                nativeValueDescriptor.set.call(this, val);
                if (this._siamaSelectInstance) {
                    this._siamaSelectInstance.syncDisplay();
                }
            }
        });
        HTMLSelectElement.prototype._siamaValueHooked = true;
    }

    function createCustomSelect(selectEl) {
        if (!selectEl || selectEl.dataset.siamaSelectInit === '1') return;
        if (selectEl.multiple || selectEl.dataset.nativeSelect === 'true') return;

        selectEl.dataset.siamaSelectInit = '1';

        const isCompact = selectEl.classList.contains('form-select-sm') ||
            selectEl.classList.contains('dashboard-select') ||
            selectEl.classList.contains('arsip-filter-select') ||
            selectEl.classList.contains('spt-filter-select') ||
            selectEl.classList.contains('penilaian-filter-select') ||
            selectEl.classList.contains('opd-select-control') ||
            selectEl.classList.contains('user-select-control') ||
            selectEl.classList.contains('acc-select') ||
            selectEl.classList.contains('act-select') ||
            selectEl.classList.contains('jra-filter-select');

        const isPerPage = selectEl.classList.contains('arsip-select-per-page') ||
            selectEl.classList.contains('spt-select-per-page') ||
            selectEl.classList.contains('penilaian-select-per-page') ||
            selectEl.classList.contains('opd-select-per-page') ||
            selectEl.classList.contains('user-select-per-page');

        const hasLeftIcon = selectEl.classList.contains('has-left-icon');

        // Wrapper
        const wrap = document.createElement('div');
        wrap.className = 'siama-cselect-wrap' + (isCompact ? ' is-compact' : '') + (isPerPage ? ' is-per-page' : '');
        if (selectEl.style.width) {
            wrap.style.width = selectEl.style.width;
        }
        if (selectEl.style.minWidth) {
            wrap.style.minWidth = selectEl.style.minWidth;
        }

        // Insert wrapper around select
        selectEl.parentNode.insertBefore(wrap, selectEl);
        wrap.appendChild(selectEl);

        // Visually hide native select while keeping it focusable/validatable for HTML5 required
        selectEl.classList.add('siama-native-select-hidden');
        selectEl.setAttribute('tabindex', '-1');
        selectEl.setAttribute('aria-hidden', 'true');

        // Trigger button
        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'siama-cselect-trigger' +
            (isCompact ? ' is-compact' : '') +
            (hasLeftIcon ? ' has-left-icon' : '');
        trigger.setAttribute('aria-haspopup', 'listbox');
        trigger.setAttribute('aria-expanded', 'false');

        const labelSpan = document.createElement('span');
        labelSpan.className = 'siama-cselect-label';

        const chevronSpan = document.createElement('span');
        chevronSpan.className = 'siama-cselect-chevron';
        chevronSpan.innerHTML = '<i class="bi bi-chevron-down"></i>';

        trigger.appendChild(labelSpan);
        trigger.appendChild(chevronSpan);
        wrap.appendChild(trigger);

        // Floating menu (appended to body when open so never clipped and width === trigger.width)
        const menu = document.createElement('div');
        menu.className = 'siama-cselect-menu';
        menu.setAttribute('role', 'listbox');

        const searchWrap = document.createElement('div');
        searchWrap.className = 'siama-cselect-search-wrap d-none';
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.className = 'form-control form-control-sm siama-cselect-search';
        searchInput.placeholder = 'Cari pilihan...';
        searchInput.setAttribute('autocomplete', 'off');
        searchWrap.appendChild(searchInput);

        const listWrap = document.createElement('div');
        listWrap.className = 'siama-cselect-list';

        menu.appendChild(searchWrap);
        menu.appendChild(listWrap);

        let isOpen = false;

        function syncDisplay() {
            const selectedOpt = selectEl.options[selectEl.selectedIndex];
            const text = selectedOpt ? selectedOpt.textContent.trim() : '';
            const val = selectedOpt ? selectedOpt.value : '';

            labelSpan.textContent = text || 'Pilih...';
            if (!val) {
                trigger.classList.add('is-placeholder');
            } else {
                trigger.classList.remove('is-placeholder');
            }

            trigger.disabled = !!selectEl.disabled;
            trigger.classList.toggle('is-disabled', !!selectEl.disabled);

            if (isOpen) {
                renderOptions(searchInput.value);
            }
        }

        function renderOptions(filterText = '') {
            listWrap.innerHTML = '';
            const options = Array.from(selectEl.options);
            const showSearch = options.length > 8;
            searchWrap.classList.toggle('d-none', !showSearch);

            const q = (filterText || '').trim().toLowerCase();
            let visibleCount = 0;

            options.forEach(function (opt) {
                const optText = opt.textContent.trim();
                if (q && !optText.toLowerCase().includes(q)) return;

                visibleCount++;
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'siama-cselect-option';
                item.setAttribute('role', 'option');
                item.dataset.value = opt.value;
                item.textContent = optText;

                if (opt.value === selectEl.value) {
                    item.classList.add('is-selected');
                    item.setAttribute('aria-selected', 'true');
                }
                if (opt.disabled) {
                    item.disabled = true;
                    item.classList.add('is-disabled');
                }

                item.addEventListener('click', function (e) {
                    e.stopPropagation();
                    if (opt.disabled) return;
                    const changed = selectEl.value !== opt.value;
                    nativeValueDescriptor.set.call(selectEl, opt.value);
                    syncDisplay();
                    closeMenu();
                    trigger.focus();

                    if (changed) {
                        selectEl.dispatchEvent(new Event('input', { bubbles: true }));
                        selectEl.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });

                listWrap.appendChild(item);
            });

            if (visibleCount === 0) {
                const empty = document.createElement('div');
                empty.className = 'siama-cselect-empty';
                empty.textContent = 'Tidak ada pilihan ditemukan';
                listWrap.appendChild(empty);
            }
        }

        function positionMenu() {
            const rect = trigger.getBoundingClientRect();
            const menuWidth = Math.round(rect.width);
            menu.style.width = menuWidth + 'px';
            menu.style.minWidth = menuWidth + 'px';
            menu.style.maxWidth = menuWidth + 'px';
            menu.style.left = Math.round(rect.left) + 'px';

            // Measure menu height
            const viewportH = window.innerHeight;
            const spaceBelow = viewportH - rect.bottom - 10;
            const spaceAbove = rect.top - 10;
            const estimatedHeight = Math.min(menu.scrollHeight || 260, 290);

            if (spaceBelow < estimatedHeight && spaceAbove > spaceBelow) {
                menu.style.top = 'auto';
                menu.style.bottom = Math.round(viewportH - rect.top + 4) + 'px';
                menu.classList.add('is-dropup');
            } else {
                menu.style.bottom = 'auto';
                menu.style.top = Math.round(rect.bottom + 4) + 'px';
                menu.classList.remove('is-dropup');
            }
        }

        function openMenu() {
            if (selectEl.disabled) return;
            if (activeInstance && activeInstance !== instance) {
                activeInstance.closeMenu();
            }

            isOpen = true;
            activeInstance = instance;
            searchInput.value = '';
            renderOptions('');

            document.body.appendChild(menu);
            positionMenu();

            menu.classList.add('show');
            trigger.classList.add('is-open');
            trigger.setAttribute('aria-expanded', 'true');

            // Scroll active option into view
            const selectedBtn = listWrap.querySelector('.siama-cselect-option.is-selected');
            if (selectedBtn) {
                selectedBtn.scrollIntoView({ block: 'nearest' });
            }

            if (!searchWrap.classList.contains('d-none')) {
                setTimeout(function () { searchInput.focus(); }, 10);
            }
        }

        function closeMenu() {
            if (!isOpen) return;
            isOpen = false;
            if (activeInstance === instance) {
                activeInstance = null;
            }
            menu.classList.remove('show');
            trigger.classList.remove('is-open');
            trigger.setAttribute('aria-expanded', 'false');
            if (menu.parentNode) {
                menu.parentNode.removeChild(menu);
            }
        }

        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            if (isOpen) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        searchInput.addEventListener('input', function () {
            renderOptions(searchInput.value);
            positionMenu();
        });

        searchInput.addEventListener('click', function (e) {
            e.stopPropagation();
        });

        // Sinkronkan ketika event change/reset dipicu langsung di select
        selectEl.addEventListener('change', syncDisplay);
        if (selectEl.form) {
            selectEl.form.addEventListener('reset', function () {
                setTimeout(syncDisplay, 10);
            });
        }

        // Pantau perubahan option dinamis (AJAX / cascade) dan atribut disabled
        const selectObserver = new MutationObserver(function () {
            syncDisplay();
        });
        selectObserver.observe(selectEl, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['disabled']
        });

        // HTML5 invalid highlight on custom trigger
        selectEl.addEventListener('invalid', function () {
            trigger.classList.add('is-invalid');
        });
        selectEl.addEventListener('change', function () {
            trigger.classList.remove('is-invalid');
        });

        const instance = {
            syncDisplay: syncDisplay,
            closeMenu: closeMenu,
            positionMenu: positionMenu,
            isOpen: function () { return isOpen; }
        };

        selectEl._siamaSelectInstance = instance;
        syncDisplay();
    }

    function initAllSelects(root) {
        const scope = root && root.querySelectorAll ? root : document;
        scope.querySelectorAll('select').forEach(createCustomSelect);
    }

    let rafPending = false;
    function scheduleSelectScan() {
        if (rafPending) return;
        rafPending = true;
        window.requestAnimationFrame(function () {
            rafPending = false;
            initAllSelects(document);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initAllSelects(document);

        // Close on outside click
        document.addEventListener('click', function (e) {
            if (activeInstance && activeInstance.isOpen()) {
                if (!e.target.closest('.siama-cselect-wrap') && !e.target.closest('.siama-cselect-menu')) {
                    activeInstance.closeMenu();
                }
            }
        });

        // Close on Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && activeInstance && activeInstance.isOpen()) {
                activeInstance.closeMenu();
            }
        });

        // Reposition / close on scroll or resize
        window.addEventListener('resize', function () {
            if (activeInstance && activeInstance.isOpen()) {
                activeInstance.positionMenu();
            }
        }, { passive: true });

        document.addEventListener('scroll', function (e) {
            if (activeInstance && activeInstance.isOpen()) {
                if (e.target && e.target.closest && e.target.closest('.siama-cselect-menu')) {
                    return;
                }
                activeInstance.closeMenu();
            }
        }, true);

        // Observe dynamic DOM additions (modals, AJAX tables, etc.)
        if (typeof MutationObserver !== 'undefined' && document.body) {
            const bodyObserver = new MutationObserver(function (mutations) {
                for (let i = 0; i < mutations.length; i++) {
                    if (mutations[i].addedNodes && mutations[i].addedNodes.length > 0) {
                        scheduleSelectScan();
                        break;
                    }
                }
            });
            bodyObserver.observe(document.body, { childList: true, subtree: true });
        }
    });

    window.SiamaSelect = {
        init: initAllSelects,
        closeAll: function () {
            if (activeInstance) activeInstance.closeMenu();
        }
    };
})(window, document);
