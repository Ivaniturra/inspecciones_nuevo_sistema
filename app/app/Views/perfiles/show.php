<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Detalles del Perfil
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <?php if ($perfil['perfil_tipo'] === 'compania'): ?>
                            <div class="bg-info rounded d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-building text-white fa-2x"></i>
                            </div>
                        <?php else: ?>
                            <div class="bg-warning rounded d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-shield-alt text-white fa-2x"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <h1 class="h3 mb-0"><?= esc($perfil['perfil_nombre']) ?></h1>
                        <p class="text-muted mb-0">
                            <?php if ($perfil['perfil_tipo'] === 'compania'): ?>
                                <span class="badge bg-info">🏢 Perfil de Compañía</span>
                            <?php else: ?>
                                <span class="badge bg-warning">🛡️ Perfil Interno</span>
                            <?php endif; ?>
                            
                            <span class="badge <?= $perfil['perfil_habil'] ? 'bg-success' : 'bg-danger' ?> ms-2">
                                <?= $perfil['perfil_habil'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                            
                            <span class="badge bg-secondary ms-2">
                                Nivel <?= $perfil['perfil_nivel'] ?>
                            </span>
                        </p>
                    </div>
                </div>
                
                <div class="btn-group">
                    <a href="<?= base_url('perfiles/edit/' . $perfil['perfil_id']) ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                    <a href="<?= base_url('perfiles') ?>" class="btn btn-outline-secondary">
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
                        <i class="fas fa-info-circle me-2"></i>
                        Información del Perfil
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user-tag text-primary me-1"></i>
                                Nombre del Perfil
                            </label>
                            <p class="form-control-plaintext"><?= esc($perfil['perfil_nombre']) ?></p>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-layer-group text-info me-1"></i>
                                Tipo de Perfil
                            </label>
                            <p class="form-control-plaintext">
                                <?php if ($perfil['perfil_tipo'] === 'compania'): ?>
                                    <span class="badge fs-6 bg-info">
                                        <i class="fas fa-building me-1"></i>
                                        Perfil de Compañía
                                    </span>
                                <?php else: ?>
                                    <span class="badge fs-6 bg-warning">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Perfil Interno
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-star text-warning me-1"></i>
                                Nivel de Acceso
                            </label>
                            <p class="form-control-plaintext">
                                <?php
                                $nivel = $perfil['perfil_nivel'];
                                $estrellas = str_repeat('⭐', $nivel);
                                $nombres = [1 => 'Básico', 2 => 'Intermedio', 3 => 'Avanzado', 4 => 'Administrador'];
                                ?>
                                <span class="fs-5"><?= $estrellas ?></span>
                                <span class="badge bg-secondary ms-2">Nivel <?= $nivel ?> - <?= $nombres[$nivel] ?></span>
                            </p>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-toggle-on text-success me-1"></i>
                                Estado
                            </label>
                            <p class="form-control-plaintext">
                                <span class="badge fs-6 <?= $perfil['perfil_habil'] ? 'bg-success' : 'bg-danger' ?>">
                                    <i class="fas <?= $perfil['perfil_habil'] ? 'fa-check' : 'fa-times' ?> me-1"></i>
                                    <?= $perfil['perfil_habil'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </p>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-align-left text-info me-1"></i>
                                Descripción
                            </label>
                            <?php if (!empty($perfil['perfil_descripcion'])): ?>
                                <p class="form-control-plaintext"><?= nl2br(esc($perfil['perfil_descripcion'])) ?></p>
                            <?php else: ?>
                                <p class="form-control-plaintext text-muted">
                                    <em><i class="fas fa-minus me-1"></i>Sin descripción registrada</em>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Permisos -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shield-alt me-2"></i>
                        Permisos Asignados
                    </h5>
                </div>
                <div class="card-body">
                    <?php 
                    $permisos = $perfil['perfil_permisos'] ?? [];
                    $permisosActivos = array_filter($permisos, function($value) { return $value === true; });
                    ?>
                    
                    <?php if (empty($permisosActivos)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Sin permisos asignados</h5>
                            <p class="text-muted">Este perfil no tiene permisos específicos configurados</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($permisosActivos as $permiso => $valor): ?>
                                <?php if ($valor): ?>
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <span><?= esc($permisosDisponibles[$permiso] ?? ucfirst(str_replace('_', ' ', $permiso))) ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Panel lateral -->
        <div class="col-lg-4">
            <!-- Información del sistema -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-cog me-2"></i>
                        Información del Sistema
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary"><?= $perfil['perfil_id'] ?></h4>
                                <small class="text-muted">ID Único</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">0</h4>
                            <small class="text-muted">Usuarios</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="small text-muted">
                        <div class="mb-2">
                            <i class="fas fa-calendar-plus me-2"></i>
                            <strong>Creado:</strong><br>
                            <?= date('d/m/Y H:i:s', strtotime($perfil['perfil_created_at'])) ?>
                        </div>
                        
                        <?php if (!empty($perfil['perfil_updated_at']) && $perfil['perfil_updated_at'] !== $perfil['perfil_created_at']): ?>
                        <div class="mb-2">
                            <i class="fas fa-calendar-edit me-2"></i>
                            <strong>Última modificación:</strong><br>
                            <?= date('d/m/Y H:i:s', strtotime($perfil['perfil_updated_at'])) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Acciones rápidas -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('perfiles/edit/' . $perfil['perfil_id']) ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>
                            Editar Perfil
                        </a>
                        
                        <button type="button" 
                                class="btn <?= $perfil['perfil_habil'] ? 'btn-outline-danger' : 'btn-outline-success' ?>"
                                onclick="toggleStatus(<?= $perfil['perfil_id'] ?>)">
                            <i class="fas <?= $perfil['perfil_habil'] ? 'fa-pause' : 'fa-play' ?> me-2"></i>
                            <?= $perfil['perfil_habil'] ? 'Desactivar' : 'Activar' ?>
                        </button>
                        
                        <a href="<?= base_url('usuarios/create?perfil_id=' . $perfil['perfil_id']) ?>" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus me-2"></i>
                            Crear Usuario
                        </a>
                        
                        <button type="button" class="btn btn-outline-info" onclick="duplicateProfile()">
                            <i class="fas fa-copy me-2"></i>
                            Duplicar Perfil
                        </button>
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
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-user-tag fa-3x text-danger mb-3"></i>
                    <h5>¿Eliminar perfil?</h5>
                    <p class="mb-3">
                        Estás a punto de eliminar el perfil <strong>"<?= esc($perfil['perfil_nombre']) ?>"</strong>
                    </p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Advertencia:</strong> Esta acción no se puede deshacer.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <form method="post" action="<?= base_url('perfiles/delete/' . $perfil['perfil_id']) ?>" style="display: inline;">
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
.form-control-plaintext {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem;
    margin-bottom: 0;
}

.badge.fs-6 {
    font-size: 0.9rem !important;
    padding: 0.5rem 0.75rem;
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
            text: 'Esta acción eliminará permanentemente el perfil',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar definitivamente',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar loading
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

// Cambiar estado del perfil
function toggleStatus(id) {
    const isActive = <?= $perfil['perfil_habil'] ? 'true' : 'false' ?>;
    const action = isActive ? 'desactivar' : 'activar';
    const newStatus = isActive ? 'inactivo' : 'activo';
    
    Swal.fire({
        title: `¿${action.charAt(0).toUpperCase() + action.slice(1)} perfil?`,
        text: `El perfil quedará ${newStatus}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: isActive ? '#dc3545' : '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Sí, ${action}`,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Procesando...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.post('<?= base_url('perfiles/toggleStatus') ?>/' + id, {
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

// Duplicar perfil
function duplicateProfile() {
    Swal.fire({
        title: 'Duplicar perfil',
        text: 'Ingresa el nombre para el nuevo perfil',
        input: 'text',
        inputValue: '<?= esc($perfil['perfil_nombre']) ?> - Copia',
        showCancelButton: true,
        confirmButtonText: 'Duplicar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value || value.length < 3) {
                return 'El nombre debe tener al menos 3 caracteres'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Aquí podrías implementar la funcionalidad de duplicar
            Swal.fire({
                icon: 'info',
                title: 'Funcionalidad en desarrollo',
                text: 'La duplicación de perfiles estará disponible próximamente'
            });
        }
    });
}
</script>
<?= $this->endSection() ?>