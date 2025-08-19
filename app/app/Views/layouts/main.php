<?php
$appTitle   = session('app_title')   ?? env('app.title');
$brandTitle = session('brand_title') ?? env('app.brand_title');
$brandLogo  = session('brand_logo')  ?? env('app.brand_logo');
$navBg      = session('nav_bg')      ?? env('app.nav_bg');
$navText    = session('nav_text')    ?? env('app.nav_text');
$sideStart  = session('sidebar_start') ?? env('app.sidebar_start');
$sideEnd    = session('sidebar_end')   ?? env('app.sidebar_end');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $this->renderSection('title') ?> - <?= esc($appTitle) ?></title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<!-- DataTables -->
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
<!-- SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">

<style>
/* Dropdown oscuro dentro de la navbar brandbar */
.navbar.brandbar .dropdown-menu{
  background: #1f2340;            /* o usa var(--brand-nav-bg) si quieres */
  border: 0;
  box-shadow: 0 8px 24px rgba(0,0,0,.25);
}
.navbar.brandbar .dropdown-item{
  color: #f8f9fa !important;
}
.navbar.brandbar .dropdown-item:hover,
.navbar.brandbar .dropdown-item:focus{
  background: rgba(255,255,255,.08);
  color: #fff !important;
}
.navbar.brandbar .dropdown-divider{
  border-color: rgba(255,255,255,.15);
}
:root{
  --nav-h: 56px;
  --rail-collapsed: 72px;
  --rail-expanded: 240px;
  --hover-bg: rgba(255,255,255,.14);
  --active-bg: rgba(255,255,255,.22);
  --accent: #0d6efd;

  --brand-nav-bg:   <?= esc($navBg) ?>;
  --brand-nav-text: <?= esc($navText) ?>;
  --brand-side-start: <?= esc($sideStart) ?>;
  --brand-side-end:   <?= esc($sideEnd) ?>;
}

/* NAVBAR */
.navbar.brandbar{ background: var(--brand-nav-bg) !important; }
.navbar.brandbar .navbar-brand,
.navbar.brandbar .nav-link,
.navbar.brandbar .navbar-toggler,
.navbar.brandbar .dropdown-item{ color: var(--brand-nav-text) !important; }

.brand-logo{ height:22px; width:auto; display:block; cursor:pointer; }
@media (min-width:576px){ .brand-logo{ height:26px; } }
.brand-text{ color:var(--brand-nav-text); }

/* aseguro clic de hamburguesa por encima */
.navbar .navbar-toggler{ position:relative; z-index:1060; }
.navbar .navbar-brand{ position:relative; z-index:1050; }

