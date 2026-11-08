/**
 * ==============================================================
 * SIAMA - Dynamic Notification Handler
 * Handles Navbar Dropdown, Unread Badge, Polling, and AJAX Reads
 * ==============================================================
 */

(function () {
    'use strict';

    const baseUrl = window.location.origin;
    let pollInterval = null;

    // Elements
    const notifDropdownBtn = document.getElementById('notifDropdownBtn');
    const navNotifBadge = document.getElementById('navNotifBadge');
    const notifDropdownList = document.getElementById('notifDropdownList');
    const dropdownUnreadBadge = document.getElementById('dropdownUnreadBadge');
    const btnMarkAllReadDropdown = document.getElementById('btnMarkAllReadDropdown');

    // Update the visual badge
    function updateBadge(count) {
        if (!navNotifBadge) return;

        const unreadCount = parseInt(count, 10) || 0;
        if (unreadCount > 0) {
            navNotifBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
            navNotifBadge.classList.remove('d-none');
            if (dropdownUnreadBadge) {
                dropdownUnreadBadge.textContent = `${unreadCount} Baru`;
                dropdownUnreadBadge.classList.remove('d-none');
            }
        } else {
            navNotifBadge.classList.add('d-none');
            if (dropdownUnreadBadge) {
                dropdownUnreadBadge.textContent = '0 Baru';
            }
        }
    }

    // Fetch unread count from server
    function fetchUnreadCount() {
        fetch(`${baseUrl}/notification/unread-count`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            if (data && data.status === 'success') {
                updateBadge(data.unread);
            }
        })
        .catch(() => {
            // Silently handle error to prevent console spam
        });
    }

    // Fetch and render dropdown items
    function loadDropdownNotifications() {
        if (!notifDropdownList) return;

        notifDropdownList.innerHTML = `
            <div class="py-4 text-center text-muted">
                <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                <div class="small">Memuat notifikasi...</div>
            </div>
        `;

        fetch(`${baseUrl}/notification/dropdown`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            if (data && data.status === 'success') {
                updateBadge(data.unread);
                renderDropdown(data.notifications);
            } else {
                renderDropdown([]);
            }
        })
        .catch(() => {
            renderDropdown([]);
        });
    }

    // Render HTML for dropdown
    function renderDropdown(notifications) {
        if (!notifDropdownList) return;

        if (!notifications || notifications.length === 0) {
            notifDropdownList.innerHTML = `
                <div class="notif-dropdown-empty">
                    <div class="notif-empty-icon">
                        <i class="bi bi-bell-slash"></i>
                    </div>
                    <h6 class="fw-semibold text-dark mb-1" style="font-size: 13.5px;">Tidak Ada Notifikasi</h6>
                    <p class="text-secondary small mb-0" style="font-size: 11.5px;">Semua tugas dan penugasan telah Anda tindak lanjuti.</p>
                </div>
            `;
            return;
        }

        let html = '';
        notifications.forEach(item => {
            const isUnread = item.is_read === 0;
            const unreadClass = isUnread ? 'unread' : '';
            const unreadDot = isUnread ? '<span class="notif-unread-dot" title="Belum dibaca"></span>' : '';

            html += `
                <a href="${item.redirect_url}" class="notif-dropdown-item ${unreadClass}">
                    <div class="notif-item-icon" style="background-color: ${item.icon_bg}; color: ${item.icon_color};">
                        <i class="bi ${item.icon}"></i>
                    </div>
                    <div class="notif-item-content">
                        <div class="notif-item-title">
                            <span class="text-truncate">${item.title}</span>
                            ${unreadDot}
                        </div>
                        <div class="notif-item-desc">${item.message}</div>
                        <div class="notif-item-footer">
                            <span class="badge bg-light text-secondary border px-1.5 py-0.5" style="font-size: 9.5px;">${item.type_label}</span>
                            <span><i class="bi bi-clock me-1"></i>${item.time_ago}</span>
                        </div>
                    </div>
                </a>
            `;
        });

        notifDropdownList.innerHTML = html;
    }

    // Mark all as read via AJAX
    function markAllAsRead() {
        const csrfInput = document.querySelector('input[name="csrf_test_name"]') ||
                          document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfInput ? (csrfInput.value || csrfInput.content) : '';

        const formData = new FormData();
        if (csrfToken) {
            formData.append('csrf_test_name', csrfToken);
        }

        fetch(`${baseUrl}/notification/mark-all-read`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data && data.status === 'success') {
                updateBadge(0);

                // Update dropdown list styling
                if (notifDropdownList) {
                    const unreadItems = notifDropdownList.querySelectorAll('.notif-dropdown-item.unread');
                    unreadItems.forEach(el => el.classList.remove('unread'));
                    const dots = notifDropdownList.querySelectorAll('.notif-unread-dot');
                    dots.forEach(el => el.remove());
                }

                // If on notification index page, update the cards too
                const indexCards = document.querySelectorAll('.notif-card.unread');
                indexCards.forEach(card => {
                    card.classList.remove('unread');
                    const badge = card.querySelector('.badge-status-unread');
                    if (badge) {
                        badge.className = 'badge bg-light text-secondary border';
                        badge.textContent = 'Sudah Dibaca';
                    }
                    const btnRead = card.querySelector('.btn-mark-single-read');
                    if (btnRead) btnRead.remove();
                });
            }
        })
        .catch(err => console.error(err));
    }

    // Mark single notification as read via AJAX on index page
    function initIndexPageListeners() {
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-mark-single-read');
            if (!btn) return;

            e.preventDefault();
            const notifId = btn.getAttribute('data-id');
            if (!notifId) return;

            const card = document.getElementById('notif-card-' + notifId);

            fetch(`${baseUrl}/notification/mark-read/${notifId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.status === 'success') {
                    updateBadge(data.unread);
                    if (card) {
                        card.classList.remove('unread');
                        const statusBadge = card.querySelector('.badge-status-unread');
                        if (statusBadge) {
                            statusBadge.className = 'badge bg-light text-secondary border';
                            statusBadge.textContent = 'Sudah Dibaca';
                        }
                        btn.remove();
                    }
                }
            })
            .catch(err => console.error(err));
        });

        // Mark all read on index page
        const btnMarkAllIndex = document.getElementById('btnMarkAllReadIndex');
        if (btnMarkAllIndex) {
            btnMarkAllIndex.addEventListener('click', function (e) {
                e.preventDefault();
                markAllAsRead();
            });
        }
    }

    // Initialization on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', function () {
        // Initial badge fetch
        fetchUnreadCount();

        // Listen for dropdown open event to load fresh notifications
        if (notifDropdownBtn) {
            notifDropdownBtn.addEventListener('show.bs.dropdown', function () {
                loadDropdownNotifications();
            });
        }

        // Mark all read click in dropdown
        if (btnMarkAllReadDropdown) {
            btnMarkAllReadDropdown.addEventListener('click', function (e) {
                e.preventDefault();
                markAllAsRead();
            });
        }

        // Initialize listeners for the /notification index page
        initIndexPageListeners();

        // Start Periodic Polling every 30 seconds
        if (!pollInterval) {
            pollInterval = setInterval(fetchUnreadCount, 30000);
        }
    });
})();

