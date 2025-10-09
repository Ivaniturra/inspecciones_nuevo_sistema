<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Nuevo Tipo de Vehículo
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Nuevo Tipo de Vehículo</h1>
                    <p class="text-muted">Agrega un nuevo tipo de vehículo al catálogo del sistema</p>
                </div>
                <a href="<?= base_url('TipoVehiculos') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al listado
                </a>
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
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-car me-2"></i>
                        Datos del Tipo de Vehículo
                    </h5>
                </div>

                <div class="card-body">
                    <form action="<?= base_url('TipoVehiculos/store') ?>" method="post" id="tipoVehiculoForm" novalidate>
                        <?= csrf_field() ?>

                        <!-- Nombre del Tipo -->
                        <div class="mb-3">
                            <label for="tipo_vehiculo_nombre" class="form-label">
                                <i class="fas fa-car text-primary me-1"></i>
                                Nombre del Tipo *
                            </label>
                            <input
                                type="text"
                                class="form-control <?= session('errors.tipo_vehiculo_nombre') ? 'is-invalid' : '' ?>"
                                id="tipo_vehiculo_nombre"
                                name="tipo_vehiculo_nombre"
                                value="<?= esc(old('tipo_vehiculo_nombre')) ?>"
                                placeholder="Ej. Automóvil, Camión, Motocicleta..."
                                required>
                            <div class="invalid-feedback">
                                <?= esc(session('errors.tipo_vehiculo_nombre')) ?>
                            </div>
                            <div class="form-text">Mínimo 2 caracteres, máximo 100.</div>
                        </div>

                        <div class="row">
                            <!-- Clave (Opcional) -->
                            <div class="col-md-6 mb-3">
                                <label for="tipo_vehiculo_clave" class="form-label">
                                    <i class="fas fa-key text-secondary me-1"></i>
                                    Clave (opcional)
                                </label>
                                <input
                                    type="text"
                                    class="form-control <?= session('errors.tipo_vehiculo_clave') ? 'is-invalid' : '' ?>"
                                    id="tipo_vehiculo_clave"
                                    name="tipo_vehiculo_clave"
                                    value="<?= esc(old('tipo_vehiculo_clave')) ?>"
                                    placeholder="ej. liviano, pesado, motocicleta">
                                <div class="invalid-feedback">
                                    <?= esc(session('errors.tipo_vehiculo_clave')) ?>
                                </div>
                                <div class="form-text">Si no se especifica, se generará automáticamente desde el nombre.</div>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6 mb-3">
                                <label for="tipo_vehiculo_activo" class="form-label">
                                    <i class="fas fa-toggle-on text-success me-1"></i>
                                    Estado
                                </label>
                                <select class="form-select" id="tipo_vehiculo_activo" name="tipo_vehiculo_activo">
                                    <option value="1" <?= old('tipo_vehiculo_activo', '1') === '1' ? 'selected' : '' ?>>? Activo</option>
                                    <option value="0" <?= old('tipo_vehiculo_activo') === '0' ? 'selected' : '' ?>>? Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-4">
                            <label for="tipo_vehiculo_descripcion" class="form-label">
                                <i class="fas fa-align-left text-info me-1"></i>
                                Descripción
                            </label>
                            <textarea
                                class="form-control <?= session('errors.tipo_vehiculo_descripcion') ? 'is-invalid' : '' ?>"
                                id="tipo_vehiculo_descripcion"
                                name="tipo_vehiculo_descripcion"
                                rows="3"
                                placeholder="Describe las características de este tipo de vehículo..."><?= esc(old('tipo_vehiculo_descripcion')) ?></textarea>
                            <div class="invalid-feedback">
                                <?= esc(session('errors.tipo_vehiculo_descripcion')) ?>
                            </div>
                            <div class="form-text">Descripción opcional del tipo de vehículo (máximo 255 caracteres).</div>
                        </div>

                        <!-- Preview Card -->
                        <div class="card bg-light mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-eye me-1"></i>
                                    Vista Previa
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i id="preview-icon" class="fas fa-car fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 id="preview-nombre" class="mb-1">Nombre del tipo</h6>
                                        <small id="preview-clave" class="text-muted badge bg-secondary">clave</small>
                                        <p id="preview-descripcion" class="text-muted small mb-0 mt-1">Descripción del tipo de vehículo...</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= base_url('TipoVehiculos') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="reset" class="btn btn-outline-warning">
                                <i class="fas fa-undo"></i> Limpiar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Tipo
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
document.addEventListener('DOMContentLoaded', function () {
    const nombreInput = document.getElementById('tipo_vehiculo_nombre');
    const claveInput = document.getElementById('tipo_vehiculo_clave');
    const descripcionInput = document.getElementById('tipo_vehiculo_descripcion');
    
    const previewNombre = document.getElementById('preview-nombre');
    const previewClave = document.getElementById('preview-clave');
    const previewDescripcion = document.getElementById('preview-descripcion');
    const previewIcon = document.getElementById('preview-icon');

    function updatePreview() {
        // Actualizar nombre
        const nombre = nombreInput.value.trim() || 'Nombre del tipo';
        previewNombre.textContent = nombre;

        // Actualizar clave
        const clave = claveInput.value.trim() || 'clave';
        previewClave.textContent = clave;
        previewClave.style.display = clave === 'clave' ? 'none' : 'inline';

        // Actualizar descripción
        const descripcion = descripcionInput.value.trim() || 'Descripción del tipo de vehículo...';
        previewDescripcion.textContent = descripcion;

        // Actualizar ícono según el nombre
        const iconClass = getIconByName(nombre.toLowerCase());
        previewIcon.className = `fas ${iconClass} fa-2x text-primary`;
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

    // Auto-generar clave desde nombre si está vacía
    nombreInput.addEventListener('input', function() {
        if (claveInput.value.trim() === '') {
            const clave = this.value.toLowerCase()
                .replace(/[áàäâ]/g, 'a')
                .replace(/[éèëê]/g, 'e')
                .replace(/[íìïî]/g, 'i')
                .replace(/[óòöô]/g, 'o')
                .replace(/[úùüû]/g, 'u')
                .replace(/ñ/g, 'n')
                .replace(/[^a-z0-9]/g, '_')
                .replace(/_+/g, '_')
                .replace(/^_|_$/g, '');
            claveInput.value = clave;
        }
        updatePreview();
    });

    claveInput.addEventListener('input', updatePreview);
    descripcionInput.addEventListener('input', updatePreview);

    // Validación del formulario
    const form = document.getElementById('tipoVehiculoForm');
    form.addEventListener('submit', function (e) {
        const nombre = nombreInput.value.trim();
        if (nombre.length < 2) {
            e.preventDefault();
            alert('El nombre del tipo debe tener al menos 2 caracteres.');
            return;
        }
    });

    // Autofocus
    if (nombreInput) {
        nombreInput.focus();
    }

    // Preview inicial
    updatePreview();
});
</script>
<?= $this->endSection() ?>