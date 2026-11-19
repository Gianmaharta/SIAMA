/**
 * ==============================================================
 * SIAMA - Dynamic Notification Handler
 * Real-time Relative Timestamps (WITA / UTC+8), Polling,
 * Navbar Dropdown, Unread Badges, and Dynamic Feed Updates
 * ==============================================================
 */

(function () {
    'use strict';

    const baseUrl = window.location.origin;
    let pollInterval = null;
    let timeInterval = null;
    let serverTimeOffset = 0; // ms difference: serverTime - clientLocalTime

    // DOM Elements
    const notifDropdownBtn = document.getElementById('notifDropdownBtn');
    const navNotifBadge = document.getElementById('navNotifBadge');
    const notifDropdownList = document.getElementById('notifDropdownList');
    const dropdownUnreadBadge = document.getElementById('dropdownUnreadBadge');
    const btnMarkAllReadDropdown = document.getElementById('btnMarkAllReadDropdown');

    /**
     * Escape special HTML characters to prevent XSS
     */
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
     * Synchronize client clock with server clock
     */
    function syncServerTime(serverTimeStr) {
        if (!serverTimeStr) return;
        const serverDate = new Date(serverTimeStr);
        if (!isNaN(serverDate.getTime())) {
            serverTimeOffset = serverDate.getTime() - Date.now();
        }
    }

    /**
     * Get current timestamp in milliseconds, calibrated with server offset
     */
    function getNowMs() {
        return Date.now() + serverTimeOffset;
    }

    /**
     * Parse DB timestamp (YYYY-MM-DD HH:mm:ss) as Asia/Makassar (WITA / UTC+8)
     */
    function parseWitaTimestamp(timeStr) {
        if (!timeStr || typeof timeStr !== 'string') return null;
        const clean = timeStr.trim();
        if (clean.includes('T') || clean.includes('+') || clean.endsWith('Z')) {
            const d = new Date(clean);
            return isNaN(d.getTime()) ? null : d;
        }
        // MySQL format "YYYY-MM-DD HH:mm:ss" -> append "+08:00" for WITA
        const iso = clean.replace(' ', 'T') + '+08:00';
        const d = new Date(iso);
        return isNaN(d.getTime()) ? null : d;
    }

    /**
     * Deconstruct a Date object into WITA calendar components
     */
    function getWitaComponents(dateObj) {
        const witaMs = dateObj.getTime() + (8 * 60 * 60 * 1000);
        const wDate = new Date(witaMs);
        return {
            year: wDate.getUTCFullYear(),
            month: wDate.getUTCMonth(),
            date: wDate.getUTCDate(),
            hours: wDate.getUTCHours(),
            minutes: wDate.getUTCMinutes()
        };
    }

    /**
     * Format relative time according to specifications:
     * - < 60 detik: Baru saja
     * - < 60 menit: 1 menit yang lalu, 5 menit yang lalu, ...
     * - < 24 jam: 1 jam yang lalu, 2 jam yang lalu, ...
     * - Kemarin (selisih 1 hari kalender): Kemarin
     * - Notifikasi lama (> 1 hari): 17 Nov 2026, 16:05 WITA
     */
    function formatRelativeTime(dbTimeStr) {
        const notifDate = parseWitaTimestamp(dbTimeStr);
        if (!notifDate) return '-';

        const nowMs = getNowMs();
        const diffMs = Math.max(0, nowMs - notifDate.getTime());
        const diffSec = Math.floor(diffMs / 1000);

        if (diffSec < 60) {
            return 'Baru saja';
        }

        const diffMin = Math.floor(diffSec / 60);
        if (diffMin < 60) {
            return `${diffMin} menit yang lalu`;
        }

        const diffHour = Math.floor(diffMin / 60);
        if (diffHour < 24) {
            return `${diffHour} jam yang lalu`;
        }

        // Calendar day comparison in WITA
        const notifWita = getWitaComponents(notifDate);
        const notifDayEpoch = Math.floor((notifDate.getTime() + 8 * 3600 * 1000) / (24 * 3600 * 1000));
        const nowDayEpoch = Math.floor((nowMs + 8 * 3600 * 1000) / (24 * 3600 * 1000));
        const calDayDiff = nowDayEpoch - notifDayEpoch;

        if (calDayDiff === 1) {
            return 'Kemarin';
        }

        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const pad = (n) => String(n).padStart(2, '0');
        const monthName = months[notifWita.month] || 'Nov';
        return `${notifWita.date} ${monthName} ${notifWita.year}, ${pad(notifWita.hours)}:${pad(notifWita.minutes)} WITA`;
    }

    /**
     * Update all relative time labels across the entire page
     */
    function updateAllRelativeTimes() {
        const timeEls = document.querySelectorAll('.notif-time-ago[data-timestamp]');
        timeEls.forEach(el => {
            const ts = el.getAttribute('data-timestamp');
            if (ts) {
                const newText = formatRelativeTime(ts);
                if (el.textContent.trim() !== newText) {
                    el.textContent = newText;
                }
            }
        });
    }

    /**
     * Update the visual badges for unread notifications
     */
    function updateBadge(count) {
        const unreadCount = parseInt(count, 10) || 0;

        if (navNotifBadge) {
            if (unreadCount > 0) {
                navNotifBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                navNotifBadge.classList.remove('d-none');
            } else {
                navNotifBadge.classList.add('d-none');
            }
        }

        if (dropdownUnreadBadge) {
            dropdownUnreadBadge.textContent = unreadCount > 0 ? `${unreadCount} Baru` : '0 Baru';
            if (unreadCount > 0) {
                dropdownUnreadBadge.classList.remove('d-none');
            }
        }
    }

    /**
     * Fetch unread count and synchronize time
     */
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
                syncServerTime(data.server_time);
                updateBadge(data.unread);
                updateAllRelativeTimes();
            }
        })
        .catch(() => {
            // Silently handle error
        });
    }

    /**
     * Fetch and render dropdown items
     */
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
                syncServerTime(data.server_time);
                updateBadge(data.unread);
                renderDropdown(data.notifications);
                updateAllRelativeTimes();
            } else {
                renderDropdown([]);
            }
        })
        .catch(() => {
            renderDropdown([]);
        });
    }

    /**
     * Render HTML for dropdown
     */
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
            const relTime = formatRelativeTime(item.created_at);

            html += `
                <a href="${item.redirect_url}" class="notif-dropdown-item ${unreadClass}">
                    <div class="notif-item-icon" style="background-color: ${item.icon_bg}; color: ${item.icon_color};">
                        <i class="bi ${item.icon}"></i>
                    </div>
                    <div class="notif-item-content">
                        <div class="notif-item-title">
                            <span class="text-truncate">${escapeHtml(item.title)}</span>
                            ${unreadDot}
                        </div>
                        <div class="notif-item-desc">${escapeHtml(item.message)}</div>
                        ${item.sender ? `<div class="notif-item-sender text-muted small" style="font-size: 11px; margin-top: 2px;"><i class="bi bi-person me-1"></i>${escapeHtml(item.sender)}</div>` : ''}
                        <div class="notif-item-footer">
                            <span class="badge bg-light text-secondary border px-1.5 py-0.5" style="font-size: 9.5px;">${escapeHtml(item.type_label)}</span>
                            <span><i class="bi bi-clock me-1"></i><span class="notif-time-ago" data-timestamp="${escapeHtml(item.created_at)}">${relTime}</span></span>
                        </div>
                    </div>
                </a>
            `;
        });

        notifDropdownList.innerHTML = html;
    }

    /**
     * Render single notification card HTML for the notification page
     */
    function renderCardHtml(notif) {
        const isUnread = notif.is_read === 0;
        const meta = notif.meta || {
            icon: 'bi-bell-fill',
            bg: '#F1F5F9',
            color: '#64748B',
            label: 'Notifikasi'
        };

        const statusBadge = isUnread
            ? `<span class="badge bg-primary text-white badge-status-unread" style="font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 6px;">Belum Dibaca</span>`
            : `<span class="badge bg-light text-secondary border" style="font-size: 11px; font-weight: 500; padding: 4px 10px; border-radius: 6px;">Sudah Dibaca</span>`;

        let senderRefHtml = '';
        if (notif.sender_nama || notif.reference_type) {
            senderRefHtml += `<div class="d-flex align-items-center gap-2 mb-3 flex-wrap" style="font-size: 12px;">`;
            if (notif.sender_nama) {
                senderRefHtml += `
                    <span class="d-inline-flex align-items-center gap-1.5 text-secondary">
                        <i class="bi bi-person-fill text-muted"></i>
                        <span class="text-dark fw-medium">${escapeHtml(notif.sender_nama)}</span>
                    </span>
                `;
                if (notif.sender_role) {
                    senderRefHtml += `
                        <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 10px; font-weight: 500;">
                            ${escapeHtml(notif.sender_role)}
                        </span>
                    `;
                }
            }
            if (notif.reference_type && notif.reference_id) {
                senderRefHtml += `
                    <span class="badge bg-light text-muted border px-2 py-0.5" style="font-size: 10px;">
                        Ref: ${escapeHtml(notif.reference_type.toUpperCase())} #${escapeHtml(String(notif.reference_id).substring(0, 8))}
                    </span>
                `;
            }
            senderRefHtml += `</div>`;
        }

        const markReadBtn = isUnread
            ? `<button type="button" class="btn-notif-action btn-notif-light btn-mark-single-read" data-id="${escapeHtml(notif.id_notification)}">
                   <i class="bi bi-check2"></i> Tandai Dibaca
               </button>`
            : '';

        let actionDocBtn = '';
        if (notif.target_url && notif.action_type !== 'retensi_arsip' && notif.action_type !== 'retensi_inaktif_arsip') {
            const redirectUrl = `${baseUrl}/notification/read-and-redirect/${escapeHtml(notif.id_notification)}`;
            const label = notif.action_label || 'Buka Dokumen';
            actionDocBtn = `
                <a href="${redirectUrl}" class="btn-notif-action btn-notif-primary">
                    <span>${escapeHtml(label)}</span>
                    <i class="bi bi-arrow-right" style="font-size: 11px;"></i>
                </a>
            `;
        }

        const relativeTime = formatRelativeTime(notif.created_at);
        const formattedDate = notif.formatted_date || (notif.created_at + ' WITA');

        return `
            <div class="notif-card ${isUnread ? 'unread' : ''}" id="notif-card-${escapeHtml(notif.id_notification)}">
                <div class="notif-card-icon" style="background-color: ${meta.bg}; color: ${meta.color};">
                    <i class="bi ${meta.icon}"></i>
                </div>
                <div class="notif-card-body">
                    <div class="notif-card-header">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h5 class="notif-card-title">${escapeHtml(notif.title)}</h5>
                            <span class="badge rounded-pill" style="background-color: ${meta.bg}; color: ${meta.color}; font-size: 11px; font-weight: 600; padding: 3px 9px;">
                                ${escapeHtml(meta.label)}
                            </span>
                        </div>
                        <div>${statusBadge}</div>
                    </div>
                    <p class="notif-card-desc" style="white-space: pre-line;">${escapeHtml(notif.message)}</p>
                    ${senderRefHtml}
                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2 pt-1 border-top" style="border-color: #F1F5F9 !important;">
                        <div class="notif-card-meta">
                            <span>
                                <i class="bi bi-calendar3 me-1"></i>
                                <span class="notif-full-date">${escapeHtml(formattedDate)}</span>
                            </span>
                            <span>
                                <i class="bi bi-clock-history me-1"></i>
                                <span class="notif-time-ago" data-timestamp="${escapeHtml(notif.created_at)}">${relativeTime}</span>
                            </span>
                        </div>
                        <div class="notif-card-actions">
                            ${markReadBtn}
                            ${actionDocBtn}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Poll feed for the /notification page to detect new notifications,
     * place them at the very top, update badges and tab counts in real-time.
     */
    function pollNotificationFeed() {
        const notifListContainer = document.getElementById('notifListContainer');
        if (!notifListContainer) return;

        const currentFilter = notifListContainer.getAttribute('data-current-filter') || 'all';

        fetch(`${baseUrl}/notification/feed?filter=${encodeURIComponent(currentFilter)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            if (!data || data.status !== 'success') return;

            syncServerTime(data.server_time);
            updateBadge(data.unread);

            // Update Tab Badge Counts
            const filterCountAll = document.getElementById('filterCountAll');
            const filterCountUnread = document.getElementById('filterCountUnread');
            const filterCountRead = document.getElementById('filterCountRead');

            if (filterCountAll && data.counts) filterCountAll.textContent = data.counts.all;
            if (filterCountUnread && data.counts) filterCountUnread.textContent = data.counts.unread;
            if (filterCountRead && data.counts) filterCountRead.textContent = data.counts.read;

            // Update "Tandai Semua Dibaca" button on index page
            const btnMarkAll = document.getElementById('btnMarkAllReadIndex');
            if (btnMarkAll) {
                if (data.unread > 0) {
                    btnMarkAll.style.display = 'inline-flex';
                } else {
                    btnMarkAll.style.display = 'none';
                }
            }

            const items = data.notifications || [];
            if (items.length === 0) {
                return;
            }

            // Remove empty state placeholder if new items arrived
            const emptyPlaceholder = notifListContainer.querySelector('.card.border-0.shadow-sm.rounded-4');
            if (emptyPlaceholder && items.length > 0) {
                emptyPlaceholder.remove();
            }

            // Loop items in reverse so prepending preserves DESC order (newest at very top)
            for (let i = items.length - 1; i >= 0; i--) {
                const item = items[i];
                const existingCard = document.getElementById(`notif-card-${item.id_notification}`);
                if (!existingCard) {
                    const temp = document.createElement('div');
                    temp.innerHTML = renderCardHtml(item).trim();
                    const newCardEl = temp.firstElementChild;
                    if (newCardEl) {
                        notifListContainer.insertBefore(newCardEl, notifListContainer.firstChild);
                    }
                } else {
                    // Update read state if it changed
                    const isUnread = item.is_read === 0;
                    if (!isUnread && existingCard.classList.contains('unread')) {
                        existingCard.classList.remove('unread');
                        const statusBadge = existingCard.querySelector('.badge-status-unread');
                        if (statusBadge) {
                            statusBadge.className = 'badge bg-light text-secondary border';
                            statusBadge.textContent = 'Sudah Dibaca';
                        }
                        const btnRead = existingCard.querySelector('.btn-mark-single-read');
                        if (btnRead) btnRead.remove();
                    }
                }
            }

            updateAllRelativeTimes();
        })
        .catch(() => {
            // Silently handle error
        });
    }

    /**
     * Mark all notifications as read via AJAX
     */
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

                // Update tab counters
                const filterCountUnread = document.getElementById('filterCountUnread');
                const filterCountRead = document.getElementById('filterCountRead');
                const filterCountAll = document.getElementById('filterCountAll');
                if (filterCountUnread && filterCountRead && filterCountAll) {
                    filterCountUnread.textContent = '0';
                    filterCountRead.textContent = filterCountAll.textContent;
                }

                const btnMarkAll = document.getElementById('btnMarkAllReadIndex');
                if (btnMarkAll) btnMarkAll.style.display = 'none';
            }
        })
        .catch(err => console.error(err));
    }

    /**
     * Mark single notification as read via AJAX on index page
     */
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

                    // Decrement unread tab count and increment read tab count
                    const filterCountUnread = document.getElementById('filterCountUnread');
                    const filterCountRead = document.getElementById('filterCountRead');
                    if (filterCountUnread) {
                        const cur = parseInt(filterCountUnread.textContent, 10) || 0;
                        filterCountUnread.textContent = Math.max(0, cur - 1);
                    }
                    if (filterCountRead) {
                        const cur = parseInt(filterCountRead.textContent, 10) || 0;
                        filterCountRead.textContent = cur + 1;
                    }

                    // Hide mark all button if unread reached 0
                    if (data.unread === 0) {
                        const btnMarkAll = document.getElementById('btnMarkAllReadIndex');
                        if (btnMarkAll) btnMarkAll.style.display = 'none';
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
        // Initial badge fetch and server time sync
        fetchUnreadCount();

        // Immediately format all relative times rendered on the page
        updateAllRelativeTimes();

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

        // Periodic Feed / Unread Count Polling every 30 seconds
        if (!pollInterval) {
            pollInterval = setInterval(function () {
                const notifListContainer = document.getElementById('notifListContainer');
                if (notifListContainer) {
                    pollNotificationFeed();
                } else {
                    fetchUnreadCount();
                }
            }, 30000);
        }

        // Relative time updater timer: runs every 30 seconds to adjust "Baru saja", "1 menit yang lalu", etc.
        if (!timeInterval) {
            timeInterval = setInterval(updateAllRelativeTimes, 30000);
        }
    });
})();
