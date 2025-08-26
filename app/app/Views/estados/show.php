<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'Detalles del Estado') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <?php 
                        // Color del ícono según el tipo de estado
                        $iconColor = match($estado['estado_nombre']) {
                            'Solicitud' => 'bg-info',
                            'Coordinador' => 'bg-primary', 
                            'Es Control de Calidad' => 'bg-warning',
                            'En Inspector' => 'bg-secondary',
                            'Terminada' => 'bg-success',
                            'Aceptada' => 'bg-success',
                            'Rechazada' => 'bg-danger',
                            default => 'bg-primary'
                        };
                        ?>
                        <div class="<?= $iconColor ?> text-white rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-tag fa-2x"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="h3 mb-0"><?= esc($estado['estado_nombre']) ?></h1>
                        <p class="text-muted mb-0">
                            <span class="badge bg-info">ID: <?= (int)$estado['estado_id'] ?></span>
                            <span class="badge bg-secondary ms-1">Estado del Sistema</span>
                        </p>
                    </div>
                </div>

                <div>
                    <a href="<?= base_url('estados') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver a Estados
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="row">
                <!-- Información Principal -->
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i> Información del Estado
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Nombre -->
                                <div class="col-md-8 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-tag text-primary me-1"></i> Nombre del Estado
                                    </label>
                                    <p class="form-control-plaintext"><?= esc($estado['estado_nombre']) ?></p>
                                </div>

                                <!-- ID -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-hashtag text-secondary me-1"></i> ID / Orden
                                    </label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-primary fs-6"><?= (int)$estado['estado_id'] ?></span>
                                    </p>
                                </div>
                            </div>

                            <!-- Descripción según el estado -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-file-alt text-info me-1"></i> Descripción
                                </label>
                                <div class="alert <?= match($estado['estado_nombre']) {
                                    'Solicitud' => 'alert-info',
                                    'Coordinador' => 'alert-primary', 
                                    'Es Control de Calidad' => 'alert-warning',
                                    'En Inspector' => 'alert-secondary',
                                    'Terminada' => 'alert-success',
                                    'Aceptada' => 'alert-success',
                                    'Rechazada' => 'alert-danger',
                                    default => 'alert-info'
                                } ?>" role="alert">
                                    <strong><?= match($estado['estado_nombre']) {
                                        'Solicitud' => '📋 Estado inicial del proceso',
                                        'Coordinador' => '👔 Asignado al coordinador de inspecciones',
                                        'Es Control de Calidad' => '🔍 En proceso de revisión de calidad',
                                        'En Inspector' => '👷 Asignado a inspector',
                                        'Terminada' => '🏁 Inspección completada satisfactoriamente',
                                        'Aceptada' => '✅ Proceso aceptado y finalizado',
                                        'Rechazada' => '❌ Proceso rechazado  ',
                                        default => '🏷️ Estado del sistema de inspecciones'
                                    } ?></strong>
                                    <hr class="my-2">
                                    <p class="mb-0">
                                        <?= match($estado['estado_nombre']) {
                                            'Solicitud' => 'Cuando se crea una nueva solicitud de inspección, esta comienza en este estado.',
                                            'Coordinador' => 'El coordinador revisa la solicitud y asigna recursos necesarios.',
                                            'Es Control de Calidad' => 'Se revisa que todos los documentos y requisitos estén completos.',
                                            'En Inspector' => 'La inspección ha sido asignada a un inspector para su ejecución.',
                                            'Terminada' => 'La inspección ha sido completada y está lista para revisión final.',
                                            'Aceptada' => 'La inspección ha sido aprobada y el proceso está finalizado.',
                                            'Rechazada' => 'La inspección ha sido rechazada y requiere correcciones o acciones adicionales.',
                                            default => 'Estado utilizado en el flujo del sistema de inspecciones.'
                                        } ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel Lateral -->
                <div class="col-lg-4">
                    <!-- Información del Sistema -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-cog me-2"></i> Información del Sistema
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <h4 class="text-primary"><?= (int)$estado['estado_id'] ?></h4>
                                <small class="text-muted">Orden en el Flujo</small>
                            </div>

                            <hr>

                            <div class="small text-muted">
                                <div class="mb-2">
                                    <i class="fas fa-calendar-plus me-2"></i>
                                    <strong>Creado:</strong><br>
                                    <?= date('d/m/Y H:i:s', strtotime($estado['estado_created_at'])) ?>
                                </div>

                                <?php if (!empty($estado['estado_updated_at']) && $estado['estado_updated_at'] !== $estado['estado_created_at']): ?>
                                    <div class="mb-2">
                                        <i class="fas fa-calendar-edit me-2"></i>
                                        <strong>Última modificación:</strong><br>
                                        <?= date('d/m/Y H:i:s', strtotime($estado['estado_updated_at'])) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="mb-2">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Estado:</strong><br>
                                        Sin modificaciones desde la creación
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Flujo de Estados -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-sitemap me-2"></i> Flujo de Estados
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="small">
                                <div class="d-flex align-items-center mb-2 <?= $estado['estado_id'] == 1 ? 'text-primary fw-bold' : '' ?>">
                                    <span class="badge <?= $estado['estado_id'] == 1 ? 'bg-primary' : 'bg-light text-dark' ?> me-2">1</span>
                                    Solicitud
                                </div>
                                <div class="d-flex align-items-center mb-2 <?= $estado['estado_id'] == 2 ? 'text-primary fw-bold' : '' ?>">
                                    <span class="badge <?= $estado['estado_id'] == 2 ? 'bg-primary' : 'bg-light text-dark' ?> me-2">2</span>
                                    Coordinador
                                </div>
                                <div class="d-flex align-items-center mb-2 <?= $estado['estado_id'] == 3 ? 'text-primary fw-bold' : '' ?>">
                                    <span class="badge <?= $estado['estado_id'] == 3 ? 'bg-primary' : 'bg-light text-dark' ?> me-2">3</span>
                                    Control de Calidad
                                </div>
                                <div class="d-flex align-items-center mb-2 <?= $estado['estado_id'] == 4 ? 'text-primary fw-bold' : '' ?>">
                                    <span class="badge <?= $estado['estado_id'] == 4 ? 'bg-primary' : 'bg-light text-dark' ?> me-2">4</span>
                                    En Inspector
                                </div>
                                <div class="d-flex align-items-center mb-2 <?= $estado['estado_id'] == 5 ? 'text-primary fw-bold' : '' ?>">
                                    <span class="badge <?= $estado['estado_id'] == 5 ? 'bg-primary' : 'bg-light text-dark' ?> me-2">5</span>
                                    Terminada
                                </div>
                                <hr>
                                <div class="d-flex align-items-center mb-2 <?= $estado['estado_id'] == 6 ? 'text-success fw-bold' : '' ?>">
                                    <span class="badge <?= $estado['estado_id'] == 6 ? 'bg-success' : 'bg-light text-dark' ?> me-2">6</span>
                                    Aceptada
                                </div>
                                <div class="d-flex align-items-center <?= $estado['estado_id'] == 7 ? 'text-danger fw-bold' : '' ?>">
                                    <span class="badge <?= $estado['estado_id'] == 7 ? 'bg-danger' : 'bg-light text-dark' ?> me-2">7</span>
                                    Rechazada
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
.form-control-plaintext { 
    background: #f8f9fa; 
    border: 1px solid #e9ecef; 
    border-radius: 8px; 
    padding: .75rem; 
    margin-bottom: 0; 
}
.badge.fs-6 { 
    font-size: .9rem !important; 
    padding: .5rem .75rem; 
}
.btn { 
    border-radius: 8px; 
}
.alert {
    border-radius: 10px;
}
</style>
<?= $this->endSection() ?>