// Bootstrap JS
import 'bootstrap';

// Sidebar toggle for mobile
document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
            if (overlay) overlay.classList.toggle('show');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 5000);
    });

    // Confirm dialogs
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });

    // Image preview on file input
    document.querySelectorAll('.image-upload-input').forEach(function (input) {
        input.addEventListener('change', function () {
            const preview = document.getElementById(this.dataset.preview);
            if (preview && this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // Tactile Button Ripple Micro-interaction
    document.querySelectorAll('.btn').forEach(function (btn) {
        btn.addEventListener('pointerdown', function (e) {
            const rect = btn.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.className = 'btn-ripple';
            const diameter = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = `${diameter}px`;
            ripple.style.left = `${e.clientX - rect.left - diameter / 2}px`;
            ripple.style.top = `${e.clientY - rect.top - diameter / 2}px`;
            btn.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });

    // Cascade Stagger Animation for Metric and Stat Card Rows
    document.querySelectorAll('.row:not(.no-stagger)').forEach(function (row) {
        const children = row.children;
        if (children.length > 1 && children.length <= 6) {
            Array.from(children).forEach(function (col, idx) {
                if (!col.classList.contains('stagger-' + (idx + 1))) {
                    col.classList.add('stagger-' + Math.min(idx + 1, 5));
                }
            });
        }
    });

    // Smooth Auto-focus for Modals
    document.querySelectorAll('.modal').forEach(function (modal) {
        modal.addEventListener('shown.bs.modal', function () {
            const autoFocusInput = modal.querySelector('input:not([type="hidden"]), select, textarea');
            if (autoFocusInput) {
                autoFocusInput.focus();
            }
        });
    });
});

