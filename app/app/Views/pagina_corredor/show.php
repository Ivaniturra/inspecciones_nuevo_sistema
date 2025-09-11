<?= $this->extend('layouts/maincorredor') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    .status-badge {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
        border-radius: 50px;
    }
    .status-pendiente { background-color: #fff3cd; color: #856404; }
    .status-en_proceso { background-color: #d1ecf1; color: #0c5460; }
    .status-completada { background-color: #d4edda; color: #155724; }
    .status-cancelada { background-color: #f8d7da; color: #721c24; }
    
    .table-actions {
        white-space: nowrap;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .stats-card {
        transition: transform 0.2s;
    }
    
    .stats-card:hover {
        transform: translateY(-2px);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Mensajes Flash -->
    <?php if (session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= session('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-tachometer-alt me-2 text-primary"></i>
                        <?= esc($title) ?>
                    </h1>
                    <p class="text-muted mb-0">Bienvenido, <?= esc($corredor_nombre) ?></p>
                </div>
                <div>
                    <a href="<?= base_url('corredor/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nueva Inspección
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-1">Solicitudes</h6>
                            <h3 class="mb-0 text-warning"><?= number_format($stats['solicitudes_pendientes']) ?></h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-1">En Inspector</h6>
                            <h3 class="mb-0 text-info"><?= number_format($stats['en_proceso']) ?></h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-search fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-1">Aceptadas</h6>
                            <h3 class="mb-0 text-success"><?= number_format($stats['completadas_mes']) ?></h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-thumbs-up fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>

    <!-- Filtros rápidos -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-2">
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-primary btn-sm filter-btn active" data-filter="all">
                            <i class="fas fa-list me-1"></i>Todas (<?= count($inspecciones) ?>)
                        </button>
                        <button class="btn btn-outline-warning btn-sm filter-btn" data-filter="pendiente">
                            <i class="fas fa-clock me-1"></i>Pendientes (<?= $stats['solicitudes_pendientes'] ?>)
                        </button>
                        <button class="btn btn-outline-info btn-sm filter-btn" data-filter="en_proceso">
                            <i class="fas fa-cog me-1"></i>En Proceso (<?= $stats['en_proceso'] ?>)
                        </button>
                        <button class="btn btn-outline-success btn-sm filter-btn" data-filter="completada">
                            <i class="fas fa-check me-1"></i>Completadas (<?= $stats['completadas_mes'] ?>)
                        </button>
                        <button class="btn btn-outline-danger btn-sm filter-btn" data-filter="cancelada">
                            <i class="fas fa-times me-1"></i>Canceladas
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <?php if (empty($inspecciones)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay inspecciones registradas</h5>
                        <p class="text-muted">Comienza creando tu primera inspección</p>
                        <a href="<?= base_url('corredor/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Nueva Inspección
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table id="inspeccionesTable" class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Asegurado</th>
                                    <th>RUT</th>
                                    <th>Patente</th>
                                    <th>Vehículo</th>
                                    <th>Compañía</th>
                                    <th>Estado</th>
                                    <th>Fecha Creación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inspecciones as $inspeccion): ?>
                                <tr>
                                    <td><strong>#<?= $inspeccion['inspeccion_id'] ?></strong></td>
                                    <td><?= esc($inspeccion['asegurado']) ?></td>
                                    <td><code><?= esc($inspeccion['rut']) ?></code></td>
                                    <td><span class="badge bg-secondary"><?= esc($inspeccion['patente']) ?></span></td>
                                    <td>
                                        <small class="text-muted d-block"><?= esc($inspeccion['marca'] ?? 'N/A') ?></small>
                                        <strong><?= esc($inspeccion['modelo'] ?? 'N/A') ?></strong>
                                    </td>
                                    <td><?= esc($inspeccion['cia_nombre'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge status-badge status-<?= $inspeccion['estado'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', $inspeccion['estado'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?= date('d/m/Y H:i', strtotime($inspeccion['created_at'])) ?></small>
                                    </td>
                                    <td class="table-actions">
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('corredor/show/' . $inspeccion['inspeccion_id']) ?>" 
                                               class="btn btn-outline-primary btn-sm" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if (in_array($inspeccion['estado'], ['pendiente', 'en_proceso'])): ?>
                                            <a href="<?= base_url('corredor/edit/' . $inspeccion['inspeccion_id']) ?>" 
                                               class="btn btn-outline-secondary btn-sm" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php endif; ?>
                                            <?php if ($inspeccion['estado'] === 'Solicitud'): ?>
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm" 
                                                    title="Eliminar"
                                                    onclick="confirmarEliminacion(<?= $inspeccion['inspeccion_id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Solo inicializar DataTable si hay datos
    <?php if (!empty($inspecciones)): ?>
    var table = $('#inspeccionesTable').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        responsive: true,
        order: [[7, 'desc']], // Ordenar por fecha de creación descendente
        pageLength: 25,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
    });

    // Filtros por estado
    $('.filter-btn').on('click', function() {
        var filter = $(this).data('filter');
        
        // Actualizar apariencia de botones
        $('.filter-btn').removeClass('btn-primary').addClass('btn-outline-secondary').removeClass('active');
        $(this).removeClass('btn-outline-secondary').addClass('btn-primary').addClass('active');
        
        // Aplicar filtro
        if (filter === 'all') {
            table.column(6).search('').draw();
        } else {
            table.column(6).search(filter).draw();
        }
    });
    <?php endif; ?>

    // Auto-ocultar alertas después de 5 segundos
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
});

function confirmarEliminacion(id) {
    if (confirm('¿Estás seguro de que deseas eliminar esta inspección?\n\nEsta acción no se puede deshacer.')) {
        // Mostrar loader
        $('body').append('<div id="loader" class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0,0,0,0.5); z-index: 9999;"><div class="spinner-border text-light" role="status"></div></div>');
        
        window.location.href = '<?= base_url('corredor/delete/') ?>' + id;
    }
}

// Función para recargar estadísticas (opcional)
function recargarEstadisticas() {
    $.get('<?= base_url('corredor/stats') ?>', function(data) {
        if (data.success) {
            // Actualizar valores de estadísticas
            $('.stats-card .text-warning').text(data.stats.solicitudes_pendientes);
            $('.stats-card .text-info').text(data.stats.en_proceso);
            $('.stats-card .text-success').text(data.stats.completadas_mes);
            $('.stats-card .text-primary').text(data.stats.total_inspecciones);
        }
    });
}
</script>
<?= $this->endSection() ?>