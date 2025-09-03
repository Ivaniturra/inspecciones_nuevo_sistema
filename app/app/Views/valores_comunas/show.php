<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'Detalles del Valor') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="h3 mb-0">
                            <?php if ($valor['valores_unidad_medida'] == 'UF'): ?>
                                UF <?= number_format($valor['valores_valor'], 2, ',', '.') ?>
                            <?php elseif ($valor['valores_unidad_medida'] == 'UTM'): ?>
                                UTM <?= number_format($valor['valores_valor'], 2, ',', '.') ?>
                            <?php else: ?>
                                $<?= number_format($valor['valores_valor'], 0, ',', '.') ?>
                            <?php endif; ?>
                            <small class="text-muted"><?= esc($valor['valores_moneda'] ?? 'CLP') ?></small>
                        </h1>
                        <p class="text-muted mb-0">
                            <span class="badge <?= $valor['valores_activo'] ? 'bg-success' : 'bg-danger' ?>">
                                <?= $valor['valores_activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                            <span class="badge bg-info ms-2">
                                ID: <?= (int)$valor['valores_id'] ?>
                            </span>
                        </p>
                    </div>
                </div>

                <div class="btn-group">
                    <a href="<?= base_url('valores-comunas/edit/' . $valor['valores_id']) ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="<?= base_url('valores-comunas') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Columna principal -->
        <div class="col-lg-8">
            <!-- Información principal -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Información del Valor
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Compañía -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-building text-primary me-1"></i>
                                Compañía
                            </label>
                            <p class="form-control-plaintext">
                                <?= esc($valor['cia_nombre'] ?? 'N/A') ?>
                            </p>
                        </div>

                        <!-- Comuna -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                Comuna
                            </label>
                            <p class="form-control-plaintext">
                                <?= esc($valor['comunas_nombre'] ?? 'Comuna: ' . $valor['comunas_id']) ?>
                                <br><small class="text-muted">Código: <?= esc($valor['comunas_id']) ?></small>
                            </p>
                        </div>

                        <!-- Región -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-globe-americas text-warning me-1"></i>
                                Región
                            </label>
                            <p class="form-control-plaintext">
                                <?= esc($valor['region_nombre'] ?? 'N/A') ?>
                            </p>
                        </div>

                        <!-- Tipo de Usuario -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user-tag text-info me-1"></i>
                                Tipo de Usuario
                            </label>
                            <p class="form-control-plaintext">
                                <span class="badge fs-6 bg-secondary">
                                    <?= ucfirst(esc($valor['valores_tipo_usuario'])) ?>
                                </span>
                            </p>
                        </div>

                        <!-- Tipo de Vehículo -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-car text-primary me-1"></i>
                                Tipo de Vehículo
                            </label>
                            <p class="form-control-plaintext">
                                <span class="badge fs-6 bg-secondary">
                                    <?= esc($valor['tipo_vehiculo_nombre'] ?? 'N/A') ?>
                                </span>
                            </p>
                        </div>

                        <!-- Valor y Moneda -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-dollar-sign text-success me-1"></i>
                                Valor
                            </label>
                            <p class="form-control-plaintext">
                                <span class="fs-4 text-success fw-bold">
                                    <?php if ($valor['valores_unidad_medida'] == 'UF'): ?>
                                        UF <?= number_format($valor['valores_valor'], 2, ',', '.') ?>
                                    <?php elseif ($valor['valores_unidad_medida'] == 'UTM'): ?>
                                        UTM <?= number_format($valor['valores_valor'], 2, ',', '.') ?>
                                    <?php else: ?>
                                        $<?= number_format($valor['valores_valor'], 2, ',', '.') ?>
                                    <?php endif; ?>
                                </span>
                                <span class="badge bg-success ms-2">
                                    <?= esc($valor['valores_moneda'] ?? 'CLP') ?>
                                </span>
                            </p>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-toggle-on text-success me-1"></i>
                                Estado
                            </label>
                            <p class="form-control-plaintext">
                                <span class="badge fs-6 <?= $valor['valores_activo'] ? 'bg-success' : 'bg-danger' ?>">
                                    <i class="fas <?= $valor['valores_activo'] ? 'fa-check' : 'fa-times' ?> me-1"></i>
                                    <?= $valor['valores_activo'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </p>
                        </div>

                        <!-- Descripción -->
                        <?php if (!empty($valor['valores_descripcion'])): ?>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-align-left text-info me-1"></i>
                                    Descripción
                                </label>
                                <p class="form-control-plaintext">
                                    <?= nl2br(esc($valor['valores_descripcion'])) ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Información de vigencia -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Vigencia del Valor
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-plus text-success me-1"></i>
                                Vigente desde
                            </label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-play me-1"></i>
                                    <?= date('d/m/Y', strtotime($valor['valores_fecha_vigencia_desde'])) ?>
                                </span>
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-times text-danger me-1"></i>
                                Vigente hasta
                            </label>
                            <p class="form-control-plaintext">
                                <?php if (!empty($valor['valores_fecha_vigencia_hasta'])): ?>
                                    <span class="badge bg-danger fs-6">
                                        <i class="fas fa-stop me-1"></i>
                                        <?= date('d/m/Y', strtotime($valor['valores_fecha_vigencia_hasta'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-primary fs-6">
                                        <i class="fas fa-infinity me-1"></i>
                                        Vigencia indefinida
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>

                        <!-- Estado de vigencia -->
                        <div class="col-12">
                            <?php 
                            $hoy = date('Y-m-d');
                            $vigenciaDesde = $valor['valores_fecha_vigencia_desde'];
                            $vigenciaHasta = $valor['valores_fecha_vigencia_hasta'];
                            
                            $esVigente = ($hoy >= $vigenciaDesde) && 
                                        (empty($vigenciaHasta) || $hoy <= $vigenciaHasta) && 
                                        $valor['valores_activo'];
                            ?>
                            <div class="alert <?= $esVigente ? 'alert-success' : 'alert-warning' ?>" role="alert">
                                <i class="fas <?= $esVigente ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> me-2"></i>
                                <strong>Estado actual:</strong>
                                <?php if ($esVigente): ?>
                                    Este valor está <strong>vigente y activo</strong> en la fecha actual.
                                <?php elseif (!$valor['valores_activo']): ?>
                                    Este valor está <strong>inactivo</strong>.
                                <?php elseif ($hoy < $vigenciaDesde): ?>
                                    Este valor <strong>aún no entra en vigencia</strong>.
                                <?php else: ?>
                                    Este valor ya <strong>no está vigente</strong>.
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna lateral -->
        <div class="col-lg-4">
            <!-- Información del sistema -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-cog me-2"></i>
                        Información del Sistema
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary"><?= (int)$valor['valores_id'] ?></h4>
                                <small class="text-muted">ID Único</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="<?= $valor['valores_activo'] ? 'text-success' : 'text-danger' ?>">
                                <i class="fas <?= $valor['valores_activo'] ? 'fa-check' : 'fa-times' ?>"></i>
                            </h4>
                            <small class="text-muted">Estado</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="small text-muted">
                        <div class="mb-2">
                            <i class="fas fa-calendar-plus me-2"></i>
                            <strong>Creado:</strong><br>
                            <?= date('d/m/Y H:i:s', strtotime($valor['valores_created_at'])) ?>
                        </div>
                        
                        <?php if (!empty($valor['valores_updated_at']) && $valor['valores_updated_at'] !== $valor['valores_created_at']): ?>
                            <div class="mb-2">
                                <i class="fas fa-calendar-edit me-2"></i>
                                <strong>Última modificación:</strong><br>
                                <?= date('d/m/Y H:i:s', strtotime($valor['valores_updated_at'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('valores-comunas/edit/' . $valor['valores_id']) ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>
                            Editar Valor
                        </a>
                        
                        <button type="button" 
                                class="btn <?= $valor['valores_activo'] ? 'btn-outline-danger' : 'btn-outline-success' ?>"
                                onclick="toggleStatus(<?= (int)$valor['valores_id'] ?>)">
                            <i class="fas <?= $valor['valores_activo'] ? 'fa-pause' : 'fa-play' ?> me-2"></i>
                            <?= $valor['valores_activo'] ? 'Desactivar' : 'Activar' ?>
                        </button>
                        
                        <a href="<?= base_url('valores-comunas/create?cia_id=' . $valor['cia_id'] . '&comunas_id=' . $valor['comunas_id']) ?>" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-copy me-2"></i>
                            Crear Valor Similar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.form-control-plaintext {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem;
    margin-bottom: 0;
}

.badge.fs-6 {
    font-size: 0.9rem !important;
    padding: 0.5rem 0.75rem;
}

.border-end {
    border-right: 1px solid #dee2e6 !important;
}

@media (max-width: 768px) {
    .border-end {
        border-right: none !important;
        border-bottom: 1px solid #dee2e6 !important;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }
}

.card { 
    border: none; 
    border-radius: 15px; 
}

.card-header { 
    border-radius: 15px 15px 0 0 !important; 
    font-weight: 600; 
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    console.log('ValoresComunas Show: Document ready');
});

// Cambiar estado del valor
function toggleStatus(id) {
    const isActive = <?= $valor['valores_activo'] ? 'true' : 'false' ?>;
    const action = isActive ? 'desactivar' : 'activar';
    const newStatus = isActive ? 'inactivo' : 'activo';
    
    Swal.fire({
        title: `¿${action.charAt(0).toUpperCase() + action.slice(1)} valor?`,
        text: `El valor quedará ${newStatus}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: isActive ? '#dc3545' : '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Sí, ${action}`,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Procesando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            
            $.post('<?= base_url('valores-comunas/toggleStatus') ?>/' + id, {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            })
            .done(function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Estado actualizado',
                        text: response.message || 'Estado cambiado correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'No se pudo cambiar el estado'
                    });
                }
            })
            .fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor'
                });
            });
        }
    });
}
</script>
<?= $this->endSection() ?>