<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
    .form-floating {
        margin-bottom: 1rem;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .required::after {
        content: " *";
        color: #dc3545;
    }
    
    .rut-input {
        text-transform: uppercase;
    }
    
    .patente-input {
        text-transform: uppercase;
    }
    
    .preview-section {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
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
                    <p class="text-muted mb-0">Complete todos los campos obligatorios</p>
                </div>
                <div>
                    <a href="<?= base_url('corredor') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensajes de error -->
    <?php if (session('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Errores encontrados:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach (session('errors') as $error): ?>
            <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Formulario -->
    <form action="<?= base_url('corredor/store') ?>" method="post" id="inspeccionForm">
        <?= csrf_field() ?>
        
        <div class="row">
            <!-- Información del Asegurado -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>
                            Información del Asegurado
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-floating">
                            <input type="text" 
                                   class="form-control" 
                                   id="asegurado" 
                                   name="asegurado" 
                                   placeholder="Nombre completo del asegurado"
                                   value="<?= old('asegurado') ?>"
                                   required>
                            <label for="asegurado" class="required">Nombre del Asegurado</label>
                        </div>

                        <div class="form-floating">
                            <input type="text" 
                                   class="form-control rut-input" 
                                   id="inspecciones_rut" 
                                   name="inspecciones_rut" 
                                   placeholder="12345678-9"
                                   value="<?= old('inspecciones_rut') ?>"
                                   maxlength="12"
                                   required>
                            <label for="inspecciones_rut" class="required">RUT</label>
                            <div class="form-text">Formato: 12345678-9</div>
                        </div>

                        <div class="form-floating">
                            <input type="text" 
                                   class="form-control" 
                                   id="inspecciones_direccion" 
                                   name="inspecciones_direccion" 
                                   placeholder="Dirección completa"
                                   value="<?= old('inspecciones_direccion') ?>"
                                   required>
                            <label for="inspecciones_direccion" class="required">Dirección</label>
                        </div>

                        <div class="form-floating">
                            <select class="form-select" id="comunas_id" name="comunas_id" required>
                                <option value="">Seleccione una comuna</option>
                                <?php foreach ($comunas as $comuna): ?>
                                <option value="<?= $comuna['comunas_id'] ?>" 
                                        <?= old('comunas_id') == $comuna['comunas_id'] ? 'selected' : '' ?>>
                                    <?= esc($comuna['comunas_nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="comunas_id" class="required">Comuna</label>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" 
                                           class="form-control" 
                                           id="celular" 
                                           name="celular" 
                                           placeholder="+56 9 1234 5678"
                                           value="<?= old('celular') ?>"
                                           required>
                                    <label for="celular" class="required">Celular</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" 
                                           class="form-control" 
                                           id="telefono" 
                                           name="telefono" 
                                           placeholder="+56 2 1234 5678"
                                           value="<?= old('telefono') ?>">
                                    <label for="telefono">Teléfono (Opcional)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Vehículo -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-car me-2"></i>
                            Información del Vehículo
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-floating">
                            <input type="text" 
                                   class="form-control patente-input" 
                                   id="patente" 
                                   name="patente" 
                                   placeholder="ABC123 o ABCD12"
                                   value="<?= old('patente') ?>"
                                   maxlength="8"
                                   required>
                            <label for="patente" class="required">Patente</label>
                            <div class="form-text">Formato antiguo: ABC123 | Formato nuevo: ABCD12</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control" 
                                           id="marca" 
                                           name="marca" 
                                           placeholder="Toyota, Chevrolet, etc."
                                           value="<?= old('marca') ?>"
                                           required>
                                    <label for="marca" class="required">Marca</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control" 
                                           id="modelo" 
                                           name="modelo" 
                                           placeholder="Corolla, Aveo, etc."
                                           value="<?= old('modelo') ?>"
                                           required>
                                    <label for="modelo" class="required">Modelo</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-floating">
                            <select class="form-select" id="cia_id" name="cia_id" required>
                                <option value="">Seleccione una compañía</option>
                                <?php foreach ($companias as $compania): ?>
                                <option value="<?= $compania['cia_id'] ?>" 
                                        <?= old('cia_id') == $compania['cia_id'] ? 'selected' : '' ?>>
                                    <?= esc($compania['cia_nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="cia_id" class="required">Compañía de Seguros</label>
                        </div>

                        <div class="form-floating">
                            <input type="text" 
                                   class="form-control" 
                                   id="n_poliza" 
                                   name="n_poliza" 
                                   placeholder="Número de póliza"
                                   value="<?= old('n_poliza') ?>"
                                   required>
                            <label for="n_poliza" class="required">Número de Póliza</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Section -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-eye me-2"></i>
                            Resumen de la Inspección
                        </h5>
                    </div>
                    <div class="card-body preview-section">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Datos del Asegurado</h6>
                                <p class="mb-1"><strong>Nombre:</strong> <span id="preview-asegurado">-</span></p>
                                <p class="mb-1"><strong>RUT:</strong> <span id="preview-rut">-</span></p>
                                <p class="mb-1"><strong>Dirección:</strong> <span id="preview-direccion">-</span></p>
                                <p class="mb-1"><strong>Celular:</strong> <span id="preview-celular">-</span></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Datos del Vehículo</h6>
                                <p class="mb-1"><strong>Patente:</strong> <span id="preview-patente">-</span></p>
                                <p class="mb-1"><strong>Vehículo:</strong> <span id="preview-vehiculo">-</span></p>
                                <p class="mb-1"><strong>Compañía:</strong> <span id="preview-compania">-</span></p>
                                <p class="mb-1"><strong>Póliza:</strong> <span id="preview-poliza">-</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-save me-2"></i>
                            Crear Inspección
                        </button>
                        <a href="<?= base_url('corredor') ?>" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Preview en tiempo real
    function updatePreview() {
        $('#preview-asegurado').text($('#inspecciones_asegurado').val() || '-');
        $('#preview-rut').text($('#inspecciones_rut').val() || '-');
        $('#preview-direccion').text($('#inspecciones_direccion').val() || '-');
        $('#preview-celular').text($('#inspecciones_celular').val() || '-');
        $('#preview-patente').text($('#inspecciones_patente').val() || '-');
        
        var marca = $('#inspecciones_marca').val();
        var modelo = $('#inspecciones_modelo').val();
        $('#preview-vehiculo').text((marca && modelo) ? `${marca} ${modelo}` : '-');
        
        var companiaText = $('#cia_id option:selected').text();
        $('#preview-compania').text(companiaText !== 'Seleccione una compañía' ? companiaText : '-');
        
        $('#preview-poliza').text($('#inspecciones_n_poliza').val() || '-');
    }

    // Actualizar preview en tiempo real
    $('input, select').on('input change', updatePreview);

    // Formatear RUT automáticamente
    $('#inspecciones_rut').on('input', function() {
        var rut = $(this).val().replace(/[^0-9kK]/g, '');
        if (rut.length > 1) {
            var dv = rut.slice(-1);
            var numero = rut.slice(0, -1);
            if (numero.length > 0) {
                var rutFormateado = numero.replace(/\B(?=(\d{3})+(?!\d))/g, '.') + '-' + dv;
                $(this).val(rutFormateado);
            }
        }
    });

    // Formatear patente automáticamente
    $('#inspecciones_patente').on('input', function() {
        var patente = $(this).val().toUpperCase().replace(/[^A-Z0-9]/g, '');
        $(this).val(patente);
    });

    // Formatear teléfonos
    function formatearTelefono(input) {
        var numero = input.replace(/[^0-9]/g, '');
        if (numero.startsWith('56')) {
            numero = numero.substring(2);
        }
        
        if (numero.startsWith('9') && numero.length === 9) {
            // Celular: +56 9 1234 5678
            return '+56 9 ' + numero.substring(1, 5) + ' ' + numero.substring(5);
        } else if (numero.length === 9 && numero.startsWith('2')) {
            // Fijo Santiago: +56 2 1234 5678
            return '+56 2 ' + numero.substring(1, 5) + ' ' + numero.substring(5);
        } else if (numero.length === 8) {
            // Celular sin 9: +56 9 1234 5678
            return '+56 9 ' + numero.substring(0, 4) + ' ' + numero.substring(4);
        }
        
        return input;
    }

    $('#inspecciones_celular, #inspecciones_telefono').on('blur', function() {
        $(this).val(formatearTelefono($(this).val()));
    });

    // Validación del formulario
    $('#inspeccionForm').on('submit', function(e) {
        var isValid = true;
        var errores = [];

        // Validar RUT
        var rut = $('#inspecciones_rut').val().replace(/[^0-9kK]/g, '');
        if (!validarRUT(rut)) {
            errores.push('El RUT ingresado no es válido');
            isValid = false;
        }

        // Validar patente
        var patente = $('#inspecciones_patente').val();
        if (!validarPatente(patente)) {
            errores.push('La patente debe tener el formato ABC123 o ABCD12');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            alert('Errores encontrados:\n' + errores.join('\n'));
        }
    });

    // Función para validar RUT chileno
    function validarRUT(rut) {
        if (rut.length < 8 || rut.length > 9) return false;
        
        var dv = rut.slice(-1);
        var numero = rut.slice(0, -1);
        
        var suma = 0;
        var multiplicador = 2;
        
        for (var i = numero.length - 1; i >= 0; i--) {
            suma += numero[i] * multiplicador;
            multiplicador = multiplicador === 7 ? 2 : multiplicador + 1;
        }
        
        var resto = suma % 11;
        var dvCalculado = 11 - resto;
        
        if (dvCalculado === 11) dvCalculado = '0';
        if (dvCalculado === 10) dvCalculado = 'K';
        
        return dv.toString() === dvCalculado.toString();
    }

    // Función para validar patente chilena
    function validarPatente(patente) {
        // Formato nuevo: 4 letras + 2 números (ABCD12)
        var formatoNuevo = /^[A-Z]{4}[0-9]{2}$/.test(patente);
        // Formato antiguo: 2 letras + 4 números (AB1234)
        var formatoAntiguo = /^[A-Z]{2}[0-9]{4}$/.test(patente);
        
        return formatoNuevo || formatoAntiguo;
    }

    // Inicializar preview
    updatePreview();
});
</script>
<?= $this->endSection() ?>
<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Preview en tiempo real
    function updatePreview() {
        $('#preview-asegurado').text($('#inspecciones_asegurado').val() || '-');
        $('#preview-rut').text($('#inspecciones_rut').val() || '-');
        $('#preview-direccion').text($('#inspecciones_direccion').val() || '-');
        $('#preview-celular').text($('#inspecciones_celular').val() || '-');
        $('#preview-patente').text($('#inspecciones_patente').val() || '-');
        
        var marca = $('#inspecciones_marca').val();
        var modelo = $('#inspecciones_modelo').val();
        $('#preview-vehiculo').text((marca && modelo) ? `${marca} ${modelo}` : '-');
        
        var companiaText = $('#cia_id option:selected').text();
        $('#preview-compania').text(companiaText !== 'Seleccione una compañía' ? companiaText : '-');
        
        $('#preview-poliza').text($('#inspecciones_n_poliza').val() || '-');
        
        // Observaciones
        var observaciones = $('#inspecciones_observaciones').val();
        if (observaciones.trim()) {
            $('#preview-observaciones').text(observaciones);
            $('#preview-observaciones-container').show();
        } else {
            $('#preview-observaciones-container').hide();
        }
    }

    // Actualizar preview en tiempo real
    $('input, select, textarea').on('input change', updatePreview);

    // Formatear RUT automáticamente
    $('#inspecciones_rut').on('input', function() {
        var rut = $(this).val().replace(/[^0-9kK]/g, '');
        if (rut.length > 1) {
            var dv = rut.slice(-1);
            var numero = rut.slice(0, -1);
            if (numero.length > 0) {
                var rutFormateado = numero.replace(/\B(?=(\d{3})+(?!\d))/g, '.') + '-' + dv;
                $(this).val(rutFormateado);
            }
        }
    });

    // Formatear patente automáticamente
    $('#inspecciones_patente').on('input', function() {
        var patente = $(this).val().toUpperCase().replace(/[^A-Z0-9]/g, '');
        $(this).val(patente);
    });

    // Formatear teléfonos
    function formatearTelefono(input) {
        var numero = input.replace(/[^0-9]/g, '');
        if (numero.startsWith('56')) {
            numero = numero.substring(2);
        }
        
        if (numero.startsWith('9') && numero.length === 9) {
            return '+56 9 ' + numero.substring(1, 5) + ' ' + numero.substring(5);
        } else if (numero.length === 9 && numero.startsWith('2')) {
            return '+56 2 ' + numero.substring(1, 5) + ' ' + numero.substring(5);
        } else if (numero.length === 8) {
            return '+56 9 ' + numero.substring(0, 4) + ' ' + numero.substring(4);
        }
        
        return input;
    }

    $('#inspecciones_celular, #inspecciones_telefono').on('blur', function() {
        $(this).val(formatearTelefono($(this).val()));
    });

    // Función para validar RUT chileno
    function validarRUT(rut) {
        if (rut.length < 8 || rut.length > 9) return false;
        
        var dv = rut.slice(-1);
        var numero = rut.slice(0, -1);
        
        var suma = 0;
        var multiplicador = 2;
        
        for (var i = numero.length - 1; i >= 0; i--) {
            suma += numero[i] * multiplicador;
            multiplicador = multiplicador === 7 ? 2 : multiplicador + 1;
        }
        
        var resto = suma % 11;
        var dvCalculado = 11 - resto;
        
        if (dvCalculado === 11) dvCalculado = '0';
        if (dvCalculado === 10) dvCalculado = 'K';
        
        return dv.toString() === dvCalculado.toString();
    }

    // Función para validar patente chilena
    function validarPatente(patente) {
        var formatoNuevo = /^[A-Z]{4}[0-9]{2}$/.test(patente);
        var formatoAntiguo = /^[A-Z]{2}[0-9]{4}$/.test(patente);
        return formatoNuevo || formatoAntiguo;
    }

    // Validación del formulario
    $('#inspeccionForm').on('submit', function(e) {
        var isValid = true;
        var errores = [];

        // Validar RUT
        var rut = $('#inspecciones_rut').val().replace(/[^0-9kK]/g, '');
        if (!validarRUT(rut)) {
            errores.push('El RUT ingresado no es válido');
            isValid = false;
        }

        // Validar patente
        var patente = $('#inspecciones_patente').val();
        if (!validarPatente(patente)) {
            errores.push('La patente debe tener el formato ABC123 o ABCD12');
            isValid = false;
        }

        // Validar campos obligatorios
        var camposObligatorios = [
            {campo: '#inspecciones_asegurado', nombre: 'Nombre del asegurado'},
            {campo: '#inspecciones_direccion', nombre: 'Dirección'},
            {campo: '#comunas_id', nombre: 'Comuna'},
            {campo: '#inspecciones_celular', nombre: 'Celular'},
            {campo: '#inspecciones_marca', nombre: 'Marca del vehículo'},
            {campo: '#inspecciones_modelo', nombre: 'Modelo del vehículo'},
            {campo: '#cia_id', nombre: 'Compañía de seguros'},
            {campo: '#inspecciones_n_poliza', nombre: 'Número de póliza'}
        ];

        camposObligatorios.forEach(function(item) {
            if (!$(item.campo).val().trim()) {
                errores.push(item.nombre + ' es obligatorio');
                isValid = false;
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Errores encontrados:\n\n' + errores.join('\n'));
        } else {
            if (!confirm('¿Estás seguro de que deseas crear esta inspección?')) {
                e.preventDefault();
            }
        }
    });

    // Manejar reset del formulario
    $('button[type="reset"]').on('click', function() {
        if (confirm('¿Estás seguro de que deseas limpiar todo el formulario?')) {
            setTimeout(updatePreview, 100);
        } else {
            return false;
        }
    });

    // Inicializar preview
    updatePreview();
});
</script>
<?= $this->endSection() ?>