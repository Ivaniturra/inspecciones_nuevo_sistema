<a href="mailto:<?= esc($inspeccion['user_email']) ?>" class="text-decoration-none">
                                <i class="fas fa-envelope me-1"></i>
                                <?= esc($inspeccion['user_email']) ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Auto-ocultar alertas después de 5 segundos
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
});
</script>
<?= $this->endSection() ?><?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
    .info-card {
        border-left: 4px solid #007bff;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .status-badge {
        font-size: 1rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
    }
    
    .status-pendiente { background-color: #fff3cd; color: #856404; }
    .status-en_proceso { background-color: #d1ecf1; color: #0c5460; }
    .status-completada { background-color: #d4edda; color: #155724; }
    .status-cancelada { background-color: #f8d7da; color: #721c24; }
    
    .detail-item {
        border-bottom: 1px solid #e9ecef;
        padding: 0.75rem 0;
    }
    
    .detail-item:last-child {
        border-bottom: none;
    }
    
    .detail-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.25rem;
    }
    
    .detail-value {
        color: #212529;
        font-size: 1.1rem;
    }
    
    .timeline-item {
        border-left: 2px solid #dee2e6;
        padding-left: 1rem;
        margin-left: 0.5rem;
        padding-bottom: 1rem;
    }
    
    .timeline-item:last-child {
        border-left: 2px solid transparent;
    }
    
    .timeline-marker {
        width: 12px;
        height: 12px;
        background-color: #007bff;
        border-radius: 50%;
        margin-left: -7px;
        margin-top: 0.25rem;
        position: absolute;
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
                        <i class="fas fa-eye me-2 text-primary"></i>
                        <?= esc($title) ?>
                    </h1>
                    <p class="text-muted mb-0">
                        Creada el <?= date('d/m/Y \a \l\a\s H:i', strtotime($inspeccion['inspecciones_created_at'] ?? $inspeccion['created_at'])) ?>
                    </p>
                </div>
                <div>
                    <div class="btn-group" role="group">
                        <a href="<?= base_url('corredor') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                        <?php if (in_array(($inspeccion['inspecciones_estado'] ?? $inspeccion['estado']), ['pendiente', 'en_proceso'])): ?>
                        <a href="<?= base_url('corredor/edit/' . $inspeccion['inspeccion_id']) ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Editar
                        </a>
                        <?php endif; ?>
                        <button type="button" class="btn btn-success" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estado de la inspección -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="mb-3">Estado Actual</h5>
                    <span class="badge status-badge status-<?= $inspeccion['inspecciones_estado'] ?? $inspeccion['estado'] ?>">
                        <?= ucfirst(str_replace('_', ' ', $inspeccion['inspecciones_estado'] ?? $inspeccion['estado'])) ?>
                    </span>
                    <p class="text-muted mt-2 mb-0">
                        Última actualización: <?= date('d/m/Y H:i', strtotime($inspeccion['inspecciones_updated_at'] ?? $inspeccion['updated_at'] ?? $inspeccion['inspecciones_created_at'] ?? $inspeccion['created_at'])) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información del Asegurado -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm info-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>
                        Información del Asegurado
                    </h5>
                </div>
                <div class="card-body">
                    <div class="detail-item">
                        <div class="detail-label">Nombre Completo</div>
                        <div class="detail-value"><?= esc($inspeccion['inspecciones_asegurado'] ?? $inspeccion['asegurado']) ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">RUT</div>
                        <div class="detail-value">
                            <code class="fs-6"><?= esc($inspeccion['inspecciones_rut'] ?? $inspeccion['rut']) ?></code>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Dirección</div>
                        <div class="detail-value"><?= esc($inspeccion['inspecciones_direccion'] ?? $inspeccion['direccion']) ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Comuna</div>
                        <div class="detail-value"><?= esc($inspeccion['comunas_nombre'] ?? 'No especificada') ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Teléfono Celular</div>
                        <div class="detail-value">
                            <a href="tel:<?= esc($inspeccion['inspecciones_celular'] ?? $inspeccion['celular']) ?>" class="text-decoration-none">
                                <i class="fas fa-phone me-1"></i>
                                <?= esc($inspeccion['inspecciones_celular'] ?? $inspeccion['celular']) ?>
                            </a>
                        </div>
                    </div>
                    
                    <?php if (!empty($inspeccion['inspecciones_telefono'] ?? $inspeccion['telefono'])): ?>
                    <div class="detail-item">
                        <div class="detail-label">Teléfono Fijo</div>
                        <div class="detail-value">
                            <a href="tel:<?= esc($inspeccion['inspecciones_telefono'] ?? $inspeccion['telefono']) ?>" class="text-decoration-none">
                                <i class="fas fa-phone me-1"></i>
                                <?= esc($inspeccion['inspecciones_telefono'] ?? $inspeccion['telefono']) ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Información del Vehículo -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm info-card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-car me-2"></i>
                        Información del Vehículo
                    </h5>
                </div>
                <div class="card-body">
                    <div class="detail-item">
                        <div class="detail-label">Patente</div>
                        <div class="detail-value">
                            <span class="badge bg-dark fs-6"><?= esc($inspeccion['inspecciones_patente'] ?? $inspeccion['patente']) ?></span>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Marca</div>
                        <div class="detail-value"><?= esc($inspeccion['inspecciones_marca'] ?? $inspeccion['marca']) ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Modelo</div>
                        <div class="detail-value"><?= esc($inspeccion['inspecciones_modelo'] ?? $inspeccion['modelo']) ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Año</div>
                        <div class="detail-value"><?= esc($inspeccion['inspecciones_año'] ?? $inspeccion['año'] ?? 'No especificado') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información de la Compañía -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm info-card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        Compañía de Seguros
                    </h5>
                </div>
                <div class="card-body">
                    <div class="detail-item">
                        <div class="detail-label">Compañía</div>
                        <div class="detail-value"><?= esc($inspeccion['cia_nombre'] ?? 'No especificada') ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Número de Póliza</div>
                        <div class="detail-value">
                            <code class="fs-6"><?= esc($inspeccion['inspecciones_n_poliza'] ?? $inspeccion['n_poliza'] ?? 'No especificada') ?></code>
                        </div>
                    </div>
                    
                    <?php if (!empty($inspeccion['cia_email'])): ?>
                    <div class="detail-item">
                        <div class="detail-label">Email de la Compañía</div>
                        <div class="detail-value">
                            <a href="mailto:<?= esc($inspeccion['cia_email']) ?>" class="text-decoration-none">
                                <i class="fas fa-envelope me-1"></i>
                                <?= esc($inspeccion['cia_email']) ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($inspeccion['cia_telefono'])): ?>
                    <div class="detail-item">
                        <div class="detail-label">Teléfono de la Compañía</div>
                        <div class="detail-value">
                            <a href="tel:<?= esc($inspeccion['cia_telefono']) ?>" class="text-decoration-none">
                                <i class="fas fa-phone me-1"></i>
                                <?= esc($inspeccion['cia_telefono']) ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Información del Corredor -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm info-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tie me-2"></i>
                        Información del Corredor
                    </h5>
                </div>
                <div class="card-body">
                    <div class="detail-item">
                        <div class="detail-label">Corredor Asignado</div>
                        <div class="detail-value"><?= esc($inspeccion['user_nombre'] ?? session('user_name') ?? 'No especificado') ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Email del Corredor</div>
                        <div class="detail-value">
                            <a href="mailto:<?= esc($inspeccion['user_email'] ?? session('user_email')) ?>" class="text-decoration-none">
                                <i