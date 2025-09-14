<?= $this->extend('layouts/maincorredor') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    .status-badge {
        font-size: 0.8rem;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-weight: 600;
        border: 1px solid rgba(255,255,255,0.2);
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    .table-actions {
        white-space: nowrap;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .stats-card {
        transition: transform 0.2s;
        border-left: 4px solid var(--bs-primary);
    }
    
    .stats-card:hover {
        transform: translateY(-2px);
    }

    .estado-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        border: 2px solid rgba(255,255,255,0.8);
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }

    .filter-estado {
        border: 2px solid transparent;
        transition: all 0.2s ease;
    }

    .filter-estado.active {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
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
                            <h6 class="card-title text-muted mb-1">En Proceso</h6>
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
                            <h6 class="card-title text-muted mb-1">Completadas</h6>
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

    <!-- Filtros por Estados del Sistema -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-primary btn-sm filter-estado active" data-filter="all">
                            <i class="fas fa-list me-1"></i>Todas (<?= count($inspecciones) ?>)
                        </button>
                        
                        <?php if (!empty($estados)): ?>
                            <?php foreach ($estados as $estadoId => $estadoData): ?>
                                <?php 
                                // Contar inspecciones por estado
                                $count = count(array_filter($inspecciones, function($insp) use ($estadoId) {
                                    return $insp['estado_id'] == $estadoId;
                                }));
                                
                                // Obtener color de texto apropiado
                                $bgColor = $estadoData['color'];
                                $textColor = $this->getTextColorForBackground($bgColor);
                                ?>
                                <button class="btn btn-sm filter-estado" 
                                        data-filter="<?= $estadoId ?>"
                                        style="background-color: <?= $bgColor ?>; color: <?= $textColor ?>; border-color: <?= $bgColor ?>;">
                                    <span class="estado-dot me-1" style="background-color: <?= $textColor ?>; opacity: 0.8;"></span>
                                    <?= esc($estadoData['nombre']) ?> (<?= $count ?>)
                                </button>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
                                <tr data-estado="<?= $inspeccion['estado_id'] ?? 'sin-estado' ?>">
                                    <td><strong>#<?= $inspeccion['inspecciones_id'] ?></strong></td>
                                    <td><?= esc($inspeccion['inspecciones_asegurado']) ?></td>
                                    <td><code><?= esc($inspeccion['inspecciones_rut']) ?></code></td>
                                    <td><span class="badge bg-secondary"><?= esc($inspeccion['inspecciones_patente']) ?></span></td>
                                    <td>
                                        <small class="text-muted d-block"><?= esc($inspeccion['inspecciones_marca'] ?? 'N/A') ?></small>
                                        <strong><?= esc($inspeccion['inspecciones_modelo'] ?? 'N/A') ?></strong>
                                    </td>
                                    <td><?= esc($inspeccion['cia_nombre'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php 
                                        $estadoId = $inspeccion['estado_id'] ?? null;
                                        if ($estadoId && isset($estados[$estadoId])):
                                            $estadoInfo = $estados[$estadoId];
                                            $bgColor = $estadoInfo['color'];
                                            $textColor = $this->getTextColorForBackground($bgColor);
                                        ?>
                                            <span class="status-badge" 
                                                  style="background-color: <?= $bgColor ?>; color: <?= $textColor ?>;">
                                                <span class="estado-dot me-1" style="background-color: <?= $textColor ?>; opacity: 0.7;"></span>
                                                <?= esc($estadoInfo['nombre']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Sin Estado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?= date('d/m/Y H:i', strtotime($inspeccion['inspecciones_created_at'])) ?></small>
                                    </td>
                                    <td class="table-actions">
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('corredor/show/' . $inspeccion['inspecciones_id']) ?>" 
                                               class="btn btn-outline-primary btn-sm" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if (in_array($inspeccion['inspecciones_estado'], ['pendiente', 'en_proceso'])): ?>
                                            <a href="<?= base_url('corredor/edit/' . $inspeccion['inspecciones_id']) ?>" 
                                               class="btn btn-outline-secondary btn-sm" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php endif; ?>
                                            <?php if ($inspeccion['inspecciones_estado'] === 'pendiente'): ?>
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm" 
                                                    title="Eliminar"
                                                    onclick="confirmarEliminacion(<?= $inspeccion['inspecciones_id'] ?>)">
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

<?php
// Helper function para calcular color de texto
function getTextColorForBackground($hexColor) {
    // Remover # si existe
    $hex = ltrim($hexColor, '#');
    
    // Convertir a RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Calcular luminancia
    $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    
    return $luminance > 0.5 ? '#000000' : '#ffffff';
}
?>
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
    $('.filter-estado').on('click', function() {
        var filter = $(this).data('filter');
        
        // Actualizar apariencia de botones
        $('.filter-estado').removeClass('active');
        $(this).addClass('active');
        
        // Aplicar filtro
        if (filter === 'all') {
            // Mostrar todas las filas
            $('#inspeccionesTable tbody tr').show();
            table.draw();
        } else {
            // Filtrar por estado_id
            $('#inspeccionesTable tbody tr').hide();
            $('#inspeccionesTable tbody tr[data-estado="' + filter + '"]').show();
            table.draw();
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
        }
    });
}
</script>
<?= $this->endSection() ?>