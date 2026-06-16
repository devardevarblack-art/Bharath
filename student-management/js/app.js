// ── CONFIG ─────────────────────────────────────────────────────────────────
const API_URL = 'api/students.php';

// ── STATE ───────────────────────────────────────────────────────────────────
let students    = [];
let editingId   = null;
let deleteId    = null;
let searchTimer = null;

// ── DOM REFS ────────────────────────────────────────────────────────────────
const studentModal  = document.getElementById('studentModal');
const deleteModal   = document.getElementById('deleteModal');
const studentForm   = document.getElementById('studentForm');
const searchInput   = document.getElementById('searchInput');
const tableBody     = document.getElementById('tableBody');
const totalCount    = document.getElementById('totalCount');
const statsTotal    = document.getElementById('statsTotal');
const statsCourses  = document.getElementById('statsCourses');
const statsDepts    = document.getElementById('statsDepts');
const modalTitle    = document.getElementById('modalTitle');
const saveBtn       = document.getElementById('saveBtn');
const toastContainer= document.getElementById('toastContainer');
const sidebar       = document.getElementById('sidebar');
const sidebarOverlay= document.getElementById('sidebarOverlay');

// ── INIT ────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  loadStudents();
  bindEvents();
});

function bindEvents() {
  searchInput.addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(searchStudents, 350);
  });

  document.getElementById('clearSearch').addEventListener('click', () => {
    searchInput.value = '';
    loadStudents();
    document.getElementById('clearSearch').style.display = 'none';
  });

  searchInput.addEventListener('input', () => {
    document.getElementById('clearSearch').style.display =
      searchInput.value ? 'flex' : 'none';
  });

  // Mobile sidebar
  document.getElementById('menuToggle').addEventListener('click', openSidebar);
  sidebarOverlay.addEventListener('click', closeSidebar);

  // Sidebar nav items
  document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', () => {
      document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
      item.classList.add('active');
      closeSidebar();
    });
  });
}

// ── SIDEBAR ─────────────────────────────────────────────────────────────────
function openSidebar() {
  sidebar.classList.add('open');
  sidebarOverlay.classList.add('show');
}
function closeSidebar() {
  sidebar.classList.remove('open');
  sidebarOverlay.classList.remove('show');
}

// ── API CALLS ────────────────────────────────────────────────────────────────
async function loadStudents(query = '') {
  showLoader();
  try {
    const url  = query ? `${API_URL}?search=${encodeURIComponent(query)}` : API_URL;
    const res  = await fetch(url);
    const data = await res.json();

    if (data.success) {
      students = data.students || [];
      renderTable(students);
      updateStats(students);
    } else {
      showToast('Failed to load students', 'error');
    }
  } catch (err) {
    showToast('Server error. Check PHP/MySQL connection.', 'error');
    renderError();
  }
}

async function searchStudents() {
  const q = searchInput.value.trim();
  await loadStudents(q);
}

async function saveStudent(payload) {
  const method = editingId ? 'PUT' : 'POST';
  if (editingId) payload.id = editingId;

  const res  = await fetch(API_URL, {
    method,
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });
  return await res.json();
}

async function deleteStudent() {
  if (!deleteId) return;
  try {
    const res  = await fetch(API_URL, {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: deleteId })
    });
    const data = await res.json();

    if (data.success) {
      showToast('Student deleted successfully', 'success');
      closeDeleteModal();
      loadStudents();
    } else {
      showToast(data.error || 'Delete failed', 'error');
    }
  } catch (err) {
    showToast('Server error', 'error');
  }
}

// ── RENDER ───────────────────────────────────────────────────────────────────
function renderTable(data) {
  totalCount.textContent = data.length;

  if (!data.length) {
    tableBody.innerHTML = `
      <tr><td colspan="7">
        <div class="empty-state">
          <div class="empty-icon">🎓</div>
          <h3>No students found</h3>
          <p>${searchInput.value ? 'Try a different search term.' : 'Add your first student using the button above.'}</p>
        </div>
      </td></tr>`;
    return;
  }

  tableBody.innerHTML = data.map((s, i) => {
    const yearLabels = ['', '1st Year', '2nd Year', '3rd Year', '4th Year'];
    const badges     = ['', 'badge-blue', 'badge-green', 'badge-purple', 'badge-amber'];
    const initials   = s.name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0,2);
    return `
    <tr>
      <td>${i + 1}</td>
      <td>
        <div class="student-name-cell">
          <div class="student-avatar">${initials}</div>
          <div>
            <div class="student-name">${escHtml(s.name)}</div>
            <div class="student-email">${escHtml(s.email)}</div>
          </div>
        </div>
      </td>
      <td>${escHtml(s.phone)}</td>
      <td><span class="badge badge-blue">${escHtml(s.course)}</span></td>
      <td>${escHtml(s.department)}</td>
      <td><span class="badge ${badges[s.year] || 'badge-blue'}">${yearLabels[s.year] || s.year}</span></td>
      <td>
        <button class="action-btn edit"   onclick="openEditModal(${s.id})" title="Edit">✏️</button>
        <button class="action-btn delete" onclick="openDeleteModal(${s.id}, '${escHtml(s.name)}')" title="Delete">🗑️</button>
      </td>
    </tr>`;
  }).join('');
}

function updateStats(data) {
  statsTotal.textContent  = data.length;
  statsCourses.textContent = [...new Set(data.map(s => s.course))].length;
  statsDepts.textContent   = [...new Set(data.map(s => s.department))].length;
  // Year 1 count
  document.getElementById('statsYear1').textContent = data.filter(s => s.year == 1).length;
}

