 <?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Nuevo Corredor
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Nuevo Corredor</h1>
                <p class="text-muted mb-0">Completa los datos para crear un nuevo corredor de seguros.</p>
            </div>
            <a href="<?= base_url('corredores') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Se encontraron los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('corredores/store') ?>" method="post" enctype="multipart/form-data" id="corredorForm">
        <?= csrf_field() ?>

        <div class="row">
            <!-- Columna datos básicos -->
            <div class="col-lg-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-user-tie me-2"></i> Datos del Corredor
                    </div>
                    <div class="card-body">
                        <!-- Nombre -->
                        <div class="mb-3">
                            <label for="corredor_nombre" class="form-label">Nombre del corredor *</label>
                            <input type="text"
                                   class="form-control <?= session('errors.corredor_nombre') ? 'is-invalid' : '' ?>"
                                   id="corredor_nombre" name="corredor_nombre"
                                   value="<?= esc(old('corredor_nombre')) ?>"
                                   placeholder="Ej. Corredora de Seguros ABC S.A." required>
                            <div class="invalid-feedback"><?= session('errors.corredor_nombre') ?></div>
                        </div>

                        <!-- Nombre comercial -->
                        <div class="mb-3">
                            <label for="corredor_display_name" class="form-label">Nombre comercial</label>
                            <input type="text"
                                   class="form-control <?= session('errors.corredor_display_name') ? 'is-invalid' : '' ?>"
                                   id="corredor_display_name" name="corredor_display_name"
                                   value="<?= esc(old('corredor_display_name')) ?>"
                                   placeholder="Ej. ABC Seguros">
                            <div class="invalid-feedback"><?= session('errors.corredor_display_name') ?></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="corredor_email" class="form-label">Email</label>
                                <input type="email"
                                       class="form-control <?= session('errors.corredor_email') ? 'is-invalid' : '' ?>"
                                       id="corredor_email" name="corredor_email"
                                       value="<?= esc(old('corredor_email')) ?>"
                                       placeholder="contacto@corredor.cl">
                                <div class="invalid-feedback"><?= session('errors.corredor_email') ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="corredor_telefono" class="form-label">Teléfono</label>
                                <input type="text"
                                       class="form-control <?= session('errors.corredor_telefono') ? 'is-invalid' : '' ?>"
                                       id="corredor_telefono" name="corredor_telefono"
                                       value="<?= esc(old('corredor_telefono')) ?>"
                                       placeholder="+56 9 1234 5678">
                                <div class="invalid-feedback"><?= session('errors.corredor_telefono') ?></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="corredor_rut" class="form-label">RUT</label>
                                <input type="text"
                                       class="form-control <?= session('errors.corredor_rut') ? 'is-invalid' : '' ?>"
                                       id="corredor_rut" name="corredor_rut"
                                       value="<?= esc(old('corredor_rut')) ?>" placeholder="12.345.678-9">
                                <div class="invalid-feedback"><?= session('errors.corredor_rut') ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="corredor_habil" class="form-label">Estado</label>
                                <select class="form-select" id="corredor_habil" name="corredor_habil">
                                    <option value="1" <?= old('corredor_habil','1') === '1' ? 'selected' : '' ?>>Activo</option>
                                    <option value="0" <?= old('corredor_habil') === '0' ? 'selected' : '' ?>>Inactivo</option>
                                </select>
                            </div>
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
                            <div class="invalid-feedback d-block" id="errCias"><?= session('errors.cias') ?></div>
                            <?php else: ?>
                            <div class="invalid-feedback d-none" id="errCias">Selecciona al menos una compañía.</div>
                            <?php endif; ?>
                            <?php if (session('errors.cias')): ?>
                                <div class="invalid-feedback d-block"><?= session('errors.cias') ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Dirección -->
                        <div class="mb-3">
                            <label for="corredor_direccion" class="form-label">Dirección</label>
                            <textarea class="form-control <?= session('errors.corredor_direccion') ? 'is-invalid' : '' ?>"
                                      id="corredor_direccion" name="corredor_direccion" rows="3"
                                      placeholder="Dirección completa del corredor"><?= esc(old('corredor_direccion')) ?></textarea>
                            <div class="invalid-feedback"><?= session('errors.corredor_direccion') ?></div>
                        </div>

                        <!-- Logo -->
                        <div class="mb-3">
                            <label for="corredor_logo" class="form-label">Logo del corredor</label>
                            <input type="file"
                                   class="form-control <?= session('errors.corredor_logo') ? 'is-invalid' : '' ?>"
                                   id="corredor_logo" name="corredor_logo"
                                   accept="image/png,image/jpeg,image/jpg,image/svg+xml">
                            <div class="invalid-feedback"><?= session('errors.corredor_logo') ?></div>
                            <div class="form-text">PNG/JPG/SVG. Máx 2MB.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna marca y colores -->
            <div class="col-lg-5">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
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
                                            <input type="color" class="form-control form-control-color p-1"
                                                   id="corredor_brand_nav_bg" name="corredor_brand_nav_bg"
                                                   value="<?= esc(old('corredor_brand_nav_bg', '#0d6efd')) ?>">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group">
                                            <span class="input-group-text">TXT</span>
                                            <input type="color" class="form-control form-control-color p-1"
                                                   id="corredor_brand_nav_text" name="corredor_brand_nav_text"
                                                   value="<?= esc(old('corredor_brand_nav_text', '#ffffff')) ?>">
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
                                            <input type="color" class="form-control form-control-color p-1"
                                                   id="corredor_brand_side_start" name="corredor_brand_side_start"
                                                   value="<?= esc(old('corredor_brand_side_start', '#667eea')) ?>">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group">
                                            <span class="input-group-text">B</span>
                                            <input type="color" class="form-control form-control-color p-1"
                                                   id="corredor_brand_side_end" name="corredor_brand_side_end"
                                                   value="<?= esc(old('corredor_brand_side_end', '#764ba2')) ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vista previa simple -->
                        <div class="mt-4">
                            <label class="form-label">Vista previa</label>
                            <div class="border rounded overflow-hidden">
                                <div id="preview-navbar" class="d-flex align-items-center justify-content-between px-3 py-2">
                                    <strong id="preview-title" class="small mb-0">
                                        <?= esc(old('corredor_display_name') ?: (old('corredor_nombre') ?: 'Corredor')) ?>
                                    </strong>
                                    <img id="preview-logo" src="" alt="Logo" style="height:24px; display:none;">
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
            </div>
        </div>

        <!-- Botones -->
        <div class="d-flex justify-content-end gap-2">
            <a href="<?= base_url('corredores') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-times me-1"></i> Cancelar
            </a>
            <button type="reset" class="btn btn-outline-warning">
                <i class="fas fa-undo me-1"></i> Limpiar
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Guardar Corredor
            </button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    // -------- Preview colores --------
    const $nav      = document.getElementById('preview-navbar');
    const $sidebar  = document.getElementById('preview-sidebar');
    const $title    = document.getElementById('preview-title');
    const $logo     = document.getElementById('preview-logo');

    const $navBg    = document.getElementById('corredor_brand_nav_bg');
    const $navTxt   = document.getElementById('corredor_brand_nav_text');
    const $sideA    = document.getElementById('corredor_brand_side_start');
    const $sideB    = document.getElementById('corredor_brand_side_end');

    const $corredorNombre   = document.getElementById('corredor_nombre');
    const $displayName      = document.getElementById('corredor_display_name');
    const $logoInput        = document.getElementById('corredor_logo');

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
    [$navBg, $navTxt, $sideA, $sideB, $corredorNombre, $displayName].forEach(inp => {
        inp.addEventListener('input', applyPreview);
        inp.addEventListener('change', applyPreview);
    });
    if ($logoInput) {
        $logoInput.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) { $logo.style.display = 'none'; $logo.src = ''; return; }
            const reader = new FileReader();
            reader.onload = e => { $logo.src = e.target.result; $logo.style.display = 'block'; };
            reader.readAsDataURL(file);
        });
    }
    applyPreview();

    // -------- Transfer list de Compañías --------
    const data = <?php
      // $cias debe venir del controlador: getActiveCias() o equivalente
      $out = array_map(fn($c)=>['id'=>(int)$c['cia_id'],'name'=>$c['cia_display_name'] ?: $c['cia_nombre']], $cias);
      echo json_encode($out, JSON_UNESCAPED_UNICODE);
    ?>;
    const preSeleccionadas = new Set(<?= json_encode(array_map('intval', old('cias', []))) ?>);

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
    const form = document.getElementById('corredorForm');
    const errCias = document.getElementById('errCias');

    function markCiasInvalid(on) {
        if (!errCias) return;
        errCias.classList.toggle('d-none', !on);
        errCias.classList.toggle('d-block', on);
        [ulDisp, ulSel].forEach(ul=>{
        ul.classList.toggle('border', on);
        ul.classList.toggle('border-danger', on);
        });
    }

    form.addEventListener('submit', function(e){
        if (preSeleccionadas.size < 1) {
        e.preventDefault();
        markCiasInvalid(true);
        ulSel.scrollIntoView({behavior:'smooth', block:'center'});
        } else {
        markCiasInvalid(false);
        }
    });

    // Si el usuario selecciona algo luego del error, esconder mensaje
    ['click','dblclick','input'].forEach(ev=>{
        ulDisp.addEventListener(ev, ()=>{ if (preSeleccionadas.size >= 1) markCiasInvalid(false); });
        ulSel .addEventListener(ev, ()=>{ if (preSeleccionadas.size >= 1) markCiasInvalid(false); });
    });

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
