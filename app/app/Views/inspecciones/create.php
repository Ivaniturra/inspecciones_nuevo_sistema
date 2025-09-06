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
    
    /* Estilos mejorados para Select2 */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }
    
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
        color: #495057;
    }
    
    .select2-container--bootstrap-5 .select2-selection__placeholder {
        color: #6c757d;
    }
    
    .select2-container--bootstrap-5 .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 8px 12px;
        font-size: 0.875rem;
    }
    
    .select2-container--bootstrap-5 .select2-results__option {
        padding: 8px 12px;
        font-size: 0.875rem;
    }
    
    .select2-container--bootstrap-5 .select2-results__option--highlighted {
        background-color: #007bff;
        color: white;
    }
    
    /* Indicador de carga */
    .select2-container--bootstrap-5 .select2-results__option.loading-results {
        text-align: center;
        color: #6c757d;
        font-style: italic;
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
                
                    <!-- Información del Asegurado -->
                    <div class="section-title">
                        <i class="fas fa-user me-2"></i>Información del Asegurado
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
                            </div>
                        </div>
                    </div>

                    <!-- Información del Vehículo -->
                    <div class="section-divider">
                        <div class="section-title">
                            <i class="fas fa-car me-2"></i>Información del Vehículo
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

                    <!-- Información de la Póliza -->
                    <div class="section-divider">
                        <div class="section-title">
                            <i class="fas fa-file-contract me-2"></i>Información de la Póliza
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="n_poliza">Número de Póliza <span class="required">*</span></label>
                                <input type="text" 
                                       class="form-control <?= isset($validation) && $validation->hasError('n_poliza') ? 'is-invalid' : '' ?>" 
                                       id="n_poliza" 
                                       name="n_poliza" 
                                       value="<?= old('n_poliza') ?>" 
                                       placeholder="Número de póliza del seguro"
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
                                <label for="cia_id">Compañía de Seguros <span class="required">*</span></label>
                                <select class="form-control select2-cias <?= isset($validation) && $validation->hasError('cia_id') ? 'is-invalid' : '' ?>" 
                                        id="cia_id" 
                                        name="cia_id" 
                                        required>
                                    <option value="">Seleccione una compañía</option>
                                    <?php if (isset($cias)): ?>
                                        <?php foreach ($cias as $cia): ?>
                                            <option value="<?= $cia['cia_id'] ?>" 
                                                    <?= old('cia_id') == $cia['cia_id'] ? 'selected' : '' ?>>
                                                <?= esc($cia['cia_nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?php if (isset($validation) && $validation->hasError('cia_id')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('cia_id') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

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
                                    <option value="">Buscar y seleccionar comuna...</option>
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
// Configuración global de Select2
const select2Config = {
    theme: 'bootstrap-5',
    allowClear: true,
    width: '100%',
    minimumInputLength: 0,
    escapeMarkup: function(markup) { return markup; },
    language: {
        noResults: function() {
            return "No se encontraron resultados";
        },
        searching: function() {
            return "Buscando...";
        },
        inputTooShort: function() {
            return "Continúe escribiendo...";
        },
        errorLoading: function() {
            return "No se pudieron cargar los resultados";
        }
    }
};

// Formateo automático del RUT con validación mejorada
document.getElementById('rut').addEventListener('input', function(e) {
    let rut = e.target.value.replace(/[^0-9kK]/g, '');
    
    if (rut.length > 1) {
        rut = rut.slice(0, -1) + '-' + rut.slice(-1);
        
        if (rut.length > 4) {
            let numbers = rut.slice(0, -2);
            let dv = rut.slice(-2);
            
            // Formatear con puntos
            numbers = numbers.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            rut = numbers + dv;
        }
    }
    
    e.target.value = rut;
});

// Validación en tiempo real del RUT
document.getElementById('rut').addEventListener('blur', function(e) {
    const rut = e.target.value;
    if (rut && !validarRUT(rut)) {
        e.target.classList.add('is-invalid');
        // Mostrar mensaje de error personalizado
        let feedback = e.target.parentNode.querySelector('.invalid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            e.target.parentNode.appendChild(feedback);
        }
        feedback.textContent = 'RUT inválido';
    } else {
        e.target.classList.remove('is-invalid');
    }
});

// Función para validar RUT chileno
function validarRUT(rut) {
    if (!/^[0-9]+[-|‐]{1}[0-9kK]{1}$/.test(rut)) return false;
    
    let tmp = rut.split('-');
    let digv = tmp[1];
    let rut_limpio = tmp[0].replace(/\./g, '');
    
    if (digv == 'K') digv = 'k';
    return (dv(rut_limpio) == digv);
}

function dv(T) {
    let M = 0, S = 1;
    for (; T; T = Math.floor(T / 10)) {
        S = (S + T % 10 * (9 - M++ % 6)) % 11;
    }
    return S ? S - 1 : 'k';
}

// Convertir patente a mayúsculas con validación
document.getElementById('patente').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
});

// Formateo de teléfonos
function formatearTelefono(input) {
    input.addEventListener('input', function(e) {
        let numero = e.target.value.replace(/\D/g, '');
        
        if (numero.startsWith('56')) {
            numero = numero.substring(2);
        }
        
        if (numero.length >= 8) {
            if (numero.startsWith('9')) {
                // Celular
                e.target.value = '+56 9 ' + numero.substring(1, 5) + ' ' + numero.substring(5, 9);
            } else if (numero.startsWith('2')) {
                // Teléfono fijo Santiago
                e.target.value = '+56 2 ' + numero.substring(1, 5) + ' ' + numero.substring(5, 9);
            } else {
                e.target.value = '+56 ' + numero;
            }
        } else {
            e.target.value = numero;
        }
    });
}

// Aplicar formateo a teléfonos
formatearTelefono(document.getElementById('celular'));
formatearTelefono(document.getElementById('telefono'));

// Inicializar Select2 optimizado
$(document).ready(function() {
    // Select2 para comunas con búsqueda optimizada
    $('.select2-comunas').select2({
        ...select2Config,
        placeholder: 'Buscar y seleccionar comuna...',
        templateResult: function(state) {
            if (!state.id) return state.text;
            
            // Resaltar el texto de búsqueda
            let term = $('.select2-search__field').val();
            if (term) {
                let highlighted = state.text.replace(
                    new RegExp(term, 'gi'), 
                    '<mark>$&</mark>'
                );
                return $('<span>' + highlighted + '</span>');
            }
            return state.text;
        }
    });

    // Select2 para compañías de seguros
    $('.select2-cias').select2({
        ...select2Config,
        placeholder: 'Seleccionar compañía de seguros...'
    });

    // Mejorar la experiencia del usuario
    $('.select2-comunas').on('select2:open', function() {
        // Enfocar automáticamente el campo de búsqueda
        setTimeout(function() {
            $('.select2-search__field').focus();
        }, 100);
    });

    // Limpiar validación al seleccionar
    $('.select2-comunas, .select2-cias').on('select2:select', function() {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').hide();
    });
});

// Validación mejorada del formulario
(function() {
    'use strict';
    
    window.addEventListener('load', function() {
        const forms = document.getElementsByClassName('needs-validation');
        
        Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                let isValid = true;

                // Validar Select2
                const select2Elements = form.querySelectorAll('.select2-hidden-accessible');
                select2Elements.forEach(function(element) {
                    if (element.hasAttribute('required') && !element.value) {
                        element.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        element.classList.remove('is-invalid');
                    }
                });

                // Validar RUT
                const rutInput = form.querySelector('#rut');
                if (rutInput && rutInput.value && !validarRUT(rutInput.value)) {
                    rutInput.classList.add('is-invalid');
                    isValid = false;
                }

                if (!form.checkValidity() || !isValid) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    // Scroll al primer error
                    const firstError = form.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center' 
                        });
                        firstError.focus();
                    }
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Guardar datos en localStorage para recuperación
function guardarBorrador() {
    const formData = new FormData(document.querySelector('form'));
    const data = {};
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    localStorage.setItem('inspeccion_borrador', JSON.stringify(data));
}

function cargarBorrador() {
    const borrador = localStorage.getItem('inspeccion_borrador');
    if (borrador) {
        const data = JSON.parse(borrador);
        Object.keys(data).forEach(key => {
            const element = document.querySelector(`[name="${key}"]`);
            if (element) {
                element.value = data[key];
                if (element.classList.contains('select2-hidden-accessible')) {
                    $(element).val(data[key]).trigger('change');
                }
            }
        });
    }
}

// Guardar borrador cada 30 segundos
setInterval(guardarBorrador, 30000);

// Cargar borrador al inicializar
$(document).ready(function() {
    if (confirm('¿Desea recuperar los datos guardados anteriormente?')) {
        cargarBorrador();
    }
});

// Limpiar borrador al enviar exitosamente
document.querySelector('form').addEventListener('submit', function() {
    setTimeout(() => {
        localStorage.removeItem('inspeccion_borrador');
    }, 1000);
});
</script>
<?= $this->endSection() ?>