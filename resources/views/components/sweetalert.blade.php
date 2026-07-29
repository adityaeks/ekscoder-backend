<!-- SweetAlert2 Local Component & Theme Customization -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Custom SweetAlert2 Dark/Light Theme Integration */
    div.swal2-container {
        font-family: 'Inter', sans-serif !important;
        z-index: 999999 !important;
    }

    /* Standard Modal Popups (Delete confirmations, etc.) */
    div.swal2-popup:not(.swal2-toast) {
        background: var(--bg-surface, #111118) !important;
        border: 1px solid var(--border, rgba(255,255,255,0.12)) !important;
        border-radius: 18px !important;
        color: var(--text-primary, #ffffff) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6) !important;
        padding: 24px !important;
    }

    div.swal2-popup:not(.swal2-toast) div.swal2-title {
        color: var(--text-primary, #ffffff) !important;
        font-size: 18px !important;
        font-weight: 700 !important;
        margin-bottom: 8px !important;
    }

    div.swal2-popup:not(.swal2-toast) div.swal2-html-container {
        color: var(--text-secondary, #8b8ba0) !important;
        font-size: 13.5px !important;
    }

    div.swal2-actions {
        display: flex !important;
        gap: 10px !important;
        margin-top: 22px !important;
    }

    /* Compact & Perfect Toast Notification Styling */
    div.swal2-popup.swal2-toast {
        background: var(--bg-surface, #111118) !important;
        border: 1px solid var(--border, rgba(255,255,255,0.15)) !important;
        border-radius: 12px !important;
        color: var(--text-primary, #ffffff) !important;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.6) !important;
        padding: 12px 16px !important;
        max-width: 360px !important;
        margin-top: 12px !important;
        margin-right: 12px !important;
    }

    div.swal2-popup.swal2-toast .swal2-icon {
        margin: 0 10px 0 0 !important;
        transform: scale(0.8) !important;
        transform-origin: center center !important;
        grid-column: 1 !important;
        grid-row: 1 / span 2 !important;
    }

    div.swal2-popup.swal2-toast .swal2-title {
        font-size: 13.5px !important;
        font-weight: 700 !important;
        color: var(--text-primary, #ffffff) !important;
        margin: 0 0 2px 0 !important;
        padding: 0 !important;
        text-align: left !important;
        grid-column: 2 !important;
    }

    div.swal2-popup.swal2-toast .swal2-html-container {
        font-size: 12px !important;
        color: var(--text-secondary, #8b8ba0) !important;
        margin: 0 !important;
        padding: 0 !important;
        text-align: left !important;
        grid-column: 2 !important;
    }

    div.swal2-popup.swal2-toast .swal2-timer-progress-bar {
        background: var(--accent, #b8ff00) !important;
        height: 3px !important;
    }

    /* Custom Styled Buttons */
    .swal2-popup .swal2-styled {
        font-family: 'Inter', sans-serif !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        border-radius: 9px !important;
        padding: 9px 20px !important;
        margin: 0 !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }

    .swal2-popup .swal2-confirm {
        background-color: var(--rose, #ff4757) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        border: 1px solid rgba(255, 71, 87, 0.4) !important;
        box-shadow: 0 4px 14px rgba(255, 71, 87, 0.3) !important;
    }
    .swal2-popup .swal2-confirm:hover {
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
    }

    .swal2-popup .swal2-cancel {
        background-color: var(--bg-elevated, #222232) !important;
        color: var(--text-secondary, #94a3b8) !important;
        border: 1px solid var(--border, rgba(255,255,255,0.12)) !important;
    }
    .swal2-popup .swal2-cancel:hover {
        background-color: rgba(255,255,255,0.08) !important;
        color: var(--text-primary, #ffffff) !important;
    }

    /* Icon Colors */
    div.swal2-icon.swal2-warning {
        border-color: var(--amber, #f59e0b) !important;
        color: var(--amber, #f59e0b) !important;
    }
    div.swal2-icon.swal2-success {
        border-color: var(--green, #10b981) !important;
        color: var(--green, #10b981) !important;
    }
    div.swal2-icon.swal2-error {
        border-color: var(--rose, #ff4757) !important;
        color: var(--rose, #ff4757) !important;
    }
</style>

<script>
    // Configured SweetAlert Instance
    const SwalCustom = Swal.mixin({
        buttonsStyling: true,
        allowOutsideClick: false,
        allowEscapeKey: false
    });

    /**
     * SweetAlert Confirmation Helper for Form Deletions or Actions
     */
    function confirmDelete(title, text, callbackOrForm) {
        SwalCustom.fire({
            title: title || 'Are you sure?',
            text: text || 'This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                if (typeof callbackOrForm === 'function') {
                    callbackOrForm();
                } else if (callbackOrForm instanceof HTMLFormElement) {
                    callbackOrForm.submit();
                }
            }
        });
    }

    /**
     * SweetAlert General Action Confirmation Helper
     */
    function confirmSwalAction(title, text, confirmText, callback) {
        SwalCustom.fire({
            title: title,
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: confirmText || 'Proceed',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed && typeof callback === 'function') {
                callback();
            }
        });
    }

    // Global Interceptor for Delete Forms with SweetAlert Confirmation
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form) return;

        const isDeleteForm = form.classList.contains('delete-form') || 
            form.querySelector('input[name="_method"][value="DELETE"]') !== null ||
            form.hasAttribute('data-confirm-text') ||
            form.hasAttribute('data-confirm-title');

        if (isDeleteForm && !form.dataset.swalConfirmed) {
            e.preventDefault();

            const title = form.getAttribute('data-confirm-title') || 'Apakah Anda Yakin?';
            const text  = form.getAttribute('data-confirm-text')  || 'Data yang dihapus tidak dapat dikembalikan!';

            SwalCustom.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.swalConfirmed = 'true';
                    form.submit();
                }
            });
        }
    });

    // Auto-trigger Toast on Session Flash Messages
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            SwalCustom.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true
            });
        @endif

        @if(session('error'))
            SwalCustom.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4500,
                timerProgressBar: true
            });
        @endif
    });
</script>
