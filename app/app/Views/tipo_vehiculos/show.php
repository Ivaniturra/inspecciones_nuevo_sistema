<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Detalles del Tipo de Vehículo
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <?php 
                        // Ícono y color según el tipo
                        $iconInfo = match(strtolower($tipo['tipo_vehiculo_nombre'])) {
                            'liviano' => ['icon' => 'fa-car', 'color' => 'bg-primary'],
                            'pesado' => ['icon' => 'fa-truck', 'color' => 'bg-warning'], 
                            'motocicleta' => ['icon' => 'fa-motorcycle', 'color' => 'bg-info'],
                            default => ['icon' => 'fa-car', 'color' => 'bg-secondary']
                        };
                        ?>
                        <div class="<?= $iconInfo['color'] ?> text-white rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px;">
                            <i class="fas <?= $iconInfo['icon'] ?> fa-2x"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="h3 mb-0"><?= esc($tipo['tipo_vehiculo_nombre']) ?></h1>
                        <p class="text-muted mb-0">
                            <span class="badge bg-info">ID: <?= (int)$tipo['tipo_vehiculo_id'] ?></span>
                            <?php if (!empty($tipo['tipo_vehiculo_clave'])): ?>
                                <span class="badge bg-secondary ms-1"><?= esc($tipo['tipo_vehiculo_clave']) ?></span>
                            <?php endif; ?>
                            <span class="badge <?= $tipo['tipo_vehiculo_activo'] ? 'bg-success' : 'bg-danger' ?> ms-1">
                                <?= $tipo['tipo_vehiculo_activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </p>
                    </div>
                </div>

                <div class="btn-group">
                    <a href="<?= base_url('TipoVehiculos/edit/' . $tipo['tipo_vehiculo_id']) ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                    <a href="<?= base_url('TipoVehiculos') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i> Información del Tipo de Vehículo
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Nombre -->
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-car text-primary me-1"></i> Nombre del Tipo
                            </label>
                            <p class="form-control-plaintext"><?= esc($tipo['tipo_vehiculo_nombre']) ?></p>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-toggle-on text-success me-1"></i> Estado
                            </label>
                            <p class="form-control-plaintext">
                                <span class="badge fs-6 <?= $tipo['tipo_vehiculo_activo'] ? 'bg-success' : 'bg-danger' ?>">
                                    <i class="fas <?= $tipo['tipo_vehiculo_activo'] ? 'fa-check' : 'fa-times' ?> me-1"></i>
                                    <?= $tipo['tipo_vehiculo_activo'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </p>
                        </div>

                        <!-- Clave -->
                        <?php if (!empty($tipo['tipo_vehiculo_clave'])): ?>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-key text-secondary me-1"></i> Clave
                                </label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-secondary fs-6"><?= esc($tipo['tipo_vehiculo_clave']) ?></span>
                                </p>
                            </div>
                        <?php endif; ?>

                        <!-- Descripción -->
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-align-left text-info me-1"></i> Descripción
                            </label>
                            <?php if (!empty($tipo['tipo_vehiculo_descripcion'])): ?>
                                <p class="form-control-plaintext"><?= nl2br(esc($tipo['tipo_vehiculo_descripcion'])) ?></p>
                            <?php else: ?>
                                <p class="form-control-plaintext text-muted">
                                    <em><i class="fas fa-minus me-1"></i>Sin descripción registrada</em>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas de uso (placeholder) -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i> Estadísticas de Uso
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Estadísticas en desarrollo</h5>
                        <p class="text-muted">Aquí se mostrarán las estadísticas de uso de este tipo de vehículo</p>
                        <div class="row text-center mt-4">
                            <div class="col-md-4">
                                <div class="border-end">
                                    <h4 class="text-primary">0</h4>
                                    <small class="text-muted">Vehículos Registrados</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border-end">
                                    <h4 class="text-success">0</h4>
                                    <small class="text-muted">Inspecciones</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h4 class="text-info">0%</h4>
                                <small class="text-muted">Uso del Sistema</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="col-lg-4">
            <!-- Información del Sistema -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-cog me-2"></i> Información del Sistema
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary"><?= (int)$tipo['tipo_vehiculo_id'] ?></h4>
                                <small class="text-muted">ID Único</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="<?= $tipo['tipo_vehiculo_activo'] ? 'text-success' : 'text-danger' ?>">
                                <i class="fas <?= $tipo['tipo_vehiculo_activo'] ? 'fa-check' : 'fa-times' ?>"></i>
                            </h4>
                            <small class="text-muted">Estado</small>
                        </div>
                    </div>

                    <hr>

                    <div class="small text-muted">
                        <div class="mb-2">
                            <i class="fas fa-calendar-plus me-2"></i>
                            <strong>Creado:</strong><br>
                            <?= date('d/m/Y H:i:s', strtotime($tipo['tipo_vehiculo_created_at'])) ?>
                        </div>

                        <?php if (!empty($tipo['tipo_vehiculo_updated_at']) && $tipo['tipo_vehiculo_updated_at'] !== $tipo['tipo_vehiculo_created_at']): ?>
                            <div class="mb-2">
                                <i class="fas fa-calendar-edit me-2"></i>
                                <strong>Última modificación:</strong><br>
                                <?= date('d/m/Y H:i:s', strtotime($tipo['tipo_vehiculo_updated_at'])) ?>
                            </div>
                        <?php else: ?>
                            <div class="mb-2">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Estado:</strong><br>
                                Sin modificaciones desde la creación
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i> Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('TipoVehiculos/edit/' . $tipo['tipo_vehiculo_id']) ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i> Editar Tipo
                        </a>

                        <button type="button" 
                                class="btn <?= $tipo['tipo_vehiculo_activo'] ? 'btn-outline-danger' : 'btn-outline-success' ?>"
                                onclick="toggleStatus(<?= (int)$tipo['tipo_vehiculo_id'] ?>)">
                            <i class="fas <?= $tipo['tipo_vehiculo_activo'] ? 'fa-pause' : 'fa-play' ?> me-2"></i>
                            <?= $tipo['tipo_vehiculo_activo'] ? 'Desactivar' : 'Activar' ?>
                        </button>

                        <button type="button" class="btn btn-outline-info" onclick="duplicateType()">
                            <i class="fas fa-copy me-2"></i> Duplicar Tipo
                        </button>

                        <a href="<?= base_url('vehiculos/create?tipo=' . $tipo['tipo_vehiculo_id']) ?>" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i> Crear Vehículo de este Tipo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-car fa-3x text-danger mb-3"></i>
                    <h5>¿Eliminar tipo de vehículo?</h5>
                    <p class="mb-3">
                        Estás a punto de eliminar el tipo <strong>"<?= esc($tipo['tipo_vehiculo_nombre']) ?>"</strong>
                    </p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Advertencia:</strong> Esta acción no se puede deshacer.
                        <?php if (!empty($tipo['tipo_vehiculo_clave'])): ?>
                            <br>Se eliminará también la clave asociada.
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <form method="post" action="<?= base_url('TipoVehiculos/delete/' . $tipo['tipo_vehiculo_id']) ?>" style="display:inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash"></i> Sí, Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.card { 
    border: none; 
    border-radius: 15px; 
}
.card-header { 
    border-radius: 15px 15px 0 0 !important; 
    font-weight: 600; 
}
.form-control-plaintext { 
    background: #f8f9fa; 
    border: 1px solid #e9ecef; 
    border-radius: 8px; 
    padding: .75rem; 
    margin-bottom: 0; 
}
.badge.fs-6 { 
    font-size: .9rem !important; 
    padding: .5rem .75rem; 
}
.btn { 
    border-radius: 8px; 
}
.border-end { 
    border-right: 1px solid #dee2e6 !important; 
}
@media (max-width: 768px) {
    .border-end { 
        border-right: none !important; 
        border-bottom: 1px solid #dee2e6 !important; 
        padding-bottom: 1rem; 
        margin-bottom: 1rem; 
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Confirmar eliminación
    $('#confirmDeleteBtn').on('click', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        
        Swal.fire({
            title: 'Última confirmación',
            text: 'Esta acción eliminará permanentemente el tipo de vehículo',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar definitivamente',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Eliminando...',
                    text: 'Por favor espera',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                form.submit();
            }
        });
    });
});

