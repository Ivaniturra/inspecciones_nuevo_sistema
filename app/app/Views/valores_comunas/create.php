<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'Nuevo Valor por Comuna') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-plus-circle text-success me-2"></i>
                        Nuevo Valor por Comuna
                    </h1>
                    <p class="text-muted mb-0">Crear un nuevo valor para una compañía en una comuna específica</p>
                </div>
                <div>
                    <a href="<?= base_url('valores-comunas') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Valores
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($validation && $validation->getErrors()): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Errores de validación:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($validation->getErrors() as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Formulario -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-form me-2"></i>
                        Información del Valor
                    </h5>
                </div>
                
                <form action="<?= base_url('valores-comunas/store') ?>" method="post" id="valorForm">
                    <?= csrf_field() ?>
                    
                    <div class="card-body">
                        <div class="row">
                            <!-- Compañía -->
                            <div class="col-md-6 mb-4">
                                <label for="cia_id" class="form-label fw-bold">
                                    <i class="fas fa-building text-primary me-1"></i>
                                    Compañía <span class="text-danger">*</span>
                                </label>
                                <select class="form-select <?= $validation && $validation->hasError('cia_id') ? 'is-invalid' : '' ?>" 
                                        id="cia_id" 
                                        name="cia_id" 
                                        required>
                                    <option value="">Seleccionar compañía...</option>
                                    <?php if (!empty($cias)): ?>
                                        <?php foreach ($cias as $id => $nombre): ?>
                                            <option value="<?= $id ?>" <?= old('cia_id') == $id ? 'selected' : '' ?>>
                                                <?= esc($nombre) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                
                                <?php if ($validation && $validation->hasError('cia_id')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('cia_id') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Tipo de Usuario -->
                            <div class="col-md-6 mb-4">
                                <label for="tipo_usuario" class="form-label fw-bold">
                                    <i class="fas fa-user-tag text-info me-1"></i>
                                    Tipo de Usuario <span class="text-danger">*</span>
                                </label>
                                <select class="form-select <?= $validation && $validation->hasError('tipo_usuario') ? 'is-invalid' : '' ?>" 
                                        id="tipo_usuario" 
                                        name="tipo_usuario" 
                                        required>
                                    <option value="">Seleccionar tipo...</option>
                                    <?php if (!empty($tipos_usuario)): ?>
                                        <?php foreach ($tipos_usuario as $value => $label): ?>
                                            <option value="<?= $value ?>" <?= old('tipo_usuario') == $value ? 'selected' : '' ?>>
                                                <?= esc($label) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                
                                <?php if ($validation && $validation->hasError('tipo_usuario')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('tipo_usuario') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Región -->
                            <div class="col-md-6 mb-4">
                                <label for="region_id" class="form-label fw-bold">
                                    <i class="fas fa-globe-americas text-warning me-1"></i>
                                    Región <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="region_id" name="region_id" required>
                                    <option value="">Seleccionar región...</option>
                                    <?php if (!empty($regiones)): ?>
                                        <?php foreach ($regiones as $id => $nombre): ?>
                                            <option value="<?= $id ?>">
                                                <?= esc($nombre) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Comuna -->
                            <div class="col-md-6 mb-4">
                                <label for="comuna_codigo" class="form-label fw-bold">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                    Comuna <span class="text-danger">*</span>
                                </label>
                                <select class="form-select <?= $validation && $validation->hasError('comuna_codigo') ? 'is-invalid' : '' ?>" 
                                        id="comuna_codigo" 
                                        name="comuna_codigo" 
                                        required>
                                    <option value="">Primero selecciona una región...</option>
                                </select>
                                
                                <?php if ($validation && $validation->hasError('comuna_codigo')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('comuna_codigo') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Valor -->
                            <div class="col-md-6 mb-4">
                                <label for="valor" class="form-label fw-bold">
                                    <i class="fas fa-dollar-sign text-success me-1"></i>
                                    Valor <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" 
                                           class="form-control <?= $validation && $validation->hasError('valor') ? 'is-invalid' : '' ?>" 
                                           id="valor" 
                                           name="valor" 
                                           value="<?= old('valor') ?>"
                                           step="0.01"
                                           min="0"
                                           placeholder="50000.00"
                                           required>
                                </div>
                                
                                <?php if ($validation && $validation->hasError('valor')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('valor') ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Ingresa el valor sin puntos ni comas separadoras
                                </div>
                            </div>

                            <!-- Moneda -->
                            <div class="col-md-6 mb-4">
                                <label for="moneda" class="form-label fw-bold">
                                    <i class="fas fa-coins text-warning me-1"></i>
                                    Moneda
                                </label>
                                <select class="form-select" id="moneda" name="moneda">
                                    <option value="CLP" <?= old('moneda') == 'CLP' ? 'selected' : '' ?>>CLP - Peso Chileno</option>
                                    <option value="USD" <?= old('moneda') == 'USD' ? 'selected' : '' ?>>USD - Dólar Americano</option>
                                    <option value="EUR" <?= old('moneda') == 'EUR' ? 'selected' : '' ?>>EUR - Euro</option>
                                </select>
                            </div>

                            <!-- Fecha de vigencia desde -->
                            <div class="col-md-6 mb-4">
                                <label for="fecha_vigencia_desde" class="form-label fw-bold">
                                    <i class="fas fa-calendar-plus text-success me-1"></i>
                                    Vigente desde <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control <?= $validation && $validation->hasError('fecha_vigencia_desde') ? 'is-invalid' : '' ?>" 
                                       id="fecha_vigencia_desde" 
                                       name="fecha_vigencia_desde" 
                                       value="<?= old('fecha_vigencia_desde', date('Y-m-d')) ?>"
                                       required>
                                
                                <?php if ($validation && $validation->hasError('fecha_vigencia_desde')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('fecha_vigencia_desde') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Fecha de vigencia hasta -->
                            <div class="col-md-6 mb-4">
                                <label for="fecha_vigencia_hasta" class="form-label fw-bold">
                                    <i class="fas fa-calendar-times text-danger me-1"></i>
                                    Vigente hasta
                                </label>
                                <input type="date" 
                                       class="form-control <?= $validation && $validation->hasError('fecha_vigencia_hasta') ? 'is-invalid' : '' ?>" 
                                       id="fecha_vigencia_hasta" 
                                       name="fecha_vigencia_hasta" 
                                       value="<?= old('fecha_vigencia_hasta') ?>">
                                
                                <?php if ($validation && $validation->hasError('fecha_vigencia_hasta')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('fecha_vigencia_hasta') ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Dejar vacío para vigencia indefinida
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="col-12 mb-4">
                                <label for="descripcion" class="form-label fw-bold">
                                    <i class="fas fa-align-left text-info me-1"></i>
                                    Descripción
                                </label>
                                <textarea class="form-control" 
                                          id="descripcion" 
                                          name="descripcion" 
                                          rows="3"
                                          placeholder="Descripción del valor o concepto..."><?= old('descripcion') ?></textarea>
                                
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Información adicional sobre este valor (opcional)
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                <small>
                                    <i class="fas fa-asterisk text-danger me-1"></i>
                                    Los campos marcados con asterisco son obligatorios
                                </small>
                            </div>
                            <div class="btn-group">
                                <a href="<?= base_url('valores-comunas') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Crear Valor
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.card { 
    border: none; 
    border-radius: 15px; 
}
.card-header { 
    border-radius: 15px 15px 0 0 !important; 
    font-weight: 600; 
}
.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #dee2e6;
}
.form-control:focus, .form-select:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}
.btn { 
    border-radius: 8px; 
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(function () {
    // Cargar comunas cuando se selecciona una región
    $('#region_id').on('change', function () {
        const regionId = $(this).val();
        const $comunaSelect = $('#comuna_codigo');
        
        $comunaSelect.html('<option value="">Cargando comunas...</option>').prop('disabled', true);
        
        if (regionId) {
            $.ajax({
                url: '<?= base_url('valores-comunas/getComunasByRegion') ?>/' + regionId,
                type: 'GET',
                dataType: 'json',
                success: function (comunas) {
                    let options = '<option value="">Seleccionar comuna...</option>';
                    
                    if (comunas && comunas.length > 0) {
                        comunas.forEach(function (comuna) {
                            options += `<option value="${comuna.comuna_codigo}">${comuna.comuna_nombre}</option>`;
                        });
                    } else {
                        options = '<option value="">No hay comunas disponibles</option>';
                    }
                    
                    $comunaSelect.html(options).prop('disabled', false);
                },
                error: function () {
                    $comunaSelect.html('<option value="">Error al cargar comunas</option>').prop('disabled', false);
                }
            });
        } else {
            $comunaSelect.html('<option value="">Primero selecciona una región...</option>').prop('disabled', false);
        }
    });

    // Formatear el valor mientras se escribe
    $('#valor').on('input', function () {
        let value = $(this).val();
        if (value) {
            // Remover caracteres no numéricos excepto punto decimal
            value = value.replace(/[^0-9.]/g, '');
            
            // Asegurar que solo haya un punto decimal
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }
            
            $(this).val(value);
        }
    });

    // Validar fechas
    $('#fecha_vigencia_hasta').on('change', function () {
        const fechaDesde = $('#fecha_vigencia_desde').val();
        const fechaHasta = $(this).val();
        
        if (fechaDesde && fechaHasta && fechaHasta <= fechaDesde) {
            Swal.fire({
                icon: 'warning',
                title: 'Fechas incorrectas',
                text: 'La fecha de vigencia hasta debe ser posterior a la fecha desde',
            });
            $(this).val('');
        }
    });

    // Validar formulario antes de enviar
    $('#valorForm').on('submit', function (e) {
        const valor = parseFloat($('#valor').val());
        
        if (valor <= 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Valor incorrecto',
                text: 'El valor debe ser mayor que 0',
            });
            $('#valor').focus();
            return false;
        }

        // Mostrar loading
        Swal.fire({
            title: 'Creando valor...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
});
</script>
<?= $this->endSection() ?>