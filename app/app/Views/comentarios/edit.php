 <?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Editar Comentario
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3">Editar Comentario</h1>
            <a href="<?= base_url('comentarios') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i> Modificar Comentario</h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('comentarios/update/'.$comentario['comentario_id']) ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">

                <!-- Comentario -->
                <div class="mb-3">
                    <label for="comentario_nombre" class="form-label">Comentario *</label>
                    <textarea id="comentario_nombre" name="comentario_nombre"
                              class="form-control"
                              rows="4"
                              required><?= esc(old('comentario_nombre', $comentario['comentario_nombre'])) ?></textarea>
                </div>

                <!-- Compañía -->
                <div class="mb-3">
                    <label for="cia_id" class="form-label">Compañía *</label>
                    <select id="cia_id" name="cia_id" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach($cias as $id => $nombre): ?>
                            <option value="<?= esc($id) ?>" <?= ($comentario['cia_id'] == $id ? 'selected' : '') ?>>
                                <?= esc($nombre) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- ID interno -->
                <div class="mb-3">
                    <label for="comentario_id_cia_interno" class="form-label">ID Interno</label>
                    <input type="number" id="comentario_id_cia_interno" name="comentario_id_cia_interno"
                           class="form-control"
                           value="<?= esc(old('comentario_id_cia_interno', $comentario['comentario_id_cia_interno'])) ?>">
                </div>

                <!-- Switches -->
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="comentario_devuelve"
                           name="comentario_devuelve" value="1"
                           <?= $comentario['comentario_devuelve'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="comentario_devuelve">¿Devuelve?</label>
                </div>

                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="comentario_elimina"
                           name="comentario_elimina" value="1"
                           <?= $comentario['comentario_elimina'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="comentario_elimina">¿Elimina?</label>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" id="comentario_envia_correo"
                           name="comentario_envia_correo" value="1"
                           <?= $comentario['comentario_envia_correo'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="comentario_envia_correo">Enviar correo</label>
                </div>

                <!-- Botones -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= base_url('comentarios') ?>" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
