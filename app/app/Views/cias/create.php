<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Nueva Compañía
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Nueva Compañía</h1>
                    <p class="text-muted">Completa los datos para crear una nueva compañía</p>
                </div>
                <a href="<?= base_url('cias') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al listado
                </a>
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
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-building me-2"></i>
                        Datos de la Compañía
                    </h5>
                </div>
                
                <div class="card-body">
                    <form action="<?= base_url('cias/store') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
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
                                       value="<?= old('cia_nombre') ?>"
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
                                    <option value="1" <?= old('cia_habil', '1') == '1' ? 'selected' : '' ?>>
                                        Activo
                                    </option>
                                    <option value="0" <?= old('cia_habil') == '0' ? 'selected' : '' ?>>
                                        Inactivo
                                    </option>
                                </select>
                                <div class="form-text">Estado inicial de la compañía</div>
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
                                      placeholder="Dirección completa de la compañía"><?= old('cia_direccion') ?></textarea>
                            <div class="invalid-feedback">
                                <?= session('errors.cia_direccion') ?>
                            </div>
                            <div class="form-text">Máximo 500 caracteres (opcional)</div>
                        </div>

                        <!-- Logo -->
                        <div class="mb-4">
                            <label for="cia_logo" class="form-label">
                                <i class="fas fa-image text-warning me-1"></i>
                                Logo de la Compañía
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
                                Formatos permitidos: JPG, JPEG, PNG. Tamaño máximo: 2MB (opcional)
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
                                        <i class="fas fa-undo"></i> Limpiar
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Guardar Compañía
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