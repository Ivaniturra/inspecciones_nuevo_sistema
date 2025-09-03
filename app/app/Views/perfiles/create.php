 <?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Nuevo Perfil
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Nuevo Perfil</h1>
                    <p class="text-muted">Crea un nuevo perfil de usuario para el sistema</p>
                </div>
                <a href="<?= base_url('perfiles') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al listado
                </a>
            </div>
        </div>
    </div>

 

    <?php
        // Determina tipo preseleccionado para fallback sin JS
        $tipoOld = old('perfil_tipo');
        // Permisos marcados tras validación (array de claves)
        $permisosMarcados = old('permisos');
        $permisosMarcados = is_array($permisosMarcados) ? $permisosMarcados : [];
    ?>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-tag me-2"></i>
                        Datos del Perfil
                    </h5>
                </div>

                <div class="card-body">
                    <form action="<?= base_url('perfiles/store') ?>" method="post" id="perfilForm" novalidate>
                        <?= csrf_field() ?>

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
                                       value="<?= old('perfil_nombre') ?>"
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
                                    <option value="compania" <?= $tipoOld === 'compania' ? 'selected' : '' ?>>🏢 Perfil de Compañía</option>
                                    <option value="interno"  <?= $tipoOld === 'interno'  ? 'selected' : '' ?>>🛡️ Perfil Interno</option>
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
                                    <option value="1" <?= old('perfil_nivel') === '1' ? 'selected' : '' ?>>⭐ Nivel 1 - Básico</option>
                                    <option value="2" <?= old('perfil_nivel') === '2' ? 'selected' : '' ?>>⭐⭐ Nivel 2 - Intermedio</option>
                                    <option value="3" <?= old('perfil_nivel') === '3' ? 'selected' : '' ?>>⭐⭐⭐ Nivel 3 - Avanzado</option>
                                    <option value="4" <?= old('perfil_nivel') === '4' ? 'selected' : '' ?>>⭐⭐⭐⭐ Nivel 4 - Administrador</option>
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
                                    <option value="1" <?= old('perfil_habil', '1') === '1' ? 'selected' : '' ?>>✅ Activo</option>
                                    <option value="0" <?= old('perfil_habil') === '0' ? 'selected' : '' ?>>❌ Inactivo</option>
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
                                      placeholder="Describe las responsabilidades y funciones de este perfil..."><?= old('perfil_descripcion') ?></textarea>
                            <div class="form-text">Descripción opcional del perfil y sus responsabilidades</div>
                        </div>

                        <!-- Permisos -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-shield-alt text-warning me-1"></i>
                                Permisos del Perfil
                            </label>

                            <!-- Permisos: Compañía -->
                            <div id="permisos-compania" style="<?= $tipoOld === 'compania' ? '' : 'display:none' ?>">
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
                                            <?php foreach ($permisosCompania as $key => $label): ?>
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input"
                                                               type="checkbox"
                                                               name="permisos[]"
                                                               value="<?= $key ?>"
                                                               id="perm_comp_<?= $key ?>"
                                                               <?= in_array($key, $permisosMarcados, true) ? 'checked' : '' ?>>
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

                            <!-- Permisos: Interno -->
                            <div id="permisos-interno" style="<?= $tipoOld === 'interno' ? '' : 'display:none' ?>">
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
                                                               <?= in_array($key, $permisosMarcados, true) ? 'checked' : '' ?>>
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

                            <!-- Placeholder cuando no hay tipo -->
                            <div id="permisos-placeholder" class="text-center text-muted" style="<?= $tipoOld ? 'display:none' : '' ?>">
                                <i class="fas fa-arrow-up me-2"></i>
                                Selecciona un tipo de perfil para ver los permisos disponibles
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= base_url('perfiles') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="reset" class="btn btn-outline-warning">
                                <i class="fas fa-undo"></i> Limpiar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Perfil
                            </button>
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
// Script mejorado para validaciones en create.php y edit.php de tipo_vehiculos
document.addEventListener('DOMContentLoaded', function () {
    const nombreInput = document.getElementById('tipo_vehiculo_nombre');
    const claveInput = document.getElementById('tipo_vehiculo_clave');
    const descripcionInput = document.getElementById('tipo_vehiculo_descripcion');
    const form = document.getElementById('tipoVehiculoForm');
    
    const previewNombre = document.getElementById('preview-nombre');
    const previewClave = document.getElementById('preview-clave');
    const previewDescripcion = document.getElementById('preview-descripcion');
    const previewIcon = document.getElementById('preview-icon');

    // Función para actualizar vista previa
    function updatePreview() {
        // Actualizar nombre
        const nombre = nombreInput.value.trim() || 'Nombre del tipo';
        if (previewNombre) previewNombre.textContent = nombre;

        // Actualizar clave
        const clave = claveInput.value.trim() || 'clave';
        if (previewClave) {
            previewClave.textContent = clave;
            previewClave.style.display = clave === 'clave' ? 'none' : 'inline';
        }

        // Actualizar descripción
        const descripcion = descripcionInput.value.trim() || 'Descripción del tipo de vehículo...';
        if (previewDescripcion) previewDescripcion.textContent = descripcion;

        // Actualizar ícono según el nombre
        if (previewIcon) {
            const iconClass = getIconByName(nombre.toLowerCase());
            previewIcon.className = `fas ${iconClass} fa-2x text-primary`;
            
            // En show.php puede ser fa-3x
            if (previewIcon.classList.contains('fa-3x')) {
                previewIcon.className = `fas ${iconClass} fa-3x text-primary`;
            }
        }
    }

    // Función para obtener ícono según el nombre
    function getIconByName(name) {
        if (name.includes('liviano') || name.includes('auto') || name.includes('carro') || name.includes('sedan')) {
            return 'fa-car';
        } else if (name.includes('pesado') || name.includes('camion') || name.includes('truck') || name.includes('carga')) {
            return 'fa-truck';
        } else if (name.includes('motocicleta') || name.includes('moto') || name.includes('motor') || name.includes('scooter')) {
            return 'fa-motorcycle';
        } else if (name.includes('bus') || name.includes('autobus') || name.includes('omnibus')) {
            return 'fa-bus';
        } else if (name.includes('van') || name.includes('furgon') || name.includes('minivan')) {
            return 'fa-shuttle-van';
        } else if (name.includes('taxi') || name.includes('uber')) {
            return 'fa-taxi';
        } else {
            return 'fa-car';
        }
    }

    // Auto-generar clave desde nombre si está vacía (solo en create)
    if (nombreInput && claveInput && window.location.pathname.includes('/create')) {
        nombreInput.addEventListener('input', function() {
            if (claveInput.value.trim() === '') {
                const clave = this.value.toLowerCase()
                    .normalize('NFD') // Normalizar para eliminar acentos
                    .replace(/[\u0300-\u036f]/g, '') // Eliminar diacríticos
                    .replace(/ñ/g, 'n')
                    .replace(/[^a-z0-9]/g, '_')
                    .replace(/_+/g, '_')
                    .replace(/^_|_$/g, '')
                    .substring(0, 50); // Limitar longitud
                claveInput.value = clave;
            }
            updatePreview();
        });
    } else if (nombreInput) {
        nombreInput.addEventListener('input', updatePreview);
    }

    // Event listeners para actualizar preview
    if (claveInput) claveInput.addEventListener('input', updatePreview);
    if (descripcionInput) descripcionInput.addEventListener('input', updatePreview);

    // Validación del formulario mejorada
    if (form) {
        form.addEventListener('submit', function (e) {
            const errors = [];
            
            // Validar nombre
            const nombre = nombreInput.value.trim();
            if (nombre.length < 2) {
                errors.push('El nombre del tipo debe tener al menos 2 caracteres');
            }
            if (nombre.length > 100) {
                errors.push('El nombre no puede exceder 100 caracteres');
            }

            // Validar clave si existe
            if (claveInput && claveInput.value.trim()) {
                const clave = claveInput.value.trim();
                if (clave.length > 50) {
                    errors.push('La clave no puede exceder 50 caracteres');
                }
                if (!/^[a-z0-9_]+$/.test(clave)) {
                    errors.push('La clave solo puede contener letras minúsculas, números y guiones bajos');
                }
            }

            // Validar descripción si existe
            if (descripcionInput && descripcionInput.value.trim()) {
                const descripcion = descripcionInput.value.trim();
                if (descripcion.length > 255) {
                    errors.push('La descripción no puede exceder 255 caracteres');
                }
            }

            // Mostrar errores si los hay
            if (errors.length > 0) {
                e.preventDefault();
                
                Swal.fire({
                    icon: 'error',
                    title: 'Errores de validación',
                    html: '<ul class="text-start mb-0">' + errors.map(error => `<li>${error}</li>`).join('') + '</ul>',
                    confirmButtonText: 'Corregir'
                });
                
                // Enfocar el primer campo con error
                nombreInput.focus();
                return false;
            }

            // Si todo está bien, mostrar loading
            Swal.fire({
                title: form.action.includes('/update/') ? 'Actualizando tipo de vehículo...' : 'Creando tipo de vehículo...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    }

    // Auto-focus mejorado
    if (nombreInput) { 
        nombreInput.focus();
        // En modo edición, seleccionar todo el texto
        if (window.location.pathname.includes('/edit/')) {
            nombreInput.select();
        }
    }

    // Contador de caracteres para campos con límite
    function addCharCounter(input, maxChars, label) {
        if (!input) return;
        
        const counter = document.createElement('div');
        counter.className = 'form-text text-end small';
        counter.id = input.id + '_counter';
        
        const updateCounter = () => {
            const current = input.value.length;
            const remaining = maxChars - current;
            counter.textContent = `${current}/${maxChars} caracteres`;
            
            if (remaining < 20) {
                counter.className = 'form-text text-end small text-warning';
            } else if (remaining < 0) {
                counter.className = 'form-text text-end small text-danger';
            } else {
                counter.className = 'form-text text-end small text-muted';
            }
        };
        
        input.addEventListener('input', updateCounter);
        input.parentNode.appendChild(counter);
        updateCounter();
    }

    // Agregar contadores de caracteres
    addCharCounter(nombreInput, 100, 'nombre');
    addCharCounter(claveInput, 50, 'clave');
    addCharCounter(descripcionInput, 255, 'descripción');

    // Prevenir pérdida de datos al salir
    let formModified = false;
    
    // Detectar cambios en el formulario
    if (form) {
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('change', () => {
                formModified = true;
            });
        });
        
        // Advertir antes de salir si hay cambios no guardados
        window.addEventListener('beforeunload', (e) => {
            if (formModified && !form.classList.contains('submitted')) {
                e.preventDefault();
                e.returnValue = '¿Estás seguro de que quieres salir? Los cambios no guardados se perderán.';
                return e.returnValue;
            }
        });
        
        // Marcar como enviado cuando se submit
        form.addEventListener('submit', () => {
            form.classList.add('submitted');
        });
    }

    // Funcionalidad de reset mejorada
    const resetBtn = form && form.querySelector('button[type="
</script>
<?= $this->endSection() ?>
