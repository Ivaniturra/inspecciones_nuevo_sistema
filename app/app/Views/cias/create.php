<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Nueva Compañía
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Nueva Compañía</h1>
                <p class="text-muted mb-0">Completa los datos para crear una nueva compañía y define su identidad visual.</p>
            </div>
            <a href="<?= base_url('cias') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>
 

    <form action="<?= base_url('cias/store') ?>" method="post" enctype="multipart/form-data" id="ciaForm">
        <?= csrf_field() ?>

        <div class="row">
            <!-- Columna datos básicos -->
            <div class="col-lg-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-building me-2"></i> Datos de la Compañía
                    </div>
                    <div class="card-body">
                        <!-- Nombre legal -->
                        <div class="mb-3">
                            <label for="cia_nombre" class="form-label">
                                Nombre legal *
                            </label>
                            <input
                                type="text"
                                class="form-control <?= session('errors.cia_nombre') ? 'is-invalid' : '' ?>"
                                id="cia_nombre"
                                name="cia_nombre"
                                value="<?= esc(old('cia_nombre')) ?>"
                                placeholder="Ej. Empresa ABC S.A."
                                required
                            >
                            <div class="invalid-feedback"><?= session('errors.cia_nombre') ?></div>
                            <div class="form-text">Mínimo 3 caracteres, máximo 255.</div>
                        </div>

                        <!-- Nombre para mostrar (branding) -->
                        <div class="mb-3">
                            <label for="display_name" class="form-label">
                                Nombre para mostrar (marca)
                            </label>
                            <input
                                type="text"
                                class="form-control <?= session('errors.display_name') ? 'is-invalid' : '' ?>"
                                id="display_name"
                                name="display_name"
                                value="<?= esc(old('display_name')) ?>"
                                placeholder="Ej. InspectZu Chile"
                            >
                            <div class="invalid-feedback"><?= session('errors.display_name') ?></div>
                            <div class="form-text">Se usa en el header y títulos (opcional).</div>
                        </div>

                        <!-- Estado -->
                        <div class="mb-3">
                            <label for="cia_habil" class="form-label">Estado</label>
                            <select class="form-select" id="cia_habil" name="cia_habil">
                                <option value="1" <?= old('cia_habil','1') === '1' ? 'selected' : '' ?>>Activo</option>
                                <option value="0" <?= old('cia_habil') === '0' ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>

                        <!-- Dirección -->
                        <div class="mb-3">
                            <label for="cia_direccion" class="form-label">Dirección</label>
                            <textarea
                                class="form-control <?= session('errors.cia_direccion') ? 'is-invalid' : '' ?>"
                                id="cia_direccion"
                                name="cia_direccion"
                                rows="3"
                                placeholder="Dirección completa de la compañía"
                            ><?= esc(old('cia_direccion')) ?></textarea>
                            <div class="invalid-feedback"><?= session('errors.cia_direccion') ?></div>
                        </div>

                        <!-- Logo -->
                        <div class="mb-3">
                            <label for="cia_logo" class="form-label">
                                Logo de la compañía
                            </label>
                            <input
                                type="file"
                                class="form-control <?= session('errors.cia_logo') ? 'is-invalid' : '' ?>"
                                id="cia_logo"
                                name="cia_logo"
                                accept="image/png,image/jpeg,image/jpg, image/svg+xml"
                            >
                            <div class="invalid-feedback"><?= session('errors.cia_logo') ?></div>
                            <div class="form-text">PNG/JPG/SVG. Máx 2MB. Se mostrará arriba a la derecha en la vista previa.</div>
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
                            <!-- Topbar -->
                            <div class="col-12">
                                <label class="form-label mb-1">
                                    Barra superior (navbar)
                                </label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="input-group">
                                            <span class="input-group-text" title="Fondo barra superior">BG</span>
                                            <input
                                                type="color"
                                                class="form-control form-control-color p-1"
                                                id="brand_nav_bg"
                                                name="brand_nav_bg"
                                                value="<?= esc(old('brand_nav_bg', '#0d6efd')) ?>"
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
                                                id="brand_nav_text"
                                                name="brand_nav_text"
                                                value="<?= esc(old('brand_nav_text', '#ffffff')) ?>"
                                                title="Color de texto barra superior"
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sidebar (degradado) -->
                            <div class="col-12">
                                <label class="form-label mb-1">
                                    Sidebar (degradado)
                                </label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="input-group">
                                            <span class="input-group-text" title="Color inicio">A</span>
                                            <input
                                                type="color"
                                                class="form-control form-control-color p-1"
                                                id="brand_side_start"
                                                name="brand_side_start"
                                                value="<?= esc(old('brand_side_start', '#667eea')) ?>"
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
                                                id="brand_side_end"
                                                name="brand_side_end"
                                                value="<?= esc(old('brand_side_end', '#764ba2')) ?>"
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
                                            <?= esc(old('display_name') ?: (old('cia_nombre') ?: 'Marca')) ?>
                                        </strong>
                                    </div>
                                    <img id="preview-logo" src="" alt="Logo" style="height:24px; display:none;">
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
            </div>
        </div>

        <!-- Botones -->
        <div class="d-flex justify-content-end gap-2">
            <a href="<?= base_url('cias') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-times me-1"></i> Cancelar
            </a>
            <button type="reset" class="btn btn-outline-warning">
                <i class="fas fa-undo me-1"></i> Limpiar
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Guardar Compañía
            </button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    const $nav      = document.getElementById('preview-navbar');
    const $sidebar  = document.getElementById('preview-sidebar');
    const $title    = document.getElementById('preview-title');
    const $logo     = document.getElementById('preview-logo');

    const $navBg    = document.getElementById('brand_nav_bg');
    const $navTxt   = document.getElementById('brand_nav_text');
    const $sideA    = document.getElementById('brand_side_start');
    const $sideB    = document.getElementById('brand_side_end');

    const $ciaNombre   = document.getElementById('cia_nombre');
    const $displayName = document.getElementById('display_name');
    const $logoInput   = document.getElementById('cia_logo');

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

        const name = ($displayName.value || $ciaNombre.value || 'Marca').trim();
        $title.textContent = name;
    }

    // Eventos de cambios
    [$navBg, $navTxt, $sideA, $sideB].forEach(inp => {
        inp.addEventListener('input', applyPreview);
        inp.addEventListener('change', applyPreview);
    });
    [$ciaNombre, $displayName].forEach(inp => {
        inp.addEventListener('input', applyPreview);
        inp.addEventListener('change', applyPreview);
    });

    // Preview del logo
    $logoInput.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (!file) {
            $logo.style.display = 'none';
            $logo.src = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
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
