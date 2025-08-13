<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Editar Perfil
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Editar Perfil</h1>
                    <p class="text-muted">Modifica los datos de: <strong><?= esc($perfil['perfil_nombre']) ?></strong></p>
                </div>
                <div class="btn-group">
                    <a href="<?= base_url('perfiles/show/' . $perfil['perfil_id']) ?>" class="btn btn-outline-info">
                        <i class="fas fa-eye"></i> Ver detalles
                    </a>
                    <a href="<?= base_url('perfiles') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al listado
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Se encontraron los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Editar Perfil
                    </h5>
                </div>
                
                <div class="card-body">
                    <form action="<?= base_url('perfiles/update/' . $perfil['perfil_id']) ?>" method="post" id="perfilForm">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">
                        
                        <div class="row">
                            <!-- Nombre del Perfil -->
                            <div class="col-md-6 mb-3">
                                <label for="perfil_nombre" class="form-label">
                                    <i class="fas fa-user-tag text-primary me-1"></i>
                                    Nombre del Perfil *
                                </label>
                                <input type="text" 
                                       class="form-control <?= (session('errors.perfil_nombre')) ? 'is-invalid' : '' ?>" 
                                       id="perfil_nombre" 
                                       name="perfil_nombre" 
                                       value="<?= old('perfil_nombre', $perfil['perfil_nombre']) ?>"
                                       placeholder="Ej. Inspector Senior"
                                       required>
                                <div class="invalid-feedback">
                                    <?= session('errors.perfil_nombre') ?>
                                </div>
                            </div>

                            <!-- Tipo de Perfil -->
                            <div class="col-md-6 mb-3">
                                <label for="perfil_tipo" class="form-label">
                                    <i class="fas fa-layer-group text-info me-1"></i>
                                    Tipo de Perfil *
                                </label>
                                <select class="form-select <?= (session('errors.perfil_tipo')) ? 'is-invalid' : '' ?>" 
                                        id="perfil_tipo" 
                                        name="perfil_tipo" 
                                        required>
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="compania" <?= old('perfil_tipo', $perfil['perfil_tipo']) == 'compania' ? 'selected' : '' ?>>
                                        🏢 Perfil de Compañía
                                    </option>
                                    <option value="interno" <?= old('perfil_tipo', $perfil['perfil_tipo']) == 'interno' ? 'selected' : '' ?>>
                                        🛡️ Perfil Interno
                                    </option>
                                </select>
                                <div class="invalid-feedback">
                                    <?= session('errors.perfil_tipo') ?>
                                </div>
                                <div class="form-text">
                                    <strong>Compañía:</strong> Para usuarios de empresas clientes<br>
                                    <strong>Interno:</strong> Para personal de la organización
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Nivel -->
                            <div class="col-md-6 mb-3">
                                <label for="perfil_nivel" class="form-label">
                                    <i class="fas fa-star text-warning me-1"></i>
                                    Nivel de Acceso *
                                </label>
                                <select class="form-select <?= (session('errors.perfil_nivel')) ? 'is-invalid' : '' ?>" 
                                        id="perfil_nivel" 
                                        name="perfil_nivel" 
                                        required>
                                    <option value="">Seleccionar nivel...</option>
                                    <option value="1" <?= old('perfil_nivel', $perfil['perfil_nivel']) == '1' ? 'selected' : '' ?>>
                                        ⭐ Nivel 1 - Básico
                                    </option>
                                    <option value="2" <?= old('perfil_nivel', $perfil['perfil_nivel']) == '2' ? 'selected' : '' ?>>
                                        ⭐⭐ Nivel 2 - Intermedio
                                    </option>
                                    <option value="3" <?= old('perfil_nivel', $perfil['perfil_nivel']) == '3' ? 'selected' : '' ?>>
                                        ⭐⭐⭐ Nivel 3 - Avanzado
                                    </option>
                                    <option value="4" <?= old('perfil_nivel', $perfil['perfil_nivel']) == '4' ? 'selected' : '' ?>>
                                        ⭐⭐⭐⭐ Nivel 4 - Administrador
                                    </option>
                                </select>
                                <div class="invalid-feedback">
                                    <?= session('errors.perfil_nivel') ?>
                                </div>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6 mb-3">
                                <label for="perfil_habil" class="form-label">
                                    <i class="fas fa-toggle-on text-success me-1"></i>
                                    Estado
                                </label>
                                <select class="form-select" id="perfil_habil" name="perfil_habil">
                                    <option value="1" <?= old('perfil_habil', $perfil['perfil_habil']) == '1' ? 'selected' : '' ?>>
                                        ✅ Activo
                                    </option>
                                    <option value="0" <?= old('perfil_habil', $perfil['perfil_habil']) == '0' ? 'selected' : '' ?>>
                                        ❌ Inactivo
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-4">
                            <label for="perfil_descripcion" class="form-label">
                                <i class="fas fa-align-left text-info me-1"></i>
                                Descripción
                            </label>
                            <textarea class="form-control" 
                                      id="perfil_descripcion" 
                                      name="perfil_descripcion" 
                                      rows="3"
                                      placeholder="Describe las responsabilidades y funciones de este perfil..."><?= old('perfil_descripcion', $perfil['perfil_descripcion']) ?></textarea>
                            <div class="form-text">Descripción opcional del perfil y sus responsabilidades</div>
                        </div>

                        <!-- Permisos -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-shield-alt text-warning me-1"></i>
                                Permisos del Perfil
                            </label>

                            <!-- Permisos para Compañía -->
                            <div id="permisos-compania" style="display: none;">
                                <div class="card bg-light">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">🏢 Permisos para Perfil de Compañía</h6>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-success select-all-permisos">
                                                Seleccionar todos
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning deselect-all-permisos">
                                                Deseleccionar todos
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <?php 
                                            $permisosActuales = old('permisos', $perfil['perfil_permisos'] ?? []);
                                            ?>
                                            <?php foreach ($permisosCompania as $key => $label): ?>
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" 
                                                               type="checkbox" 
                                                               name="permisos[]" 
                                                               value="<?= $key ?>" 
                                                               id="perm_comp_<?= $key ?>"
                                                               <?= (is_array($permisosActuales) && (in_array($key, $permisosActuales) || (isset($permisosActuales[$key]) && $permisosActuales[$key]))) ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="perm_comp_<?= $key ?>">
                                                            <?= esc($label) ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Permisos para Interno -->
                            <div id="permisos-interno" style="display: none;">
                                <div class="card bg-light">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">🛡️ Permisos para Perfil Interno</h6>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-success select-all-permisos">
                                                Seleccionar todos
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning deselect-all-permisos">
                                                Deseleccionar todos
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <?php foreach ($permisosInternos as $key => $label): ?>
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" 
                                                               type="checkbox" 
                                                               name="permisos[]" 
                                                               value="<?= $key ?>" 
                                                               id="perm_int_<?= $key ?>"
                                                               <?= (is_array($permisosActuales) && (in_array($key, $permisosActuales) || (isset($permisosActuales[$key]) && $permisosActuales[$key]))) ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="perm_int_<?= $key ?>">
                                                            <?= esc($label) ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-info-circle text-info me-1"></i>
                                            Información del registro
                                        </h6>
                                        <small class="text-muted">
                                            <strong>ID:</strong> <?= $perfil['perfil_id'] ?><br>
                                            <strong>Creado:</strong> <?= date('d/m/Y H:i', strtotime($perfil['created_at'])) ?><br>
                                            <?php if (!empty($perfil['updated_at'])): ?>
                                                <strong>Última modificación:</strong> <?= date('d/m/Y H:i', strtotime($perfil['updated_at'])) ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="<?= base_url('perfiles') ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <button type="button" class="btn btn-outline-warning" id="resetBtn">
                                        <i class="fas fa-undo"></i> Restaurar
                                    </button>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Actualizar Perfil
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    const originalData = {
        nombre: $('#perfil_nombre').val(),
        tipo: $('#perfil_tipo').val(),
        nivel: $('#perfil_nivel').val(),
        descripcion: $('#perfil_descripcion').val(),
        estado: $('#perfil_habil').val()
    };

    // Mostrar/ocultar permisos según el tipo seleccionado
    $('#perfil_tipo').on('change', function() {
        const tipo = $(this).val();
        
        $('#permisos-compania, #permisos-interno').hide();
        
        if (tipo === 'compania') {
            $('#permisos-compania').show();
        } else if (tipo === 'interno') {
            $('#permisos-interno').show();
        }
    });
    
    // Trigger inicial para mostrar permisos del tipo actual
    $('#perfil_tipo').trigger('change');
    
    // Detectar cambios en el formulario
    function hasChanges() {
        return (
            $('#perfil_nombre').val() !== originalData.nombre ||
            $('#perfil_tipo').val() !== originalData.tipo ||
            $('#perfil_nivel').val() !== originalData.nivel ||
            $('#perfil_descripcion').val() !== originalData.descripcion ||
            $('#perfil_habil').val() !== originalData.estado
        );
    }
    
    // Validación del formulario
    $('#perfilForm').on('submit', function(e) {
        const nombre = $('#perfil_nombre').val().trim();
        const tipo = $('#perfil_tipo').val();
        const nivel = $('#perfil_nivel').val();
        
        // Verificar si hay cambios
        if (!hasChanges()) {
            e.preventDefault();
            Swal.fire({
                icon: 'info',
                title: 'Sin cambios',
                text: 'No se han detectado cambios en los datos'
            });
            return;
        }
        
        if (nombre.length < 3) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: 'El nombre del perfil debe tener al menos 3 caracteres'
            });
            $('#perfil_nombre').focus();
            return;
        }
        
        if (!tipo) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: 'Debes seleccionar un tipo de perfil'
            });
            $('#perfil_tipo').focus();
            return;
        }
        
        if (!nivel) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: 'Debes seleccionar un nivel de acceso'
            });
            $('#perfil_nivel').focus();
            return;
        }
    });
    
    // Restaurar datos originales
    $('#resetBtn').on('click', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: '¿Restaurar datos originales?',
            text: 'Se perderán todos los cambios realizados',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, restaurar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#perfil_nombre').val(originalData.nombre);
                $('#perfil_tipo').val(originalData.tipo).trigger('change');
                $('#perfil_nivel').val(originalData.nivel);
                $('#perfil_descripcion').val(originalData.descripcion);
                $('#perfil_habil').val(originalData.estado);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Datos restaurados',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    });
    
    // Seleccionar todos los permisos
    $(document).on('click', '.select-all-permisos', function() {
        const container = $(this).closest('.card-body');
        container.find('input[type="checkbox"]').prop('checked', true);
    });
    
    // Deseleccionar todos los permisos
    $(document).on('click', '.deselect-all-permisos', function() {
        const container = $(this).closest('.card-body');
        container.find('input[type="checkbox"]').prop('checked', false);
    });
    
    // Advertencia al salir si hay cambios sin guardar
    $(window).on('beforeunload', function() {
        if (hasChanges()) {
            return 'Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?';
        }
    });
    
    // Remover advertencia al enviar formulario
    $('#perfilForm').on('submit', function() {
        $(window).off('beforeunload');
    });
    
    // Auto-focus en el primer campo
    $('#perfil_nombre').focus().select();
});
</script>
<?= $this->endSection() ?>