// Cambiar estado del tipo de vehículo
function toggleStatus(id) {
    const isActive = <?= $tipo['tipo_vehiculo_activo'] ? 'true' : 'false' ?>;
    const action = isActive ? 'desactivar' : 'activar';
    const newStatus = isActive ? 'inactivo' : 'activo';
    
    Swal.fire({
        title: `¿${action.charAt(0).toUpperCase() + action.slice(1)} tipo de vehículo?`,
        text: `El tipo quedará ${newStatus}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: isActive ? '#dc3545' : '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Sí, ${action}`,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Procesando...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.post('<?= base_url('TipoVehiculos/toggleStatus') ?>/' + id, {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            })
            .done(function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Estado actualizado',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            })
            .fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor'
                });
            });
        }
    });
}

// Duplicar tipo de vehículo
function duplicateType() {
    Swal.fire({
        title: 'Duplicar tipo de vehículo',
        text: 'Ingresa el nombre para el nuevo tipo',
        input: 'text',
        inputValue: '<?= esc($tipo['tipo_vehiculo_nombre']) ?> - Copia',
        showCancelButton: true,
        confirmButtonText: 'Duplicar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value || value.length < 2) {
                return 'El nombre debe tener al menos 2 caracteres'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirigir al formulario de creación con datos prellenados
            const params = new URLSearchParams({
                nombre: result.value,
                descripcion: '<?= esc($tipo['tipo_vehiculo_descripcion']) ?>',
                duplicate: 'true'
            });
            window.location.href = '<?= base_url('TipoVehiculos/create') ?>?' + params;
        }
    });
}
</script>
<?= $this->endSection() ?>