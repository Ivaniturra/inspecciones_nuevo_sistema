 <?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'Gestión de Estados') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-tags text-primary me-2"></i>
                        Gestión de Estados
                    </h1>
                    <p class="text-muted mb-0">Administra los estados del sistema de inspecciones</p>
                </div>
            </div>
        </div>
    </div> 

    <!-- Stats Card -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 48px; height: 48px;">
                                <i class="fas fa-tags"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-primary mb-0"><?= isset($estados) ? count($estados) : 0 ?></h5>
                            <small class="text-muted">Estados Disponibles</small>
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
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 48px; height: 48px;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-success mb-0">Sistema</h5>
                            <small class="text-muted">Estados del Sistema</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Estados -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2 text-secondary"></i>
                        Lista de Estados del Flujo
                    </h5>
                </div>
                <div class="col-auto">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" id="searchTable" 
                               placeholder="Buscar estado...">
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <?php if (!isset($estados) || empty($estados)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay estados registrados</h5>
                    <p class="text-muted">Los estados se crearon automáticamente con la migración del sistema</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="estadosTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0" style="width: 80px">Orden</th>
                                <th class="border-0">Estado</th>
                                <th class="border-0" style="width: 200px">Creado</th>
                                <th class="border-0" style="width: 200px">Modificado</th>
                                <th class="border-0 text-center" style="width: 120px">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($estados as $index => $estado): ?>
                                <tr>
                                    <td class="align-middle">
                                        <span class="badge bg-primary"><?= (int)$estado['estado_id'] ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <?php 
                                                // Íconos según el tipo de estado (compatibles con Font Awesome 5)
                                                $icon = match($estado['estado_nombre']) {
                                                    'Solicitud' => 'fa-plus-circle text-info',
                                                    'Coordinador' => 'fa-user-tie text-primary',
                                                    'Es Control de Calidad' => 'fa-clipboard-check text-warning',
                                                    'En Inspector' => 'fa-user-cog text-secondary',
                                                    'Terminada' => 'fa-flag-checkered text-success',
                                                    'Aceptada' => 'fa-check-circle text-success',
                                                    'Rechazada' => 'fa-times-circle text-danger',
                                                    default => 'fa-tag text-primary'
                                                };
                                                ?>
                                                <i class="fas <?= $icon ?>"></i>
                                            </div>
                                            <div>
                                                <strong><?= esc($estado['estado_nombre']) ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    <?= match($estado['estado_nombre']) {
                                                        'Solicitud' => 'Estado inicial del proceso',
                                                        'Coordinador' => 'Asignado al coordinador',
                                                        'Es Control de Calidad' => 'En revisión de calidad',
                                                        'En Inspector' => 'Asignado a inspector',
                                                        'Terminada' => 'Inspección completada',
                                                        'Aceptada' => 'Proceso aceptado',
                                                        'Rechazada' => 'Proceso rechazado',
                                                        default => 'Estado del sistema'
                                                    } ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?= date('d/m/Y H:i', strtotime($estado['estado_created_at'])) ?>
                                        </small>
                                    </td>
                                    <td class="align-middle">
                                        <?php if (!empty($estado['estado_updated_at']) && $estado['estado_updated_at'] !== $estado['estado_created_at']): ?>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-edit me-1"></i>
                                                <?= date('d/m/Y H:i', strtotime($estado['estado_updated_at'])) ?>
                                            </small>
                                        <?php else: ?>
                                            <small class="text-muted">
                                                <i class="fas fa-minus me-1"></i>Sin cambios
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="<?= base_url('estados/show/' . $estado['estado_id']) ?>" 
                                           class="btn btn-outline-info btn-sm" 
                                           data-bs-toggle="tooltip" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($estados)): ?>
        <div class="card-footer bg-light">
            <div class="row align-items-center">
                <div class="col">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Estos estados representan el flujo del proceso de inspecciones
                    </small>
                </div>
                <div class="col-auto">
                    <small class="text-muted">
                        Total: <strong><?= count($estados) ?></strong> estados
                    </small>
                </div>
            </div>
        </div>
        <?php endif; ?>
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
.card-footer {
    border-radius: 0 0 15px 15px !important;
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
        $('#estadosTable tbody tr').filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});
</script>
<?= $this->endSection() ?>