function showLoader() {
  tableBody.innerHTML = `<tr><td colspan="7"><div class="loader"><div class="spinner"></div></div></td></tr>`;
}
function renderError() {
  tableBody.innerHTML = `
    <tr><td colspan="7">
      <div class="empty-state">
        <div class="empty-icon">⚠️</div>
        <h3>Connection Error</h3>
        <p>Could not reach the server. Make sure PHP & MySQL are running.</p>
      </div>
    </td></tr>`;
}

// ── ADD MODAL ────────────────────────────────────────────────────────────────
function openAddModal() {
  editingId = null;
  modalTitle.textContent = '➕ Add New Student';
  saveBtn.textContent    = 'Add Student';
  studentForm.reset();
  clearErrors();
  showModal(studentModal);
}

function openEditModal(id) {
  const s = students.find(x => x.id == id);
  if (!s) { showToast('Student not found', 'error'); return; }

  editingId = id;
  modalTitle.textContent = '✏️ Edit Student';
  saveBtn.textContent    = 'Update Student';
  clearErrors();

  document.getElementById('fName').value       = s.name;
  document.getElementById('fEmail').value      = s.email;
  document.getElementById('fPhone').value      = s.phone;
  document.getElementById('fDob').value        = s.dob;
  document.getElementById('fGender').value     = s.gender;
  document.getElementById('fCourse').value     = s.course;
  document.getElementById('fDept').value       = s.department;
  document.getElementById('fYear').value       = s.year;
  document.getElementById('fAddress').value    = s.address || '';

  showModal(studentModal);
}

function closeStudentModal() { hideModal(studentModal); }

// ── DELETE MODAL ──────────────────────────────────────────────────────────────
function openDeleteModal(id, name) {
  deleteId = id;
  document.getElementById('deleteName').textContent = name;
  showModal(deleteModal);
}
function closeDeleteModal() { hideModal(deleteModal); deleteId = null; }

// ── FORM SUBMIT ───────────────────────────────────────────────────────────────
async function submitForm() {
  if (!validateForm()) return;

  const payload = {
    name:       document.getElementById('fName').value.trim(),
    email:      document.getElementById('fEmail').value.trim(),
    phone:      document.getElementById('fPhone').value.trim(),
    dob:        document.getElementById('fDob').value,
    gender:     document.getElementById('fGender').value,
    course:     document.getElementById('fCourse').value.trim(),
    department: document.getElementById('fDept').value.trim(),
    year:       parseInt(document.getElementById('fYear').value),
    address:    document.getElementById('fAddress').value.trim()
  };

  saveBtn.disabled    = true;
  saveBtn.textContent = editingId ? 'Updating…' : 'Adding…';

  try {
    const data = await saveStudent(payload);
    if (data.success) {
      showToast(editingId ? 'Student updated successfully!' : 'Student added successfully!', 'success');
      closeStudentModal();
      loadStudents();
    } else {
      showToast(data.error || 'Save failed', 'error');
    }
  } catch (err) {
    showToast('Server error', 'error');
  } finally {
    saveBtn.disabled    = false;
    saveBtn.textContent = editingId ? 'Update Student' : 'Add Student';
  }
}

// ── VALIDATION ────────────────────────────────────────────────────────────────
function validateForm() {
  clearErrors();
  let valid = true;

  const rules = {
    fName:   v => v.length >= 2              || 'Name must be at least 2 characters',
    fEmail:  v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) || 'Enter a valid email address',
    fPhone:  v => /^\d{10}$/.test(v)         || 'Enter a valid 10-digit phone number',
    fDob:    v => !!v                         || 'Date of birth is required',
    fGender: v => !!v                         || 'Select a gender',
    fCourse: v => v.length >= 2              || 'Course name is required',
    fDept:   v => v.length >= 2              || 'Department name is required',
    fYear:   v => v >= 1 && v <= 6           || 'Select a valid year',
  };

  Object.entries(rules).forEach(([id, check]) => {
    const el  = document.getElementById(id);
    const val = el.value.trim();
    const msg = check(val);
    if (msg !== true) {
      setError(id, msg);
      valid = false;
    }
  });

  return valid;
}

function setError(id, msg) {
  const el = document.getElementById(id);
  el.classList.add('error');
  const err = document.createElement('div');
  err.className   = 'form-error';
  err.textContent = msg;
  el.parentNode.appendChild(err);
}

function clearErrors() {
  document.querySelectorAll('.form-control.error').forEach(el => el.classList.remove('error'));
  document.querySelectorAll('.form-error').forEach(el => el.remove());
}

// ── MODAL HELPERS ─────────────────────────────────────────────────────────────
function showModal(modal) { modal.classList.add('show'); document.body.style.overflow = 'hidden'; }
function hideModal(modal) { modal.classList.remove('show'); document.body.style.overflow = ''; }

// ── TOAST ─────────────────────────────────────────────────────────────────────
function showToast(message, type = 'success', duration = 3500) {
  const icons = { success: '✅', error: '❌', warning: '⚠️' };
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `<span class="toast-icon">${icons[type] || 'ℹ️'}</span><span>${message}</span>`;
  toastContainer.appendChild(toast);
  setTimeout(() => { toast.style.animation = 'none'; toast.style.opacity = '0'; toast.style.transition = 'opacity .3s'; setTimeout(() => toast.remove(), 300); }, duration);
}

// ── UTILS ────────────────────────────────────────────────────────────────────
function escHtml(str = '') {
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
