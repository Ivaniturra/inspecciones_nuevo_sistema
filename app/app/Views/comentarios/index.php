<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Gestión de Comentarios
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
/* Estilos para comentarios deshabilitados */
.comentario-disabled {
    opacity: 0.7;
    background-color: #f8f9fa !important;
}

.comentario-disabled .comentario-text {
    text-decoration: line-through;
    color: #6c757d !important;
}

.comentario-disabled .badge {
    filter: grayscale(50%);
}

/* Indicador de estado */
.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 6px;
}

.status-active {
    background-color: #28a745;
    box-shadow: 0 0 4px rgba(40, 167, 69, 0.5);
}

.status-inactive {
    background-color: #dc3545;
    box-shadow: 0 0 4px rgba(220, 53, 69, 0.5);
}

/* Toggle switch */
.status-toggle {
    width: 45px;
    height: 22px;
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

/* Mejores badges para flags */
.flag-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
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
                    <h1 class="h3 mb-0">Gestión de Comentarios</h1>
                    <p class="text-muted">Administra los comentarios del sistema organizados por compañía y perfil</p>
                </div>
                <a href="<?= base_url('comentarios/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Comentario
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="total-comentarios"><?= count($rows) ?></h4>
                            <span>Total Comentarios</span>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-comments fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="activos-count">0</h4>
                            <span>Activos</span>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="email-count">0</h4>
                            <span>Con Email</span>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-envelope fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="devolucion-count">0</h4>
                            <span>Devoluciones</span>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-undo fa-2x"></i>
                        </div>
                    </div>
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

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filtros mejorados -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="cia_id" class="form-label small text-muted">
                        <i class="fas fa-building me-1"></i>Compañía
                    </label>
                    <select name="cia_id" id="cia_id" class="form-select">
                        <option value="">Todas</option>
                        <?php foreach ($cias as $id => $nombre): ?>
                            <option value="<?= esc($id) ?>" <?= $filtros['cia_id']==$id ? 'selected' : '' ?>>
                                <?= esc($nombre) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="perfil_id" class="form-label small text-muted">
                        <i class="fas fa-user-tag me-1"></i>Perfil
                    </label>
                    <select name="perfil_id" id="perfil_id" class="form-select">
                        <option value="">Todos</option>
                        <optgroup label="🛡️ Perfiles Internos">
                            <?php foreach ($perfiles as $id => $nombre): ?>
                                <?php if (strpos($nombre, 'Interno') !== false || strpos($nombre, 'Admin') !== false): ?>
                                    <option value="<?= esc($id) ?>" <?= $filtros['perfil_id']==$id ? 'selected' : '' ?>>
                                        <?= esc($nombre) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach ?>
                        </optgroup>
                        <optgroup label="🏢 Perfiles de Compañía">
                            <?php foreach ($perfiles as $id => $nombre): ?>
                                <?php if (strpos($nombre, 'Interno') === false && strpos($nombre, 'Admin') === false): ?>
                                    <option value="<?= esc($id) ?>" <?= $filtros['perfil_id']==$id ? 'selected' : '' ?>>
                                        <?= esc($nombre) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach ?>
                        </optgroup>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="estado" class="form-label small text-muted">
                        <i class="fas fa-toggle-on me-1"></i>Estado
                    </label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" <?= (string)($filtros['estado'] ?? '') === '1' ? 'selected' : '' ?>>Activos</option>
                        <option value="0" <?= (string)($filtros['estado'] ?? '') === '0' ? 'selected' : '' ?>>Inactivos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="q" class="form-label small text-muted">
                        <i class="fas fa-search me-1"></i>Buscar
                    </label>
                    <input type="text" name="q" id="q" class="form-control" 
                           placeholder="Buscar en comentarios..." 
                           value="<?= esc($filtros['q']) ?>">
                </div>
                <div class="col-md-1">
                    <label for="per_page" class="form-label small text-muted">Mostrar</label>
                    <select name="per_page" id="per_page" class="form-select">
                        <?php foreach ([10,20,50,100] as $pp): ?>
                            <option value="<?= $pp ?>" <?= $filtros['per_page']==$pp ? 'selected' : '' ?>>
                                <?= $pp ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="btn-group w-100" role="group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="<?= base_url('comentarios') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-comments text-primary me-2"></i>
                Listado de Comentarios
                <?php if (!empty($filtros['cia_id']) || !empty($filtros['perfil_id']) || !empty($filtros['q'])): ?>
                    <small class="text-muted">
                        (<?= is_countable($rows) ? count($rows) : 0 ?> resultados filtrados)
                    </small>
                <?php endif; ?>
            </h5>
        </div>
        
        <div class="card-body p-0">
            <?php if (empty($rows)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay comentarios registrados</h5>
                    <p class="text-muted">
                        <?php if (!empty($filtros['cia_id']) || !empty($filtros['perfil_id']) || !empty($filtros['q'])): ?>
                            No se encontraron comentarios con los filtros aplicados.
                        <?php else: ?>
                            Comienza creando tu primer comentario.
                        <?php endif; ?>
                    </p>
                    <?php if (empty($filtros['cia_id']) && empty($filtros['perfil_id']) && empty($filtros['q'])): ?>
                        <a href="<?= base_url('comentarios/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Comentario
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:60px">ID</th>
                                <th style="width:160px">Compañía</th>
                                <th style="width:140px">Perfil</th>
                                <th>Comentario</th>
                                <th style="width:80px" class="text-center">ID Int.</th>
                                <th style="width:180px" class="text-center">Flags</th>
                                <th style="width:80px" class="text-center">Estado</th>
                                <th style="width:100px" class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $r): ?>
                                <tr data-id="<?= $r['comentario_id'] ?>" 
                                    data-estado="<?= !empty($r['comentario_habil']) ? 'activo' : 'inactivo' ?>"
                                    class="<?= empty($r['comentario_habil']) ? 'comentario-disabled' : '' ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="status-indicator <?= !empty($r['comentario_habil']) ? 'status-active' : 'status-inactive' ?>"></span>
                                            <span class="badge bg-light text-dark"><?= esc($r['comentario_id']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong class="<?= empty($r['comentario_habil']) ? 'text-muted' : '' ?>">
                                                <?= esc($r['cia_nombre'] ?? 'Compañía #' . $r['cia_id']) ?>
                                            </strong>
                                            <br>
                                            <small class="text-muted">ID: <?= esc($r['cia_id']) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($r['perfil_nombre'])): ?>
                                            <span class="badge <?= strpos($r['perfil_nombre'], 'Interno') !== false ? 'bg-warning' : 'bg-info' ?>">
                                                <?php if (strpos($r['perfil_nombre'], 'Interno') !== false): ?>
                                                    <i class="fas fa-shield-alt me-1"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-building me-1"></i>
                                                <?php endif; ?>
                                                <?= esc($r['perfil_nombre']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-users me-1"></i>Todos
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="comentario-text <?= empty($r['comentario_habil']) ? 'text-muted' : '' ?>" 
                                             style="max-width: 280px;" 
                                             title="<?= esc($r['comentario_nombre']) ?>">
                                            <?php 
                                            $comentario = $r['comentario_nombre'];
                                            echo esc(strlen($comentario) > 80 ? substr($comentario, 0, 80) . '...' : $comentario);
                                            ?>
                                            <?php if (empty($r['comentario_habil'])): ?>
                                                <small class="text-danger">
                                                    <i class="fas fa-ban ms-1"></i>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($r['comentario_created_at'])): ?>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                <?= date('d/m/Y H:i', strtotime($r['comentario_created_at'])) ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($r['comentario_id_cia_interno'])): ?>
                                            <span class="badge bg-warning text-dark"><?= esc($r['comentario_id_cia_interno']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-wrap justify-content-center gap-1">
                                            <?php if (!empty($r['comentario_devuelve'])): ?>
                                                <span class="badge bg-warning text-dark flag-badge" title="Requiere devolución">
                                                    <i class="fas fa-undo me-1"></i>Dev
                                                </span>
                                            <?php endif; ?>
                                            <?php if (!empty($r['comentario_elimina'])): ?>
                                                <span class="badge bg-danger flag-badge" title="Sugiere eliminación">
                                                    <i class="fas fa-trash me-1"></i>Del
                                                </span>
                                            <?php endif; ?>
                                            <?php if (!empty($r['comentario_envia_correo'])): ?>
                                                <span class="badge bg-success flag-badge" title="Envía notificación por correo">
                                                    <i class="fas fa-envelope me-1"></i>Mail
                                                </span>
                                            <?php endif; ?>
                                            <?php if (empty($r['comentario_devuelve']) && empty($r['comentario_elimina']) && empty($r['comentario_envia_correo'])): ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <!-- Badge de estado -->
                                            <span class="badge me-2 <?= !empty($r['comentario_habil']) ? 'bg-success' : 'bg-danger' ?>">
                                                <i class="fas <?= !empty($r['comentario_habil']) ? 'fa-check' : 'fa-times' ?> me-1"></i>
                                                <?= !empty($r['comentario_habil']) ? 'Activo' : 'Inactivo' ?>
                                            </span>
                                            
                                            <!-- Toggle switch -->
                                            <div class="form-check form-switch">
                                                <input class="form-check-input status-toggle"
                                                    type="checkbox"
                                                    <?= !empty($r['comentario_habil']) ? 'checked' : '' ?>
                                                    data-id="<?= $r['comentario_id'] ?>"
                                                    data-name="Comentario #<?= $r['comentario_id'] ?>"
                                                    title="<?= !empty($r['comentario_habil']) ? 'Desactivar comentario' : 'Activar comentario' ?>">
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-info view-comment-btn"
                                                    data-id="<?= $r['comentario_id'] ?>"
                                                    data-comment="<?= esc($r['comentario_nombre']) ?>"
                                                    title="Ver comentario completo">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="<?= base_url('comentarios/edit/'.$r['comentario_id']) ?>" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Editar comentario">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Paginación -->
    <?php if (!empty($rows) && isset($pager)): ?>
        <div class="d-flex justify-content-center mt-4">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    console.log('=== Comentarios: Document ready ===');
    
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
    
    // Contar estadísticas iniciales
    function updateCounters() {
        let activos = 0, email = 0, devolucion = 0;
        
        $('tr[data-habil]').each(function() {
            const habil = $(this).attr('data-habil');
            const row = $(this);
            
            // Contar activos usando el atributo data-habil
            if (habil === '1') activos++;
            
            // Contar flags
            if (row.find('.flag-badge:contains("Mail")').length) email++;
            if (row.find('.flag-badge:contains("Dev")').length) devolucion++;
        });
        
        $('#activos-count').text(activos);
        $('#email-count').text(email);
        $('#devolucion-count').text(devolucion);
    }
    
    // Actualizar contadores iniciales
    updateCounters();

    // ✅ TOGGLE STATUS para comentarios
    $('.status-toggle').on('change', function() {
        const toggle = $(this);
        const id = toggle.data('id');
        const name = toggle.data('name') || 'comentario';
        const isChecked = toggle.is(':checked');
        const action = isChecked ? 'activar' : 'desactivar';
        const row = toggle.closest('tr');
        
        // Confirmación antes del cambio
        Swal.fire({
            title: `¿${action.charAt(0).toUpperCase() + action.slice(1)} comentario?`,
            text: `¿Deseas ${action} este comentario?`,
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
                    url: '<?= base_url('comentarios/toggleStatus') ?>/' + id,
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
                        
                        // Actualizar data-estado
                        row.attr('data-estado', isChecked ? 'activo' : 'inactivo');
                        
                        // Actualizar contadores
                        updateCounters();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Estado actualizado',
                            text: `Comentario ${isChecked ? 'activado' : 'desactivado'} correctamente`,
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
        const comentarioText = row.find('.comentario-text');
        const toggle = row.find('.status-toggle');
        
        if (isActive) {
            // Activar comentario
            row.removeClass('comentario-disabled');
            statusIndicator.removeClass('status-inactive').addClass('status-active');
            statusBadge.removeClass('bg-danger').addClass('bg-success')
                .html('<i class="fas fa-check me-1"></i>Activo');
            comentarioText.removeClass('text-muted');
            comentarioText.find('.fa-ban').remove();
            toggle.attr('title', 'Desactivar comentario');
        } else {
            // Desactivar comentario
            row.addClass('comentario-disabled');
            statusIndicator.removeClass('status-active').addClass('status-inactive');
            statusBadge.removeClass('bg-success').addClass('bg-danger')
                .html('<i class="fas fa-times me-1"></i>Inactivo');
            comentarioText.addClass('text-muted');
            if (!comentarioText.find('.fa-ban').length) {
                comentarioText.append('<small class="text-danger"><i class="fas fa-ban ms-1"></i></small>');
            }
            toggle.attr('title', 'Activar comentario');
        }
    }

    // Ver comentario completo
    $('.view-comment-btn').on('click', function() {
        const comment = $(this).data('comment');
        const id = $(this).data('id');
        
        Swal.fire({
            title: `Comentario #${id}`,
            text: comment,
            icon: 'info',
            confirmButtonText: 'Cerrar',
            confirmButtonColor: '#17a2b8',
            width: 600,
            customClass: {
                content: 'text-start'
            }
        });
    });

    // Auto-hide alerts
    $('.alert').delay(5000).fadeOut();

    // Tooltip para comentarios truncados
    $('[title]').tooltip();
});
</script>
<?= $this->endSection() ?>