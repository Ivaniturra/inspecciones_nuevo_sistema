 <?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'Gestión de Valores por Comuna') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-dollar-sign text-success me-2"></i>
                        Gestión de Valores por Comuna
                    </h1>
                    <p class="text-muted mb-0">Administra los valores de las compañías por comuna y tipo de usuario</p>
                </div>
                <div>
                    <a href="<?= base_url('valores-comunas/create') ?>" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Nuevo Valor
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Estadísticas -->
    <?php if (isset($estadisticas)): ?>
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 48px; height: 48px;">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-success mb-0"><?= $estadisticas['total_valores'] ?? 0 ?></h5>
                            <small class="text-muted">Total Valores</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 48px; height: 48px;">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-primary mb-0"><?= $estadisticas['total_companias'] ?? 0 ?></h5>
                            <small class="text-muted">Compañías</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 48px; height: 48px;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-info mb-0"><?= $estadisticas['total_comunas'] ?? 0 ?></h5>
                            <small class="text-muted">Comunas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 48px; height: 48px;">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-warning mb-0"><?= $estadisticas['tipos_usuario'] ?? 0 ?></h5>
                            <small class="text-muted">Tipos Usuario</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h6>
        </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('valores-comunas/filter') ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Compañía</label>
                        <select name="cia_id" class="form-select">
                            <option value="">Todas las compañías</option>
                            <?php if (!empty($cias)): ?>
                                <?php foreach ($cias as $id => $nombre): ?>
                                    <option value="<?= $id ?>" <?= (isset($filtros['cia_id']) && $filtros['cia_id'] == $id) ? 'selected' : '' ?>>
                                        <?= esc($nombre) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Tipo de Usuario</label>
                        <select name="tipo_usuario" class="form-select">
                            <option value="">Todos los tipos</option>
                            <option value="inspector" <?= (isset($filtros['tipo_usuario']) && $filtros['tipo_usuario'] == 'inspector') ? 'selected' : '' ?>>Inspector</option>
                            <option value="compania" <?= (isset($filtros['tipo_usuario']) && $filtros['tipo_usuario'] == 'compania') ? 'selected' : '' ?>>Compañía</option>
                            <option value="supervisor" <?= (isset($filtros['tipo_usuario']) && $filtros['tipo_usuario'] == 'supervisor') ? 'selected' : '' ?>>Supervisor</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Filtrar
                            </button>
                            <a href="<?= base_url('valores-comunas') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Valores -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2 text-secondary"></i>
                        Lista de Valores por Comuna
                    </h5>
                </div>
                <div class="col-auto">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" id="searchTable" 
                               placeholder="Buscar en la tabla...">
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <?php if (!isset($valores) || empty($valores)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-dollar-sign fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay valores registrados</h5>
                    <p class="text-muted">Comienza agregando valores para las comunas</p>
                    <a href="<?= base_url('valores-comunas/create') ?>" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Crear Primer Valor
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="valoresTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0">Compañía</th>
                                <th class="border-0">Comuna</th>
                                <th class="border-0">Usuario</th>
                                <th class="border-0">Vehículo</th>
                                <th class="border-0">Valor</th>
                                <th class="border-0">Vigencia</th>
                                <th class="border-0 text-center">Estado</th>
                                <th class="border-0 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($valores as $valor): ?>
                                <tr>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <i class="fas fa-building text-primary"></i>
                                            </div>
                                            <div>
                                                <strong><?= esc($valor['cia_nombre'] ?? 'N/A') ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <i class="fas fa-map-marker-alt text-info"></i>
                                            </div>
                                            <div>
                                                <?= esc($valor['comunas_nombre'] ?? ('Comuna: ' . ($valor['comunas_id'] ?? 'N/A'))) ?>
                                                <br><small class="text-muted"><?= esc($valor['region_nombre'] ?? 'N/A') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <?php $tipo = $valor['tipo_usuario'] ?? $valor['valores_tipo_usuario'] ?? null; ?>
                                        <span class="badge bg-<?= $tipo === 'inspector' ? 'primary' : ($tipo ? 'success' : 'secondary') ?>">
                                            <?= $tipo ? ucfirst(esc($tipo)) : 'N/A' ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <div>
                                            <span class="badge bg-secondary">
                                                <?= esc($valor['tipo_vehiculo_nombre'] ?? 'N/A') ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <?php
                                            $unidad  = $valor['unidad_medida'] ?? $valor['valores_unidad_medida'] ?? 'CLP';
                                            $moneda  = $valor['moneda']        ?? $valor['valores_moneda']        ?? $unidad;
                                            $monto   = (float)($valor['valores_valor'] ?? $valor['valor'] ?? 0);
                                            $simbolo = ($unidad === 'UF') ? 'UF' : (($unidad === 'UTM') ? 'UTM' : '$');
                                            $fmt     = ($simbolo === '$')
                                                        ? number_format($monto, 0, ',', '.')
                                                        : number_format($monto, 2, ',', '.');
                                        ?>
                                        <div>
                                            <strong class="text-success"><?= $simbolo . ' ' . $fmt ?></strong>
                                            <small class="text-muted d-block"><?= esc($moneda) ?></small>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div>
                                            <?php if (!empty($valor['valores_fecha_vigencia_desde'])): ?>
                                                <small class="text-success">
                                                    <i class="fas fa-play me-1"></i>
                                                    <?= date('d/m/Y', strtotime($valor['valores_fecha_vigencia_desde'])) ?>
                                                </small>
                                            <?php endif; ?>
                                            <?php if (!empty($valor['valores_fecha_vigencia_hasta'])): ?>
                                                <br>
                                                <small class="text-danger">
                                                    <i class="fas fa-stop me-1"></i>
                                                    <?= date('d/m/Y', strtotime($valor['valores_fecha_vigencia_hasta'])) ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge <?= !empty($valor['valores_activo']) ? 'bg-success' : 'bg-danger' ?>">
                                            <?= !empty($valor['valores_activo']) ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('valores-comunas/show/' . (int)$valor['valores_id']) ?>" 
                                               class="btn btn-outline-info" 
                                               data-bs-toggle="tooltip" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('valores-comunas/edit/' . (int)$valor['valores_id']) ?>" 
                                               class="btn btn-outline-warning"
                                               data-bs-toggle="tooltip" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-outline-<?= !empty($valor['valores_activo']) ? 'danger' : 'success' ?>" 
                                                    onclick="toggleStatus(<?= (int)$valor['valores_id'] ?>, '<?= !empty($valor['valores_activo']) ? 'desactivar' : 'activar' ?>')"
                                                    data-bs-toggle="tooltip" 
                                                    title="<?= !empty($valor['valores_activo']) ? 'Desactivar' : 'Activar' ?>">
                                                <i class="fas fa-<?= !empty($valor['valores_activo']) ? 'pause' : 'play' ?>"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-outline-danger" 
                                                    onclick="confirmDelete(<?= (int)$valor['valores_id'] ?>, '<?= esc(($valor['cia_nombre'] ?? '') . ' - ' . ($valor['comunas_nombre'] ?? ($valor['comunas_id'] ?? ''))) ?>')"
                                                    data-bs-toggle="tooltip" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-dollar-sign fa-3x text-danger mb-3"></i>
                    <h5>¿Eliminar valor?</h5>
                    <p class="mb-3">
                        Estás a punto de eliminar el valor para <strong id="valorInfo"></strong>
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
                <form id="deleteForm" method="post" style="display:inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">
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
.btn { 
    border-radius: 8px; 
}
.table th { 
    font-weight: 600; 
    color: #495057; 
}
.badge { 
    font-size: 0.75rem; 
}
.table td {
    vertical-align: middle;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(function () {
    // Inicializar tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Búsqueda en tabla
    $('#searchTable').on('keyup', function () {
        const value = $(this).val().toLowerCase();
        $('#valoresTable tbody tr').filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});

// Toggle estado
function toggleStatus(id, action) {
    const actionText = action === 'activar' ? 'activar' : 'desactivar';
    const newStatus = action === 'activar' ? 'activo' : 'inactivo';

    Swal.fire({
        title: `¿${actionText.charAt(0).toUpperCase() + actionText.slice(1)} valor?`,
        text: `El valor quedará ${newStatus}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: action === 'activar' ? '#198754' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Sí, ${actionText}`,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ 
                title: 'Procesando...', 
                allowOutsideClick: false, 
                didOpen: () => Swal.showLoading() 
            });

            $.post('<?= base_url('valores-comunas/toggleStatus') ?>/' + id, {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            })
            .done(function (response) {
                if (response.success) {
                    Swal.fire({ 
                        icon: 'success', 
                        title: 'Estado actualizado', 
                        text: response.message, 
                        timer: 1500, 
                        showConfirmButton: false 
                    }).then(() => location.reload());
                } else {
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'Error', 
                        text: response.message 
                    });
                }
            })
            .fail(function () {
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Error de conexión', 
                    text: 'No se pudo conectar con el servidor' 
                });
            });
        }
    });
}

// Confirmar eliminación
function confirmDelete(id, info) {
    $('#valorInfo').text(info);
    $('#deleteForm').attr('action', '<?= base_url('valores-comunas/delete') ?>/' + id);
    $('#deleteModal').modal('show');
}

// Envío del form de eliminación
$(document).on('submit', '#deleteForm', function (e) {
    e.preventDefault();
    Swal.fire({
        title: 'Eliminando valor...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    setTimeout(() => { this.submit(); }, 500);
});
</script>
<?= $this->endSection() ?>
