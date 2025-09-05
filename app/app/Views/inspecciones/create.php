<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
    .form-container {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        padding: 2rem;
        margin-top: 1rem;
    }
    .form-header {
        border-bottom: 2px solid #007bff;
        padding-bottom: 1rem;
        margin-bottom: 2rem;
    }
    .required {
        color: #dc3545;
    }
    .form-group label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .btn-submit {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
        padding: 12px 30px;
        font-weight: 600;
        border-radius: 50px;
        transition: all 0.3s ease;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
    }
    .section-divider {
        border-top: 1px solid #dee2e6;
        margin: 2rem 0 1.5rem 0;
        padding-top: 1.5rem;
    }
    .section-title {
        color: #6c757d;
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1rem;
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
                        <i class="fas fa-plus-circle me-2 text-primary"></i>
                        <?= esc($title) ?>
                    </h1>
                    <p class="text-muted mb-0">Complete todos los campos para crear una nueva inspecci�n</p>
                </div>
                <div>
                    <a href="<?= base_url('inspecciones') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al listado
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <div class="row">
        <div class="col-12">
            <div class="form-container">
                <div class="form-header">
                    <h4><i class="fas fa-file-alt me-2"></i>Datos de la Inspecci�n</h4>
                    <small class="text-muted">Los campos marcados con <span class="required">*</span> son obligatorios</small>
                </div>

                <?= form_open('inspecciones/store', ['class' => 'needs-validation', 'novalidate' => true]) ?>
                
                    <!-- Informaci�n del Asegurado -->
                    <div class="section-title">
                        <i class="fas fa-user me-2"></i>Informaci�n del Asegurado
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="asegurado">Nombre del Asegurado <span class="required">*</span></label>
                                <input type="text" 
                                       class="form-control <?= isset($validation) && $validation->hasError('asegurado') ? 'is-invalid' : '' ?>" 
                                       id="asegurado" 
                                       name="asegurado" 
                                       value="<?= old('asegurado') ?>" 
                                       placeholder="Nombre completo del asegurado"
                                       required>
                                <?php if (isset($validation) && $validation->hasError('asegurado')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('asegurado') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="rut">RUT <span class="required">*</span></label>
                                <input type="text" 
                                       class="form-control <?= isset($validation) && $validation->hasError('rut') ? 'is-invalid' : '' ?>" 
                                       id="rut" 
                                       name="rut" 
                                       value="<?= old('rut') ?>" 
                                       placeholder="12.345.678-9"
                                       maxlength="12"
                                       required>
                                <?php if (isset($validation) && $validation->hasError('rut')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('rut') ?>
                                    </div>
                                <?php endif; ?>
                                <small class="form-text text-muted">Formato: 12.345.678-9</small>
                            </div>
                        </div>
                    </div>

                    <!-- Informaci�n del Veh�culo -->
                    <div class="section-divider">
                        <div class="section-title">
                            <i class="fas fa-car me-2"></i>Informaci�n del Veh�culo
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="patente">Patente <span class="required">*</span></label>
                                <input type="text" 
                                       class="form-control <?= isset($validation) && $validation->hasError('patente') ? 'is-invalid' : '' ?>" 
                                       id="patente" 
                                       name="patente" 
                                       value="<?= old('patente') ?>" 
                                       placeholder="ABC123"
                                       maxlength="8"
                                       style="text-transform: uppercase"
                                       required>
                                <?php if (isset($validation) && $validation->hasError('patente')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('patente') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="marca">Marca <span class="required">*</span></label>
                                <input type="text" 
                                       class="form-control <?= isset($validation) && $validation->hasError('marca') ? 'is-invalid' : '' ?>" 
                                       id="marca" 
                                       name="marca" 
                                       value="<?= old('marca') ?>" 
                                       placeholder="Toyota, Chevrolet, etc."
                                       required>
                                <?php if (isset($validation) && $validation->hasError('marca')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('marca') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="modelo">Modelo <span class="required">*</span></label>
                                <input type="text" 
                                       class="form-control <?= isset($validation) && $validation->hasError('modelo') ? 'is-invalid' : '' ?>" 
                                       id="modelo" 
                                       name="modelo" 
                                       value="<?= old('modelo') ?>" 
                                       placeholder="Corolla, Aveo, etc."
                                       required>
                                <?php if (isset($validation) && $validation->hasError('modelo')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('modelo') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Informaci�n de la P�liza -->
                    <div class="section-divider">
                        <div class="section-title">
                            <i class="fas fa-file-contract me-2"></i>Informaci�n de la P�liza
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="n_poliza">N�mero de P�liza <span class="required">*</span></label>
                                <input type="text" 
                                       class="form-control <?= isset($validation) && $validation->hasError('n_poliza') ? 'is-invalid' : '' ?>" 
                                       id="n_poliza" 
                                       name="n_poliza" 
                                       value="<?= old('n_poliza') ?>" 
                                       placeholder="N�mero de p�liza del seguro"
                                       required>
                                <?php if (isset($validation) && $validation->hasError('n_poliza')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('n_poliza') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="cia_id">Compa��a de Seguros <span class="required">*</span></label>
                                <select class="form-control <?= isset($validation) && $validation->hasError('cia_id') ? 'is-invalid' : '' ?>" 
                                        id="cia_id" 
                                        name="cia_id" 
                                        required>
                                    <option value="">Seleccione una compa��a</option>
                                    <?php foreach ($cias as $cia): ?>
                                        <option value="<?= $cia['cia_id'] ?>" 
                                                <?= old('cia_id') == $cia['cia_id'] ? 'selected' : '' ?>>
                                            <?= esc($cia['cia_nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($validation) && $validation->hasError('cia_id')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('cia_id') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Informaci�n de Contacto -->
                    <div class="section-divider">
                        <div class="section-title">
                            <i class="fas fa-map-marker-alt me-2"></i>Informaci�n de Contacto
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="direccion">Direcci�n <span class="required">*</span></label>
                                <input type="text" 
                                       class="form-control <?= isset($validation) && $validation->hasError('direccion') ? 'is-invalid' : '' ?>" 
                                       id="direccion" 
                                       name="direccion" 
                                       value="<?= old('direccion') ?>" 
                                       placeholder="Direcci�n completa"
                                       required>
                                <?php if (isset($validation) && $validation->hasError('direccion')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('direccion') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="comuna">Comuna <span class="required">*</span></label>
                                <input type="text" 
                                       class="form-control <?= isset($validation) && $validation->hasError('comuna') ? 'is-invalid' : '' ?>" 
                                       id="comuna" 
                                       name="comuna" 
                                       value="<?= old('comuna') ?>" 
                                       placeholder="Comuna de residencia"
                                       required>
                                <?php if (isset($validation) && $validation->hasError('comuna')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('comuna') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="celular">Celular <span class="required">*</span></label>
                                <input type="tel" 
                                       class="form-control <?= isset($validation) && $validation->hasError('celular') ? 'is-invalid' : '' ?>" 
                                       id="celular" 
                                       name="celular" 
                                       value="<?= old('celular') ?>" 
                                       placeholder="+56 9 1234 5678"
                                       required>
                                <?php if (isset($validation) && $validation->hasError('celular')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('celular') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="telefono">Tel�fono (Opcional)</label>
                                <input type="tel" 
                                       class="form-control <?= isset($validation) && $validation->hasError('telefono') ? 'is-invalid' : '' ?>" 
                                       id="telefono" 
                                       name="telefono" 
                                       value="<?= old('telefono') ?>" 
                                       placeholder="+56 2 1234 5678">
                                <?php if (isset($validation) && $validation->hasError('telefono')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('telefono') ?>
                                    </div>
                                <?php endif; ?>
                                <small class="form-text text-muted">Tel�fono fijo (opcional)</small>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="section-divider">
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('inspecciones') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary btn-submit">
                                <i class="fas fa-save me-2"></i>Crear Inspecci�n
                            </button>
                        </div>
                    </div>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
// Formateo autom�tico del RUT
document.getElementById('rut').addEventListener('input', function(e) {
    let rut = e.target.value.replace(/[^0-9kK]/g, '');
    
    if (rut.length > 1) {
        rut = rut.slice(0, -1) + '-' + rut.slice(-1);
        
        if (rut.length > 4) {
            let numbers = rut.slice(0, -2);
            let dv = rut.slice(-2);
            
            // Agregar puntos cada 3 d�gitos
            numbers = numbers.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            rut = numbers + dv;
        }
    }
    
    e.target.value = rut;
});

// Convertir patente a may�sculas
document.getElementById('patente').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});

// Validaci�n del formulario
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>
<?= $this->endSection() ?>