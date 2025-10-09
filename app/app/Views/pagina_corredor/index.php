<?= $this->extend('layouts/maincorredor') ?>

<?= $this->section('title') ?>
Dashboard Corredor
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
// FALLBACK DE SEGURIDAD
if (!isset($cias) || !is_array($cias)) {
    $cias = [];
}
$search = $search ?? '';
$ciaId = $ciaId ?? '';
$inspecciones = $inspecciones ?? [];
$stats = $stats ?? [];
?>

<div class="container-fluid">
    <!-- Header con Estadísticas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-tachometer-alt text-primary me-2"></i>
                        Dashboard de Corredor
                    </h1>
                    <p class="text-muted mb-0">Bienvenido, <?= esc($corredor_nombre) ?></p>
                </div>
                <a href="<?= base_url('corredor/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Nueva Inspección
                </a>
            </div>

            <!-- Cards de Estadísticas -->
            <div class="row g-3">
                <div class="col-md-4 col-lg-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                            <h3 class="mb-0"><?= $stats['solicitudes_pendientes'] ?? 0 ?></h3>
                            <small class="text-muted">Pendientes</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                <i class="fas fa-spinner fa-2x text-info"></i>
                            </div>
                            <h3 class="mb-0"><?= $stats['en_proceso'] ?? 0 ?></h3>
                            <small class="text-muted">En Proceso</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                            <h3 class="mb-0"><?= $stats['completadas_mes'] ?? 0 ?></h3>
                            <small class="text-muted">Completadas</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                <i class="fas fa-thumbs-up fa-2x text-primary"></i>
                            </div>
                            <h3 class="mb-0"><?= $stats['aceptadas'] ?? 0 ?></h3>
                            <small class="text-muted">Aceptadas</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                <i class="fas fa-times-circle fa-2x text-danger"></i>
                            </div>
                            <h3 class="mb-0"><?= $stats['rechazadas'] ?? 0 ?></h3>
                            <small class="text-muted">Rechazadas</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-2">
                    <div class="card border-0 shadow-sm h-100 bg-primary text-white">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                <i class="fas fa-clipboard-list fa-2x"></i>
                            </div>
                            <h3 class="mb-0"><?= $stats['total_inspecciones'] ?? 0 ?></h3>
                            <small>Total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= base_url('corredor') ?>" class="row g-3">
                <div class="col-md-5">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           value="<?= esc($search) ?>" 
                           placeholder="Asegurado, patente, RUT, email...">
                </div>
                <div class="col-md-3">
                    <label for="cia_id" class="form-label">Compañía</label>
                    <select class="form-select" id="cia_id" name="cia_id">
                        <option value="">Todas las compañías</option>
                        <?php if (!empty($cias)): ?>
                            <?php foreach ($cias as $cia): ?>
                                <option value="<?= $cia['cia_id'] ?>" <?= $ciaId == $cia['cia_id'] ? 'selected' : '' ?>>
                                    <?= esc($cia['cia_display_name'] ?: $cia['cia_nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No hay compañías disponibles</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Buscar
                    </button>
                    <a href="<?= base_url('corredor') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Limpiar
                    </a>
                </div>
            </form>
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
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Tabla de Inspecciones -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-clipboard-list text-primary me-2"></i>
                Mis Inspecciones
                <span class="badge bg-light text-dark ms-2"><?= count($inspecciones) ?></span>
            </h5>
        </div>

        <div class="card-body p-0">
            <?php if (empty($inspecciones)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay inspecciones registradas</h5>
                    <p class="text-muted">Comienza creando tu primera inspección</p>
                    <a href="<?= base_url('corredor/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Nueva Inspección
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">ID</th>
                                <th>Asegurado</th>
                                <th>Patente</th>
                                <th>Compañía</th>
                                <th>Tipo</th>
                                <th>Comuna</th>
                                <th class="text-center">Estado</th>
                                <th>Fecha</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inspecciones as $insp): ?>
                                <tr>
                                    <td class="fw-bold">#<?= $insp['inspecciones_id'] ?></td>
                                    <td>
                                        <div>
                                            <a href="<?= base_url('corredor/show/' . $insp['inspecciones_id']) ?>" 
                                               class="text-decoration-none fw-medium">
                                                <?= esc($insp['inspecciones_asegurado']) ?>
                                            </a>
                                            <?php if (!empty($insp['inspecciones_rut'])): ?>
                                                <br><small class="text-muted"><?= esc($insp['inspecciones_rut']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary font-monospace">
                                            <?= esc($insp['inspecciones_patente']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?= esc($insp['cia_display_name'] ?: $insp['cia_nombre']) ?></small>
                                    </td>
                                    <td>
                                        <small><?= esc($insp['tipo_inspeccion_nombre'] ?? 'N/A') ?></small>
                                    </td>
                                    <td>
                                        <small><?= esc($insp['comunas_nombre'] ?? 'N/A') ?></small>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $estadoColor = $insp['estado_color'] ?? '#6c757d';
                                        $estadoNombre = $insp['estado_nombre'] ?? 'Sin estado';
                                        ?>
                                        <span class="badge" style="background-color: <?= esc($estadoColor) ?>">
                                            <?= esc($estadoNombre) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            <?= date('d/m/Y', strtotime($insp['inspecciones_created_at'])) ?>
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('corredor/show/' . $insp['inspecciones_id']) ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($insp['estado_id'] == 1): // Solo editar si está en estado Solicitud ?>
                                                <a href="<?= base_url('corredor/edit/' . $insp['inspecciones_id']) ?>" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
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
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.card {
    border-radius: 12px;
}
.card-header {
    border-radius: 12px 12px 0 0 !important;
}
.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(function() {
    // Auto-submit del filtro al cambiar compañía
    $('#cia_id').on('change', function() {
        $(this).closest('form').submit();
    });
});
</script>
<?= $this->endSection() ?>