// Sidebar toggle
document.getElementById('sidebarToggle').addEventListener('click', function () {
    document.getElementById('sidebar').classList.toggle('show');
});

// DataTables init
$(document).ready(function () {
    if ($('.datatable').length) {
        $('.datatable').DataTable({
            pageLength: 10,
            language: { search: "Search:", lengthMenu: "Show _MENU_ entries" }
        });
    }
});

// Image preview
function previewImage(input) {
    const preview = document.getElementById('imgPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.add('show');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Password toggle
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (!input || !icon) return;
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Confirm delete
function confirmDelete(url) {
    if (confirm('Are you sure you want to delete this item?')) {
        window.location.href = url;
    }
}
