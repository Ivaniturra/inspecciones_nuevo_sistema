<?= $this->extend('layouts/maincorredor') ?> 

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header del Dashboard -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        <?= esc($title) ?>
                    </h1>
                    <p class="text-muted mb-0">
                        Bienvenido, <?= esc($corredor_nombre) ?>
                    </p>
                </div>
                <div class="text-end">
                    <small class="text-muted">
                        Última actualización: <?= date('d/m/Y H:i') ?>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-gradient rounded-circle p-3">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title text-muted mb-1">Solicitudes Pendientes</h6>
                            <h2 class="mb-0"><?= number_format($stats['solicitudes_pendientes']) ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-gradient rounded-circle p-3">
                                <i class="fas fa-users text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title text-muted mb-1">Clientes Activos</h6>
                            <h2 class="mb-0"><?= number_format($stats['clientes_activos']) ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-gradient rounded-circle p-3">
                                <i class="fas fa-dollar-sign text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title text-muted mb-1">Comisiones del Mes</h6>
                            <h2 class="mb-0">$<?= number_format($stats['comisiones_mes']) ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="row">
        <!-- Panel de Navegación Rápida -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="<?= base_url('corredor/solicitudes') ?>" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center">
                                <i class="fas fa-file-alt fa-2x mb-2"></i>
                                <span>Gestionar Solicitudes</span>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="<?= base_url('corredor/clientes') ?>" class="btn btn-outline-success w-100 h-100 d-flex flex-column justify-content-center">
                                <i class="fas fa-address-book fa-2x mb-2"></i>
                                <span>Mis Clientes</span>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="<?= base_url('corredor/comisiones') ?>" class="btn btn-outline-warning w-100 h-100 d-flex flex-column justify-content-center">
                                <i class="fas fa-chart-line fa-2x mb-2"></i>
                                <span>Comisiones</span>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="<?= base_url('corredor/reportes') ?>" class="btn btn-outline-info w-100 h-100 d-flex flex-column justify-content-center">
                                <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                <span>Reportes</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Actividad Reciente -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>
                        Actividad Reciente
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Ejemplo de actividad reciente -->
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Nueva solicitud</h6>
                                <p class="text-muted small mb-1">Cliente: Juan Pérez</p>
                                <small class="text-muted">Hace 2 horas</small>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Comisión generada</h6>
                                <p class="text-muted small mb-1">$250.000</p>
                                <small class="text-muted">Ayer</small>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Cliente actualizado</h6>
                                <p class="text-muted small mb-1">María González</p>
                                <small class="text-muted">Hace 2 días</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="<?= base_url('corredor/actividad') ?>" class="btn btn-sm btn-outline-secondary">
                            Ver toda la actividad
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos/Estadísticas (opcional) -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-area me-2"></i>
                        Resumen Mensual
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="border-end">
                                <h4 class="text-primary mb-1">15</h4>
                                <small class="text-muted">Solicitudes este mes</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-end">
                                <h4 class="text-success mb-1">12</h4>
                                <small class="text-muted">Aprobadas</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-end">
                                <h4 class="text-warning mb-1">2</h4>
                                <small class="text-muted">En proceso</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-danger mb-1">1</h4>
                            <small class="text-muted">Rechazadas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS adicional para la timeline -->
<style>
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--bs-border-color);
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
}

.timeline-marker {
    position: absolute;
    left: -15px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px var(--bs-border-color);
}

.timeline-content {
    background: var(--bs-light);
    padding: 0.75rem;
    border-radius: 0.375rem;
    border-left: 3px solid var(--bs-primary);
}
</style>
<?= $this->endSection() ?>