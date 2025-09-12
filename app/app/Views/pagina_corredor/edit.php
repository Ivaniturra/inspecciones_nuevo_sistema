<?= $this->extend('layouts/maincorredor') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
    body { background-color: #f8f9fa; }
    .container-fluid { padding: 2rem; }
    .form-floating { margin-bottom: 1rem !important; }
    .form-floating > .form-control, .form-floating > .form-select {
        height: calc(3.5rem + 2px);
        border: 1px solid #ced4da;
        border-radius: 0.5rem;
    }
    .form-floating > .form-control:focus, .form-floating > .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
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
        font-weight: 600;
    }
    .card-body { padding: 2rem; background: white; }
    .required::after { content: " *"; color: #dc3545 !important; font-weight: bold; }
    .rut-input, .patente-input { text-transform: uppercase !important; }
    .estado-badge {
        font-size: 0.9rem;
        padding: 0.4rem 0.8rem;
        border-radius: 50px;
    }
    .status-pendiente { background-color: #fff3cd; color: #856404; }
    .status-en_proceso { background-color: #d1ecf1; color: #0c5460; }
    .status-completada { background-color: #d4edda; color: #155724; }
    .status-cancelada { background-color: #f8d7da; color: #721c24; }
    .btn {
        border-radius: 10px;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        transition: all 0.3s ease;
    }
    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        box-shadow: 0 4px 6px rgba(40, 167, 69, 0.3);
    }
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(40, 167, 69, 0.4);
    }
    .btn-lg { padding: 1rem 2rem; font-size: 1.1rem; }
    .h3 { color: #495057; font-weight: 600; }
    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
    }
    .invalid-feedback {
        display: block !important;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #dc3545;
    }
    .form-text { margin-top: 0.25rem; font-size: 0.875rem; color: #6c757d; }
    .alert { border-radius: 12px; border: none; margin-bottom: 2rem; }
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
                        <i class="fas fa-edit me-2 text-primary"></i>
                        <?= esc($title) ?>
                    </h1>
                    <p class="text-muted mb-0">
                        Estado actual: 
                        <span class="badge estado-badge status-<?= $inspeccion['inspecciones_estado'] ?>">
                            <?= ucfirst(str_replace('_', ' ', $inspeccion['inspecciones_estado'])) ?>
                        </span>
                    </p>
                </div>
                <div>
                    <a href="<?= base_url('corredor/show/' . $inspeccion['inspecciones_id']) ?>" class="btn btn-outline-info me-2">
                        <i class="fas fa-eye me-2"></i>Ver Detalle
                    </a>
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


    <?php if (session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?= esc(session('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Formulario -->
    <form action="<?= base_url('corredor/update/' . $inspeccion['inspecciones_id']) ?>" method="post" id="editInspeccionForm">
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
                                   value="<?= old('asegurado', $inspeccion['inspecciones_asegurado']) ?>"
                                   required>
                            <label for="asegurado" class="required">Nombre del Asegurado</label>
                        </div>

                        <div class="form-floating">
                            <input type="text" 
                                   class="form-control rut-input" 
                                   id="inspecciones_rut" 
                                   name="inspecciones_rut" 
                                   placeholder="12345678-9"
                                   value="<?= old('inspecciones_rut', $inspeccion['inspecciones_rut']) ?>"
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
                                   value="<?= old('inspecciones_direccion', $inspeccion['inspecciones_direccion']) ?>"
                                   required>
                            <label for="inspecciones_direccion" class="required">Dirección</label>
                        </div>

                        <div class="form-floating">
                            <select class="form-select" id="comunas_id" name="comunas_id" required>
                                <option value="">Seleccione una comuna</option>
                                <?php if(isset($comunas)): ?>
                                    <?php foreach ($comunas as $comuna): ?>
                                    <option value="<?= $comuna['comunas_id'] ?>" 
                                            <?= (old('comunas_id', $inspeccion['comunas_id']) == $comuna['comunas_id']) ? 'selected' : '' ?>>
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
                                           value="<?= old('celular', $inspeccion['inspecciones_celular']) ?>"
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
                                           value="<?= old('telefono', $inspeccion['inspecciones_telefono'] ?? '') ?>">
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
                                   value="<?= old('patente', $inspeccion['inspecciones_patente']) ?>"
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
                                           value="<?= old('marca', $inspeccion['inspecciones_marca']) ?>"
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
                                           value="<?= old('modelo', $inspeccion['inspecciones_modelo']) ?>"
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
                                            <?= (old('cia_id', $inspeccion['cia_id']) == $compania['cia_id']) ? 'selected' : '' ?>>
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
                                   value="<?= old('n_poliza', $inspeccion['inspecciones_n_poliza']) ?>"
                                   required>
                            <label for="n_poliza" class="required">Número de Póliza</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Observaciones adicionales -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-sticky-note me-2"></i>
                            Observaciones
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-floating">
                            <textarea class="form-control" 
                                      id="inspecciones_observaciones" 
                                      name="inspecciones_observaciones" 
                                      placeholder="Observaciones adicionales..."
                                      style="height: 100px"><?= old('inspecciones_observaciones', $inspeccion['inspecciones_observaciones'] ?? '') ?></textarea>
                            <label for="inspecciones_observaciones">Observaciones (Opcional)</label>
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
                        <button type="submit" class="btn btn-success btn-lg me-3">
                            <i class="fas fa-save me-2"></i>
                            Guardar Cambios
                        </button>
                        <a href="<?= base_url('corredor/show/' . $inspeccion['inspecciones_id']) ?>" class="btn btn-outline-info btn-lg me-3">
                            <i class="fas fa-eye me-2"></i>
                            Ver Detalle
                        </a>
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

<?= $this->section('scripts') ?>
<script>
// Esperar a que jQuery esté disponible
(function waitForjQuery() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        setTimeout(waitForjQuery, 50);
        return;
    }
    
    $(document).ready(function() {
        console.log('Edit form script loaded');

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

        // Patente
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
                $input.after('<div class="invalid-feedback">Formato inválido</div>');
            } else if (patente) {
                $input.addClass('is-valid');
            }
        });

        // Teléfonos
        $(document).on('blur', '#celular, #telefono', function() {
            $(this).val(formatearTelefonoCL($(this).val()));
        });

        // Validación del formulario
        $('#editInspeccionForm').on('submit', function(e) {
            const errores = [];
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

            return confirm('¿Está seguro de que desea guardar los cambios realizados?');
        });

        // Auto-ocultar alertas
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    });
})();
</script>
<?= $this->endSection() ?>