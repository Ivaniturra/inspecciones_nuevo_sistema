 <?= $this->extend('layouts/maincorredor') ?>  

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

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
    .required { color: #dc3545; }
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
    .btn-submit {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
        padding: 12px 30px;
        font-weight: 600;
        border-radius: 50px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">
                <i class="fas fa-plus-circle me-2 text-primary"></i>
                <?= esc($title) ?>
            </h1>
            <p class="text-muted mb-0">Complete todos los campos para crear una nueva inspección</p>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-container">
                <div class="form-header">
                    <h4><i class="fas fa-file-alt me-2"></i>Datos de la Inspección</h4>
                    <small class="text-muted">Los campos marcados con <span class="required">*</span> son obligatorios</small>
                </div>

                <?= form_open('inspecciones/store', ['class' => 'needs-validation', 'novalidate' => true]) ?>
                    <!-- Información de Contacto -->
                    <div class="section-divider">
                        <div class="section-title">
                            <i class="fas fa-map-marker-alt me-2"></i>Información de Contacto
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="direccion">Dirección <span class="required">*</span></label>
                                <input type="text" 
                                       class="form-control <?= isset($validation) && $validation->hasError('direccion') ? 'is-invalid' : '' ?>" 
                                       id="direccion" 
                                       name="direccion" 
                                       value="<?= old('direccion') ?>" 
                                       placeholder="Dirección completa"
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
                                <label for="comunas_id">Comuna <span class="required">*</span></label>
                                <select class="form-control select2-comunas <?= isset($validation) && $validation->hasError('comunas_id') ? 'is-invalid' : '' ?>" 
                                        id="comunas_id" 
                                        name="comunas_id" 
                                        required>
                                    <option value="">Seleccionar comuna</option>
                                    <?php if (isset($comunas)): ?>
                                        <?php foreach ($comunas as $comuna): ?>
                                            <option value="<?= $comuna['comunas_id'] ?>" 
                                                    <?= old('comunas_id') == $comuna['comunas_id'] ? 'selected' : '' ?>>
                                                <?= esc($comuna['comunas_nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?php if (isset($validation) && $validation->hasError('comunas_id')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('comunas_id') ?>
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
                                <label for="telefono">Teléfono (Opcional)</label>
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
                                <i class="fas fa-save me-2"></i>Crear Inspección
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
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// Inicializar Select2 para el campo de comunas
$(document).ready(function() {
    $('#comunas_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Seleccionar comuna...',
        allowClear: true,  // Permite limpiar la selección
        width: '100%'  // Ajuste para el tamaño del campo
    });
});

// Formateo automático del RUT
document.getElementById('rut').addEventListener('input', function(e) {
    let rut = e.target.value.replace(/[^0-9kK]/g, '');
    
    if (rut.length > 1) {
        rut = rut.slice(0, -1) + '-' + rut.slice(-1);
        
        if (rut.length > 4) {
            let numbers = rut.slice(0, -2);
            let dv = rut.slice(-2);
            
            numbers = numbers.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            rut = numbers + dv;
        }
    }
    
    e.target.value = rut;
});

// Convertir patente a mayúsculas
document.getElementById('patente').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});

// Validación del formulario
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
