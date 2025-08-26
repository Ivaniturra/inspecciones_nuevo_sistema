 <?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Editar Compañía
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Editar Compañía</h1>
                    <p class="text-muted">Modifica los datos de: <strong><?= esc($cia['cia_nombre']) ?></strong></p>
                </div>
                <div class="btn-group">
                    <a href="<?= base_url('cias/show/' . $cia['cia_id']) ?>" class="btn btn-outline-info">
                        <i class="fas fa-eye"></i> Ver detalles
                    </a>
                    <a href="<?= base_url('cias') ?>" class="btn btn-outline-secondary">
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

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Editar Compañía
                    </h5>
                </div>

                <div class="card-body">
                    <form action="<?= base_url('cias/update/' . $cia['cia_id']) ?>" method="post" enctype="multipart/form-data" id="ciaForm">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">

                        <!-- Datos básicos -->
                        <div class="row">
                            <!-- Nombre de la Compañía -->
                            <div class="col-md-6 mb-3">
                                <label for="cia_nombre" class="form-label">
                                    <i class="fas fa-building text-primary me-1"></i>
                                    Nombre de la Compañía *
                                </label>
                                <input type="text"
                                       class="form-control <?= (session('errors.cia_nombre')) ? 'is-invalid' : '' ?>"
                                       id="cia_nombre"
                                       name="cia_nombre"
                                       value="<?= old('cia_nombre', $cia['cia_nombre']) ?>"
                                       placeholder="Ej. Empresa ABC S.A."
                                       required>
                                <div class="invalid-feedback">
                                    <?= session('errors.cia_nombre') ?>
                                </div>
                                <div class="form-text">Mínimo 3 caracteres, máximo 255</div>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-3 mb-3">
                                <label for="cia_habil" class="form-label">
                                    <i class="fas fa-toggle-on text-success me-1"></i>
                                    Estado
                                </label>
                                <select class="form-select" id="cia_habil" name="cia_habil">
                                    <option value="1" <?= old('cia_habil', (string)$cia['cia_habil']) === '1' ? 'selected' : '' ?>>Activo</option>
                                    <option value="0" <?= old('cia_habil', (string)$cia['cia_habil']) === '0' ? 'selected' : '' ?>>Inactivo</option>
                                </select>
                            </div>

                            <!-- Display Name (título de marca) -->
                            <div class="col-md-3 mb-3">
                                <label for="cia_display_name" class="form-label">
                                    <i class="fas fa-signature text-info me-1"></i>
                                    Nombre comercial / Título
                                </label>
                                <input type="text"
                                       class="form-control <?= (session('errors.cia_display_name')) ? 'is-invalid' : '' ?>"
                                       id="cia_display_name"
                                       name="cia_display_name"
                                       value="<?= old('cia_display_name', $cia['cia_display_name'] ?? $cia['cia_nombre']) ?>"
                                       placeholder="Texto que verás en el header">
                                <div class="invalid-feedback">
                                    <?= session('errors.cia_display_name') ?>
                                </div>
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="mb-3">
                            <label for="cia_direccion" class="form-label">
                                <i class="fas fa-map-marker-alt text-info me-1"></i>
                                Dirección
                            </label>
                            <textarea class="form-control <?= (session('errors.cia_direccion')) ? 'is-invalid' : '' ?>"
                                      id="cia_direccion"
                                      name="cia_direccion"
                                      rows="3"
                                      placeholder="Dirección completa de la compañía"><?= old('cia_direccion', $cia['cia_direccion']) ?></textarea>
                            <div class="invalid-feedback">
                                <?= session('errors.cia_direccion') ?>
                            </div>
                            <div class="form-text">Máximo 500 caracteres (opcional)</div>
                        </div>

                        <!-- Branding y Apariencia -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-palette me-1 text-warning"></i>
                                    Branding y Apariencia
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <!-- Logo actual -->
                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <i class="fas fa-image text-warning me-1"></i>
                                            Logo actual
                                        </label>
                                        <div class="border rounded p-3 bg-white text-center">
                                            <img id="logoPreview"
                                                 src="<?= !empty($cia['cia_logo']) ? base_url('uploads/logos/' . $cia['cia_logo']) : 'https://via.placeholder.com/200x120?text=Sin+logo' ?>"
                                                 alt="Logo"
                                                 class="img-fluid"
                                                 style="max-height: 120px; object-fit: contain;">
                                            <?php if (!empty($cia['cia_logo'])): ?>
                                                <div class="mt-2">
                                                    <small class="text-muted"><i class="fas fa-file me-1"></i><?= esc($cia['cia_logo']) ?></small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <label for="cia_logo" class="form-label mt-3">
                                            <i class="fas fa-upload text-success me-1"></i>
                                            <?= !empty($cia['cia_logo']) ? 'Cambiar Logo' : 'Subir Logo' ?>
                                        </label>
                                        <input type="file"
                                               class="form-control <?= (session('errors.cia_logo')) ? 'is-invalid' : '' ?>"
                                               id="cia_logo"
                                               name="cia_logo"
                                               accept="image/jpeg,image/jpg,image/png">
                                        <div class="invalid-feedback">
                                            <?= session('errors.cia_logo') ?>
                                        </div>
                                        <div class="form-text">
                                            Formatos: JPG/JPEG/PNG. Máx 2MB.
                                            <?= !empty($cia['cia_logo']) ? 'Deja vacío para mantener el actual.' : '' ?>
                                        </div>
                                    </div>

                                    <!-- Colores -->
                                    <div class="col-md-8">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Color barra superior (fondo)</label>
                                                <input type="color"
                                                       class="form-control form-control-color <?= (session('errors.cia_brand_nav_bg')) ? 'is-invalid' : '' ?>"
                                                       id="cia_brand_nav_bg"
                                                       name="cia_brand_nav_bg"
                                                       value="<?= old('cia_brand_nav_bg', $cia['cia_brand_nav_bg'] ?? '#0D6EFD') ?>">
                                                <div class="invalid-feedback"><?= session('errors.cia_brand_nav_bg') ?></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Color barra superior (texto/iconos)</label>
                                                <input type="color"
                                                       class="form-control form-control-color <?= (session('errors.cia_brand_nav_text')) ? 'is-invalid' : '' ?>"
                                                       id="cia_brand_nav_text"
                                                       name="cia_brand_nav_text"
                                                       value="<?= old('cia_brand_nav_text', $cia['cia_brand_nav_text'] ?? '#FFFFFF') ?>">
                                                <div class="invalid-feedback"><?= session('errors.cia_brand_nav_text') ?></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Sidebar degradado (inicio)</label>
                                                <input type="color"
                                                       class="form-control form-control-color <?= (session('errors.cia_brand_side_start')) ? 'is-invalid' : '' ?>"
                                                       id="cia_brand_side_start"
                                                       name="cia_brand_side_start"
                                                       value="<?= old('cia_brand_side_start', $cia['cia_brand_side_start'] ?? '#667EEA') ?>">
                                                <div class="invalid-feedback"><?= session('errors.cia_brand_side_start') ?></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Sidebar degradado (fin)</label>
                                                <input type="color"
                                                       class="form-control form-control-color <?= (session('errors.cia_brand_side_end')) ? 'is-invalid' : '' ?>"
                                                       id="cia_brand_side_end"
                                                       name="cia_brand_side_end"
                                                       value="<?= old('cia_brand_side_end', $cia['cia_brand_side_end'] ?? '#764BA2') ?>">
                                                <div class="invalid-feedback"><?= session('errors.cia_brand_side_end') ?></div>
                                            </div>
                                        </div>

                                        <!-- Preview en vivo -->
                                        <div class="mt-4">
                                            <label class="form-label">Vista previa</label>
                                            <div class="border rounded overflow-hidden">
                                                <div id="previewTopbar" class="p-2 d-flex align-items-center" style="background:#0D6EFD;color:#fff;">
                                                    <img id="previewLogo"
                                                         src="<?= !empty($cia['cia_logo']) ? base_url('uploads/logos/' . $cia['cia_logo']) : 'https://via.placeholder.com/32x32?text=IZ' ?>"
                                                         alt="logo" class="me-2" style="width:32px;height:32px;object-fit:contain;">
                                                    <strong id="previewTitle"><?= esc(old('cia_display_name', $cia['cia_display_name'] ?? $cia['cia_nombre'])) ?></strong>
                                                </div>
                                                <div id="previewSidebar" class="p-3 text-white" style="background:linear-gradient(135deg,#667EEA,#764BA2); min-height:100px;">
                                                    <div class="mb-2"><i class="fas fa-circle me-2"></i>Menú 1</div>
                                                    <div class="mb-2"><i class="fas fa-circle me-2"></i>Menú 2</div>
                                                    <div><i class="fas fa-circle me-2"></i>Menú 3</div>
                                                </div>
                                            </div>
                                            <small class="text-muted">Esto es solo una referencia visual.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-info-circle text-info me-1"></i>
                                            Información del registro
                                        </h6>
                                        <small class="text-muted">
                                            <strong>ID:</strong> <?= $cia['cia_id'] ?><br>
                                            <strong>Creado:</strong> <?= date('d/m/Y H:i', strtotime($cia['cia_created_at'])) ?><br>
                                            <?php if (!empty($cia['cia_updated_at'])): ?>
                                                <strong>Última modificación:</strong> <?= date('d/m/Y H:i', strtotime($cia['cia_updated_at'])) ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= base_url('cias') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a> 
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar Compañía
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function() {
    const navBg   = document.getElementById('cia_brand_nav_bg');
    const navText = document.getElementById('cia_brand_nav_text');
    const sideA   = document.getElementById('cia_brand_side_start');
    const sideB   = document.getElementById('cia_brand_side_end');
    const disp    = document.getElementById('cia_display_name');

    const prevTop = document.getElementById('previewTopbar');
    const prevSide= document.getElementById('previewSidebar');
    const prevTit = document.getElementById('previewTitle');
    const logoInp = document.getElementById('cia_logo');
    const logoTag = document.getElementById('logoPreview');
    const prevLogo= document.getElementById('previewLogo');

    function applyPreview() {
        if (navBg)   prevTop.style.background = navBg.value || '#0D6EFD';
        if (navText) prevTop.style.color      = navText.value || '#FFFFFF';
        if (sideA && sideB) prevSide.style.background = 'linear-gradient(135deg,' + sideA.value + ',' + sideB.value + ')';
        if (disp)    prevTit.textContent = disp.value || 'InspectZu';
    }

    [navBg, navText, sideA, sideB, disp].forEach(function(el){
        if (el) el.addEventListener('input', applyPreview);
    });

    // Preview de logo seleccionado
    if (logoInp) {
        logoInp.addEventListener('change', function() {
            const file = this.files && this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                if (logoTag) logoTag.src = e.target.result;
                if (prevLogo) prevLogo.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    // Restaurar preview al reset
    const btnReset = document.getElementById('btnReset');
    if (btnReset) {
        btnReset.addEventListener('click', function() {
            setTimeout(applyPreview, 0); // espera a que el form haga reset
        });
    }

    // Inicial
    applyPreview();
})();
</script>
<?= $this->endSection() ?>