/* ============================================================
   js/dashboard.js — Interaksi Dashboard DapurRumpin
   ============================================================ */

/* ---- Sidebar Toggle (mobile) ---- */
function toggleSidebar() {
  var sidebar  = document.getElementById('sidebar');
  var overlay  = document.getElementById('sidebarOverlay');
  if (!sidebar) return;
  sidebar.classList.toggle('open');
  overlay.classList.toggle('open');
}

/* ---- Tutup sidebar kalau klik di luar ---- */
document.addEventListener('DOMContentLoaded', function () {

  /* Auto-dismiss alert setelah 4 detik */
  document.querySelectorAll('.alert-dismissible').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity 0.5s ease';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 500);
    }, 4000);
  });

  /* Highlight nav-item yang aktif berdasarkan URL */
  var path = window.location.pathname;
  document.querySelectorAll('.nav-item').forEach(function (a) {
    if (a.href && path.endsWith(a.getAttribute('href'))) {
      a.classList.add('active');
    }
  });
});