<?= $this->extend('layouts/maincorredor') ?>

<?= $this->section('title') ?>
Inspección #<?= $inspeccion['inspecciones_id'] ?>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
    .status-badge {
        font-size: 1rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        border: 2px solid rgba(255,255,255,0.3);
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    .info-card {
        border-left: 4px solid #0d6efd;
        transition: all 0.3s ease;
    }
    
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .info-label {
        font-weight: 600;
        color: #495057;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        font-size: 1.1rem;
        color: #212529;
        margin-bottom: 0;
    }
    
    .timeline {
        position: relative;
        padding-left: 2rem;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 1rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 2rem;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -1.5rem;
        top: 0.5rem;
        width: 1rem;
        height: 1rem;
        background: var(--estado-color, #0d6efd);
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 0 0 3px #dee2e6;
    }
    
    .btn-action {
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .estado-flow {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
    }

    .estado-step {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        border: 2px solid #e9ecef;
        background: #f8f9fa;
        color: #6c757d;
        transition: all 0.2s ease;
    }

    .estado-step.active {
        border-color: var(--estado-color);
        background: var(--estado-color);
        color: var(--estado-text-color);
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    .estado-arrow {
        color: #dee2e6;
        font-size: 0.8rem;
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
                        <i class="fas fa-file-alt me-2 text-primary"></i>
                        Inspección #<?= $inspeccion['inspecciones_id'] ?>
                    </h1>
                    <p class="text-muted mb-0">
                        Creada el <?= date('d/m/Y \a \l\a\s H:i', strtotime($inspeccion['inspecciones_created_at'])) ?>
                    </p>
                </div>
                <div>
                    <?php if (!empty($inspeccion['estado_color'])): ?>
                        <?php 
                        $bgColor = $inspeccion['estado_color'];
                        $textColor = getTextColorForBackground($bgColor);
                        ?>
                        <span class="status-badge" 
                              style="background-color: <?= $bgColor ?>; color: <?= $textColor ?>;">
                            <i class="fas fa-tag me-2"></i>
                            <?= esc($inspeccion['estado_nombre']) ?>
                        </span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Sin Estado</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Flujo de Estados -->
    <?php if (!empty($estados)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-route me-2"></i>
                        Flujo del Proceso
                    </h6>
                </div>
                <div class="card-body py-3">
                    <div class="estado-flow">
                        <?php foreach ($estados as $index => $estado): ?>
                            <?php 
                            $isActive = ($estado['estado_id'] == $inspeccion['estado_id']);
                            $bgColor = $estado['estado_color'] ?? '#6c757d';
                            $textColor = getTextColorForBackground($bgColor);
                            ?>
                            
                            <div class="estado-step <?= $isActive ? 'active' : '' ?>"
                                 <?= $isActive ? 'style="--estado-color: '.$bgColor.'; --estado-text-color: '.$textColor.';"' : '' ?>>
                                <span class="badge bg-light text-dark me-1"><?= $estado['estado_id'] ?></span>
                                <?= esc($estado['estado_nombre']) ?>
                            </div>
                            
                            <?php if ($index < count($estados) - 1): ?>
                                <i class="fas fa-chevron-right estado-arrow"></i>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Acciones -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex flex-wrap align-items-center">
                        <a href="<?= base_url('corredor') ?>" class="btn btn-outline-secondary btn-action">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                        </a>
                         
                        <a href="<?= base_url('corredor/edit/' . $inspeccion['inspecciones_id']) ?>" 
                           class="btn btn-primary btn-action">
                            <i class="fas fa-edit me-2"></i>Editar
                        </a> 
                        
                        <a href="<?= base_url('corredor/print/' . $inspeccion['inspecciones_id']) ?>" 
                           class="btn btn-outline-info btn-action" target="_blank">
                            <i class="fas fa-print me-2"></i>Imprimir
                        </a>
                        
                        <?php if ($inspeccion['inspecciones_estado'] === 'pendiente'): ?>
                        <button type="button" class="btn btn-outline-danger btn-action" 
                                onclick="confirmarEliminacion(<?= $inspeccion['inspecciones_id'] ?>)">
                            <i class="fas fa-trash me-2"></i>Eliminar
                        </button>
                        <?php endif; ?>
                    </div>
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
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="info-label">Nombre Completo</div>
                            <div class="info-value"><?= esc($inspeccion['inspecciones_asegurado']) ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">RUT</div>
                            <div class="info-value">
                                <code class="fs-6"><?= esc($inspeccion['inspecciones_rut']) ?></code>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Celular</div>
                            <div class="info-value">
                                <a href="tel:<?= esc($inspeccion['inspecciones_celular']) ?>" class="text-decoration-none">
                                    <?= esc($inspeccion['inspecciones_celular']) ?>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="info-label">Dirección</div>
                            <div class="info-value"><?= esc($inspeccion['inspecciones_direccion']) ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Comuna</div>
                            <div class="info-value"><?= esc($inspeccion['comunas_nombre'] ?? 'N/A') ?></div>
                        </div>
                        <?php if (!empty($inspeccion['inspecciones_telefono'])): ?>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Teléfono</div>
                            <div class="info-value">
                                <a href="tel:<?= esc($inspeccion['inspecciones_telefono']) ?>" class="text-decoration-none">
                                    <?= esc($inspeccion['inspecciones_telefono']) ?>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
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
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Patente</div>
                            <div class="info-value">
                                <span class="badge bg-dark fs-6"><?= esc($inspeccion['inspecciones_patente']) ?></span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Marca</div>
                            <div class="info-value"><?= esc($inspeccion['inspecciones_marca']) ?></div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="info-label">Modelo</div>
                            <div class="info-value"><?= esc($inspeccion['inspecciones_modelo']) ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Compañía de Seguros</div>
                            <div class="info-value"><?= esc($inspeccion['cia_nombre'] ?? 'N/A') ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Número de Póliza</div>
                            <div class="info-value">
                                <code class="fs-6"><?= esc($inspeccion['inspecciones_n_poliza']) ?></code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información Adicional -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <?php if (!empty($inspeccion['inspecciones_observaciones'])): ?>
            <div class="card border-0 shadow-sm info-card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-sticky-note me-2"></i>
                        Observaciones
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?= nl2br(esc($inspeccion['inspecciones_observaciones'])) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Timeline de estados con colores -->
            <div class="card border-0 shadow-sm info-card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Historial del Proceso
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item" style="--estado-color: <?= $inspeccion['estado_color'] ?? '#17a2b8' ?>;">
                            <div class="info-label">
                                <?= date('d/m/Y H:i', strtotime($inspeccion['inspecciones_created_at'])) ?>
                            </div>
                            <div class="info-value">Inspección creada</div>
                            <small class="text-muted">Estado inicial: Solicitud</small>
                        </div>
                        
                        <?php if (!empty($inspeccion['inspecciones_fecha_inspeccion'])): ?>
                        <div class="timeline-item" style="--estado-color: #007bff;">
                            <div class="info-label">
                                <?= date('d/m/Y H:i', strtotime($inspeccion['inspecciones_fecha_inspeccion'])) ?>
                            </div>
                            <div class="info-value">Fecha de inspección programada</div>
                            <small class="text-muted">Asignado al coordinador</small>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Estado actual -->
                        <?php if ($inspeccion['estado_id'] && $inspeccion['estado_id'] > 1): ?>
                        <div class="timeline-item" style="--estado-color: <?= $inspeccion['estado_color'] ?? '#28a745' ?>;">
                            <div class="info-label">
                                <?= date('d/m/Y H:i', strtotime($inspeccion['inspecciones_updated_at'])) ?>
                            </div>
                            <div class="info-value">Estado actual: <?= esc($inspeccion['estado_nombre']) ?></div>
                            <small class="text-muted">
                                <?= match($inspeccion['estado_nombre']) {
                                    'Coordinador' => 'Revisión y asignación de recursos',
                                    'Es Control de Calidad' => 'Verificación de documentos y requisitos',
                                    'En Inspector' => 'Inspección en proceso de ejecución',
                                    'Terminada' => 'Inspección completada satisfactoriamente',
                                    'Aceptada' => 'Proceso finalizado y aprobado',
                                    'Rechazada' => 'Proceso rechazado, requiere correcciones',
                                    default => 'Estado del sistema de inspecciones'
                                } ?>
                            </small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Sistema -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm info-card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-cog me-2"></i>
                        Información del Sistema
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="info-label">ID de Inspección</div>
                        <div class="info-value">#<?= $inspeccion['inspecciones_id'] ?></div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Estado Actual</div>
                        <div class="info-value">
                            <?php if (!empty($inspeccion['estado_color'])): ?>
                                <?php 
                                $bgColor = $inspeccion['estado_color'];
                                $textColor = getTextColorForBackground($bgColor);
                                ?>
                                <span class="status-badge" 
                                      style="background-color: <?= $bgColor ?>; color: <?= $textColor ?>;">
                                    <span class="badge bg-light text-dark me-1"><?= $inspeccion['estado_id'] ?></span>
                                    <?= esc($inspeccion['estado_nombre']) ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Sin Estado</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Fecha de Creación</div>
                        <div class="info-value">
                            <?= date('d/m/Y H:i:s', strtotime($inspeccion['inspecciones_created_at'])) ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Última Actualización</div>
                        <div class="info-value">
                            <?= date('d/m/Y H:i:s', strtotime($inspeccion['inspecciones_updated_at'])) ?>
                        </div>
                    </div>
                    <?php if ($inspeccion['inspecciones_total_comentarios'] > 0): ?>
                    <div class="mb-3">
                        <div class="info-label">Total de Comentarios</div>
                        <div class="info-value">
                            <span class="badge bg-primary"><?= $inspeccion['inspecciones_total_comentarios'] ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Panel de Estados Disponibles -->
            <?php if (!empty($estados)): ?>
            <div class="card border-0 shadow-sm info-card mt-4">
                <div class="card-header" 
                     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h6 class="mb-0">
                        <i class="fas fa-sitemap me-2"></i>
                        Estados del Sistema
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <?php foreach ($estados as $estado): ?>
                            <?php 
                            $isActive = ($estado['estado_id'] == $inspeccion['estado_id']);
                            $bgColor = $estado['estado_color'] ?? '#6c757d';
                            $textColor = getTextColorForBackground($bgColor);
                            ?>
                            <div class="d-flex align-items-center mb-2 <?= $isActive ? 'fw-bold' : '' ?>">
                                <span class="badge me-2" 
                                      style="background-color: <?= $bgColor ?>; color: <?= $textColor ?>;">
                                    <?= $estado['estado_id'] ?>
                                </span>
                                <span class="<?= $isActive ? 'text-primary' : '' ?>">
                                    <?= esc($estado['estado_nombre']) ?>
                                </span>
                                <?php if ($isActive): ?>
                                    <i class="fas fa-check-circle text-success ms-auto"></i>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
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

<?= $this->section('scripts') ?>
<script>
function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Eliminando...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });
            
            window.location.href = '<?= base_url('corredor/delete/') ?>' + id;
        }
    });
}

// Auto-ocultar alertas
setTimeout(function() {
    $('.alert').fadeOut();
}, 5000);
</script>
<?= $this->endSection() ?>