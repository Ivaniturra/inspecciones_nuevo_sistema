<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'Gestión de Valores por Comuna') ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
/* Estilos para valores deshabilitados */
.valor-disabled {
    opacity: 0.7;
    background-color: #f8f9fa !important;
}

.valor-disabled .valor-amount {
    text-decoration: line-through;
    color: #6c757d !important;
}

.valor-disabled .badge {
    filter: grayscale(50%);
}

.valor-disabled td > div > strong {
    color: #6c757d !important;
    text-decoration: line-through;
}

/* Indicador de estado */
.status-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
}

.status-active {
    background-color: #28a745;
    box-shadow: 0 0 5px rgba(40, 167, 69, 0.5);
}

.status-inactive {
    background-color: #dc3545;
    box-shadow: 0 0 5px rgba(220, 53, 69, 0.5);
}

/* Toggle switch mejorado */
.status-toggle {
    width: 50px;
    height: 25px;
    cursor: pointer;
}

.status-toggle:checked {
    background-color: #198754;
    border-color: #198754;
}

.status-toggle:not(:checked) {
    background-color: #dc3545;
    border-color: #dc3545;
}

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
                            <h5 class="text-success mb-0" id="total-valores"><?= $estadisticas['total_valores'] ?? 0 ?></h5>
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
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-warning mb-0" id="activos-count">0</h5>
                            <small class="text-muted">Activos</small>
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
                    <div class="col-md-3">
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
                    
                    <div class="col-md-3">
                        <label class="form-label">Tipo de Usuario</label>
                        <select name="tipo_usuario" class="form-select">
                            <option value="">Todos los tipos</option>
                            <option value="inspector" <?= (isset($filtros['tipo_usuario']) && $filtros['tipo_usuario'] == 'inspector') ? 'selected' : '' ?>>Inspector</option>
                            <option value="compania" <?= (isset($filtros['tipo_usuario']) && $filtros['tipo_usuario'] == 'compania') ? 'selected' : '' ?>>Compañía</option>
                            <option value="supervisor" <?= (isset($filtros['tipo_usuario']) && $filtros['tipo_usuario'] == 'supervisor') ? 'selected' : '' ?>>Supervisor</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            <option value="1" <?= (isset($filtros['estado']) && $filtros['estado'] == '1') ? 'selected' : '' ?>>Activos</option>
                            <option value="0" <?= (isset($filtros['estado']) && $filtros['estado'] == '0') ? 'selected' : '' ?>>Inactivos</option>
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
                                <tr data-id="<?= $valor['valores_id'] ?>" 
                                    data-estado="<?= !empty($valor['valores_activo']) ? 'activo' : 'inactivo' ?>"
                                    data-activo="<?= !empty($valor['valores_activo']) ? '1' : '0' ?>"
                                    class="<?= empty($valor['valores_activo']) ? 'valor-disabled' : '' ?>">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <span class="status-indicator <?= !empty($valor['valores_activo']) ? 'status-active' : 'status-inactive' ?>"></span>
                                            <div class="me-2">
                                                <i class="fas fa-building text-primary"></i>
                                            </div>
                                            <div>
                                                <strong class="<?= empty($valor['valores_activo']) ? 'text-muted' : '' ?>">
                                                    <?= esc($valor['cia_nombre'] ?? 'N/A') ?>
                                                </strong>
                                                <?php if (empty($valor['valores_activo'])): ?>
                                                    <small class="text-danger ms-1">
                                                        <i class="fas fa-ban"></i>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <i class="fas fa-map-marker-alt text-info"></i>
                                            </div>
                                            <div>
                                                <span class="<?= empty($valor['valores_activo']) ? 'text-muted' : '' ?>">
                                                    <?= esc($valor['comunas_nombre'] ?? ('Comuna: ' . ($valor['comunas_id'] ?? 'N/A'))) ?>
                                                </span>
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
                                            <strong class="text-success valor-amount <?= empty($valor['valores_activo']) ? 'text-muted' : '' ?>">
                                                <?= $simbolo . ' ' . $fmt ?>
                                            </strong>
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
                                        <div class="d-flex align-items-center justify-content-center">
                                            <!-- Badge de estado -->
                                            <span class="badge me-2 <?= !empty($valor['valores_activo']) ? 'bg-success' : 'bg-danger' ?>">
                                                <i class="fas <?= !empty($valor['valores_activo']) ? 'fa-check' : 'fa-times' ?> me-1"></i>
                                                <?= !empty($valor['valores_activo']) ? 'Activo' : 'Inactivo' ?>
                                            </span>
                                            
                                            <!-- Toggle switch -->
                                            <div class="form-check form-switch">
                                                <input class="form-check-input status-toggle"
                                                    type="checkbox"
                                                    <?= !empty($valor['valores_activo']) ? 'checked' : '' ?>
                                                    data-id="<?= $valor['valores_id'] ?>"
                                                    data-name="Valor #<?= $valor['valores_id'] ?>"
                                                    title="<?= !empty($valor['valores_activo']) ? 'Desactivar valor' : 'Activar valor' ?>">
                                            </div>
                                        </div>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    console.log('=== ValoresComunas: Document ready ===');
    
    // Token CSRF global
    let CSRF = { 
        name: '<?= csrf_token() ?>', 
        hash: '<?= csrf_hash() ?>' 
    };
    
    // Flash messages
    const flashSuccess = `<?= addslashes(session()->getFlashdata('success') ?? '') ?>`;
    if (flashSuccess) {
        Swal.fire({
            icon: 'success',
            title: flashSuccess,
            timer: 2500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }
    
    // Inicializar tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Búsqueda en tabla
    $('#searchTable').on('keyup', function () {
        const value = $(this).val().toLowerCase();
        $('#valoresTable tbody tr').filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
    
    // Contar valores activos
    function updateCounters() {
        let activos = 0;
        
        $('tr[data-activo]').each(function() {
            const activo = $(this).attr('data-activo');
            if (activo === '1') activos++;
        });
        
        $('#activos-count').text(activos);
    }
    
    // Actualizar contadores iniciales
    updateCounters();

    // ✅ TOGGLE STATUS para valores
    $('.status-toggle').on('change', function() {
        const toggle = $(this);
        const id = toggle.data('id');
        const name = toggle.data('name') || 'valor';
        const isChecked = toggle.is(':checked');
        const action = isChecked ? 'activar' : 'desactivar';
        const row = toggle.closest('tr');
        
        // Confirmación antes del cambio
        Swal.fire({
            title: `¿${action.charAt(0).toUpperCase() + action.slice(1)} valor?`,
            text: `¿Deseas ${action} este valor?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: isChecked ? '#28a745' : '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Sí, ${action}`,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                toggle.prop('disabled', true);
                
                $.ajax({
                    url: '<?= base_url('valores-comunas/toggleStatus') ?>/' + id,
                    type: 'POST',
                    data: {
                        [CSRF.name]: CSRF.hash
                    },
                    headers: {
                        'X-CSRF-TOKEN': CSRF.hash,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .done(function(response, textStatus, xhr) {
                    // Actualizar token CSRF
                    const newToken = xhr.getResponseHeader('X-CSRF-TOKEN');
                    if (newToken) {
                        CSRF.hash = newToken;
                    }
                    
                    if (response.success) {
                        // Actualizar visualmente la fila
                        updateRowStatus(row, isChecked);
                        
                        // Actualizar data-estado y data-activo
                        row.attr('data-estado', isChecked ? 'activo' : 'inactivo');
                        row.attr('data-activo', isChecked ? '1' : '0');
                        
                        // Actualizar contadores
                        updateCounters();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Estado actualizado',
                            text: `Valor ${isChecked ? 'activado' : 'desactivado'} correctamente`,
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    } else {
                        toggle.prop('checked', !isChecked);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'No se pudo cambiar el estado'
                        });
                    }
                })
                .fail(function(xhr) {
                    const newToken = xhr.getResponseHeader('X-CSRF-TOKEN');
                    if (newToken) {
                        CSRF.hash = newToken;
                    }
                    
                    toggle.prop('checked', !isChecked);
                    
                    let errorMsg = 'Error de conexión';
                    if (xhr.status === 403) errorMsg = 'Sin permisos. Recarga la página.';
                    else if (xhr.status === 419) errorMsg = 'Sesión expirada. Recarga la página.';
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg
                    });
                })
                .always(function() {
                    toggle.prop('disabled', false);
                });
            } else {
                toggle.prop('checked', !isChecked);
            }
        });
    });
    
    // Función para actualizar visualmente una fila
    function updateRowStatus(row, isActive) {
        const statusIndicator = row.find('.status-indicator');
        const statusBadge = row.find('.badge:contains("Activo"), .badge:contains("Inactivo")');
        const valorAmount = row.find('.valor-amount');
        const companyName = row.find('td:first strong');
        const toggle = row.find('.status-toggle');
        
        if (isActive) {
            // Activar valor
            row.removeClass('valor-disabled');
            statusIndicator.removeClass('status-inactive').addClass('status-active');
            statusBadge.removeClass('bg-danger').addClass('bg-success')
                .html('<i class="fas fa-check me-1"></i>Activo');
            valorAmount.removeClass('text-muted');
            companyName.removeClass('text-muted');
            row.find('.fa-ban').parent().remove();
            toggle.attr('title', 'Desactivar valor');
        } else {
            // Desactivar valor
            row.addClass('valor-disabled');
            statusIndicator.removeClass('status-active').addClass('status-inactive');
            statusBadge.removeClass('bg-success').addClass('bg-danger')
                .html('<i class="fas fa-times me-1"></i>Inactivo');
            valorAmount.addClass('text-muted');
            companyName.addClass('text-muted');
            if (!row.find('.fa-ban').length) {
                companyName.append('<small class="text-danger ms-1"><i class="fas fa-ban"></i></small>');
            }
            toggle.attr('title', 'Activar valor');
        }
    }

    // Auto-hide alerts
    $('.alert').delay(5000).fadeOut();
});
</script>
<?= $this->endSection() ?>