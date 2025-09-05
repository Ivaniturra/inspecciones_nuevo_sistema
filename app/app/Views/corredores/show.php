<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'Detalles de Corredor') ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Inicio</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('corredores') ?>">Corredores</a></li>
        <li class="breadcrumb-item active"><?= esc($corredor['corredor_nombre']) ?></li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    // Fallbacks de branding por si no existen
    $displayName   = $corredor['corredor_display_name']      ?? $corredor['corredor_nombre'];
    $navBg         = $corredor['corredor_brand_nav_bg']      ?? '#0D6EFD';
    $navText       = $corredor['corredor_brand_nav_text']    ?? '#FFFFFF';
    $sideStart     = $corredor['corredor_brand_side_start']  ?? '#667EEA';
    $sideEnd       = $corredor['corredor_brand_side_end']    ?? '#764BA2';
    $logoUrl       = !empty($corredor['corredor_logo'])
                        ? base_url('uploads/corredores/' . $corredor['corredor_logo'])
                        : 'https://via.placeholder.com/200x120?text=Sin+logo';
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
                             alt="<?= esc($corredor['corredor_nombre']) ?>"
                             class="rounded"
                             style="width:60px;height:60px;object-fit:contain;background:#fff;">
                    </div>
                    <div>
                        <h1 class="h3 mb-0"><?= esc($corredor['corredor_nombre']) ?></h1>
                        <p class="text-muted mb-0">
                            <span class="badge <?= $corredor['corredor_habil'] ? 'bg-success' : 'bg-danger' ?>">
                                <?= $corredor['corredor_habil'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                            <span class="ms-2">ID: <?= (int)$corredor['corredor_id'] ?></span>
                            <?php if (!empty($displayName) && $displayName !== $corredor['corredor_nombre']): ?>
                                <span class="ms-2 badge bg-info text-dark">
                                    <i class="fas fa-signature me-1"></i><?= esc($displayName) ?>
                                </span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <div class="btn-group">
                    <a href="<?= base_url('corredores/edit/' . $corredor['corredor_id']) ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>    
                    <a href="<?= base_url('corredores') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Columna principal -->
        <div class="col-lg-8">
            <!-- Información del corredor -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-tie me-2"></i> Información del Corredor
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Nombre -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user-tie text-primary me-1"></i> Nombre del Corredor
                            </label>
                            <p class="form-control-plaintext"><?= esc($corredor['corredor_nombre']) ?></p>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-toggle-on text-success me-1"></i> Estado
                            </label>
                            <p class="form-control-plaintext">
                                <span class="badge fs-6 <?= $corredor['corredor_habil'] ? 'bg-success' : 'bg-danger' ?>">
                                    <i class="fas <?= $corredor['corredor_habil'] ? 'fa-check' : 'fa-times' ?> me-1"></i>
                                    <?= $corredor['corredor_habil'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </p>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-envelope text-info me-1"></i> Email
                            </label>
                            <?php if (!empty($corredor['corredor_email'])): ?>
                                <p class="form-control-plaintext">
                                    <a href="mailto:<?= esc($corredor['corredor_email']) ?>" class="text-decoration-none">
                                        <?= esc($corredor['corredor_email']) ?>
                                    </a>
                                </p>
                            <?php else: ?>
                                <p class="form-control-plaintext text-muted">
                                    <em><i class="fas fa-minus me-1"></i> Sin email registrado</em>
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Teléfono -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-phone text-success me-1"></i> Teléfono
                            </label>
                            <?php if (!empty($corredor['corredor_telefono'])): ?>
                                <p class="form-control-plaintext">
                                    <a href="tel:<?= esc($corredor['corredor_telefono']) ?>" class="text-decoration-none">
                                        <?= esc($corredor['corredor_telefono']) ?>
                                    </a>
                                </p>
                            <?php else: ?>
                                <p class="form-control-plaintext text-muted">
                                    <em><i class="fas fa-minus me-1"></i> Sin teléfono registrado</em>
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- RUT -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-id-card text-warning me-1"></i> RUT
                            </label>
                            <?php if (!empty($corredor['corredor_rut'])): ?>
                                <p class="form-control-plaintext font-monospace"><?= esc($corredor['corredor_rut']) ?></p>
                            <?php else: ?>
                                <p class="form-control-plaintext text-muted">
                                    <em><i class="fas fa-minus me-1"></i> Sin RUT registrado</em>
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Dirección -->
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-map-marker-alt text-info me-1"></i> Dirección
                            </label>
                            <?php if (!empty($corredor['corredor_direccion'])): ?>
                                <p class="form-control-plaintext"><?= nl2br(esc($corredor['corredor_direccion'])) ?></p>
                            <?php else: ?>
                                <p class="form-control-plaintext text-muted">
                                    <em><i class="fas fa-minus me-1"></i> Sin dirección registrada</em>
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Logo -->
                        <?php if (!empty($corredor['corredor_logo'])): ?>
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-image text-warning me-1"></i> Logo del Corredor
                                </label>
                                <div class="mt-2">
                                    <img src="<?= base_url('uploads/corredores/' . $corredor['corredor_logo']) ?>"
                                         alt="<?= esc($corredor['corredor_nombre']) ?>"
                                         class="img-thumbnail"
                                         style="max-width:300px;max-height:200px;cursor:pointer;"
                                         data-bs-toggle="modal" data-bs-target="#logoModal">
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-file me-1"></i><?= esc($corredor['corredor_logo']) ?>
                                            <span class="ms-2"><i class="fas fa-search-plus me-1"></i>Clic para ampliar</span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Compañías asociadas -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-building me-2"></i> Compañías Asociadas
                        <span class="badge bg-light text-dark ms-2"><?= count($cias) ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($cias)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Sin compañías asociadas</h5>
                            <p class="text-muted">Este corredor no tiene compañías de seguros asociadas</p>
                            <a href="<?= base_url('corredores/edit/' . (int)$corredor['corredor_id']) ?>" class="btn btn-outline-primary">
                                <i class="fas fa-plus"></i> Asignar Compañías
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($cias as $cia): ?>
                                <div class="col-md-6">
                                    <div class="card border h-100">
                                        <div class="card-body d-flex align-items-center">
                                            <div class="me-3">
                                                <?php if (!empty($cia['cia_logo'])): ?>
                                                    <img src="<?= base_url('uploads/logos/' . $cia['cia_logo']) ?>"
                                                         alt="<?= esc($cia['cia_nombre']) ?>"
                                                         class="rounded"
                                                         style="width:40px;height:40px;object-fit:contain;">
                                                <?php else: ?>
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                         style="width:40px;height:40px;">
                                                        <i class="fas fa-building text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                               <?php
                                                $ciaName = $cia['cia_display_name']
                                                        ?? $cia['cia_nombre']
                                                        ?? ('Compañía #'.($cia['cia_id'] ?? ''));
                                                ?>
                                                <a class="dropdown-item" href="<?= base_url('cias/show/' . ($cia['cia_id'] ?? 0)) ?>">
                                                <i class="fas fa-eye me-2"></i><?= esc($ciaName) ?>
                                                </a>
                                            </div>
                                            <div>
                                                <a href="<?= base_url('cias/show/' . $cia['cia_id']) ?>" 
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="Ver compañía">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
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

                    <?php if (!empty($displayName) && $displayName !== $corredor['corredor_nombre']): ?>
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
                                <h4 class="text-primary"><?= (int)$corredor['corredor_id'] ?></h4>
                                <small class="text-muted">ID Único</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success"><?= count($cias) ?></h4>
                            <small class="text-muted">Compañía<?= count($cias) !== 1 ? 's' : '' ?></small>
                        </div>
                    </div>
                    <hr>
                    <div class="small text-muted">
                        <div class="mb-2">
                            <i class="fas fa-calendar-plus me-2"></i>
                            <strong>Creado:</strong><br>
                            <?= date('d/m/Y H:i:s', strtotime($corredor['corredor_created_at'])) ?>
                        </div>
                        <?php if (!empty($corredor['corredor_updated_at']) && $corredor['corredor_updated_at'] !== $corredor['corredor_created_at']): ?>
                            <div class="mb-2">
                                <i class="fas fa-calendar-edit me-2"></i>
                                <strong>Última modificación:</strong><br>
                                <?= date('d/m/Y H:i:s', strtotime($corredor['corredor_updated_at'])) ?>
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
                        <a href="<?= base_url('corredores/edit/' . $corredor['corredor_id']) ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i> Editar Corredor
                        </a>
                        <button type="button"
                                class="btn <?= $corredor['corredor_habil'] ? 'btn-outline-danger' : 'btn-outline-success' ?>"
                                onclick="toggleStatus(<?= (int)$corredor['corredor_id'] ?>)">
                            <i class="fas <?= $corredor['corredor_habil'] ? 'fa-pause' : 'fa-play' ?> me-2"></i>
                            <?= $corredor['corredor_habil'] ? 'Desactivar' : 'Activar' ?>
                        </button>
                        <?php if (count($cias) > 0): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-building me-2"></i> Ver Compañías
                            </button>
                            <ul class="dropdown-menu w-100">
                                <?php foreach ($cias as $cia): ?>
                                <?php
                                    $id   = (int)($cia['cia_id'] ?? 0);
                                    $name = $cia['cia_display_name'] ?? $cia['cia_nombre'] ?? 'Sin nombre';
                                    ?>
                                    <li>
                                    <a class="dropdown-item" href="<?= base_url('cias/show/'.$id) ?>">
                                        <i class="fas fa-eye me-2"></i><?= esc($name) ?>
                                    </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i> Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-user-tie fa-3x text-danger mb-3"></i>
                    <h5>¿Eliminar corredor?</h5>
                    <p class="mb-3">
                        Estás a punto de eliminar <strong>"<?= esc($corredor['corredor_nombre']) ?>"</strong>
                    </p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Advertencia:</strong> Esta acción no se puede deshacer.
                        <?php if (!empty($corredor['corredor_logo'])): ?>
                            <br>También se eliminará el logo asociado.
                        <?php endif; ?>
                        <?php if (count($cias) > 0): ?>
                            <br>Se eliminarán las relaciones con <?= count($cias) ?> compañía<?= count($cias) > 1 ? 's' : '' ?>.
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <form method="post" action="<?= base_url('corredores/delete/' . (int)$corredor['corredor_id']) ?>" style="display:inline;">
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

<!-- Modal ampliar logo -->
<?php if (!empty($corredor['corredor_logo'])): ?>
<div class="modal fade" id="logoModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-image me-2"></i> Logo de <?= esc($corredor['corredor_nombre']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="<?= base_url('uploads/corredores/' . $corredor['corredor_logo']) ?>" alt="<?= esc($corredor['corredor_nombre']) ?>" class="img-fluid rounded">
                <div class="mt-3">
                    <small class="text-muted"><i class="fas fa-file me-1"></i><?= esc($corredor['corredor_logo']) ?></small>
                </div>
            </div>
            <div class="modal-footer">
                <a href="<?= base_url('uploads/corredores/' . $corredor['corredor_logo']) ?>" target="_blank" class="btn btn-primary">
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
$(function () {
    // Confirmación de eliminación
    $('#confirmDeleteBtn').on('click', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Última confirmación',
            text: 'Esta acción eliminará permanentemente el corredor',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar definitivamente',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Eliminando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                form.submit();
            }
        });
    });
});

// Toggle estado
function toggleStatus(id) {
    const isActive = <?= $corredor['corredor_habil'] ? 'true' : 'false' ?>;
    const action = isActive ? 'desactivar' : 'activar';
    const newStatus = isActive ? 'inactivo' : 'activo';

    Swal.fire({
        title: `¿${action.charAt(0).toUpperCase() + action.slice(1)} corredor?`,
        text: `El corredor quedará ${newStatus}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: isActive ? '#dc3545' : '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Sí, ${action}`,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            $.post('<?= base_url('corredores/toggleStatus') ?>/' + id, {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            })
            .done(function (response) {
                if (response.success) {
                    Swal.fire({ icon: 'success', title: 'Estado actualizado', text: response.message, timer: 1500, showConfirmButton: false })
                    .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message });
                }
            })
            .fail(function () {
                Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo conectar con el servidor' });
            });
        }
    });
}
</script>
<?= $this->endSection() ?>