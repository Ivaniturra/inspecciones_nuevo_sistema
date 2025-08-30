<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'Detalles de Compañía') ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Inicio</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('cias') ?>">Compañías</a></li>
        <li class="breadcrumb-item active"><?= esc($cia['cia_nombre']) ?></li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    // Fallbacks de branding por si no existen
    $displayName   = $cia['cia_display_name']      ?? $cia['cia_nombre'];
    $navBg         = $cia['cia_brand_nav_bg']      ?? '#0D6EFD';
    $navText       = $cia['cia_brand_nav_text']    ?? '#FFFFFF';
    $sideStart     = $cia['cia_brand_side_start']  ?? '#667EEA';
    $sideEnd       = $cia['cia_brand_side_end']    ?? '#764BA2';
    $logoUrl       = !empty($cia['cia_logo'])
                        ? base_url('uploads/logos/' . $cia['cia_logo'])
                        : 'https://via.placeholder.com/200x120?text=Sin+logo';
    
    // Estadísticas de usuarios
    $userStats = $userStats ?? ['total' => 0, 'activos' => 0, 'inactivos' => 0];
?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <!-- Logo -->
                    <div class="me-3">
                        <img src="<?= esc($logoUrl) ?>"
                             alt="<?= esc($cia['cia_nombre']) ?>"
                             class="rounded"
                             style="width:60px;height:60px;object-fit:contain;background:#fff;">
                    </div>
                    <div>
                        <h1 class="h3 mb-0"><?= esc($cia['cia_nombre']) ?></h1>
                        <p class="text-muted mb-0">
                            <span class="badge <?= $cia['cia_habil'] ? 'bg-success' : 'bg-danger' ?>">
                                <?= $cia['cia_habil'] ? 'Activa' : 'Inactiva' ?>
                            </span>
                            <span class="ms-2">ID: <?= (int)$cia['cia_id'] ?></span>
                            <?php if (!empty($displayName) && $displayName !== $cia['cia_nombre']): ?>
                                <span class="ms-2 badge bg-info text-dark">
                                    <i class="fas fa-signature me-1"></i><?= esc($displayName) ?>
                                </span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <div class="btn-group">
                    <a href="<?= base_url('cias/edit/' . $cia['cia_id']) ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <button type="button" 
                            class="btn <?= $cia['cia_habil'] ? 'btn-outline-danger' : 'btn-outline-success' ?>"
                            onclick="toggleStatus(<?= (int)$cia['cia_id'] ?>)">
                        <i class="fas <?= $cia['cia_habil'] ? 'fa-pause' : 'fa-play' ?> me-1"></i>
                        <?= $cia['cia_habil'] ? 'Desactivar' : 'Activar' ?>
                    </button>
                    <a href="<?= base_url('cias') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
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

    <div class="row">
        <!-- Columna principal -->
        <div class="col-lg-8">
            <!-- Información de la compañía -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i> Información de la Compañía
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Nombre -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-building text-primary me-1"></i> Nombre de la Compañía
                            </label>
                            <p class="form-control-plaintext"><?= esc($cia['cia_nombre']) ?></p>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-toggle-on text-success me-1"></i> Estado
                            </label>
                            <p class="form-control-plaintext">
                                <span class="badge fs-6 <?= $cia['cia_habil'] ? 'bg-success' : 'bg-danger' ?>">
                                    <i class="fas <?= $cia['cia_habil'] ? 'fa-check' : 'fa-times' ?> me-1"></i>
                                    <?= $cia['cia_habil'] ? 'Activa' : 'Inactiva' ?>
                                </span>
                                <?php if (!$cia['cia_habil'] && $userStats['total'] > 0): ?>
                                    <br><small class="text-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Los usuarios asociados están desactivados
                                    </small>
                                <?php endif; ?>
                            </p>
                        </div>

                        <!-- Dirección -->
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-map-marker-alt text-info me-1"></i> Dirección
                            </label>
                            <?php if (!empty($cia['cia_direccion'])): ?>
                                <p class="form-control-plaintext"><?= nl2br(esc($cia['cia_direccion'])) ?></p>
                            <?php else: ?>
                                <p class="form-control-plaintext text-muted">
                                    <em><i class="fas fa-minus me-1"></i> Sin dirección registrada</em>
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Logo -->
                        <?php if (!empty($cia['cia_logo'])): ?>
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-image text-warning me-1"></i> Logo de la Compañía
                                </label>
                                <div class="mt-2">
                                    <img src="<?= base_url('uploads/logos/' . $cia['cia_logo']) ?>"
                                         alt="<?= esc($cia['cia_nombre']) ?>"
                                         class="img-thumbnail"
                                         style="max-width:300px;max-height:200px;cursor:pointer;"
                                         data-bs-toggle="modal" data-bs-target="#logoModal">
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-file me-1"></i><?= esc($cia['cia_logo']) ?>
                                            <span class="ms-2"><i class="fas fa-search-plus me-1"></i>Clic para ampliar</span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Usuarios asociados -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i> Usuarios Asociados
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($userStats['total'] > 0): ?>
                        <div class="row text-center mb-4">
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">
                                    <h3 class="text-primary mb-1"><?= $userStats['total'] ?></h3>
                                    <small class="text-muted">Total usuarios</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">
                                    <h3 class="text-success mb-1"><?= $userStats['activos'] ?></h3>
                                    <small class="text-muted">Activos</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">
                                    <h3 class="text-warning mb-1"><?= $userStats['inactivos'] ?></h3>
                                    <small class="text-muted">Inactivos</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <a href="<?= base_url('users?cia_id=' . (int)$cia['cia_id']) ?>" class="btn btn-primary">
                                <i class="fas fa-users me-2"></i> Ver todos los usuarios
                            </a>
                            <a href="<?= base_url('users/create?cia_id=' . (int)$cia['cia_id']) ?>" class="btn btn-outline-primary">
                                <i class="fas fa-user-plus me-2"></i> Agregar usuario
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Sin usuarios asociados</h5>
                            <p class="text-muted">Esta compañía aún no tiene usuarios registrados</p>
                            <a href="<?= base_url('users/create?cia_id=' . (int)$cia['cia_id']) ?>" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> Crear primer usuario
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Columna lateral -->
        <div class="col-lg-4">
            <!-- Branding / Apariencia -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-palette me-2"></i> Branding y Apariencia
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Preview -->
                    <div class="border rounded overflow-hidden mb-3">
                        <div class="p-2 d-flex align-items-center"
                             style="background: <?= esc($navBg) ?>; color: <?= esc($navText) ?>;">
                            <img src="<?= esc($logoUrl) ?>" alt="logo" class="me-2"
                                 style="width:28px;height:28px;object-fit:contain;background:#fff;border-radius:4px;">
                            <strong><?= esc($displayName) ?></strong>
                        </div>
                        <div class="p-3 text-white"
                             style="background: linear-gradient(135deg, <?= esc($sideStart) ?>, <?= esc($sideEnd) ?>); min-height:90px;">
                            <div class="mb-2"><i class="fas fa-circle me-2"></i> Menú 1</div>
                            <div class="mb-2"><i class="fas fa-circle me-2"></i> Menú 2</div>
                            <div><i class="fas fa-circle me-2"></i> Menú 3</div>
                        </div>
                    </div>

                    <!-- Chips de colores -->
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-2 border rounded">
                                <small class="text-muted d-block mb-1">Topbar fondo</small>
                                <div class="d-flex align-items-center">
                                    <span class="me-2 d-inline-block rounded"
                                          style="width:18px;height:18px;background:<?= esc($navBg) ?>;border:1px solid #ddd;"></span>
                                    <code class="small"><?= esc($navBg) ?></code>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded">
                                <small class="text-muted d-block mb-1">Topbar texto</small>
                                <div class="d-flex align-items-center">
                                    <span class="me-2 d-inline-block rounded"
                                          style="width:18px;height:18px;background:<?= esc($navText) ?>;border:1px solid #ddd;"></span>
                                    <code class="small"><?= esc($navText) ?></code>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded">
                                <small class="text-muted d-block mb-1">Sidebar inicio</small>
                                <div class="d-flex align-items-center">
                                    <span class="me-2 d-inline-block rounded"
                                          style="width:18px;height:18px;background:<?= esc($sideStart) ?>;border:1px solid #ddd;"></span>
                                    <code class="small"><?= esc($sideStart) ?></code>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded">
                                <small class="text-muted d-block mb-1">Sidebar fin</small>
                                <div class="d-flex align-items-center">
                                    <span class="me-2 d-inline-block rounded"
                                          style="width:18px;height:18px;background:<?= esc($sideEnd) ?>;border:1px solid #ddd;"></span>
                                    <code class="small"><?= esc($sideEnd) ?></code>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($displayName) && $displayName !== $cia['cia_nombre']): ?>
                        <div class="mt-3">
                            <small class="text-muted d-block">Nombre comercial</small>
                            <div class="p-2 border rounded bg-light"><?= esc($displayName) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Información del sistema -->
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
                                <h4 class="text-primary"><?= (int)$cia['cia_id'] ?></h4>
                                <small class="text-muted">ID Único</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success"><?= $userStats['total'] ?></h4>
                            <small class="text-muted">Usuarios</small>
                        </div>
                    </div>
                    <hr>
                    <div class="small text-muted">
                        <div class="mb-2">
                            <i class="fas fa-calendar-plus me-2"></i>
                            <strong>Creado:</strong><br>
                            <?= date('d/m/Y H:i:s', strtotime($cia['cia_created_at'])) ?>
                        </div>
                        <?php if (!empty($cia['cia_updated_at']) && $cia['cia_updated_at'] !== $cia['cia_created_at']): ?>
                            <div class="mb-2">
                                <i class="fas fa-calendar-edit me-2"></i>
                                <strong>Última modificación:</strong><br>
                                <?= date('d/m/Y H:i:s', strtotime($cia['cia_updated_at'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i> Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('cias/edit/' . $cia['cia_id']) ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i> Editar Compañía
                        </a>
                        <button type="button"
                                class="btn <?= $cia['cia_habil'] ? 'btn-outline-danger' : 'btn-outline-success' ?>"
                                onclick="toggleStatus(<?= (int)$cia['cia_id'] ?>)">
                            <i class="fas <?= $cia['cia_habil'] ? 'fa-pause' : 'fa-play' ?> me-2"></i>
                            <?= $cia['cia_habil'] ? 'Desactivar Compañía' : 'Activar Compañía' ?>
                        </button>
                        <a href="<?= base_url('users?cia_id=' . (int)$cia['cia_id']) ?>" class="btn btn-outline-primary">
                            <i class="fas fa-users me-2"></i> Gestionar Usuarios (<?= $userStats['total'] ?>)
                        </a>
                        <?php if ($userStats['total'] === 0): ?>
                            <a href="<?= base_url('users/create?cia_id=' . (int)$cia['cia_id']) ?>" class="btn btn-outline-success">
                                <i class="fas fa-user-plus me-2"></i> Crear Primer Usuario
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información importante -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info border-0">
                <div class="d-flex">
                    <div class="me-3">
                        <i class="fas fa-info-circle fa-2x text-info"></i>
                    </div>
                    <div>
                        <h6 class="alert-heading">Política de Datos</h6>
                        <p class="mb-1">
                            <strong>Desactivación:</strong> Al desactivar esta compañía, todos los usuarios asociados (<?= $userStats['total'] ?>) se desactivarán automáticamente para mantener la integridad del sistema.
                        </p>
                        <p class="mb-0">
                            <strong>Conservación:</strong> Los datos de la compañía y usuarios se conservan en el sistema. No se eliminan registros, solo se cambia su estado.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal ampliar logo -->
<?php if (!empty($cia['cia_logo'])): ?>
<div class="modal fade" id="logoModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-image me-2"></i> Logo de <?= esc($cia['cia_nombre']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="<?= base_url('uploads/logos/' . $cia['cia_logo']) ?>" alt="<?= esc($cia['cia_nombre']) ?>" class="img-fluid rounded">
                <div class="mt-3">
                    <small class="text-muted"><i class="fas fa-file me-1"></i><?= esc($cia['cia_logo']) ?></small>
                </div>
            </div>
            <div class="modal-footer">
                <a href="<?= base_url('uploads/logos/' . $cia['cia_logo']) ?>" target="_blank" class="btn btn-primary">
                    <i class="fas fa-external-link-alt"></i> Ver tamaño completo
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.card { border: none; border-radius: 15px; }
.card-header { border-radius: 15px 15px 0 0 !important; font-weight: 600; }
.form-control-plaintext { background:#f8f9fa; border:1px solid #e9ecef; border-radius:8px; padding:.75rem; margin-bottom:0; }
.img-thumbnail { border-radius:12px; transition: transform .2s ease; }
.img-thumbnail:hover { transform: scale(1.02); }
.badge.fs-6 { font-size:.9rem !important; padding:.5rem .75rem; }
.btn { border-radius:8px; }
.border-end { border-right:1px solid #dee2e6 !important; }
@media (max-width: 768px) {
  .border-end { border-right:none !important; border-bottom:1px solid #dee2e6 !important; padding-bottom:1rem; margin-bottom:1rem; }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Script corregido para app/Views/cias/show.php
$(function () {
    // Auto-hide alerts
    $('.alert-dismissible').delay(5000).fadeOut();
});

// Variable global para el token CSRF
let csrfToken = '<?= csrf_hash() ?>';
const csrfName = '<?= csrf_token() ?>';

// Toggle estado
function toggleStatus(id) {
    const isActive = <?= $cia['cia_habil'] ? 'true' : 'false' ?>;
    const totalUsers = <?= $userStats['total'] ?>;
    const action = isActive ? 'desactivar' : 'activar';
    const newStatus = isActive ? 'inactiva' : 'activa';

    let confirmText = `La compañía quedará ${newStatus}`;
    let warningHtml = '';
    
    if (!isActive) {
        confirmText += '. Los usuarios podrán volver a acceder.';
    } else if (totalUsers > 0) {
        warningHtml = `
            <div class="alert alert-warning mt-3 mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Importante:</strong> Esta acción desactivará automáticamente ${totalUsers} usuario${totalUsers > 1 ? 's' : ''} asociado${totalUsers > 1 ? 's' : ''}. 
                Los usuarios no podrán acceder hasta ser reactivados individualmente.
            </div>
        `;
    }

    Swal.fire({
        title: `¿${action.charAt(0).toUpperCase() + action.slice(1)} compañía?`,
        html: `<p>${confirmText}</p>${warningHtml}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: isActive ? '#dc3545' : '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Sí, ${action}`,
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
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
            .done(function (response, textStatus, xhr) {
                // CRÍTICO: Actualizar el token CSRF para la siguiente petición
                const newToken = xhr.getResponseHeader('X-CSRF-TOKEN');
                if (newToken) {
                    csrfToken = newToken;
                    // También actualizar el token en cualquier input hidden del DOM si existe
                    $('input[name="' + csrfName + '"]').val(newToken);
                }
                
                if (response.success) {
                    Swal.fire({ 
                        icon: 'success', 
                        title: 'Estado actualizado', 
                        text: response.message, 
                        timer: 3000, 
                        showConfirmButton: false 
                    }).then(() => location.reload());
                } else {
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'Error', 
                        text: response.message || 'No se pudo actualizar el estado' 
                    });
                }
            })
            .fail(function (xhr) {
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
    });
}
</script>
<?= $this->endSection() ?>