/* SIDEBAR */
.sidebar-rail{
  position: fixed; top: var(--nav-h); left: 0;
  width: var(--rail-collapsed);
  height: calc(100vh - var(--nav-h));
  overflow-y: auto;
  background: linear-gradient(180deg,var(--brand-side-start) 0%,var(--brand-side-end) 100%);
  z-index: 1030;
  transition: width .18s ease, transform .18s ease;
  box-shadow: 2px 0 10px rgba(0,0,0,.06);
}
.sidebar-rail .rail-header{
  height:48px; display:flex; align-items:center; justify-content:flex-end;
  padding:0 .5rem; border-bottom:1px solid rgba(255,255,255,.25);
}
.sidebar-rail .pin-btn{ border:0; background:transparent; color:#f8f9fa; width:34px; height:34px; border-radius:8px; }

.sidebar-rail .nav{ padding:.5rem .5rem 1rem }
.sidebar-rail .nav-link{
  display:flex; align-items:center; gap:.75rem;
  color:#212529; border-radius:10px;
  padding:.6rem .75rem; margin:.15rem .25rem;
  white-space:nowrap; background:transparent;
  transition: background .15s ease, color .15s ease;
}
.sidebar-rail .nav-link .icon{ width:22px; text-align:center; font-size:1.05rem; }
.sidebar-rail .nav-link .label{ opacity:0; transform:translateX(-6px); transition:opacity .15s, transform .15s; }
.sidebar-rail .nav-link:hover{ background:var(--hover-bg); color:#111; }
.sidebar-rail .nav-link.active{ background:var(--active-bg); color:var(--accent); }

.sidebar-rail .rail-section{
  font-size:.72rem; letter-spacing:.02em;
  color:rgba(255,255,255,.85);
  padding:.65rem .9rem .25rem; text-transform:uppercase;
}

/* expandido por pin */
.sidebar-rail.pinned{ width: var(--rail-expanded); }
.sidebar-rail.pinned .label{ opacity:1; transform:none }

/* CONTENIDO */
.main-wrap{ padding-left: var(--rail-collapsed); transition: padding-left .18s ease; }
main.main-content{ min-height: calc(100vh - var(--nav-h)); }
.sidebar-rail.pinned ~ .main-wrap{ padding-left: var(--rail-expanded); }

/* MÓVIL */
@media (max-width:991.98px){
  .sidebar-rail{ transform: translateX(-100%); width: var(--rail-expanded); }
  .sidebar-rail.show{ transform: translateX(0); }
  .main-wrap{ padding-left:0; }
  .sidebar-backdrop{
    position: fixed; inset: var(--nav-h) 0 0 0;
    background: rgba(0,0,0,.35); z-index:1029; display:none;
  }
}
.sidebar-backdrop { pointer-events: none; }
.sidebar-backdrop.show { display:block; pointer-events: auto; }

/* Ocultar títulos colapsado */
.sidebar-rail .rail-section { display: none; }
.sidebar-rail.pinned .rail-section { display: block; }
.sidebar-rail.pinned .label { opacity: 1; transform: none; }
@media (max-width: 991.98px){
  .sidebar-rail.show .rail-section { display: block; }
  .sidebar-rail.show .label { opacity: 1; transform: none; }
}
/* Asegura que el tooltip NO capture el mouse (evita flicker) */
/* Evita layout shift en hover */
.tooltip{ pointer-events: none !important; }

</style>

<?= $this->renderSection('styles') ?>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark brandbar sticky-top" style="height:var(--nav-h);">
  <div class="container-fluid">
    <!-- Hamburguesa (móvil) -->
    <button class="navbar-toggler d-lg-none me-1" type="button" id="toggleRailBtn" aria-label="Abrir menú">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Marca: logo (botón) + texto con href -->
    <div class="navbar-brand d-flex align-items-center">
        <?php if (!empty($brandLogo)): ?>
            <img id="brandPinLogo"
                src="<?= strpos($brandLogo, 'http') === 0 ? esc($brandLogo) : esc(base_url($brandLogo)) ?>"
                alt="<?= esc($brandTitle) ?>"
                class="brand-logo me-3"
                role="button"
                aria-pressed="false"
                onerror="this.style.display='none'; this.insertAdjacentHTML('beforebegin','<i class=&quot;fas fa-shield-alt me-3&quot;></i>');">
        <?php else: ?>
            <i class="fas fa-shield-alt me-3" role="button" id="brandPinLogo"></i>
        <?php endif; ?>

        <a href="#" class="brand-text text-decoration-none d-none d-sm-inline">
            <?= esc($brandTitle) ?>
        </a>
    </div>
    <div class="ms-auto d-flex align-items-center">
        <!-- Pin/unpin (desktop) -->
        <button class="btn btn-sm btn-outline-light me-2 d-none d-lg-inline-flex" id="btnPin" title="Fijar/soltar menú">
            <i class="fas fa-thumbtack"></i>
        </button>

        <!-- Switch Light/Dark -->
        <button id="themeToggle" class="btn btn-sm btn-outline-light me-2" title="Cambiar tema">
            <i class="fas fa-moon"></i>
        </button>

        <!-- Usuario -->
        <div class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
            <i class="fas fa-user-circle me-2"></i>
            <span class="d-none d-sm-inline"><?= esc(session('user_name') ?? 'Usuario') ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="<?= base_url('users/edit/'.session('user_id')) ?>"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
            <!--<li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Configuración</a></li>-->
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Salir</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- SIDEBAR -->
<aside id="sidebar" class="sidebar-rail">
  <div class="rail-header">
    <button class="pin-btn" id="btnPinHeader" title="Expandir/Colapsar"><i class="fa-solid fa-thumbtack"></i></button>
  </div>

  <nav class="nav flex-column">
    <div class="rail-section">General</div>
    <a class="nav-link <?= url_is('/dashboard') ? 'active' : '' ?>" href="<?= base_url('/dashboard') ?>" data-bs-toggle="tooltip" data-bs-title="Dashboard">
      <i class="icon fa-solid fa-chart-simple"></i><span class="label">Dashboard</span>
    </a>

    <div class="rail-section">Administración</div>
    <a class="nav-link <?= url_is('cias') || url_is('cias/*') ? 'active' : '' ?>" href="<?= base_url('cias') ?>" data-bs-toggle="tooltip" data-bs-title="Compañías">
      <i class="icon fa-solid fa-building"></i><span class="label">Compañías</span>
    </a>
    <a class="nav-link <?= url_is('perfiles') || url_is('perfiles/*') ? 'active' : '' ?>" href="<?= base_url('perfiles') ?>" data-bs-toggle="tooltip" data-bs-title="Perfiles">
      <i class="icon fa-solid fa-id-badge"></i><span class="label">Perfiles</span>
    </a>
    <a class="nav-link <?= url_is('users') || url_is('users/*') ? 'active' : '' ?>" href="<?= base_url('users') ?>" data-bs-toggle="tooltip" data-bs-title="Usuarios">
      <i class="icon fa-solid fa-user-group"></i><span class="label">Usuarios</span>
    </a>
    <a class="nav-link <?= url_is('comentarios') || url_is('comentarios/*') ? 'active' : '' ?>" href="<?= base_url('comentarios') ?>" data-bs-toggle="tooltip" data-bs-title="Comentarios">
      <i class="icon fa-solid fa-comments"></i>
      <span class="label">Comentarios</span>
    </a>
<!--
    <div class="rail-section">Operación</div>
    <a class="nav-link <?= url_is('inspecciones') || url_is('inspecciones/*') ? 'active' : '' ?>" href="<?= base_url('inspecciones') ?>" data-bs-toggle="tooltip" data-bs-title="Inspecciones">
      <i class="icon fa-solid fa-clipboard-list"></i><span class="label">Inspecciones</span>
    </a>
    <a class="nav-link <?= url_is('reportes') || url_is('reportes/*') ? 'active' : '' ?>" href="<?= base_url('reportes') ?>" data-bs-toggle="tooltip" data-bs-title="Reportes">
      <i class="icon fa-solid fa-chart-bar"></i><span class="label">Reportes</span>
    </a>

    <div class="rail-section">Sistema</div>
    <a class="nav-link <?= url_is('sistema') || url_is('sistema/*') ? 'active' : '' ?>" href="<?= base_url('sistema') ?>" data-bs-toggle="tooltip" data-bs-title="Sistema">
      <i class="icon fa-solid fa-gear"></i><span class="label">Sistema</span>
    </a>-->
  </nav>
</aside>

<div id="sidebarBackdrop" class="sidebar-backdrop"></div>

<!-- CONTENIDO -->
<div class="main-wrap">
  <main class="main-content px-3 px-md-4 pt-3 pb-4">
    <?= $this->renderSection('breadcrumb') ?>
    <?= $this->renderSection('content') ?>
  </main>
</div>

<!-- JS libs -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<!-- === Lógica de Sidebar con tooltips estables === -->
<script>
 const themeToggle = document.getElementById("themeToggle");
const html = document.documentElement;

// Si ya hay preferencia guardada
if (localStorage.getItem("theme")) {
html.setAttribute("data-bs-theme", localStorage.getItem("theme"));
themeToggle.innerHTML = localStorage.getItem("theme") === "dark"
    ? '<i class="fas fa-sun"></i>'
    : '<i class="fas fa-moon"></i>';
} else {
// Detectar tema del sistema si no hay preferencia
const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
html.setAttribute("data-bs-theme", prefersDark ? "dark" : "light");
themeToggle.innerHTML = prefersDark 
    ? '<i class="fas fa-sun"></i>'
    : '<i class="fas fa-moon"></i>';
}

// Alternar manualmente
themeToggle.addEventListener("click", () => {
let current = html.getAttribute("data-bs-theme");
let next = current === "light" ? "dark" : "light";
html.setAttribute("data-bs-theme", next);
localStorage.setItem("theme", next);
themeToggle.innerHTML = next === "dark"
    ? '<i class="fas fa-sun"></i>'
    : '<i class="fas fa-moon"></i>';
});
(() => {
  const sidebar   = document.getElementById('sidebar');
  const backdrop  = document.getElementById('sidebarBackdrop');
  const btnOpen   = document.getElementById('toggleRailBtn');
  const btnPins = [
    document.getElementById('btnPin'),
    document.getElementById('btnPinHeader'),
    document.getElementById('pinRailBtn')
  ].filter(Boolean);
  const brandPinLogo = document.getElementById('brandPinLogo');

  const isDesktop = () => window.innerWidth >= 992;

  // Persistencia de pin solo para desktop
  const getPrefPinned = () => localStorage.getItem('railPinned') === '1';
  const setPrefPinned = (v) => localStorage.setItem('railPinned', v ? '1' : '0');

 // === TOOLTIP HELPERS (anclados al ícono, pegados) ===
/* === TOOLTIP HELPERS sin parpadeo, anclados al ícono === */
/* === TOOLTIP HELPERS (robustos, sin flicker, anclados al ícono) === */
/* === TOOLTIP HELPERS: anclado al .icon, hover desde el <a> === */
const enableTooltips = () => {
  const DIST = 4; // distancia desde el borde derecho del icono (px)

  document.querySelectorAll('#sidebar a.nav-link').forEach(link => {
    const icon = link.querySelector('.icon') || link; // fallback

    // 1) Tomar título (data-bs-title / title / .label)
    let title =
      icon.getAttribute('data-bs-title') ||
      icon.getAttribute('title') ||
      link.getAttribute('data-bs-title') ||
      link.getAttribute('title') ||
      (link.querySelector('.label')?.textContent || '');

    title = (title || '').trim();
    if (!title) return;

    // 2) Destruir instancias previas y limpiar attrs en ambos
    [link, icon].forEach(el => {
      const t = bootstrap.Tooltip.getInstance(el);
      if (t) t.dispose();
      el.removeAttribute('title');
      el.removeAttribute('data-bs-title');
      el.removeAttribute('data-bs-toggle');
    });

    // 3) Crear tooltip SOLO en el icono (posición perfecta)
    icon.setAttribute('data-bs-toggle', 'tooltip');
    icon.setAttribute('data-bs-title', title);

    const tip = new bootstrap.Tooltip(icon, {
      placement: 'right',
      container: 'body',
      boundary: 'viewport',
      trigger: 'manual',          // lo manejamos nosotros para evitar flicker
      delay: { show: 120, hide: 120 },
      popperConfig: (def) => {
        const cfg = typeof def === 'function' ? def() : (def || {});
        const mods = (cfg.modifiers || []).filter(m => m.name !== 'offset');
        mods.push(
          { name: 'offset', options: { offset: [0, DIST] } },
          { name: 'flip', options: { fallbackPlacements: ['right-start','right-end'] } },
          { name: 'preventOverflow', options: { padding: 4, tether: true } }
        );
        return { ...cfg, modifiers: mods };
      }
    });

    // 4) Reenviar hover del LINK al tooltip del ICONO (sin parpadeo)
    let inside = false, hideTimer = null;
    const show = () => { clearTimeout(hideTimer); if (!inside) { inside = true; tip.show(); } };
    const hide = () => { hideTimer = setTimeout(() => { inside = false; tip.hide(); }, 100); };

    link.addEventListener('mouseenter', show);
    link.addEventListener('mouseleave', hide);
    // también por accesibilidad
    link.addEventListener('focusin', show);
    link.addEventListener('focusout', hide);
  });
};

const disableTooltips = () => {
  document.querySelectorAll('#sidebar a.nav-link, #sidebar a.nav-link .icon').forEach(el => {
    const t = bootstrap.Tooltip.getInstance(el);
    if (t) t.dispose();
    el.removeAttribute('title');
    el.removeAttribute('data-bs-title');
    el.removeAttribute('data-bs-toggle');
  });
};
  const refreshTooltipsAfterTransition = () => {
    // tooltips solo cuando el rail está colapsado en desktop
    const shouldEnable = isDesktop() && !sidebar.classList.contains('pinned') && !sidebar.classList.contains('show');
    if (shouldEnable) enableTooltips();
  };

  // Estado inicial según viewport
  const applyDesktopState = () => {
    sidebar.classList.remove('show');
    backdrop.classList.remove('show');
    const wantPinned = getPrefPinned();
    sidebar.classList.toggle('pinned', wantPinned);
    if (wantPinned) disableTooltips(); else enableTooltips();
  };
  const applyMobileState = () => {
    sidebar.classList.remove('pinned');
    disableTooltips();
  };

  // Acciones
  const togglePinned = () => {
    if (!isDesktop()) return;
    disableTooltips(); // descartar antes del cambio

    const nowPinned = !sidebar.classList.contains('pinned');
    sidebar.classList.toggle('pinned', nowPinned);
    setPrefPinned(nowPinned);

    sidebar.classList.remove('show');
    backdrop.classList.remove('show');

    const onEnd = (e) => {
      if (e.propertyName === 'width' || e.propertyName === 'transform') {
        sidebar.removeEventListener('transitionend', onEnd);
        refreshTooltipsAfterTransition();
      }
    };
    sidebar.addEventListener('transitionend', onEnd);
  };

  const toggleMobile = () => {
    disableTooltips();            // descartar antes del slide
    sidebar.classList.remove('pinned'); // en móvil no usamos pinned

    const isOpen = sidebar.classList.contains('show');
    sidebar.classList.toggle('show', !isOpen);
    backdrop.classList.toggle('show', !isOpen);

    const onEnd = (e) => {
      if (e.propertyName === 'transform') {
        sidebar.removeEventListener('transitionend', onEnd);
        refreshTooltipsAfterTransition();
      }
    };
    sidebar.addEventListener('transitionend', onEnd);
  };

  const closeMobile = () => {
    sidebar.classList.remove('show');
    backdrop.classList.remove('show');
  };

  // Eventos
  document.querySelectorAll('#toggleRailBtn, .navbar-toggler').forEach(el=>{
    el.addEventListener('click', (e)=>{ e.preventDefault(); e.stopPropagation(); toggleMobile(); });
  });
  btnPins.forEach(btn => btn.addEventListener('click', () => isDesktop() ? togglePinned() : toggleMobile()));
  if (brandPinLogo) {
    brandPinLogo.addEventListener('click', (e) => {
      e.preventDefault?.(); e.stopPropagation?.();
      if (isDesktop()) togglePinned(); else toggleMobile();
    });
  }
  backdrop.addEventListener('click', closeMobile);
  sidebar.querySelectorAll('a.nav-link').forEach(a => a.addEventListener('click', () => { if (!isDesktop()) closeMobile(); }));

  // Resize: cambiar de modo limpio
  let lastIsDesktop = isDesktop();
  const handleResize = () => {
    const now = isDesktop();
    if (now !== lastIsDesktop) {
      disableTooltips();
      if (now) applyDesktopState(); else applyMobileState();
      refreshTooltipsAfterTransition();
      lastIsDesktop = now;
    }
  };
  window.addEventListener('resize', handleResize);

  // Estado inicial
  if (isDesktop()) applyDesktopState(); else applyMobileState();

  // API opcional
  window.Sidebar = { toggleMobile, closeMobile, togglePinned, isDesktop };
})();
</script>

<!-- === Tus scripts jQuery integrados === -->
<script>
$(document).ready(function() {
  // Auto-hide alerts
  $('.alert').delay(5000).fadeOut();

  // Sidebar toggle via API común (evita estados duplicados)
  $('.navbar-toggler').on('click', function(e) {
    e.preventDefault(); e.stopPropagation();
    window.Sidebar?.toggleMobile();
  });

  // Cerrar al hacer click fuera (móvil)
  $(document).on('click', function(e) {
    if (!window.Sidebar?.isDesktop() && $('#sidebar').hasClass('show')) {
      if (!$(e.target).closest('#sidebar, .navbar-toggler, #brandPinLogo').length) {
        window.Sidebar?.closeMobile();
      }
    }
  });

  // Active navigation mejorado
  const currentPath = window.location.pathname;
  $('.sidebar-rail .nav-link').removeClass('active');
  let exactMatch = false;
  $('.sidebar-rail .nav-link').each(function() {
    const linkPath = new URL(this.href, window.location.origin).pathname;
    if (currentPath === linkPath) {
      $(this).addClass('active');
      exactMatch = true;
      return false;
    }
  });
  if (!exactMatch) {
    $('.sidebar-rail .nav-link').each(function() {
      const linkPath = new URL(this.href, window.location.origin).pathname;
      if (linkPath !== '/' && currentPath.startsWith(linkPath)) {
        $(this).addClass('active');
        return false;
      }
    });
  }

  // Tooltips globales (fuera del rail), también pegados
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]:not(#sidebar [data-bs-toggle="tooltip"])'));
  tooltipTriggerList.map(function (el) {
    return new bootstrap.Tooltip(el, { placement: 'top', offset: [0, 6] });
  });

  // Confirm dialogs
  $('.btn-danger[data-confirm]').on('click', function(e) {
    e.preventDefault();
    const form = $(this).closest('form');
    const message = $(this).data('confirm') || '¿Estás seguro de realizar esta acción?';
    Swal.fire({
      title: 'Confirmar acción',
      text: message,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, continuar',
      cancelButtonText: 'Cancelar'
    }).then((result) => { if (result.isConfirmed) form.submit(); });
  });
});

// Global AJAX setup básico (puedes añadir CSRF headers aquí si los necesitas)
$.ajaxSetup({
  beforeSend: function(xhr, settings) {
    if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type) && !this.crossDomain) {
      xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    }
  }
});

// Helpers SweetAlert globales
function showSuccess(message) {
  Swal.fire({ icon: 'success', title: 'Éxito', text: message, timer: 3000, showConfirmButton: false });
}
function showError(message) {
  Swal.fire({ icon: 'error', title: 'Error', text: message });
}
function showLoading() {
  Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
}
</script>

<?= $this->renderSection('scripts') ?>
</body>
</html>
