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
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-tag fa-2x"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="h3 mb-0"><?= esc($estado['estado_nombre']) ?></h1>
                        <p class="text-muted mb-0">
                            <span class="badge bg-info">ID: <?= (int)$estado['estado_id'] ?></span>
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
        <div class="col-lg-8">
            <!-- Información del Estado -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i> Información del Estado
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Nombre -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-tag text-primary me-1"></i> Nombre del Estado
                            </label>
                            <p class="form-control-plaintext"><?= esc($estado['estado_nombre']) ?></p>
                        </div>

                        <!-- ID -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-hashtag text-secondary me-1"></i> ID del Estado
                            </label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-secondary fs-6"><?= (int)$estado['estado_id'] ?></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Sistema -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-cog me-2"></i> Información del Sistema
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-plus text-success me-1"></i> Fecha de Creación
                            </label>
                            <p class="form-control-plaintext">
                                <?= date('d/m/Y H:i:s', strtotime($estado['created_at'])) ?>
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-edit text-warning me-1"></i> Última Modificación
                            </label>
                            <p class="form-control-plaintext">
                                <?php if (!empty($estado['updated_at']) && $estado['updated_at'] !== $estado['created_at']): ?>
                                    <?= date('d/m/Y H:i:s', strtotime($estado['updated_at'])) ?>
                                <?php else: ?>
                                    <em class="text-muted">Sin modificaciones</em>
                                <?php endif; ?>
                            </p>
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
</style>
<?= $this->endSection() ?>