<?= $this->extend('layouts/maincorredor') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
    .form-section {
        background: #f8f9fa;
        border-left: 4px solid #0d6efd;
        border-radius: 0 10px 10px 0;
        margin-bottom: 2rem;
    }
    
    .form-section h5 {
        color: #0d6efd;
        font-weight: 600;
    }
    
    .required-field::after {
        content: " *";
        color: #dc3545;
        font-weight: bold;
    }
    
    .phone-input {
        position: relative;
    }
    
    .phone-prefix {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        font-weight: 500;
        z-index: 10;
        pointer-events: none;
    }
    
    .phone-input input {
        padding-left: 60px;
    }
    
    .whatsapp-btn {
        background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }
    
    .whatsapp-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(37, 211, 102, 0.3);
        color: white;
    }
    
    .btn-save {
        background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }
    
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(13, 110, 253, 0.3);
        color: white;
    }
    
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .spinner-container {
        display: none;
        justify-content: center;
        align-items: center;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
    }
    
    .card {
        border: none;
        border-radius: 15px;
    }
    
    .card-header {
        border-radius: 15px 15px 0 0 !important;
    }
    
    .btn {
        border-radius: 8px;
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
                    <p class="text-muted mb-0">Complete todos los campos para crear la inspección</p>
                </div>
                <div>
                    <a href="<?= base_url('corredor') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <form id="inspeccionForm" novalidate>
        <div class="row">
            <!-- Información del Asegurado -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header form-section">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>
                            Información del Asegurado
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="inspecciones_asegurado" class="form-label required-field">Nombre Completo</label>
                                <input type="text" class="form-control" id="inspecciones_asegurado" 
                                       name="inspecciones_asegurado" placeholder="Ingrese el nombre completo" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="inspecciones_rut" class="form-label required-field">RUT</label>
                                <input type="text" class="form-control" id="inspecciones_rut" 
                                       name="inspecciones_rut" placeholder="12.345.678-9" 
                                       maxlength="12" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="inspecciones_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="inspecciones_email" 
                                       name="inspecciones_email" placeholder="correo@ejemplo.com">
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="inspecciones_celular" class="form-label required-field">Celular</label>
                                <div class="phone-input">
                                    <span class="phone-prefix">+569</span>
                                    <input type="tel" class="form-control" id="inspecciones_celular" 
                                           name="inspecciones_celular" placeholder="XXXX XXXX" 
                                           maxlength="9" required>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="inspecciones_telefono" class="form-label">Teléfono Fijo</label>
                                <input type="tel" class="form-control" id="inspecciones_telefono" 
                                       name="inspecciones_telefono" placeholder="22 XXX XXXX">
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="inspecciones_direccion" class="form-label required-field">Dirección</label>
                                <input type="text" class="form-control" id="inspecciones_direccion" 
                                       name="inspecciones_direccion" placeholder="Ingrese la dirección completa" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="comunas_id" class="form-label required-field">Comuna</label>
                                <select class="form-select" id="comunas_id" name="comunas_id" required>
                                    <option value="">Seleccione una comuna</option>
                                    <?php foreach ($comunas as $comuna): ?>
                                        <option value="<?= $comuna['comunas_id'] ?>">
                                            <?= esc($comuna['comunas_nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Vehículo -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header form-section">
                        <h5 class="mb-0">
                            <i class="fas fa-car me-2"></i>
                            Información del Vehículo
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="inspecciones_patente" class="form-label required-field">Patente</label>
                                <input type="text" class="form-control" id="inspecciones_patente" 
                                       name="inspecciones_patente" placeholder="ABCD12" 
                                       style="text-transform: uppercase;" maxlength="6" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="tipo_inspeccion_id" class="form-label required-field">Tipo de Inspección</label>
                                <select class="form-select" id="tipo_inspeccion_id" name="tipo_inspeccion_id" required>
                                    <option value="">Seleccione tipo de inspección</option>
                                    <?php foreach ($tipos_inspeccion as $id => $nombre): ?>
                                        <option value="<?= $id ?>"><?= esc($nombre) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="tipo_carroceria_id" class="form-label required-field">Tipo de Carrocería</label>
                                <select class="form-select" id="tipo_carroceria_id" name="tipo_carroceria_id" required disabled>
                                    <option value="">Primero seleccione tipo de inspección</option>
                                </select>
                                <div class="invalid-feedback"></div>
                                <div class="form-text">
                                    <small>Las opciones de carrocería dependen del tipo de inspección seleccionado</small>
                                </div>
                            </div>
                            
                            <div class="col-md-12 mb-3" id="tipoInspeccionInfo" style="display: none;">
                                <div class="alert alert-info mb-0">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <div>
                                                    <strong>Información de la Inspección:</strong>
                                                    <br>
                                                    <small id="descripcionTipoInspeccion"></small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-end">
                                                <small class="text-muted">
                                                    <strong>Duración:</strong> <span id="duracionEstimada"></span><br>
                                                    <strong>Costo aprox:</strong> <span id="costoAproximado"></span>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="inspecciones_marca" class="form-label required-field">Marca</label>
                                <input type="text" class="form-control" id="inspecciones_marca" 
                                       name="inspecciones_marca" placeholder="Toyota, Chevrolet, etc." required>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="inspecciones_modelo" class="form-label required-field">Modelo</label>
                                <input type="text" class="form-control" id="inspecciones_modelo" 
                                       name="inspecciones_modelo" placeholder="Corolla, Spark, etc." required>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="cia_id" class="form-label required-field">Compañía de Seguros</label>
                                <select class="form-select" id="cia_id" name="cia_id" required>
                                    <option value="">Seleccione una compañía</option>
                                    <?php foreach ($companias as $cia): ?>
                                        <option value="<?= $cia['cia_id'] ?>">
                                            <?= esc($cia['cia_nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="inspecciones_n_poliza" class="form-label required-field">Número de Póliza</label>
                                <input type="text" class="form-control" id="inspecciones_n_poliza" 
                                       name="inspecciones_n_poliza" placeholder="Ingrese el número de póliza" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Observaciones -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header form-section">
                        <h5 class="mb-0">
                            <i class="fas fa-sticky-note me-2"></i>
                            Observaciones Adicionales
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="inspecciones_observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="inspecciones_observaciones" 
                                      name="inspecciones_observaciones" rows="4" 
                                      placeholder="Ingrese cualquier observación relevante para la inspección (opcional)"></textarea>
                            <div class="form-text">
                                Puede incluir detalles específicos sobre el vehículo, ubicación especial, horarios preferidos, etc.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <button type="button" class="btn btn-outline-secondary me-3" onclick="history.back()">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-save btn-lg px-5" id="btnGuardar">
                            <i class="fas fa-save me-2"></i>Crear Inspección
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Spinner de Carga -->
<div class="spinner-container" id="spinnerContainer">
    <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Cargando...</span>
    </div>
</div>

<!-- Modal de Éxito con WhatsApp -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>¡Inspección Creada!
                </h5>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="fas fa-clipboard-check fa-3x text-success mb-3"></i>
                    <h5>La inspección ha sido creada exitosamente</h5>
                    <p class="text-muted">ID de Inspección: <strong id="inspeccionId">#</strong></p>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="#" id="whatsappBtn" class="btn whatsapp-btn btn-lg" target="_blank">
                        <i class="fab fa-whatsapp me-2"></i>Enviar WhatsApp al Cliente
                    </a>
                    <a href="<?= base_url('corredor') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>Ir al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    alert("aa")
    // Manejar cambio de tipo de inspección
    $('#tipo_inspeccion_id').on('change', function() {
        const tipoInspeccionId = $(this).val();
        const $carroceria = $('#tipo_carroceria_id');
        const $info = $('#tipoInspeccionInfo');
        
        // Limpiar carrocerías
        $carroceria.html('<option value="">Cargando...</option>').prop('disabled', true);
        $info.hide();
        
        if (tipoInspeccionId) {
            // Cargar carrocerías por AJAX
            $.ajax({
                url: '<?= base_url("inspecciones/carrocerias/") ?>' + tipoInspeccionId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        let options = '<option value="">Seleccione carrocería</option>';
                        
                        if (Object.keys(response.carrocerias).length > 0) {
                            $.each(response.carrocerias, function(id, nombre) {
                                options += `<option value="${id}">${nombre}</option>`;
                            });
                            $carroceria.prop('disabled', false);
                        } else {
                            options = '<option value="">No hay carrocerías disponibles</option>';
                        }
                        
                        $carroceria.html(options);
                    }
                },
                error: function() {
                    $carroceria.html('<option value="">Error al cargar carrocerías</option>');
                }
            });
            
            // Cargar información del tipo de inspección
            alert(tipoInspeccionId)
            $.ajax({
                url: '<?= base_url("inspecciones/tipo-inspeccion-info/") ?>' + tipoInspeccionId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const tipo = response.tipo;
                        const info = response.info_adicional;
                        
                        $('#descripcionTipoInspeccion').text(tipo.tipo_inspeccion_descripcion);
                        $('#duracionEstimada').text(info.duracion_estimada);
                        $('#costoAproximado').text(info.costo_aproximado);
                        
                        // Cambiar color del alert según el tipo
                        const $alert = $info.find('.alert');
                        $alert.removeClass('alert-info alert-warning alert-success');
                        $alert.addClass('alert-' + info.color_badge);
                        
                        $info.show();
                    }
                },
                error: function() {
                    console.error('Error al cargar información del tipo de inspección');
                }
            });
        } else {
            $carroceria.html('<option value="">Primero seleccione tipo de inspección</option>').prop('disabled', true);
        }
    });

    // Formatear RUT mientras se escribe
    $('#inspecciones_rut').on('input', function() {
        let rut = $(this).val().replace(/[^0-9kK]/g, '');
        if (rut.length > 1) {
            rut = rut.slice(0, -1).replace(/\B(?=(\d{3})+(?!\d))/g, '.') + '-' + rut.slice(-1);
        }
        $(this).val(rut);
    });

    // Formatear celular para que solo permita números
    $('#inspecciones_celular').on('input', function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        if (value.length > 8) value = value.substring(0, 8);
        
        // Formatear con espacio
        if (value.length > 4) {
            value = value.substring(0, 4) + ' ' + value.substring(4);
        }
        $(this).val(value);
    });

    // Formatear teléfono fijo
    $('#inspecciones_telefono').on('input', function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        if (value.length > 9) value = value.substring(0, 9);
        
        // Formatear según longitud
        if (value.length > 2) {
            if (value.length <= 6) {
                value = value.substring(0, 2) + ' ' + value.substring(2);
            } else {
                value = value.substring(0, 2) + ' ' + value.substring(2, 5) + ' ' + value.substring(5);
            }
        }
        $(this).val(value);
    });

    // Validación y envío del formulario
    $('#inspeccionForm').on('submit', function(e) {
        e.preventDefault();
        
        // Limpiar errores previos
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        // Validación básica antes de enviar
        let hasErrors = false;
        
        // Validar campos requeridos
        $('input[required], select[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                $(this).siblings('.invalid-feedback').text('Este campo es obligatorio');
                hasErrors = true;
            }
        });
        
        // Validar RUT
        const rut = $('#inspecciones_rut').val();
        if (rut && !validarRUT(rut)) {
            $('#inspecciones_rut').addClass('is-invalid');
            $('#inspecciones_rut').siblings('.invalid-feedback').text('RUT inválido');
            hasErrors = true;
        }
        
        // Validar email si se ingresó
        const email = $('#inspecciones_email').val();
        if (email && !isValidEmail(email)) {
            $('#inspecciones_email').addClass('is-invalid');
            $('#inspecciones_email').siblings('.invalid-feedback').text('Email inválido');
            hasErrors = true;
        }
        
        if (hasErrors) {
            showAlert('error', 'Por favor corrija los errores en el formulario');
            return;
        }
        
        // Mostrar spinner
        $('#spinnerContainer').show();
        $('#btnGuardar').prop('disabled', true);
        
        // Preparar datos
        const formData = new FormData(this);
        
        // Enviar AJAX
        $.ajax({
            url: '<?= base_url("corredor/store") ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Respuesta exitosa:', response);
                
                if (response.success) {
                    // Actualizar modal con información
                    $('#inspeccionId').text('#' + response.id);
                    if (response.whatsapp_url) {
                        $('#whatsappBtn').attr('href', response.whatsapp_url);
                    }
                    
                    // Mostrar modal
                    $('#successModal').modal('show');
                } else {
                    handleErrors(response);
                }
            },
            error: function(xhr) {
                console.error('Error AJAX:', xhr);
                let response = {};
                try {
                    response = JSON.parse(xhr.responseText);
                } catch (e) {
                    response = { message: 'Error de conexión' };
                }
                handleErrors(response);
            },
            complete: function() {
                $('#spinnerContainer').hide();
                $('#btnGuardar').prop('disabled', false);
            }
        });
    });
    
    function handleErrors(response) {
        if (response.errors && Array.isArray(response.errors)) {
            // Mostrar errores específicos
            response.errors.forEach(function(error) {
                showAlert('error', error);
            });
        } else if (response.message) {
            showAlert('error', response.message);
        } else {
            showAlert('error', 'Error desconocido al crear la inspección');
        }
    }
    
    function showAlert(type, message) {
        const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
        const icon = type === 'error' ? 'fas fa-exclamation-circle' : 'fas fa-check-circle';
        
        const alert = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="${icon} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.container-fluid').prepend(alert);
        
        // Auto-ocultar después de 5 segundos
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
        
        // Scroll al top
        $('html, body').animate({ scrollTop: 0 }, 500);
    }
    
    // Validación RUT
    function validarRUT(rut) {
        rut = rut.replace(/[^0-9kK]/g, '');
        
        if (rut.length < 8 || rut.length > 9) return false;
        
        const dv = rut.slice(-1).toLowerCase();
        const numero = rut.slice(0, -1);
        
        let suma = 0;
        let multiplicador = 2;
        
        for (let i = numero.length - 1; i >= 0; i--) {
            suma += parseInt(numero[i]) * multiplicador;
            multiplicador = (multiplicador === 7) ? 2 : multiplicador + 1;
        }
        
        const resto = suma % 11;
        const dvCalculado = (resto === 0) ? '0' : ((resto === 1) ? 'k' : String(11 - resto));
        
        return dv === dvCalculado;
    }
    
    // Validación email
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Validación en tiempo real
    $('input[required], select[required]').on('blur', function() {
        const $this = $(this);
        const value = $this.val().trim();
        
        if (!value) {
            $this.addClass('is-invalid');
            $this.siblings('.invalid-feedback').text('Este campo es obligatorio');
        } else {
            $this.removeClass('is-invalid');
            $this.siblings('.invalid-feedback').text('');
        }
    });
    
    // Validación específica para RUT
    $('#inspecciones_rut').on('blur', function() {
        const rut = $(this).val();
        if (rut && !validarRUT(rut)) {
            $(this).addClass('is-invalid');
            $(this).siblings('.invalid-feedback').text('RUT inválido');
        } else if (rut) {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').text('');
        }
    });
    
    // Validación específica para email
    $('#inspecciones_email').on('blur', function() {
        const email = $(this).val();
        if (email && !isValidEmail(email)) {
            $(this).addClass('is-invalid');
            $(this).siblings('.invalid-feedback').text('Email inválido');
        } else if (email) {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').text('');
        }
    });
});
</script>
<?= $this->endSection() ?>