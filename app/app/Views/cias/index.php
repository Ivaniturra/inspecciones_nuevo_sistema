<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Gestión de Compañías
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Gestión de Compañías</h1>
                    <p class="text-muted">Administra las compañías del sistema</p>
                </div>

                <?php if (function_exists('can') ? can('gestionar_companias') : true): ?>
                <a href="<?= base_url('cias/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Compañía
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Table Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-building text-primary me-2"></i>
                Listado de Compañías
                <small class="text-muted">(<?= count($cias) ?> total)</small>
            </h5>
        </div>

        <div class="card-body p-0">
            <?php if (empty($cias)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay compañías registradas</h5>
                    <p class="text-muted">Comienza creando tu primera compañía</p>
                    <?php if (function_exists('can') ? can('gestionar_companias') : true): ?>
                    <a href="<?= base_url('cias/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Crear Compañía
                    </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">Logo</th>
                                <th>Nombre</th>
                                <th>Dirección</th>
                                <th class="text-center" style="width: 120px;">Usuarios</th>
                                <th class="text-center" style="width: 140px;">Estado</th>
                                <th class="text-end" style="width: 160px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cias as $cia): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($cia['cia_logo'])): ?>
                                            <img src="<?= base_url('uploads/logos/' . $cia['cia_logo']) ?>"
                                                 alt="<?= esc($cia['cia_nombre']) ?>"
                                                 class="rounded"
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-building text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div>
                                            <a class="fw-medium text-decoration-none" href="<?= base_url('cias/show/' . $cia['cia_id']) ?>">
                                                <?= esc($cia['cia_nombre']) ?>
                                            </a>
                                            <?php if (!empty($cia['cia_display_name']) && $cia['cia_display_name'] !== $cia['cia_nombre']): ?>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-signature me-1"></i><?= esc($cia['cia_display_name']) ?>
                                                </small>
                                            <?php endif; ?>
                                            <br>
                                            <small class="text-muted">ID: <?= $cia['cia_id'] ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($cia['cia_direccion'])): ?>
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="<?= esc($cia['cia_direccion']) ?>">
                                                <?= esc($cia['cia_direccion']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">Sin dirección</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Total usuarios (si viene desde getCiasWithUserCount) -->
                                    <td class="text-center">
                                        <?php 
                                            $totalUsers = isset($cia['total_usuarios']) ? (int)$cia['total_usuarios'] : 0;
                                            $badgeClass = $totalUsers > 0 ? 'bg-primary' : 'bg-light text-dark';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>" title="Total de usuarios asociados">
                                            <i class="fas fa-users me-1"></i><?= $totalUsers ?>
                                        </span>
                                    </td>

                                    <!-- Estado con switch AJAX -->
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="form-check form-switch me-2">
                                                <input class="form-check-input cia-status-toggle"
                                                       type="checkbox"
                                                       <?= $cia['cia_habil'] ? 'checked' : '' ?>
                                                       data-id="<?= $cia['cia_id'] ?>"
                                                       data-users="<?= $totalUsers ?>"
                                                       title="<?= $cia['cia_habil'] ? 'Clic para desactivar' : 'Clic para activar' ?>">
                                            </div>
                                            <span class="badge <?= $cia['cia_habil'] ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= $cia['cia_habil'] ? 'Activa' : 'Inactiva' ?>
                                            </span>
                                        </div>
                                        <?php if (!$cia['cia_habil'] && $totalUsers > 0): ?>
                                            <small class="text-warning d-block mt-1">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                Usuarios desactivados
                                            </small>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('cias/show/' . $cia['cia_id']) ?>"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Ver detalles"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <?php if (function_exists('can') ? can('gestionar_companias') : true): ?>
                                            <a href="<?= base_url('cias/edit/' . $cia['cia_id']) ?>"
                                               class="btn btn-sm btn-outline-warning"
                                               title="Editar compañía"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <a href="<?= base_url('users?cia_id=' . $cia['cia_id']) ?>"
                                               class="btn btn-sm btn-outline-info"
                                               title="Ver usuarios (<?= $totalUsers ?>)"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-users"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Info adicional -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Importante:</strong> Al desactivar una compañía, todos los usuarios asociados se desactivarán automáticamente. 
                Los registros no se eliminan del sistema, solo cambian su estado a inactivo.
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Script corregido para app/Views/cias/index.php
$(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Variable para mantener el token CSRF actualizado
    let csrfToken = '<?= csrf_hash() ?>';
    const csrfName = '<?= csrf_token() ?>';

    // Toggle estado compañía (AJAX)
    $('.cia-status-toggle').on('change', function() {
        const $toggle = $(this);
        const id = $toggle.data('id');
        const users = $toggle.data('users');
        const checked = $toggle.is(':checked');
        const action = checked ? 'activar' : 'desactivar';

        // Mostrar confirmación especial si va a desactivar y tiene usuarios
        if (!checked && users > 0) {
            Swal.fire({
                title: '¿Desactivar compañía?',
                html: `
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <p>Esta acción desactivará la compañía y <strong>automáticamente desactivará ${users} usuario${users > 1 ? 's' : ''} asociado${users > 1 ? 's' : ''}.</strong></p>
                        <div class="alert alert-warning">
                            Los usuarios no podrán acceder al sistema hasta que sean reactivados individualmente.
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, desactivar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    executeToggle(id, $toggle, checked);
                } else {
                    $toggle.prop('checked', !checked); // revertir
                }
            });
        } else {
            executeToggle(id, $toggle, checked);
        }
    });

    function executeToggle(id, $toggle, checked) {
        // Mostrar loading
        const $row = $toggle.closest('tr');
        $row.addClass('table-warning');
        
        Swal.fire({
            title: 'Procesando...',
            text: 'Actualizando estado de la compañía',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        // Preparar data con token CSRF actualizado
        const postData = {};
        postData[csrfName] = csrfToken;

        $.post('<?= base_url('cias/toggleStatus') ?>/' + id, postData)
        .done(function(resp, textStatus, xhr) {
            $row.removeClass('table-warning');
            
            // CRÍTICO: Actualizar el token CSRF para la siguiente petición
            const newToken = xhr.getResponseHeader('X-CSRF-TOKEN');
            if (newToken) {
                csrfToken = newToken;
                // También actualizar el token en cualquier input hidden del DOM si existe
                $('input[name="' + csrfName + '"]').val(newToken);
            }
            
            if (!resp || !resp.success) {
                $toggle.prop('checked', !checked); // revertir
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: (resp && resp.message) ? resp.message : 'No se pudo actualizar el estado'
                });
            } else {
                // Actualizar badge y tooltip
                const $badge = $row.find('.badge:last');
                const $statusInfo = $row.find('small.text-warning');
                
                if (resp.newStatus === 1) {
                    $badge.removeClass('bg-secondary').addClass('bg-success').text('Activa');
                    $toggle.attr('title', 'Clic para desactivar');
                    $statusInfo.hide();
                } else {
                    $badge.removeClass('bg-success').addClass('bg-secondary').text('Inactiva');
                    $toggle.attr('title', 'Clic para activar');
                    if ($toggle.data('users') > 0) {
                        if ($statusInfo.length === 0) {
                            $badge.after('<small class="text-warning d-block mt-1"><i class="fas fa-exclamation-triangle me-1"></i>Usuarios desactivados</small>');
                        } else {
                            $statusInfo.show();
                        }
                    }
                }
                
                Swal.fire({
                    icon: 'success',
                    title: 'Estado actualizado',
                    text: resp.message,
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        })
        .fail(function(xhr) {
            $row.removeClass('table-warning');
            $toggle.prop('checked', !checked); // revertir
            
            let errorMsg = 'No se pudo conectar con el servidor';
            if (xhr.status === 403) {
                errorMsg = 'Token de seguridad expirado. Recarga la página.';
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: errorMsg
            });
        });
    }

    // Auto-hide alerts
    $('.alert').delay(8000).fadeOut();
});
</script>
<?= $this->endSection() ?>