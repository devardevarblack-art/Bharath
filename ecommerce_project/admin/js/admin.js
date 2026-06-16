// Sidebar Toggle
document.getElementById('sidebarToggle')?.addEventListener('click', function () {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('mainContent');
    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('show');
    } else {
        sidebar.style.width = sidebar.style.width === '60px' ? '260px' : '60px';
        main.style.marginLeft = main.style.marginLeft === '60px' ? '260px' : '60px';
    }
});

// Initialize DataTables
$(document).ready(function () {
    if ($('.datatable').length) {
        $('.datatable').DataTable({
            pageLength: 10,
            responsive: true,
            language: { search: '', searchPlaceholder: 'Search...' }
        });
    }

    // Auto dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        });
    }, 3000);
});

// Image preview for product upload form
function previewImage(input) {
    const preview = document.getElementById('imgPreview');
    if (!preview) return;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.add('show');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Confirm delete
function confirmDelete(url, msg) {
    if (confirm(msg || 'Are you sure you want to delete this?')) {
        window.location.href = url;
    }
}

// Confirm action
function confirmAction(url, msg) {
    if (confirm(msg || 'Are you sure?')) {
        window.location.href = url;
    }
}
