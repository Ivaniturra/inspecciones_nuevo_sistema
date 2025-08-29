<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Editar Corredor
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Editar Corredor</h1>
                    <p class="text-muted">Modifica los datos de: <strong><?= esc($corredor['corredor_nombre']) ?></strong></p>
                </div>
                <div class="btn-group">
                    <a href="<?= base_url('corredores/show/' . $corredor['corredor_id']) ?>" class="btn btn-outline-info">
                        <i class="fas fa-eye"></i> Ver detalles
                    </a>
                    <a href="<?= base_url('corredores') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al listado
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Se encontraron los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('corredores/update/' . $corredor['corredor_id']) ?>" method="post" enctype="multipart/form-data" id="corredorForm">
        <?= csrf_field() ?>
        <input type="hidden" name="_method" value="PUT">

        <div class="row">
            <!-- Columna datos básicos -->
            <div class="col-lg-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-user-tie me-2"></i> Datos del Corredor
                    </div>
                    <div class="card-body">
                        <!-- Nombre -->
                        <div class="mb-3">
                            <label for="corredor_nombre" class="form-label">
                                Nombre del corredor *
                            </label>
                            <input
                                type="text"
                                class="form-control <?= session('errors.corredor_nombre') ? 'is-invalid' : '' ?>"
                                id="corredor_nombre"
                                name="corredor_nombre"
                                value="<?= esc(old('corredor_nombre', $corredor['corredor_nombre'])) ?>"
                                placeholder="Ej. Corredora de Seguros ABC S.A."
                                required
                            >
                            <div class="invalid-feedback"><?= session('errors.corredor_nombre') ?></div>
                        </div>

                        <!-- Nombre para mostrar -->
                        <div class="mb-3">
                            <label for="corredor_display_name" class="form-label">
                                Nombre comercial
                            </label>
                            <input
                                type="text"
                                class="form-control <?= session('errors.corredor_display_name') ? 'is-invalid' : '' ?>"
                                id="corredor_display_name"
                                name="corredor_display_name"
                                value="<?= esc(old('corredor_display_name', $corredor['corredor_display_name'])) ?>"
                                placeholder="Ej. ABC Seguros"
                            >
                            <div class="invalid-feedback"><?= session('errors.corredor_display_name') ?></div>
                        </div>

                        <div class="row">
                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="corredor_email" class="form-label">Email</label>
                                <input
                                    type="email"
                                    class="form-control <?= session('errors.corredor_email') ? 'is-invalid' : '' ?>"
                                    id="corredor_email"
                                    name="corredor_email"
                                    value="<?= esc(old('corredor_email', $corredor['corredor_email'])) ?>"
                                    placeholder="contacto@corredor.cl"
                                >
                                <div class="invalid-feedback"><?= session('errors.corredor_email') ?></div>
                            </div>

                            <!-- Teléfono -->
                            <div class="col-md-6 mb-3">
                                <label for="corredor_telefono" class="form-label">Teléfono</label>
                                <input
                                    type="text"
                                    class="form-control <?= session('errors.corredor_telefono') ? 'is-invalid' : '' ?>"
                                    id="corredor_telefono"
                                    name="corredor_telefono"
                                    value="<?= esc(old('corredor_telefono', $corredor['corredor_telefono'])) ?>"
                                    placeholder="+56 9 1234 5678"
                                >
                                <div class="invalid-feedback"><?= session('errors.corredor_telefono') ?></div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- RUT -->
                            <div class="col-md-6 mb-3">
                                <label for="corredor_rut" class="form-label">RUT</label>
                                <input
                                    type="text"
                                    class="form-control <?= session('errors.corredor_rut') ? 'is-invalid' : '' ?>"
                                    id="corredor_rut"
                                    name="corredor_rut"
                                    value="<?= esc(old('corredor_rut', $corredor['corredor_rut'])) ?>"
                                    placeholder="12.345.678-9"
                                >
                                <div class="invalid-feedback"><?= session('errors.corredor_rut') ?></div>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6 mb-3">
                                <label for="corredor_habil" class="form-label">Estado</label>
                                <select class="form-select" id="corredor_habil" name="corredor_habil">
                                    <option value="1" <?= old('corredor_habil', $corredor['corredor_habil']) == '1' ? 'selected' : '' ?>>Activo</option>
                                    <option value="0" <?= old('corredor_habil', $corredor['corredor_habil']) == '0' ? 'selected' : '' ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <!-- Compañías (Transfer List) -->
                        <div class="mb-3">
                            <label class="form-label">Compañías *</label>

                            <div class="row g-2">
                                <!-- Columna disponibles -->
                                <div class="col-md-5">
                                <input type="text" id="filtroDisp" class="form-control mb-2" placeholder="Buscar disponibles…">
                                <ul id="listaDisp" class="list-group" style="max-height:260px;overflow:auto;"></ul>
                                <small class="text-muted d-block mt-1">Doble clic o botón → para mover</small>
                                </div>

                                <!-- Botones -->
                                <div class="col-md-2 d-grid gap-2 align-content-center">
                                <button type="button" class="btn btn-outline-secondary" id="btnAdd"    title="Mover seleccionados a la derecha">→</button>
                                <button type="button" class="btn btn-outline-secondary" id="btnAddAll" title="Mover todos a la derecha">≫</button>
                                <button type="button" class="btn btn-outline-secondary" id="btnRem"    title="Mover seleccionados a la izquierda">←</button>
                                <button type="button" class="btn btn-outline-secondary" id="btnRemAll" title="Mover todos a la izquierda">≪</button>
                                </div>

                                <!-- Columna seleccionadas -->
                                <div class="col-md-5">
                                <input type="text" id="filtroSel" class="form-control mb-2" placeholder="Buscar seleccionadas…">
                                <ul id="listaSel" class="list-group" style="max-height:260px;overflow:auto;"></ul>
                                <small class="text-muted d-block mt-1">Doble clic o botón ← para devolver</small>
                                </div>
                            </div>

                            <!-- Aquí se inyectan los inputs hidden name="cias[]" para el submit -->
                            <div id="hiddenCias"></div>

                            <?php if (session('errors.cias')): ?>
                                <div class="invalid-feedback d-block"><?= session('errors.cias') ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Dirección -->
                        <div class="mb-3">
                            <label for="corredor_direccion" class="form-label">Dirección</label>
                            <textarea
                                class="form-control <?= session('errors.corredor_direccion') ? 'is-invalid' : '' ?>"
                                id="corredor_direccion"
                                name="corredor_direccion"
                                rows="3"
                                placeholder="Dirección completa del corredor"
                            ><?= esc(old('corredor_direccion', $corredor['corredor_direccion'])) ?></textarea>
                            <div class="invalid-feedback"><?= session('errors.corredor_direccion') ?></div>
                        </div>

                        <!-- Logo actual y cambiar -->
                        <div class="mb-3">
                            <label class="form-label">Logo actual</label>
                            <div class="border rounded p-3 bg-white text-center mb-3">
                                <img id="logoPreview"
                                     src="<?= !empty($corredor['corredor_logo']) ? base_url('uploads/corredores/' . $corredor['corredor_logo']) : 'https://via.placeholder.com/200x120?text=Sin+logo' ?>"
                                     alt="Logo"
                                     class="img-fluid"
                                     style="max-height: 120px; object-fit: contain;">
                                <?php if (!empty($corredor['corredor_logo'])): ?>
                                    <div class="mt-2">
                                        <small class="text-muted"><i class="fas fa-file me-1"></i><?= esc($corredor['corredor_logo']) ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <label for="corredor_logo" class="form-label">
                                <?= !empty($corredor['corredor_logo']) ? 'Cambiar logo' : 'Subir logo' ?>
                            </label>
                            <input
                                type="file"
                                class="form-control <?= session('errors.corredor_logo') ? 'is-invalid' : '' ?>"
                                id="corredor_logo"
                                name="corredor_logo"
                                accept="image/png,image/jpeg,image/jpg,image/svg+xml"
                            >
                            <div class="invalid-feedback"><?= session('errors.corredor_logo') ?></div>
                            <div class="form-text">PNG/JPG/SVG. Máx 2MB. Deja vacío para mantener el actual.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna marca y colores -->
            <div class="col-lg-5">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-palette me-2"></i> Identidad visual
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Topbar -->
                            <div class="col-12">
                                <label class="form-label mb-1">Barra superior (navbar)</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="input-group">
                                            <span class="input-group-text" title="Fondo barra superior">BG</span>
                                            <input
                                                type="color"
                                                class="form-control form-control-color p-1"
                                                id="corredor_brand_nav_bg"
                                                name="corredor_brand_nav_bg"
                                                value="<?= esc(old('corredor_brand_nav_bg', $corredor['corredor_brand_nav_bg'] ?? '#0d6efd')) ?>"
                                                title="Color de fondo barra superior"
                                            >
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group">
                                            <span class="input-group-text" title="Texto barra superior">TXT</span>
                                            <input
                                                type="color"
                                                class="form-control form-control-color p-1"
                                                id="corredor_brand_nav_text"
                                                name="corredor_brand_nav_text"
                                                value="<?= esc(old('corredor_brand_nav_text', $corredor['corredor_brand_nav_text'] ?? '#ffffff')) ?>"
                                                title="Color de texto barra superior"
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sidebar (degradado) -->
                            <div class="col-12">
                                <label class="form-label mb-1">Sidebar (degradado)</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="input-group">
                                            <span class="input-group-text" title="Color inicio">A</span>
                                            <input
                                                type="color"
                                                class="form-control form-control-color p-1"
                                                id="corredor_brand_side_start"
                                                name="corredor_brand_side_start"
                                                value="<?= esc(old('corredor_brand_side_start', $corredor['corredor_brand_side_start'] ?? '#667eea')) ?>"
                                                title="Color inicio degradado"
                                            >
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group">
                                            <span class="input-group-text" title="Color fin">B</span>
                                            <input
                                                type="color"
                                                class="form-control form-control-color p-1"
                                                id="corredor_brand_side_end"
                                                name="corredor_brand_side_end"
                                                value="<?= esc(old('corredor_brand_side_end', $corredor['corredor_brand_side_end'] ?? '#764ba2')) ?>"
                                                title="Color fin degradado"
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vista previa -->
                        <div class="mt-4">
                            <label class="form-label">Vista previa</label>
                            <div class="border rounded overflow-hidden">
                                <!-- Navbar preview -->
                                <div id="preview-navbar" class="d-flex align-items-center justify-content-between px-3 py-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="rounded-circle d-inline-block" style="width:8px;height:8px;background:currentColor;opacity:.9"></span>
                                        <strong id="preview-title" class="small mb-0">
                                            <?= esc(old('corredor_display_name', $corredor['corredor_display_name']) ?: old('corredor_nombre', $corredor['corredor_nombre'])) ?>
                                        </strong>
                                    </div>
                                    <img id="preview-logo" 
                                         src="<?= !empty($corredor['corredor_logo']) ? base_url('uploads/corredores/' . $corredor['corredor_logo']) : '' ?>" 
                                         alt="Logo" 
                                         style="height:24px; <?= empty($corredor['corredor_logo']) ? 'display:none;' : '' ?>">
                                </div>

                                <div class="d-flex" style="min-height:140px;">
                                    <!-- Sidebar preview -->
                                    <div id="preview-sidebar" style="width:160px;"></div>
                                    <!-- Content preview -->
                                    <div class="flex-grow-1 p-3 bg-light">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body">
                                                <div class="fw-semibold mb-1">Contenido de ejemplo</div>
                                                <div class="text-muted small">Así se verán tus colores aplicados.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <small class="text-muted d-block mt-2">
                                La vista previa es referencial. Los colores se aplicarán al layout real.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Info del registro -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle text-info me-1"></i>
                            Información del registro
                        </h6>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">
                            <strong>ID:</strong> <?= $corredor['corredor_id'] ?><br>
                            <strong>Creado:</strong> <?= date('d/m/Y H:i', strtotime($corredor['corredor_created_at'])) ?><br>
                            <?php if (!empty($corredor['corredor_updated_at'])): ?>
                                <strong>Última modificación:</strong> <?= date('d/m/Y H:i', strtotime($corredor['corredor_updated_at'])) ?>
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="d-flex justify-content-end gap-2">
            <a href="<?= base_url('corredores') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-times me-1"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-save me-1"></i> Actualizar Corredor
            </button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    (function () {
  // Datos PHP → JS
  const data = <?php
    // Prepara arreglo de compañías (id, name)
    $out = array_map(function($c){
      return [
        'id'   => (int) $c['cia_id'],
        'name' => $c['cia_display_name'] ?: $c['cia_nombre'],
      ];
    }, $cias);
    echo json_encode($out, JSON_UNESCAPED_UNICODE);
  ?>;

  const preSeleccionadas = new Set(<?= json_encode(array_map('intval', old('cias', $ciasDelCorredor ?? []))) ?>);

  // Elementos UI
  const ulDisp     = document.getElementById('listaDisp');
  const ulSel      = document.getElementById('listaSel');
  const filtroDisp = document.getElementById('filtroDisp');
  const filtroSel  = document.getElementById('filtroSel');
  const hidden     = document.getElementById('hiddenCias');

  // Helpers
  function crearItem(item) {
    const li = document.createElement('li');
    li.className = 'list-group-item d-flex justify-content-between align-items-center';
    li.dataset.id = item.id;
    li.dataset.name = (item.name || '').toLowerCase();
    li.tabIndex = 0;

    const txt = document.createElement('span');
    txt.textContent = item.name;

    const badge = document.createElement('span');
    badge.className = 'badge bg-light text-muted';
    badge.textContent = '#' + item.id;

    li.appendChild(txt);
    li.appendChild(badge);

    // Toggle selección visual con click
    li.addEventListener('click', () => li.classList.toggle('active'));
    // Doble clic mueve entre listas
    li.addEventListener('dblclick', () => {
      const id = +li.dataset.id;
      if (preSeleccionadas.has(id)) {
        preSeleccionadas.delete(id);
      } else {
        preSeleccionadas.add(id);
      }
      render();
      aplicarFiltros();
    });

    return li;
  }

  function render() {
    ulDisp.innerHTML = '';
    ulSel.innerHTML  = '';
    hidden.innerHTML = '';

    data.forEach(item => {
      const el = crearItem(item);
      if (preSeleccionadas.has(item.id)) {
        ulSel.appendChild(el);

        // Hidden para enviar al backend
        const h = document.createElement('input');
        h.type  = 'hidden';
        h.name  = 'cias[]';
        h.value = item.id;
        hidden.appendChild(h);
      } else {
        ulDisp.appendChild(el);
      }
    });
  }

  function aplicarFiltros() {
    const qDisp = (filtroDisp.value || '').trim().toLowerCase();
    const qSel  = (filtroSel.value  || '').trim().toLowerCase();

    Array.from(ulDisp.children).forEach(li => {
      li.style.display = li.dataset.name.includes(qDisp) ? '' : 'none';
    });
    Array.from(ulSel.children).forEach(li => {
      li.style.display = li.dataset.name.includes(qSel) ? '' : 'none';
    });
  }

  function seleccionadosDe(ul) {
    return Array.from(ul.querySelectorAll('.active')).map(li => +li.dataset.id);
  }

  // Botones
  document.getElementById('btnAdd').addEventListener('click', () => {
    seleccionadosDe(ulDisp).forEach(id => preSeleccionadas.add(id));
    render(); aplicarFiltros();
  });

  document.getElementById('btnAddAll').addEventListener('click', () => {
    data.forEach(item => preSeleccionadas.add(item.id));
    render(); aplicarFiltros();
  });

  document.getElementById('btnRem').addEventListener('click', () => {
    seleccionadosDe(ulSel).forEach(id => preSeleccionadas.delete(id));
    render(); aplicarFiltros();
  });

  document.getElementById('btnRemAll').addEventListener('click', () => {
    preSeleccionadas.clear();
    render(); aplicarFiltros();
  });

  // Filtros
  filtroDisp.addEventListener('input', aplicarFiltros);
  filtroSel .addEventListener('input', aplicarFiltros);

  // Render inicial
  render();
  aplicarFiltros();
})();
(function () {
    const $nav      = document.getElementById('preview-navbar');
    const $sidebar  = document.getElementById('preview-sidebar');
    const $title    = document.getElementById('preview-title');
    const $logo     = document.getElementById('preview-logo');

    const $navBg    = document.getElementById('corredor_brand_nav_bg');
    const $navTxt   = document.getElementById('corredor_brand_nav_text');
    const $sideA    = document.getElementById('corredor_brand_side_start');
    const $sideB    = document.getElementById('corredor_brand_side_end');

    const $corredorNombre   = document.getElementById('corredor_nombre');
    const $displayName = document.getElementById('corredor_display_name');
    const $logoInput   = document.getElementById('corredor_logo');
    const $logoPreview = document.getElementById('logoPreview');

    function applyPreview() {
        if ($nav) {
            $nav.style.backgroundColor = $navBg.value || '#0d6efd';
            $nav.style.color           = $navTxt.value || '#ffffff';
        }
        if ($sidebar) {
            const a = $sideA.value || '#667eea';
            const b = $sideB.value || '#764ba2';
            $sidebar.style.background = `linear-gradient(135deg, ${a} 0%, ${b} 100%)`;
        }

        const name = ($displayName.value || $corredorNombre.value || 'Corredor').trim();
        $title.textContent = name;
    }

    // Eventos de cambios
    [$navBg, $navTxt, $sideA, $sideB].forEach(inp => {
        inp.addEventListener('input', applyPreview);
        inp.addEventListener('change', applyPreview);
    });
    [$corredorNombre, $displayName].forEach(inp => {
        inp.addEventListener('input', applyPreview);
        inp.addEventListener('change', applyPreview);
    });

    // Preview del logo
    $logoInput.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (!file) return;
        
        const reader = new FileReader();
        reader.onload = function (e) {
            $logoPreview.src = e.target.result;
            $logo.src = e.target.result;
            $logo.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });

    // Primera carga
    applyPreview();
})();
</script>
<?= $this->endSection() ?>