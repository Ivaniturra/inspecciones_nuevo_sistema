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
document.addEventListener('DOMContentLoaded', function () {
    const tipoSelect = document.getElementById('perfil_tipo');
    const comp = document.getElementById('permisos-compania');
    const inte = document.getElementById('permisos-interno');
    const ph   = document.getElementById('permisos-placeholder');

    function togglePermisos() {
        const t = tipoSelect.value;
        comp.style.display = (t === 'compania') ? '' : 'none';
        inte.style.display = (t === 'interno')  ? '' : 'none';
        ph.style.display   = (t === '')         ? '' : 'none';

        // Al cambiar tipo desmarca todo para evitar “permisos cruzados”
        if (t === 'compania') {
            inte.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        } else if (t === 'interno') {
            comp.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        } else {
            document.querySelectorAll('input[name="permisos[]"]').forEach(cb => cb.checked = false);
        }
    }

    tipoSelect.addEventListener('change', togglePermisos);

    // Botones seleccionar/deseleccionar todos (funcionan en ambos bloques)
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('select-all-permisos')) {
            const card = e.target.closest('.card');
            card.querySelectorAll('.card-body input[type="checkbox"]').forEach(cb => cb.checked = true);
        }
        if (e.target.classList.contains('deselect-all-permisos')) {
            const card = e.target.closest('.card');
            card.querySelectorAll('.card-body input[type="checkbox"]').forEach(cb => cb.checked = false);
        }
    });

    // Validación mínima en cliente
    const form = document.getElementById('perfilForm');
    form.addEventListener('submit', function (e) {
        const nombre = document.getElementById('perfil_nombre').value.trim();
        const tipo   = tipoSelect.value;
        const nivel  = document.getElementById('perfil_nivel').value;

        if (nombre.length < 3) { e.preventDefault(); alert('El nombre del perfil debe tener al menos 3 caracteres'); return; }
        if (!tipo)             { e.preventDefault(); alert('Debes seleccionar un tipo de perfil'); return; }
        if (!nivel)            { e.preventDefault(); alert('Debes seleccionar un nivel de acceso'); return; }
    });

    // Auto-focus
    const nombreInput = document.getElementById('perfil_nombre');
    if (nombreInput) { nombreInput.focus(); }

    // Estado inicial coherente (por si no hay JS antes)
    togglePermisos();
});
</script>
<?= $this->endSection() ?>
