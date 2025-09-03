<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Editar Comentario
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Editar Comentario</h1>
                    <p class="text-muted">Modifica los datos del comentario ID: <strong><?= esc($comentario['comentario_id']) ?></strong></p>
                </div>
                <div class="btn-group">
                    <a href="<?= base_url('comentarios') ?>" class="btn btn-outline-secondary">
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
                        Editar Comentario
                    </h5>
                </div>

                <div class="card-body">
                    <form action="<?= base_url('comentarios/update/' . $comentario['comentario_id']) ?>" method="post" id="comentarioForm" novalidate>
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">

                        <!-- Comentario -->
                        <div class="mb-3">
                            <label for="comentario_nombre" class="form-label">
                                <i class="fas fa-comment-dots text-primary me-1"></i>
                                Comentario *
                            </label>
                            <textarea
                                class="form-control <?= session('errors.comentario_nombre') ? 'is-invalid' : '' ?>"
                                id="comentario_nombre"
                                name="comentario_nombre"
                                rows="4"
                                placeholder="Escribe el comentario..."
                                required><?= esc(old('comentario_nombre', $comentario['comentario_nombre'])) ?></textarea>
                            <div class="invalid-feedback">
                                <?= esc(session('errors.comentario_nombre')) ?>
                            </div>
                            <div class="form-text">Mínimo 2 caracteres, máximo 2000.</div>
                        </div>

                        <div class="row">
                            <!-- Compañía -->
                            <div class="col-md-6 mb-3">
                                <label for="cia_id" class="form-label">
                                    <i class="fas fa-building text-info me-1"></i>
                                    Compañía *
                                </label>
                                <select
                                    class="form-select <?= session('errors.cia_id') ? 'is-invalid' : '' ?>"
                                    id="cia_id" name="cia_id" required>
                                    <option value="">Seleccionar compañía...</option>
                                    <?php foreach (($cias ?? []) as $id => $nombre): ?>
                                        <option value="<?= esc($id) ?>" <?= (string)old('cia_id', $comentario['cia_id']) === (string)$id ? 'selected' : '' ?>>
                                            <?= esc($nombre) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= esc(session('errors.cia_id')) ?>
                                </div>
                            </div>

                            <!-- Perfil mejorado -->
                            <div class="col-md-6 mb-3">
                                <label for="perfil_id" class="form-label">
                                    <i class="fas fa-user-tag text-success me-1"></i>
                                    Perfil (opcional)
                                </label>
                                <select
                                    class="form-select <?= session('errors.perfil_id') ? 'is-invalid' : '' ?>"
                                    id="perfil_id" name="perfil_id">
                                    <option value="">Todos los perfiles...</option>
                                    <optgroup label="🛡️ Perfiles Internos">
                                        <?php foreach (($perfiles ?? []) as $id => $nombre): ?>
                                            <?php if (strpos($nombre, 'Interno') !== false || strpos($nombre, 'Admin') !== false): ?>
                                                <option value="<?= esc($id) ?>" <?= (string)old('perfil_id', $comentario['perfil_id']) === (string)$id ? 'selected' : '' ?>>
                                                    <?= esc($nombre) ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </optgroup>
                                    <optgroup label="🏢 Perfiles de Compañía">
                                        <?php foreach (($perfiles ?? []) as $id => $nombre): ?>
                                            <?php if (strpos($nombre, 'Interno') === false && strpos($nombre, 'Admin') === false): ?>
                                                <option value="<?= esc($id) ?>" <?= (string)old('perfil_id', $comentario['perfil_id']) === (string)$id ? 'selected' : '' ?>>
                                                    <?= esc($nombre) ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                                <div class="invalid-feedback">
                                    <?= esc(session('errors.perfil_id')) ?>
                                </div>
                                <div class="form-text">Si no seleccionas un perfil, el comentario será visible para todos.</div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- ID interno -->
                            <div class="col-md-6 mb-3">
                                <label for="comentario_id_cia_interno" class="form-label">
                                    <i class="fas fa-hashtag text-secondary me-1"></i>
                                    ID Interno (opcional)
                                </label>
                                <input type="number"
                                    class="form-control <?= session('errors.comentario_id_cia_interno') ? 'is-invalid' : '' ?>"
                                    id="comentario_id_cia_interno"
                                    name="comentario_id_cia_interno"
                                    value="<?= esc(old('comentario_id_cia_interno', $comentario['comentario_id_cia_interno'])) ?>"
                                    placeholder="Ej. 12345">
                                <div class="invalid-feedback">
                                    <?= esc(session('errors.comentario_id_cia_interno')) ?>
                                </div>
                                <div class="form-text">Relaciona este comentario con un registro interno de la compañía.</div>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6 mb-3">
                                <label for="comentario_habil" class="form-label">
                                    <i class="fas fa-toggle-on text-success me-1"></i>
                                    Estado
                                </label>
                                <select class="form-select" id="comentario_habil" name="comentario_habil">
                                    <option value="1" <?= old('comentario_habil', $comentario['comentario_habil']) == '1' ? 'selected' : '' ?>>
                                        ✅ Activo (visible para los usuarios)
                                    </option>
                                    <option value="0" <?= old('comentario_habil', $comentario['comentario_habil']) == '0' ? 'selected' : '' ?>>
                                        ❌ Inactivo (oculto para los usuarios)
                                    </option>
                                </select>
                                <div class="form-text">Estado actual del comentario.</div>
                            </div>
                        </div>

                        <!-- Flags -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-cogs me-1 text-secondary"></i>
                                    Configuración de Acciones
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label d-block">
                                            <i class="fas fa-undo text-warning me-1"></i>
                                            ¿Devuelve?
                                        </label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                   id="comentario_devuelve" name="comentario_devuelve"
                                                   value="1" <?= old('comentario_devuelve', $comentario['comentario_devuelve']) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="comentario_devuelve">
                                                Requiere devolución
                                            </label>
                                        </div>
                                        <small class="text-muted">Marca si este comentario requiere que se devuelva algo.</small>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label d-block">
                                            <i class="fas fa-trash-alt text-danger me-1"></i>
                                            ¿Elimina?
                                        </label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                   id="comentario_elimina" name="comentario_elimina"
                                                   value="1" <?= old('comentario_elimina', $comentario['comentario_elimina']) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="comentario_elimina">
                                                Sugiere eliminación
                                            </label>
                                        </div>
                                        <small class="text-muted">Marca si este comentario sugiere eliminar algo.</small>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label d-block">
                                            <i class="fas fa-envelope text-success me-1"></i>
                                            Enviar correo
                                        </label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                   id="comentario_envia_correo" name="comentario_envia_correo"
                                                   value="1" <?= old('comentario_envia_correo', $comentario['comentario_envia_correo']) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="comentario_envia_correo">
                                                Notificar por email
                                            </label>
                                        </div>
                                        <small class="text-muted">Envía notificación por correo al guardar.</small>
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
                                            <strong>ID:</strong> <?= (int)$comentario['comentario_id'] ?><br>
                                            <strong>Estado actual:</strong> 
                                            <span class="badge <?= !empty($comentario['comentario_habil']) ? 'bg-success' : 'bg-danger' ?>">
                                                <?= !empty($comentario['comentario_habil']) ? 'Activo' : 'Inactivo' ?>
                                            </span><br>
                                            <?php if (!empty($comentario['cia_nombre'])): ?>
                                                <strong>Compañía:</strong> <?= esc($comentario['cia_nombre']) ?><br>
                                            <?php endif; ?>
                                            <?php if (!empty($comentario['perfil_nombre'])): ?>
                                                <strong>Perfil:</strong> <?= esc($comentario['perfil_nombre']) ?><br>
                                            <?php endif; ?>
                                            <?php if (!empty($comentario['comentario_created_at'])): ?>
                                                <strong>Creado:</strong> <?= date('d/m/Y H:i', strtotime($comentario['comentario_created_at'])) ?><br>
                                            <?php endif; ?>
                                            <?php if (!empty($comentario['comentario_updated_at'])): ?>
                                                <strong>Última modificación:</strong> <?= date('d/m/Y H:i', strtotime($comentario['comentario_updated_at'])) ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= base_url('comentarios') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar Comentario
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
    const form = document.getElementById('comentarioForm');

    // Validación mínima en cliente
    form.addEventListener('submit', function (e) {
        const texto = document.getElementById('comentario_nombre').value.trim();
        const cia   = document.getElementById('cia_id').value;

        if (texto.length < 2) { 
            e.preventDefault(); 
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: 'El comentario debe tener al menos 2 caracteres.'
            });
            return; 
        }
        if (!cia) { 
            e.preventDefault(); 
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: 'Debes seleccionar una compañía.'
            });
            return; 
        }
    });

    // Autofocus en el textarea
    const textarea = document.getElementById('comentario_nombre');
    if (textarea) { 
        textarea.focus(); 
        textarea.setSelectionRange(textarea.value.length, textarea.value.length);
    }
});
</script>
<?= $this->endSection() ?>