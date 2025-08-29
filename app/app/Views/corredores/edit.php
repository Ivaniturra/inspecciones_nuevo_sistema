<!-- app/Views/corredores/edit.php -->
<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Editar Corredor<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3 mb-0">Editar Corredor</h1>
      <p class="text-muted mb-0">Modifica los datos de: <strong><?= esc($corredor['corredor_nombre']) ?></strong></p>
    </div>
    <div class="btn-group">
      <a href="<?= base_url('corredores') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Volver
      </a>
      <a href="<?= base_url('corredores/show/'.$corredor['corredor_id']) ?>" class="btn btn-outline-info">
        <i class="fas fa-eye me-1"></i> Ver
      </a>
    </div>
  </div>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Revisa los siguientes campos:</strong>
      <ul class="mb-0 mt-2">
        <?php foreach (session()->getFlashdata('errors') as $e): ?>
          <li><?= esc($e) ?></li>
        <?php endforeach; ?>
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <form action="<?= base_url('corredores/update/'.$corredor['corredor_id']) ?>" method="post" enctype="multipart/form-data" id="formCorredor">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="PUT">

    <div class="row">
      <!-- Col 1: datos -->
      <div class="col-lg-7">
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-warning text-dark">
            <i class="fas fa-user-tie me-2"></i> Datos del Corredor
          </div>
          <div class="card-body">
            <!-- Nombre -->
            <div class="mb-3">
              <label for="corredor_nombre" class="form-label">Nombre del corredor *</label>
              <input type="text" id="corredor_nombre" name="corredor_nombre"
                     class="form-control <?= session('errors.corredor_nombre') ? 'is-invalid' : '' ?>"
                     value="<?= esc(old('corredor_nombre', $corredor['corredor_nombre'])) ?>"
                     required>
              <div class="invalid-feedback"><?= session('errors.corredor_nombre') ?></div>
            </div>

            <!-- Nombre comercial -->
            <div class="mb-3">
              <label for="corredor_display_name" class="form-label">Nombre comercial</label>
              <input type="text" id="corredor_display_name" name="corredor_display_name"
                     class="form-control <?= session('errors.corredor_display_name') ? 'is-invalid' : '' ?>"
                     value="<?= esc(old('corredor_display_name', $corredor['corredor_display_name'])) ?>">
              <div class="invalid-feedback"><?= session('errors.corredor_display_name') ?></div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="corredor_email" class="form-label">Email</label>
                <input type="email" id="corredor_email" name="corredor_email"
                       class="form-control <?= session('errors.corredor_email') ? 'is-invalid' : '' ?>"
                       value="<?= esc(old('corredor_email', $corredor['corredor_email'])) ?>">
                <div class="invalid-feedback"><?= session('errors.corredor_email') ?></div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="corredor_telefono" class="form-label">Teléfono</label>
                <input type="text" id="corredor_telefono" name="corredor_telefono"
                       class="form-control <?= session('errors.corredor_telefono') ? 'is-invalid' : '' ?>"
                       value="<?= esc(old('corredor_telefono', $corredor['corredor_telefono'])) ?>">
                <div class="invalid-feedback"><?= session('errors.corredor_telefono') ?></div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="corredor_rut" class="form-label">RUT</label>
                <input type="text" id="corredor_rut" name="corredor_rut"
                       class="form-control <?= session('errors.corredor_rut') ? 'is-invalid' : '' ?>"
                       value="<?= esc(old('corredor_rut', $corredor['corredor_rut'])) ?>">
                <div class="invalid-feedback"><?= session('errors.corredor_rut') ?></div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="corredor_habil" class="form-label">Estado</label>
                <select id="corredor_habil" name="corredor_habil" class="form-select">
                  <option value="1" <?= old('corredor_habil', $corredor['corredor_habil']) == '1' ? 'selected' : '' ?>>Activo</option>
                  <option value="0" <?= old('corredor_habil', $corredor['corredor_habil']) == '0' ? 'selected' : '' ?>>Inactivo</option>
                </select>
              </div>
            </div>

            <!-- Dirección -->
            <div class="mb-3">
              <label for="corredor_direccion" class="form-label">Dirección</label>
              <textarea id="corredor_direccion" name="corredor_direccion" rows="3"
                        class="form-control <?= session('errors.corredor_direccion') ? 'is-invalid' : '' ?>"
                        placeholder="Dirección completa"><?= esc(old('corredor_direccion', $corredor['corredor_direccion'])) ?></textarea>
              <div class="invalid-feedback"><?= session('errors.corredor_direccion') ?></div>
            </div>

            <!-- Logo actual + subir -->
            <div class="mb-3">
              <label class="form-label">Logo actual</label>
              <div class="border rounded p-3 bg-white text-center mb-2">
                <?php
                  $logo = $corredor['corredor_logo'] ?? '';
                  $logoUrl = $logo ? base_url('uploads/corredores/'.$logo) : '';
                ?>
                <?php if ($logoUrl): ?>
                  <img id="logoPreview" src="<?= esc($logoUrl) ?>" alt="Logo" class="img-fluid" style="max-height:120px;object-fit:contain;">
                  <div class="mt-2"><small class="text-muted"><i class="fas fa-file me-1"></i><?= esc($logo) ?></small></div>
                <?php else: ?>
                  <img id="logoPreview" src="https://via.placeholder.com/200x120?text=Sin+logo" alt="Logo" class="img-fluid" style="max-height:120px;object-fit:contain;">
                <?php endif; ?>
              </div>

              <label for="corredor_logo" class="form-label">Cambiar logo</label>
              <input type="file" id="corredor_logo" name="corredor_logo"
                     class="form-control <?= session('errors.corredor_logo') ? 'is-invalid' : '' ?>"
                     accept="image/png,image/jpeg,image/jpg,image/svg+xml">
              <div class="form-text">PNG/JPG/SVG. Máx 2MB. Deja vacío para mantener el actual.</div>
              <div class="invalid-feedback"><?= session('errors.corredor_logo') ?></div>
            </div>

            <!-- Compañías (Transfer list) -->
            <div class="mb-3">
              <label class="form-label">Compañías *</label>
              <div class="row g-2">
                <div class="col-md-5">
                  <input type="text" id="filtroDisp" class="form-control mb-2" placeholder="Buscar disponibles…">
                  <ul id="listaDisp" class="list-group" style="max-height:260px;overflow:auto;"></ul>
                  <small class="text-muted d-block mt-1">Doble clic o botón → para mover</small>
                </div>
                <div class="col-md-2 d-grid gap-2 align-content-center">
                  <button type="button" class="btn btn-outline-secondary" id="btnAdd">→</button>
                  <button type="button" class="btn btn-outline-secondary" id="btnAddAll">≫</button>
                  <button type="button" class="btn btn-outline-secondary" id="btnRem">←</button>
                  <button type="button" class="btn btn-outline-secondary" id="btnRemAll">≪</button>
                </div>
                <div class="col-md-5">
                  <input type="text" id="filtroSel" class="form-control mb-2" placeholder="Buscar seleccionadas…">
                  <ul id="listaSel" class="list-group" style="max-height:260px;overflow:auto;"></ul>
                  <small class="text-muted d-block mt-1">Doble clic o botón ← para devolver</small>
                </div>
              </div>
              <!-- inputs hidden que se envían -->
              <div id="hiddenCias"></div>
              <?php if (session('errors.cias')): ?>
                <div class="invalid-feedback d-block"><?= session('errors.cias') ?></div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Col 2: identidad -->
      <div class="col-lg-5">
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-warning text-dark">
            <i class="fas fa-palette me-2"></i> Identidad visual
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label mb-1">Barra superior (navbar)</label>
                <div class="row g-2">
                  <div class="col-6">
                    <div class="input-group">
                      <span class="input-group-text">BG</span>
                      <input type="color" id="corredor_brand_nav_bg" name="corredor_brand_nav_bg"
                             class="form-control form-control-color p-1"
                             value="<?= esc(old('corredor_brand_nav_bg', $corredor['corredor_brand_nav_bg'] ?? '#0d6efd')) ?>">
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="input-group">
                      <span class="input-group-text">TXT</span>
                      <input type="color" id="corredor_brand_nav_text" name="corredor_brand_nav_text"
                             class="form-control form-control-color p-1"
                             value="<?= esc(old('corredor_brand_nav_text', $corredor['corredor_brand_nav_text'] ?? '#ffffff')) ?>">
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <label class="form-label mb-1">Sidebar (degradado)</label>
                <div class="row g-2">
                  <div class="col-6">
                    <div class="input-group">
                      <span class="input-group-text">A</span>
                      <input type="color" id="corredor_brand_side_start" name="corredor_brand_side_start"
                             class="form-control form-control-color p-1"
                             value="<?= esc(old('corredor_brand_side_start', $corredor['corredor_brand_side_start'] ?? '#667eea')) ?>">
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="input-group">
                      <span class="input-group-text">B</span>
                      <input type="color" id="corredor_brand_side_end" name="corredor_brand_side_end"
                             class="form-control form-control-color p-1"
                             value="<?= esc(old('corredor_brand_side_end', $corredor['corredor_brand_side_end'] ?? '#764ba2')) ?>">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Preview colores -->
            <div class="mt-4">
              <label class="form-label">Vista previa</label>
              <div class="border rounded overflow-hidden">
                <div id="preview-navbar" class="d-flex align-items-center justify-content-between px-3 py-2">
                  <strong id="preview-title" class="small mb-0">
                    <?= esc(old('corredor_display_name', $corredor['corredor_display_name']) ?: old('corredor_nombre', $corredor['corredor_nombre'])) ?>
                  </strong>
                  <?php if ($logoUrl): ?>
                    <img id="preview-logo" src="<?= esc($logoUrl) ?>" alt="Logo" style="height:24px;">
                  <?php else: ?>
                    <img id="preview-logo" src="" alt="Logo" style="height:24px;display:none;">
                  <?php endif; ?>
                </div>
                <div class="d-flex" style="min-height:140px;">
                  <div id="preview-sidebar" style="width:160px;"></div>
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
              <small class="text-muted d-block mt-2">La vista previa es referencial.</small>
            </div>
          </div>
        </div>

        <!-- Info -->
        <div class="card shadow-sm">
          <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-info-circle text-info me-1"></i> Información del registro</h6>
          </div>
          <div class="card-body">
            <small class="text-muted d-block">
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

    <div class="d-flex justify-content-end gap-2">
      <a href="<?= base_url('corredores') ?>" class="btn btn-outline-secondary"><i class="fas fa-times me-1"></i> Cancelar</a>
      <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i> Actualizar</button>
    </div>
  </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
  // -------- Preview de colores y logo --------
  const $nav = document.getElementById('preview-navbar');
  const $sidebar = document.getElementById('preview-sidebar');
  const $title = document.getElementById('preview-title');
  const $logo = document.getElementById('preview-logo');
  const $logoInput = document.getElementById('corredor_logo');
  const $logoPreview = document.getElementById('logoPreview');

  const $navBg  = document.getElementById('corredor_brand_nav_bg');
  const $navTxt = document.getElementById('corredor_brand_nav_text');
  const $sideA  = document.getElementById('corredor_brand_side_start');
  const $sideB  = document.getElementById('corredor_brand_side_end');
  const $name   = document.getElementById('corredor_nombre');
  const $disp   = document.getElementById('corredor_display_name');

  function applyPreview() {
    if ($nav) { $nav.style.backgroundColor = $navBg.value; $nav.style.color = $navTxt.value; }
    if ($sidebar) { $sidebar.style.background = `linear-gradient(135deg, ${$sideA.value} 0%, ${$sideB.value} 100%)`; }
    $title.textContent = ($disp.value || $name.value || 'Corredor').trim();
  }

  [$navBg,$navTxt,$sideA,$sideB,$name,$disp].forEach(el=>{
    if (!el) return;
    el.addEventListener('input', applyPreview);
    el.addEventListener('change', applyPreview);
  });

  if ($logoInput) {
    $logoInput.addEventListener('change', function () {
      const f = this.files?.[0]; if (!f) return;
      const reader = new FileReader();
      reader.onload = e => {
        $logoPreview.src = e.target.result;
        $logo.src = e.target.result;
        $logo.style.display = 'block';
      };
      reader.readAsDataURL(f);
    });
  }
  applyPreview();

  // -------- Transfer list de Compañías --------
  const data = <?php
    $out = array_map(fn($c)=>['id'=>(int)$c['cia_id'],'name'=>$c['cia_display_name'] ?: $c['cia_nombre']], $cias);
    echo json_encode($out, JSON_UNESCAPED_UNICODE);
  ?>;
  const preSeleccionadas = new Set(<?= json_encode(array_map('intval', old('cias', $ciasDelCorredor ?? []))) ?>);

  const ulDisp = document.getElementById('listaDisp');
  const ulSel  = document.getElementById('listaSel');
  const filtroDisp = document.getElementById('filtroDisp');
  const filtroSel  = document.getElementById('filtroSel');
  const hidden = document.getElementById('hiddenCias');

  function crearItem(item){
    const li=document.createElement('li');
    li.className='list-group-item d-flex justify-content-between align-items-center';
    li.dataset.id=item.id;
    li.dataset.name=(item.name||'').toLowerCase();
    li.tabIndex=0;
    li.innerHTML = `<span>${item.name}</span><span class="badge bg-light text-muted">#${item.id}</span>`;
    li.addEventListener('click',()=>li.classList.toggle('active'));
    li.addEventListener('dblclick',()=>{
      const id=+li.dataset.id;
      preSeleccionadas.has(id) ? preSeleccionadas.delete(id) : preSeleccionadas.add(id);
      render(); aplicarFiltros();
    });
    return li;
  }

  function render(){
    ulDisp.innerHTML=''; ulSel.innerHTML=''; hidden.innerHTML='';
    data.forEach(it=>{
      const el=crearItem(it);
      if (preSeleccionadas.has(it.id)) {
        ulSel.appendChild(el);
        const h=document.createElement('input');
        h.type='hidden'; h.name='cias[]'; h.value=it.id;
        hidden.appendChild(h);
      } else {
        ulDisp.appendChild(el);
      }
    });
  }

  function aplicarFiltros(){
    const q1=(filtroDisp.value||'').toLowerCase();
    const q2=(filtroSel.value||'').toLowerCase();
    Array.from(ulDisp.children).forEach(li=>li.style.display = li.dataset.name.includes(q1)?'':'none');
    Array.from(ulSel.children ).forEach(li=>li.style.display = li.dataset.name.includes(q2)?'':'none');
  }

  function idsActivos(ul){ return Array.from(ul.querySelectorAll('.active')).map(li=>+li.dataset.id); }

  document.getElementById('btnAdd').onclick    = ()=>{ idsActivos(ulDisp).forEach(id=>preSeleccionadas.add(id)); render(); aplicarFiltros(); };
  document.getElementById('btnAddAll').onclick = ()=>{ data.forEach(i=>preSeleccionadas.add(i.id)); render(); aplicarFiltros(); };
  document.getElementById('btnRem').onclick    = ()=>{ idsActivos(ulSel ).forEach(id=>preSeleccionadas.delete(id)); render(); aplicarFiltros(); };
  document.getElementById('btnRemAll').onclick = ()=>{ preSeleccionadas.clear(); render(); aplicarFiltros(); };

  filtroDisp.addEventListener('input', aplicarFiltros);
  filtroSel .addEventListener('input', aplicarFiltros);

  render(); aplicarFiltros();
})();
</script>
<?= $this->endSection() ?>
