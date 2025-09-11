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
    
    .estado-badge {
        font-size: 0.9rem;
        padding: 0.4rem 0.8rem;
        border-radius: 50px;
    }
    
    .status-pendiente { background-color: #fff3cd; color: #856404; }
    .status-en_proceso { background-color: #d1ecf1; color: #0c5460; }
    .status-completada { background-color: #d4edda; color: #155724; }
    .status-cancelada { background-color: #f8d7da; color: #721c24; }
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
                        <span class="badge estado-badge status-<?= $inspeccion['inspecciones_estado'] ?? $inspeccion['estado'] ?>">
                            <?= ucfirst(str_replace('_', ' ', $inspeccion['inspecciones_estado'] ?? $inspeccion['estado'])) ?>
                        </span>
                    </p>
                </div>
                <div>
                    <a href="<?= base_url('corredor/show/' . $inspeccion['inspeccion_id']) ?>" class="btn btn-outline-info me-2">
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

    <!-- Formulario -->
    <form action="<?= base_url('corredor/update/' . $inspeccion['inspecciones_id']) ?>" method="post" id="editInspeccionForm">
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
                                   id="inspecciones_asegurado" 
                                   name="inspecciones_asegurado" 
                                   placeholder="Nombre completo del asegurado"
                                   value="<?= old('inspecciones_asegurado', $inspeccion['inspecciones_asegurado']) ?>"
                                   required>
                            <label for="inspecciones_asegurado" class="required">Nombre del Asegurado</label>
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
                                <?php foreach ($comunas as $comuna): ?>
                                <option value="<?= $comuna['comunas_id'] ?>" 
                                        <?= (old('comunas_id', $inspeccion['comunas_id']) == $comuna['comunas_id']) ? 'selected' : '' ?>>
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
                                           id="inspecciones_celular" 
                                           name="inspecciones_celular" 
                                           placeholder="+56 9 1234 5678"
                                           value="<?= old('inspecciones_celular', $inspeccion['inspecciones_celular']) ?>"
                                           required>
                                    <label for="inspecciones_celular" class="required">Celular</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" 
                                           class="form-control" 
                                           id="inspecciones_telefono" 
                                           name="inspecciones_telefono" 
                                           placeholder="+56 2 1234 5678"
                                           value="<?= old('inspecciones_telefono', $inspeccion['inspecciones_telefono'] ?? '') ?>">
                                    <label for="inspecciones_telefono">Teléfono (Opcional)</label>
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
                                   id="inspecciones_patente" 
                                   name="inspecciones_patente" 
                                   placeholder="ABC123 o ABCD12"
                                   value="<?= old('inspecciones_patente', $inspeccion['inspecciones_patente']) ?>"
                                   maxlength="8"
                                   required>
                            <label for="inspecciones_patente" class="required">Patente</label>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control" 
                                           id="inspecciones_marca" 
                                           name="inspecciones_marca" 
                                           placeholder="Toyota, Chevrolet, etc."
                                           value="<?= old('inspecciones_marca', $inspeccion['inspecciones_marca']) ?>"
                                           required>
                                    <label for="inspecciones_marca" class="required">Marca</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control" 
                                           id="inspecciones_modelo" 
                                           name="inspecciones_modelo" 
                                           placeholder="Corolla, Aveo, etc."
                                           value="<?= old('inspecciones_modelo', $inspeccion['inspecciones_modelo']) ?>"
                                           required>
                                    <label for="inspecciones_modelo" class="required">Modelo</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-floating">
                            <select class="form-select" id="cia_id" name="cia_id" required>
                                <option value="">Seleccione una compañía</option>
                                <?php foreach ($companias as $compania): ?>
                                <option value="<?= $compania['cia_id'] ?>" 
                                        <?= (old('cia_id', $inspeccion['cia_id']) == $compania['cia_id']) ? 'selected' : '' ?>>
                                    <?= esc($compania['cia_nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="cia_id" class="required">Compañía de Seguros</label>
                        </div>

                        <div class="form-floating">
                            <input type="text" 
                                   class="form-control" 
                                   id="inspecciones_n_poliza" 
                                   name="inspecciones_n_poliza" 
                                   placeholder="Número de póliza"
                                   value="<?= old('inspecciones_n_poliza', $inspeccion['inspecciones_n_poliza']) ?>"
                                   required>
                            <label for="inspecciones_n_poliza" class="required">Número de Póliza</label>
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

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
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

    // Auto-ocultar alertas después de 5 segundos
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
});
</script>
<?= $this->endSection() ?>