<?= $this->extend('layouts/maincorredor') ?>
<?= $this->extend('layouts/main') ?>

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
                        <
