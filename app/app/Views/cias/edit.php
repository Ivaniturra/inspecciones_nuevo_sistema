 <?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Editar Compañía
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Editar Compañía</h1>
                    <p class="text-muted">Modifica los datos de: <strong><?= esc($cia['cia_nombre']) ?></strong></p>
                </div>
                <div class="btn-group">
                    <a href="<?= base_url('cias/show/' . $cia['cia_id']) ?>" class="btn btn-outline-info">
                        <i class="fas fa-eye"></i> Ver detalles
                    </a>
                    <a href="<?= base_url('cias') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al listado
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Se encontraron los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Editar Compañía
                    </h5>
                </div>
                
                <div class="card-body">
                    <form action="<?= base_url('cias/update/' . $cia['cia_id']) ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">
                        
                        <div class="row">
                            <!-- Nombre de la Compañía -->
                            <div class="col-md-6 mb-3">
                                <label for="cia_nombre" class="form-label">
                                    <i class="fas fa-building text-primary me-1"></i>
                                    Nombre de la Compañía *
                                </label>
                                <input type="text" 
                                       class="form-control <?= (session('errors.cia_nombre')) ? 'is-invalid' : '' ?>" 
                                       id="cia_nombre" 
                                       name="cia_nombre" 
                                       value="<?= old('cia_nombre', $cia['cia_nombre']) ?>"
                                       placeholder="Ej. Empresa ABC S.A."
                                       required>
                                <div class="invalid-feedback">
                                    <?= session('errors.cia_nombre') ?>
                                </div>
                                <div class="form-text">Mínimo 3 caracteres, máximo 255</div>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6 mb-3">
                                <label for="cia_habil" class="form-label">
                                    <i class="fas fa-toggle-on text-success me-1"></i>
                                    Estado
                                </label>
                                <select class="form-select" id="cia_habil" name="cia_habil">
                                    <option value="1" <?= old('cia_habil', $cia['cia_habil']) == '1' ? 'selected' : '' ?>>
                                        Activo
                                    </option>
                                    <option value="0" <?= old('cia_habil', $cia['cia_habil']) == '0' ? 'selected' : '' ?>>
                                        Inactivo
                                    </option>
                                </select>
                                <div class="form-text">Estado actual de la compañía</div>
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="mb-3">
                            <label for="cia_direccion" class="form-label">
                                <i class="fas fa-map-marker-alt text-info me-1"></i>
                                Dirección
                            </label>
                            <textarea class="form-control <?= (session('errors.cia_direccion')) ? 'is-invalid' : '' ?>" 
                                      id="cia_direccion" 
                                      name="cia_direccion" 
                                      rows="3"
                                      placeholder="Dirección completa de la compañía"><?= old('cia_direccion', $cia['cia_direccion']) ?></textarea>
                            <div class="invalid-feedback">
                                <?= session('errors.cia_direccion') ?>
                            </div>
                            <div class="form-text">Máximo 500 caracteres (opcional)</div>
                        </div>

                        <!-- Logo actual y nuevo -->
                        <div class="mb-4">
                            <!-- Logo actual -->
                            <?php if (!empty($cia['cia_logo'])): ?>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-image text-warning me-1"></i>
                                        Logo Actual
                                    </label>
                                    <div class="border rounded p-3 bg-light">
                                        <img src="<?= base_url('uploads/logos/' . $cia['cia_logo']) ?>" 
                                             alt="<?= esc($cia['cia_nombre']) ?>" 
                                             class="img-thumbnail"
                                             style="max-width: 200px; max-height: 150px;">
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-file me-1"></i>
                                                <?= $cia['cia_logo'] ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Nuevo logo -->
                            <label for="cia_logo" class="form-label">
                                <i class="fas fa-upload text-success me-1"></i>
                                <?= !empty($cia['cia_logo']) ? 'Cambiar Logo' : 'Subir Logo' ?>
                            </label>
                            <input type="file" 
                                   class="form-control <?= (session('errors.cia_logo')) ? 'is-invalid' : '' ?>" 
                                   id="cia_logo" 
                                   name="cia_logo"
                                   accept="image/jpeg,image/jpg,image/png">
                            <div class="invalid-feedback">
                                <?= session('errors.cia_logo') ?>
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Formatos permitidos: JPG, JPEG, PNG. Tamaño máximo: 2MB 
                                <?= !empty($cia['cia_logo']) ? '(dejar vacío para mantener el actual)' : '(opcional)' ?>
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-info-circle text-info me-1"></i>
                                            Información del registro
                                        </h6>
                                        <small class="text-muted">
                                            <strong>ID:</strong> <?= $cia['cia_id'] ?><br>
                                            <strong>Creado:</strong> <?= date('d/m/Y H:i', strtotime($cia['created_at'])) ?><br>
                                            <?php if (!empty($cia['updated_at'])): ?>
                                                <strong>Última modificación:</strong> <?= date('d/m/Y H:i', strtotime($cia['updated_at'])) ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="<?= base_url('cias') ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <button type="reset" class="btn btn-outline-warning">
                                        <i class="fas fa-undo"></i> Restaurar
                                    </button>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Actualizar Compañía
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>