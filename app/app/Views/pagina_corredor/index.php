 <?= $this->extend('layouts/maincorredor') ?> 

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
    
    /* Estilos para el filtro de comunas */
    .filtro-container {
        position: relative;
    }
    
    #filtro-comuna {
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
        border-bottom: none;
    }
    
    #comunas_id {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
        border-top: 1px solid #dee2e6;
        max-height: 200px;
        overflow-y: auto;
    }
    
    .selected-comuna {
        background-color: #e3f2fd;
        border: 2px solid #2196f3;
        border-radius: 5px;
        padding: 8px 12px;
        margin-top: 10px;
        display: none;
    }
    
    .info-badge {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 0.5rem;
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
                                
                                <div class="filtro-container">
                                    <!-- Input para filtrar -->
                                    <input type="text" 
                                           class="form-control" 
                                           id="filtro-comuna" 
                                           placeholder="Escriba para filtrar comunas..."
                                           autocomplete="off">
                                  
                                    <!-- Select normal -->
                                    <select class="form-control <?= isset($validation) && $validation->hasError('comunas_id') ? 'is-invalid' : '' ?>" 
                                            id="comunas_id" 
                                            name="comunas_id" 
                                            size="8"
                                            required>
                                        <option value="">-- Seleccionar comuna --</option>
                                        <?php if (isset($comunas)): ?>
                                            <?php foreach ($comunas as $comuna): ?>
                                                <option value="<?= $comuna['comunas_id'] ?>" 
                                                        data-nombre="<?= strtolower($comuna['comunas_nombre']) ?>"
                                                        <?= old('comunas_id') == $comuna['comunas_id'] ? 'selected' : '' ?>>
                                                    <?= esc($comuna['comunas_nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                
                                <!-- Comuna seleccionada -->
                                <div id="comuna-seleccionada" class="selected-comuna">
                                    <strong>Comuna seleccionada:</strong> <span id="nombre-comuna"></span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="limpiarSeleccion()">
                                        Cambiar
                                    </button>
                                </div>
                                
                                <div class="info-badge">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Escribe para filtrar. Click en una comuna para seleccionar.
                                </div>
                                
                                <?php if (isset($validation) && $validation->hasError('comunas_id')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('comunas_id') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="section-divider">
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('corredor/inspecciones') ?>" class="btn btn-outline-secondary">
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
<script>
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

// Filtro de comunas sin librerías externas
document.getElementById('filtro-comuna').addEventListener('input', function() {
    const filtro = this.value.toLowerCase();
    const select = document.getElementById('comunas_id');
    const opciones = select.getElementsByTagName('option');
    let visibles = 0;
    
    // Mostrar el select si está escribiendo
    if (filtro.length > 0) {
        select.style.display = 'block';
    } else {
        select.style.display = 'none';  // Ocultar select si el campo está vacío
    }
    
    // Filtrar opciones
    for (let i = 1; i < opciones.length; i++) { // Empezar en 1 para saltar el placeholder
        const nombre = opciones[i].getAttribute('data-nombre');
        if (nombre.includes(filtro)) {
            opciones[i].style.display = '';  // Mostrar la opción
            visibles++;
        } else {
            opciones[i].style.display = 'none';  // Ocultar la opción
        }
    }
    
    // Si no hay coincidencias, ocultar el select
    if (visibles === 0) {
        select.style.display = 'none';
    }
});

// Al seleccionar una comuna
document.getElementById('comunas_id').addEventListener('change', function() {
    if (this.value) {
        const textoSeleccionado = this.options[this.selectedIndex].text;
        
        // Ocultar el select grande
        this.style.display = 'none';
        
        // Mostrar la comuna seleccionada
        document.getElementById('nombre-comuna').textContent = textoSeleccionado;
        document.getElementById('comuna-seleccionada').style.display = 'block';
        
        // Actualizar el input con el nombre seleccionado
        document.getElementById('filtro-comuna').value = textoSeleccionado;
        document.getElementById('filtro-comuna').readOnly = true;
    }
});

// Función para limpiar la selección
function limpiarSeleccion() {
    document.getElementById('comunas_id').value = '';
    document.getElementById('comunas_id').style.display = 'block';
    document.getElementById('comunas_id').size = 8;
    document.getElementById('comuna-seleccionada').style.display = 'none';
    document.getElementById('filtro-comuna').value = '';
    document.getElementById('filtro-comuna').readOnly = false;
    document.getElementById('filtro-comuna').focus();
}

// Al hacer click en el input cuando está readonly, permitir cambiar
document.getElementById('filtro-comuna').addEventListener('click', function() {
    if (this.readOnly) {
        limpiarSeleccion();
    }
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
