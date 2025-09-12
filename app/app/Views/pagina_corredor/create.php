 <?= $this->extend('layouts/maincorredor') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<!-- Asegurar que Bootstrap y Font Awesome estén cargados -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
    body {
        background-color: #f8f9fa;
    }
    
    .container-fluid {
        padding: 2rem;
    }
    
    .form-floating {
        margin-bottom: 1rem !important;
    }
    
    .form-floating > .form-control,
    .form-floating > .form-select {
        height: calc(3.5rem + 2px);
        line-height: 1.25;
        border: 1px solid #ced4da;
        border-radius: 0.5rem;
        font-size: 1rem;
    }
    
    .form-floating > .form-control:focus,
    .form-floating > .form-select:focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .form-floating > label {
        padding: 1rem 0.75rem;
        color: #6c757d;
    }
    
    .card {
        border: none !important;
        border-radius: 15px !important;
        box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.1) !important;
        margin-bottom: 2rem;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
        border-bottom: none !important;
        padding: 1.5rem;
        border-radius: 15px 15px 0 0 !important;
    }
    
    .card-header h5 {
        margin: 0;
        font-weight: 600;
    }
    
    .card-body {
        padding: 2rem;
        background: white;
    }
    
    .card-header.bg-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
    }
    
    .required::after {
        content: " *";
        color: #dc3545 !important;
        font-weight: bold;
    }
    
    .rut-input,
    .patente-input {
        text-transform: uppercase !important;
    }
    
    .preview-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        border-radius: 12px !important;
        padding: 1.5rem !important;
        margin-top: 0;
        border: 1px solid #dee2e6;
    }
    
    .preview-section h6 {
        color: #0d6efd;
        font-weight: 600;
        margin-bottom: 1rem;
        border-bottom: 2px solid #0d6efd;
        padding-bottom: 0.5rem;
    }
    
    .preview-section p {
        margin-bottom: 0.5rem;
        color: #495057;
    }
    
    .preview-section strong {
        color: #212529;
        min-width: 100px;
        display: inline-block;
    }
    
    .btn {
        border-radius: 10px;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(102, 126, 234, 0.4);
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    }
    
    .btn-outline-secondary {
        border: 2px solid #6c757d;
        color: #6c757d;
        background: transparent;
    }
    
    .btn-outline-secondary:hover {
        background: #6c757d;
        color: white;
        transform: translateY(-1px);
    }
    
    .btn-lg {
        padding: 1rem 2rem;
        font-size: 1.1rem;
    }
    
    .h3 {
        color: #495057;
        font-weight: 600;
    }
    
    .text-primary {
        color: #667eea !important;
    }
    
    .alert {
        border-radius: 12px;
        border: none;
        margin-bottom: 2rem;
    }
    
    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
    }
    
    .invalid-feedback {
        display: block !important;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #dc3545;
    }
    
    .form-text {
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 1rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }
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
                <div class="card">
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
                                <?php if(isset($comunas)): ?>
                                    <?php foreach ($comunas as $comuna): ?>
                                    <option value="<?= $comuna['comunas_id'] ?>" 
                                            <?= old('comunas_id') == $comuna['comunas_id'] ? 'selected' : '' ?>>
                                        <?= esc($comuna['comunas_nombre']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
                <div class="card">
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
                            <div class="form-text">Formato: ABC123 o ABCD12</div>
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
                                <?php if(isset($companias)): ?>
                                    <?php foreach ($companias as $compania): ?>
                                    <option value="<?= $compania['cia_id'] ?>" 
                                            <?= old('cia_id') == $compania['cia_id'] ? 'selected' : '' ?>>
                                        <?= esc($compania['cia_nombre']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
                <div class="card">
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
                <div class="card">
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
<!-- Asegurar que jQuery esté cargado -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    console.log('Script cargado correctamente');
    console.log('jQuery version:', $.fn.jquery);
    console.log('Elemento #inspecciones_rut encontrado:', $('#inspecciones_rut').length);

    // ---------- Funciones Helper ----------
    function normalizarRutInput(val) {
        return (val || '').replace(/[^0-9kK]/g, '');
    }

    function formatearRutVisual(rutRaw) {
        const rut = normalizarRutInput(rutRaw);
        if (rut.length < 2) return rutRaw;
        const dv = rut.slice(-1).toUpperCase();
        const num = rut.slice(0, -1);
        const conPuntos = num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return conPuntos + '-' + dv;
    }

    function validarRUT(rutRaw) {
        const rut = normalizarRutInput(rutRaw);
        if (rut.length < 8 || rut.length > 9) return false;
        
        const dv = rut.slice(-1).toUpperCase();
        const num = rut.slice(0, -1);
        
        if (!/^\d+$/.test(num)) return false;
        
        let suma = 0;
        let mult = 2;
        
        for (let i = num.length - 1; i >= 0; i--) {
            suma += parseInt(num[i], 10) * mult;
            mult = (mult === 7) ? 2 : mult + 1;
        }
        
        const resto = suma % 11;
        let dvCalc = 11 - resto;
        
        if (dvCalc === 11) dvCalc = '0';
        else if (dvCalc === 10) dvCalc = 'K';
        else dvCalc = String(dvCalc);
        
        return dv === dvCalc;
    }

    function validarPatente(val) {
        const p = (val || '').toUpperCase().replace(/[^A-Z0-9]/g, '');
        const nuevo = /^[A-Z]{4}[0-9]{2}$/;  // ABCD12
        const antiguo = /^[A-Z]{2}[0-9]{4}$/; // AB1234
        return nuevo.test(p) || antiguo.test(p);
    }

    function formatearTelefonoCL(input) {
        let n = (input || '').replace(/[^0-9]/g, '');
        if (n.startsWith('56')) n = n.slice(2);
        
        if (n.length === 9 && n.startsWith('9')) {
            return '+56 9 ' + n.slice(1,5) + ' ' + n.slice(5);
        }
        if (n.length === 9 && n.startsWith('2')) {
            return '+56 2 ' + n.slice(1,5) + ' ' + n.slice(5);
        }
        if (n.length === 8) {
            return '+56 9 ' + n.slice(0,4) + ' ' + n.slice(4);
        }
        return input;
    }

    // ---------- Event Handlers ----------
    
    // RUT - Formateo en tiempo real
    $(document).on('input', '#inspecciones_rut', function() {
        console.log('Formateando RUT...');
        const cursorPos = this.selectionStart;
        const valorAnterior = $(this).val();
        const valorFormateado = formatearRutVisual(valorAnterior);
        
        $(this).val(valorFormateado);
        
        const nuevaPos = Math.min(cursorPos + (valorFormateado.length - valorAnterior.length), valorFormateado.length);
        this.setSelectionRange(nuevaPos, nuevaPos);
    });

    // RUT - Validación al perder foco
    $(document).on('blur', '#inspecciones_rut', function() {
        const $input = $(this);
        const rut = $input.val();
        
        $input.removeClass('is-invalid is-valid');
        $input.siblings('.invalid-feedback').remove();
        
        if (rut && !validarRUT(rut)) {
            $input.addClass('is-invalid');
            $input.after('<div class="invalid-feedback">RUT inválido</div>');
        } else if (rut) {
            $input.addClass('is-valid');
        }
    });

    // Patente - Formateo y validación
    $(document).on('input', '#patente', function() {
        let valor = $(this).val().toUpperCase().replace(/[^A-Z0-9-]/g, '');
        if (valor.length > 7) valor = valor.substring(0, 7);
        $(this).val(valor);
    });

    $(document).on('blur', '#patente', function() {
        const $input = $(this);
        const patente = $input.val();
        
        $input.removeClass('is-invalid is-valid');
        $input.siblings('.invalid-feedback').remove();
        
        if (patente && !validarPatente(patente)) {
            $input.addClass('is-invalid');
            $input.after('<div class="invalid-feedback">Formato inválido (ej: ABC123 o ABCD12)</div>');
        } else if (patente) {
            $input.addClass('is-valid');
        }
    });

    // Teléfonos - Formateo
    $(document).on('blur', '#celular, #telefono', function() {
        $(this).val(formatearTelefonoCL($(this).val()));
    });

    // ---------- Preview en tiempo real ----------
    function updatePreview() {
        $('#preview-asegurado').text($('#asegurado').val() || '-');
        $('#preview-rut').text($('#inspecciones_rut').val() || '-');
        $('#preview-direccion').text($('#inspecciones_direccion').val() || '-');
        $('#preview-celular').text($('#celular').val() || '-');
        $('#preview-patente').text($('#patente').val() || '-');

        const marca = $('#marca').val();
        const modelo = $('#modelo').val();
        $('#preview-vehiculo').text((marca && modelo) ? (marca + ' ' + modelo) : '-');

        const ciaText = $('#cia_id option:selected').text();
        $('#preview-compania').text(ciaText && ciaText !== 'Seleccione una compañía' ? ciaText : '-');

        $('#preview-poliza').text($('#n_poliza').val() || '-');
    }
    
    $(document).on('input change', 'input, select', updatePreview);
    updatePreview();

    // ---------- Validación del formulario ----------
    $('#inspeccionForm').on('submit', function(e) {
        console.log('Validando formulario...');
        const errores = [];

        // Limpiar validaciones previas
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        // Validar RUT
        const rutValue = $('#inspecciones_rut').val();
        if (!rutValue || !validarRUT(rutValue)) {
            errores.push('El RUT ingresado no es válido.');
            $('#inspecciones_rut').addClass('is-invalid');
        }

        // Validar patente
        const patenteValue = $('#patente').val();
        if (!patenteValue || !validarPatente(patenteValue)) {
            errores.push('La patente debe tener formato AB1234 o ABCD12.');
            $('#patente').addClass('is-invalid');
        }

        // Validar campos obligatorios
        const obligatorios = [
            ['#asegurado', 'Nombre del asegurado'],
            ['#inspecciones_direccion', 'Dirección'],
            ['#comunas_id', 'Comuna'],
            ['#celular', 'Celular'],
            ['#marca', 'Marca del vehículo'],
            ['#modelo', 'Modelo del vehículo'],
            ['#cia_id', 'Compañía de seguros'],
            ['#n_poliza', 'Número de póliza']
        ];
        
        obligatorios.forEach(([selector, nombre]) => {
            const $el = $(selector);
            const val = ($el.is('select') ? $el.find('option:selected').val() : $el.val()) || '';
            if (!val.toString().trim()) {
                errores.push(`${nombre} es obligatorio.`);
                $el.addClass('is-invalid');
            }
        });

        if (errores.length) {
            e.preventDefault();
            
            let mensajeError = 'Se encontraron los siguientes errores:\n\n';
            errores.forEach((error, index) => {
                mensajeError += `${index + 1}. ${error}\n`;
            });
            
            alert(mensajeError);
            $('.is-invalid').first().focus();
            return false;
        }

        return confirm('¿Está seguro de que desea crear esta inspección?');
    });
});
</script>
<?= $this->endSection() ?>