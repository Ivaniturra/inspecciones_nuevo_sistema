<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Editar Tipo de Inspección
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-edit text-warning me-2"></i>
                        Editar Tipo de Inspección
                    </h1>
                    <p class="text-muted mb-0">Modifica los datos de: <strong><?= esc($tipo['tipo_inspeccion_nombre']) ?></strong></p>
                </div>
                <div class="btn-group">
                    <a href="<?= base_url('tipos-inspeccion/show/' . $tipo['tipo_inspeccion_id']) ?>" class="btn btn-outline-info">
                        <i class="fas fa-eye me-1"></i>Ver detalles
                    </a>
                    <a href="<?= base_url('tipos-inspeccion') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Volver al listado
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
                        Formulario de Edición
                    </h5>
                </div>

                <div class="card-body">
                    <form action="<?= base_url('tipos-inspeccion/update/' . $tipo['tipo_inspeccion_id']) ?>" method="post" id="tipoInspeccionForm" novalidate>
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">

                        <div class="row">
                            <div class="col-lg-8">
                                <!-- Nombre del Tipo -->
                                <div class="mb-3">
                                    <label for="tipo_inspeccion_nombre" class="form-label">
                                        <i class="fas fa-car text-primary me-1"></i>
                                        Nombre del Tipo <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control <?= session('errors.tipo_inspeccion_nombre') ? 'is-invalid' : '' ?>"
                                        id="tipo_inspeccion_nombre"
                                        name="tipo_inspeccion_nombre"
                                        value="<?= esc(old('tipo_inspeccion_nombre', $tipo['tipo_inspeccion_nombre'])) ?>"
                                        placeholder="Ej. Liviano, Pesado, Motocicleta..."
                                        required
                                        maxlength="100">
                                    <div class="invalid-feedback">
                                        <?= esc(session('errors.tipo_inspeccion_nombre')) ?>
                                    </div>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Mínimo 2 caracteres, máximo 100.
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Clave -->
                                    <div class="col-md-6 mb-3">
                                        <label for="tipo_inspeccion_clave" class="form-label">
                                            <i class="fas fa-key text-secondary me-1"></i>
                                            Clave
                                        </label>
                                        <input
                                            type="text"
                                            class="form-control <?= session('errors.tipo_inspeccion_clave') ? 'is-invalid' : '' ?>"
                                            id="tipo_inspeccion_clave"
                                            name="tipo_inspeccion_clave"
                                            value="<?= esc(old('tipo_inspeccion_clave', $tipo['tipo_inspeccion_clave'])) ?>"
                                            placeholder="ej. liviano, pesado, moto"
                                            maxlength="50">
                                        <div class="invalid-feedback">
                                            <?= esc(session('errors.tipo_inspeccion_clave')) ?>
                                        </div>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Identificador único (opcional).
                                        </div>
                                    </div>

                                    <!-- Estado -->
                                    <div class="col-md-6 mb-3">
                                        <label for="tipo_inspeccion_activo" class="form-label">
                                            <i class="fas fa-toggle-on text-success me-1"></i>
                                            Estado
                                        </label>
                                        <select class="form-select" id="tipo_inspeccion_activo" name="tipo_inspeccion_activo">
                                            <?php $activoOld = old('tipo_inspeccion_activo', (string)$tipo['tipo_inspeccion_activo']); ?>
                                            <option value="1" <?= $activoOld === '1' ? 'selected' : '' ?>>✅ Activo</option>
                                            <option value="0" <?= $activoOld === '0' ? 'selected' : '' ?>>❌ Inactivo</option>
                                        </select>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Solo los tipos activos aparecen en los formularios.
                                        </div>
                                    </div>
                                </div>

                                <!-- Descripción -->
                                <div class="mb-4">
                                    <label for="tipo_inspeccion_descripcion" class="form-label">
                                        <i class="fas fa-align-left text-info me-1"></i>
                                        Descripción
                                    </label>
                                    <textarea
                                        class="form-control <?= session('errors.tipo_inspeccion_descripcion') ? 'is-invalid' : '' ?>"
                                        id="tipo_inspeccion_descripcion"
                                        name="tipo_inspeccion_descripcion"
                                        rows="3"
                                        maxlength="255"
                                        placeholder="Describe las características de este tipo de inspección..."><?= esc(old('tipo_inspeccion_descripcion', $tipo['tipo_inspeccion_descripcion'])) ?></textarea>
                                    <div class="invalid-feedback">
                                        <?= esc(session('errors.tipo_inspeccion_descripcion')) ?>
                                    </div>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Descripción opcional (máximo 255 caracteres).
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <!-- Preview Card -->
                                <div class="card bg-light mb-4">
                                    <div class="card-header bg-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-eye me-1"></i>
                                            Vista Previa
                                        </h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i id="preview-icon" class="fas fa-car fa-3x text-primary"></i>
                                        </div>
                                        <h6 id="preview-nombre" class="mb-2"><?= esc($tipo['tipo_inspeccion_nombre']) ?></h6>
                                        <div class="mb-2">
                                            <small id="preview-clave" class="badge bg-secondary"><?= esc($tipo['tipo_inspeccion_clave']) ?></small>
                                        </div>
                                        <p id="preview-descripcion" class="text-muted small mb-0"><?= esc($tipo['tipo_inspeccion_descripcion'] ?: 'Sin descripción') ?></p>
                                        <div class="mt-3">
                                            <span id="preview-estado" class="badge bg-success">Activo</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Info del registro -->
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-info-circle text-info me-1"></i>
                                            Información del registro
                                        </h6>
                                        <small class="text-muted">
                                            <strong>ID:</strong> <?= (int)$tipo['tipo_inspeccion_id'] ?><br>
                                            <?php if (!empty($tipo['tipo_inspeccion_created_at'])): ?>
                                                <strong>Creado:</strong> <?= date('d/m/Y H:i', strtotime($tipo['tipo_inspeccion_created_at'])) ?><br>
                                            <?php endif; ?>
                                            <?php if (!empty($tipo['tipo_inspeccion_updated_at'])): ?>
                                                <strong>Modificado:</strong> <?= date('d/m/Y H:i', strtotime($tipo['tipo_inspeccion_updated_at'])) ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-asterisk text-danger me-1"></i>
                                    Los campos marcados son obligatorios
                                </small>
                                <div class="btn-group">
                                    <a href="<?= base_url('tipos-inspeccion') ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save me-1"></i>Actualizar Tipo
                                    </button>
                                </div>
                            </div>
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
document.addEventListener('DOMContentLoaded', function () {
    const nombreInput = document.getElementById('tipo_inspeccion_nombre');
    const claveInput = document.getElementById('tipo_inspeccion_clave');
    const descripcionInput = document.getElementById('tipo_inspeccion_descripcion');
    const activoSelect = document.getElementById('tipo_inspeccion_activo');
    
    const previewNombre = document.getElementById('preview-nombre');
    const previewClave = document.getElementById('preview-clave');
    const previewDescripcion = document.getElementById('preview-descripcion');
    const previewIcon = document.getElementById('preview-icon');
    const previewEstado = document.getElementById('preview-estado');

    function updatePreview() {
        // Actualizar nombre
        const nombre = nombreInput.value.trim() || 'Nombre del tipo';
        previewNombre.textContent = nombre;

        // Actualizar clave
        const clave = claveInput.value.trim();
        if (clave) {
            previewClave.textContent = clave;
            previewClave.style.display = 'inline';
        } else {
            previewClave.style.display = 'none';
        }

        // Actualizar descripción
        const descripcion = descripcionInput.value.trim() || 'Sin descripción';
        previewDescripcion.textContent = descripcion;

        // Actualizar estado
        const activo = activoSelect.value === '1';
        if (activo) {
            previewEstado.className = 'badge bg-success';
            previewEstado.textContent = '✅ Activo';
        } else {
            previewEstado.className = 'badge bg-secondary';
            previewEstado.textContent = '❌ Inactivo';
        }

        // Actualizar ícono según el nombre
        const iconClass = getIconByName(nombre.toLowerCase());
        previewIcon.className = `fas ${iconClass} fa-3x text-primary`;
    }

    function getIconByName(name) {
        if (name.includes('liviano') || name.includes('auto') || name.includes('carro')) {
            return 'fa-car';
        } else if (name.includes('pesado') || name.includes('camion') || name.includes('truck')) {
            return 'fa-truck';
        } else if (name.includes('motocicleta') || name.includes('moto') || name.includes('motor')) {
            return 'fa-motorcycle';
        } else if (name.includes('bus') || name.includes('autobus')) {
            return 'fa-bus';
        } else {
            return 'fa-car';
        }
    }

    // Event listeners para actualizar preview
    nombreInput.addEventListener('input', updatePreview);
    claveInput.addEventListener('input', updatePreview);
    descripcionInput.addEventListener('input', updatePreview);
    activoSelect.addEventListener('change', updatePreview);

    // Validación del formulario
    const form = document.getElementById('tipoInspeccionForm');
    form.addEventListener('submit', function (e) {
        const nombre = nombreInput.value.trim();
        if (nombre.length < 2) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: 'El nombre del tipo debe tener al menos 2 caracteres.'
            });
            nombreInput.focus();
            return false;
        }

        // Mostrar loading
        Swal.fire({
            title: 'Actualizando...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
    });

    // Autofocus y selección
    if (nombreInput) {
        nombreInput.focus();
        nombreInput.select();
    }

    // Preview inicial
    updatePreview();

    // Character counter para descripción
    const maxChars = 255;
    descripcionInput.addEventListener('input', function() {
        const remaining = maxChars - this.value.length;
        const counterText = `${remaining} caracteres restantes`;
        
        let counter = this.nextElementSibling;
        if (!counter || !counter.classList.contains('char-counter')) {
            counter = document.createElement('small');
            counter.className = 'char-counter text-muted d-block mt-1';
            this.parentNode.appendChild(counter);
        }
        
        counter.textContent = counterText;
        
        if (remaining < 50) {
            counter.classList.add('text-warning');
        } else {
            counter.classList.remove('text-warning');
        }
    });

    // Auto-hide alerts
    setTimeout(() => {
        document.querySelectorAll('.alert-dismissible').forEach(alert => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        });
    }, 5000);
});
</script>
<?= $this->endSection() ?>