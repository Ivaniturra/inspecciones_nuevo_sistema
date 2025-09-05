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
                        <i class="fas fa-clipboard-list me-2 text-primary"></i>
                        <?= esc($title) ?>
                    </h1>
                    <p class="text-muted mb-0">Gestión de inspecciones vehiculares</p>
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
                        <button class="btn btn-outline-secondary btn-sm filter-btn" data-filter="all">
                            <i class="fas fa-list me-1"></i>Todas
                        </button>
                        <button class="btn btn-outline-warning btn-sm filter-btn" data-filter="pendiente">
                            <i class="fas fa-clock me-1"></i>Pendientes
                        </button>
                        <button class="btn btn-outline-info btn-sm filter-btn" data-filter="en_proceso">
                            <i class="fas fa-cog me-1"></i>En Proceso
                        </button>
                        <button class="btn btn-outline-success btn-sm filter-btn" data-filter="completada">
                            <i class="fas fa-check me-1"></i>Completadas
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
                                        <small class="text-muted d-block"><?= esc($inspeccion['marca']) ?></small>
                                        <strong><?= esc($inspeccion['modelo']) ?></strong>
                                    </td>
                                    <td><?= esc($inspeccion['cia_nombre']) ?></td>
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
                                            <a href="<?= base_url('inspecciones/show/' . $inspeccion['inspeccion_id']) ?>" 
                                               class="btn btn-outline-primary btn-sm" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('inspecciones/edit/' . $inspeccion['inspeccion_id']) ?>" 
                                               class="btn btn-outline-secondary btn-sm" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm" 
                                                    title="Eliminar"
                                                    onclick="confirmarEliminacion(<?= $inspeccion['inspeccion_id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
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
    // Inicializar DataTable
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
        $('.filter-btn').removeClass('btn-primary').addClass('btn-outline-secondary');
        $(this).removeClass('btn-outline-secondary').addClass('btn-primary');
        
        // Aplicar filtro
        if (filter === 'all') {
            table.column(6).search('').draw();
        } else {
            table.column(6).search(filter).draw();
        }
    });
});

function confirmarEliminacion(id) {
    if (confirm('¿Estás seguro de que deseas eliminar esta inspección? Esta acción no se puede deshacer.')) {
        // Aquí puedes implementar la eliminación via AJAX
        window.location.href = '<?= base_url('inspecciones/delete/') ?>' + id;
    }
}
</script>
<?= $this->endSection() ?>