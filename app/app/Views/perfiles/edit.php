 <?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Editar Perfil
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    // Normaliza permisos actuales: acepta old('permisos') (array de claves)
    // o $perfil['perfil_permisos'] (json o array asociativo key=>bool)
    $permisosMarcados = old('permisos');
    if (!is_array($permisosMarcados)) {
        $raw = $perfil['perfil_permisos'] ?? [];
        if (is_string($raw)) {
            $raw = json_decode($raw, true) ?? [];
        }
        // Convierte array asociativo key=>bool en lista de claves
        $permisosMarcados = [];
        if (is_array($raw)) {
            $isAssoc = array_keys($raw) !== range(0, count($raw) - 1);
            if ($isAssoc) {
                foreach ($raw as $k => $v) {
                    if ($v) { $permisosMarcados[] = (string)$k; }
                }
            } else {
                // ya es lista de claves
                $permisosMarcados = array_map('strval', $raw);
            }
        }
    }
    $tipoOld = old('perfil_tipo', $perfil['perfil_tipo'] ?? '');
?>
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
                    <form action="<?= base_url('perfiles/update/' . $perfil['perfil_id']) ?>" method="post" id="perfilForm" novalidate>
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">

                        <div class="row">
                            <!-- Nombre -->
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

                            <!-- Tipo -->
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
                                    <?php $nivelOld = old('perfil_nivel', (string)($perfil['perfil_nivel'] ?? '')); ?>
                                    <option value="">Seleccionar nivel...</option>
                                    <option value="1" <?= $nivelOld === '1' ? 'selected' : '' ?>>⭐ Nivel 1 - Básico</option>
                                    <option value="2" <?= $nivelOld === '2' ? 'selected' : '' ?>>⭐⭐ Nivel 2 - Intermedio</option>
                                    <option value="3" <?= $nivelOld === '3' ? 'selected' : '' ?>>⭐⭐⭐ Nivel 3 - Avanzado</option>
                                    <option value="4" <?= $nivelOld === '4' ? 'selected' : '' ?>>⭐⭐⭐⭐ Nivel 4 - Administrador</option>
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
                                <?php $habilOld = old('perfil_habil', (string)($perfil['perfil_habil'] ?? '1')); ?>
                                <select class="form-select" id="perfil_habil" name="perfil_habil">
                                    <option value="1" <?= $habilOld === '1' ? 'selected' : '' ?>>✅ Activo</option>
                                    <option value="0" <?= $habilOld === '0' ? 'selected' : '' ?>>❌ Inactivo</option>
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
                                      placeholder="Describe las responsabilidades y funciones de este perfil..."><?= old('perfil_descripcion', $perfil['perfil_descripcion'] ?? '') ?></textarea>
                            <div class="form-text">Descripción opcional del perfil y sus responsabilidades</div>
                        </div>

                        <!-- Permisos -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-shield-alt text-warning me-1"></i>
                                Permisos del Perfil
                            </label>

                            <!-- Compañía -->
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
                                                               <?= in_array((string)$key, $permisosMarcados, true) ? 'checked' : '' ?>>
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

                            <!-- Interno -->
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
                                                               <?= in_array((string)$key, $permisosMarcados, true) ? 'checked' : '' ?>>
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

                        <!-- Info del registro -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-info-circle text-info me-1"></i>
                                            Información del registro
                                        </h6>
                                        <small class="text-muted">
                                            <strong>ID:</strong> <?= (int)$perfil['perfil_id'] ?><br>
                                            <?php if (!empty($perfil['perfil_created_at'])): ?>
                                                <strong>Creado:</strong> <?= date('d/m/Y H:i', strtotime($perfil['perfil_created_at'])) ?><br>
                                            <?php endif; ?>
                                            <?php if (!empty($perfil['perfil_updated_at'])): ?>
                                                <strong>Última modificación:</strong> <?= date('d/m/Y H:i', strtotime($perfil['perfil_updated_at'])) ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= base_url('perfiles') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar Perfil
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

    function togglePermisos() {
        const t = tipoSelect.value;
        comp.style.display = (t === 'compania') ? '' : 'none';
        inte.style.display = (t === 'interno')  ? '' : 'none';

        // Evita "permisos cruzados" al cambiar tipo
        if (t === 'compania') {
            inte.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        } else if (t === 'interno') {
            comp.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        }
    }

    tipoSelect.addEventListener('change', togglePermisos);

    // Select/Deselect all
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
    if (nombreInput) { nombreInput.focus(); nombreInput.select?.(); }

    // Estado inicial coherente
    togglePermisos();
});
</script>
<?= $this->endSection() ?>