<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Detalles del Usuario
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <!-- Avatar -->
                    <?php if (!empty($usuario['user_avatar'])): ?>
                        <img src="<?= base_url('uploads/avatars/' . $usuario['user_avatar']) ?>" 
                             alt="<?= esc($usuario['user_nombre']) ?>" 
                             class="rounded-circle me-3"
                             style="width: 80px; height: 80px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 80px; height: 80px;">
                            <span class="text-white fw-bold fs-3">
                                <?= strtoupper(substr($usuario['user_nombre'], 0, 2)) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <div>
                        <h1 class="h3 mb-0"><?= esc($usuario['user_nombre']) ?></h1>
                        <p class="text-muted mb-0">
                            <?php if ($usuario['perfil_tipo'] === 'interno'): ?>
                                <span class="badge bg-warning">🛡️ Usuario Interno</span>
                            <?php else: ?>
                                <span class="badge bg-info">🏢 Usuario de Compañía</span>
                            <?php endif; ?>
                            
                            <span class="badge <?= $usuario['user_habil'] ? 'bg-success' : 'bg-danger' ?> ms-2">
                                <?= $usuario['user_habil'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                            
                            <span class="badge bg-secondary ms-2">
                                ID: <?= $usuario['user_id'] ?>
                            </span>
                        </p>
                    </div>
                </div>
                
                <div class="btn-group">
                    <a href="<?= base_url('users/edit/' . $usuario['user_id']) ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <button type="button" class="btn btn-info reset-password-btn" 
                            data-id="<?= $usuario['user_id'] ?>"
                            data-name="<?= esc($usuario['user_nombre']) ?>">
                        <i class="fas fa-key"></i> Reset Password
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                    <a href="<?= base_url('users') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-lg-8">
            <!-- Información Personal -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>
                        Información Personal
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user text-primary me-1"></i>
                                Nombre Completo
                            </label>
                            <p class="form-control-plaintext"><?= esc($usuario['user_nombre']) ?></p>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-envelope text-info me-1"></i>
                                Email
                            </label>
                            <p class="form-control-plaintext">
                                <a href="mailto:<?= esc($usuario['user_email']) ?>" class="text-decoration-none">
                                    <?= esc($usuario['user_email']) ?>
                                </a>
                            </p>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-phone text-success me-1"></i>
                                Teléfono
                            </label>
                            <?php if (!empty($usuario['user_telefono'])): ?>
                                <p class="form-control-plaintext">
                                    <a href="tel:<?= esc($usuario['user_telefono']) ?>" class="text-decoration-none">
                                        <?= esc($usuario['user_telefono']) ?>
                                    </a>
                                </p>
                            <?php else: ?>
                                <p class="form-control-plaintext text-muted">
                                    <em><i class="fas fa-minus me-1"></i>Sin teléfono registrado</em>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-toggle-on text-success me-1"></i>
                                Estado
                            </label>
                            <p class="form-control-plaintext">
                                <span class="badge fs-6 <?= $usuario['user_habil'] ? 'bg-success' : 'bg-danger' ?>">
                                    <i class="fas <?= $usuario['user_habil'] ? 'fa-check' : 'fa-times' ?> me-1"></i>
                                    <?= $usuario['user_habil'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Sistema -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Configuración del Sistema
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user-tag text-primary me-1"></i>
                                Perfil de Usuario
                            </label>
                            <div class="form-control-plaintext">
                                <div class="d-flex align-items-center">
                                    <?php if ($usuario['perfil_tipo'] === 'interno'): ?>
                                        <span class="badge bg-warning me-2">
                                            <i class="fas fa-shield-alt"></i>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-info me-2">
                                            <i class="fas fa-building"></i>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <div>
                                        <span class="fw-medium"><?= esc($usuario['perfil_nombre']) ?></span>
                                        <br>
                                        <small class="text-muted">
                                            <?php
                                            $nivel = $usuario['perfil_nivel'];
                                            $estrellas = str_repeat('★', $nivel);
                                            echo $estrellas . ' Nivel ' . $nivel;
                                            ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-building text-info me-1"></i>
                                Compañía
                            </label>
                            <?php if (!empty($usuario['cia_nombre'])): ?>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-light text-dark fs-6">
                                        <?= esc($usuario['cia_nombre']) ?>
                                    </span>
                                </p>
                            <?php else: ?>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-secondary fs-6">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Usuario Interno
                                    </span>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permisos -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shield-alt me-2"></i>
                        Permisos Asignados
                    </h5>
                </div>
                <div class="card-body">
                    <?php 
                    $permisos = [];
                    if (!empty($usuario['perfil_permisos'])) {
                        $permisos = is_string($usuario['perfil_permisos']) ? 
                            json_decode($usuario['perfil_permisos'], true) : 
                            $usuario['perfil_permisos'];
                    }
                    $permisosActivos = array_filter($permisos ?: [], function($value) { return $value === true; });
                    ?>
                    
                    <?php if (empty($permisosActivos)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Sin permisos específicos</h5>
                            <p class="text-muted">Este perfil no tiene permisos configurados</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($permisosActivos as $permiso => $valor): ?>
                                <?php if ($valor): ?>
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <span><?= esc(ucfirst(str_replace('_', ' ', $permiso))) ?></span>
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
            <!-- Estadísticas -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Información de Acceso
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary"><?= $usuario['user_intentos_login'] ?></h4>
                                <small class="text-muted">Intentos de Login</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-<?= $usuario['user_habil'] ? 'success' : 'danger' ?>">
                                <?= $usuario['user_habil'] ? 'ON' : 'OFF' ?>
                            </h4>
                            <small class="text-muted">Estado</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="small text-muted">
                        <div class="mb-2">
                            <i class="fas fa-calendar-plus me-2"></i>
                            <strong>Registrado:</strong><br>
                            <?= date('d/m/Y H:i:s', strtotime($usuario['created_at'])) ?>
                        </div>
                        
                        <div class="mb-2">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            <strong>Último acceso:</strong><br>
                            <?php if (!empty($usuario['user_ultimo_acceso'])): ?>
                                <?= date('d/m/Y H:i:s', strtotime($usuario['user_ultimo_acceso'])) ?>
                            <?php else: ?>
                                <span class="text-warning">Nunca se ha conectado</span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($usuario['updated_at']) && $usuario['updated_at'] !== $usuario['created_at']): ?>
                        <div class="mb-2">
                            <i class="fas fa-calendar-edit me-2"></i>
                            <strong>Última modificación:</strong><br>
                            <?= date('d/m/Y H:i:s', strtotime($usuario['updated_at'])) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Acciones rápidas -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('users/edit/' . $usuario['user_id']) ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>
                            Editar Usuario
                        </a>
                        
                        <button type="button" 
                                class="btn <?= $usuario['user_habil'] ? 'btn-outline-danger' : 'btn-outline-success' ?> toggle-status-btn"
                                data-id="<?= $usuario['user_id'] ?>"
                                data-current-status="<?= $usuario['user_habil'] ? '1' : '0' ?>">
                            <i class="fas <?= $usuario['user_habil'] ? 'fa-pause' : 'fa-play' ?> me-2"></i>
                            <?= $usuario['user_habil'] ? 'Desactivar' : 'Activar' ?>
                        </button>
                        
                        <button type="button" class="btn btn-outline-info reset-password-btn" 
                                data-id="<?= $usuario['user_id'] ?>"
                                data-name="<?= esc($usuario['user_nombre']) ?>">
                            <i class="fas fa-key me-2"></i>
                            Resetear Contraseña
                        </button>
                        
                        <button type="button" class="btn btn-outline-warning send-email-btn"
                                data-email="<?= esc($usuario['user_email']) ?>"
                                data-name="<?= esc($usuario['user_nombre']) ?>">
                            <i class="fas fa-envelope me-2"></i>
                            Enviar Email
                        </button>
                        
                        <button type="button" class="btn btn-outline-secondary view-activity-btn"
                                data-id="<?= $usuario['user_id'] ?>">
                            <i class="fas fa-history me-2"></i>
                            Ver Actividad
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
                    <i class="fas fa-user fa-3x text-danger mb-3"></i>
                    <h5>¿Eliminar usuario?</h5>
                    <p class="mb-3">
                        Estás a punto de eliminar al usuario <strong>"<?= esc($usuario['user_nombre']) ?>"</strong>
                    </p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Advertencia:</strong> Esta acción no se puede deshacer.
                        <?php if (!empty($usuario['user_avatar'])): ?>
                            <br>También se eliminará el avatar asociado.
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <form method="post" action="<?= base_url('users/delete/' . $usuario['user_id']) ?>" style="display: inline;">
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
// Función global para copiar contraseña (accesible desde SweetAlert)
window.copyPassword = function() {
    const passwordField = document.getElementById('tempPasswordField');
    if (passwordField) {
        passwordField.select();
        passwordField.setSelectionRange(0, 99999); // Para móviles
        
        try {
            document.execCommand('copy');
            
            // Feedback visual
            Swal.fire({
                icon: 'success',
                title: 'Copiado',
                text: 'Contraseña copiada al portapapeles',
                timer: 1500,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        } catch (err) {
            console.error('Error al copiar:', err);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo copiar la contraseña',
                timer: 2000,
                showConfirmButton: false
            });
        }
    }
}

$(document).ready(function() {
    console.log('Document ready - Vista de detalles de usuario');
    
    // Confirmar eliminación
    $('#confirmDeleteBtn').on('click', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        
        Swal.fire({
            title: 'Última confirmación',
            text: 'Esta acción eliminará permanentemente el usuario',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar definitivamente',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Reset password - Evento unificado
    $('.reset-password-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        Swal.fire({
            title: 'Resetear contraseña',
            text: `¿Generar nueva contraseña temporal para ${name}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, resetear',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar loading
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Generando nueva contraseña',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.post('<?= base_url('users/resetPassword') ?>/' + id, {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                })
                .done(function(response) {
                    if (response.success) {
                        // Mostrar la contraseña temporal
                        Swal.fire({
                            icon: 'success',
                            title: 'Contraseña Reseteada',
                            html: `
                                <div class="text-start">
                                    <p><strong>Usuario:</strong> ${response.userName}</p>
                                    <p><strong>Email:</strong> ${response.userEmail}</p>
                                    <div class="alert alert-warning mt-3">
                                        <strong>Nueva contraseña temporal:</strong>
                                        <div class="input-group mt-2">
                                            <input type="text" class="form-control" 
                                                value="${response.tempPassword}" 
                                                id="tempPasswordField" readonly>
                                            <button class="btn btn-outline-secondary" 
                                                    type="button" 
                                                    onclick="copyPassword()">
                                                <i class="fas fa-copy"></i> Copiar
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        El usuario deberá cambiar esta contraseña en su próximo inicio de sesión.
                                    </small>
                                </div>
                            `,
                            showConfirmButton: true,
                            confirmButtonText: 'Entendido',
                            width: 600
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Error al resetear la contraseña'
                        });
                    }
                })
                .fail(function(xhr) {
                    console.error('Error AJAX:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor'
                    });
                });
            }
        });
    });

    // Toggle status - Cambiar estado del usuario
    $('.toggle-status-btn').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');
        const currentStatus = btn.data('current-status');
        const action = currentStatus == '1' ? 'desactivar' : 'activar';
        const newStatusText = currentStatus == '1' ? 'inactivo' : 'activo';
        
        Swal.fire({
            title: `¿${action.charAt(0).toUpperCase() + action.slice(1)} usuario?`,
            text: `El usuario quedará ${newStatusText}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: currentStatus == '1' ? '#dc3545' : '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Sí, ${action}`,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= base_url('users/toggleStatus') ?>/' + id, {
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
                            text: response.message || 'No se pudo actualizar el estado'
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
    });

    // Enviar email
    $('.send-email-btn').on('click', function() {
        const email = $(this).data('email');
        const name = $(this).data('name');
        
        Swal.fire({
            title: 'Enviar Email',
            html: `
                <p>Enviar correo a <strong>${name}</strong></p>
                <p class="text-muted">${email}</p>
                <div class="form-group text-start mt-3">
                    <label for="emailSubject">Asunto:</label>
                    <input type="text" id="emailSubject" class="form-control" 
                           placeholder="Asunto del correo">
                </div>
                <div class="form-group text-start mt-3">
                    <label for="emailMessage">Mensaje:</label>
                    <textarea id="emailMessage" class="form-control" rows="4" 
                              placeholder="Escribe tu mensaje aquí..."></textarea>
                </div>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Enviar',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                const subject = document.getElementById('emailSubject').value;
                const message = document.getElementById('emailMessage').value;
                
                if (!subject || !message) {
                    Swal.showValidationMessage('Por favor completa todos los campos');
                    return false;
                }
                
                return { subject, message };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Aquí iría la lógica para enviar el email
                Swal.fire({
                    icon: 'info',
                    title: 'Funcionalidad en desarrollo',
                    text: 'El sistema de envío de emails será implementado próximamente'
                });
            }
        });
    });

    // Ver actividad
    $('.view-activity-btn').on('click', function() {
        const userId = $(this).data('id');
        
        Swal.fire({
            title: 'Historial de Actividad',
            html: `
                <div class="text-start">
                    <p class="text-muted">Esta funcionalidad mostrará:</p>
                    <ul class="text-start">
                        <li>Historial de inicios de sesión</li>
                        <li>Cambios realizados por el usuario</li>
                        <li>Acciones en el sistema</li>
                        <li>Registro de auditoría</li>
                    </ul>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i> Funcionalidad en desarrollo
                    </div>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#17a2b8',
            width: 500
        });
    });

    // Auto-hide alerts después de 5 segundos
    $('.alert-dismissible').delay(5000).fadeOut('slow');
    
    // Tooltip initialization si usas Bootstrap tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
<?= $this->endSection() ?>