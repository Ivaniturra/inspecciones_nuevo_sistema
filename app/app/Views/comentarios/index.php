 <?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Gestión de Comentarios
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <h2>Listado de Comentarios</h2>

    <!-- Mensajes flash -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif ?>

    <!-- Filtros -->
    <form method="get" class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="cia_id" class="form-select">
                <option value="">-- Todas las compañías --</option>
                <?php foreach ($cias as $id => $nombre): ?>
                    <option value="<?= esc($id) ?>" <?= $filtros['cia_id']==$id ? 'selected' : '' ?>>
                        <?= esc($nombre) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" name="q" class="form-control" placeholder="Buscar..." value="<?= esc($filtros['q']) ?>">
        </div>
        <div class="col-md-2">
            <select name="per_page" class="form-select">
                <?php foreach ([10,20,50,100] as $pp): ?>
                    <option value="<?= $pp ?>" <?= $filtros['per_page']==$pp ? 'selected' : '' ?>>
                        <?= $pp ?> por página
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
        <div class="col-md-2">
            <a href="<?= base_url('comentarios/create') ?>" class="btn btn-success w-100">Nuevo</a>
        </div>
    </form>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:80px">ID</th>
                    <th>Compañía</th>
                    <th>Comentario</th>
                    <th style="width:120px">ID Interno</th>
                    <th style="width:280px" class="text-center">Flags</th>
                    <th style="width:200px" class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rows): ?>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><?= esc($r['comentario_id']) ?></td>
                            <td><?= esc($cias[$r['cia_id']] ?? $r['cia_id']) ?></td>
                            <td><?= esc($r['comentario_nombre']) ?></td>
                            <td><?= esc($r['comentario_id_cia_interno']) ?></td>
                            <td class="text-center">
                                <?= $r['comentario_devuelve'] ? '<span class="badge bg-info">Devuelve</span>' : '' ?>
                                <?= $r['comentario_elimina'] ? '<span class="badge bg-danger">Elimina</span>' : '' ?>
                                <?= $r['comentario_envia_correo'] ? '<span class="badge bg-success">Correo</span>' : '' ?>
                            </td>
                            <td class="text-end"> 
                                <a href="<?= base_url('comentarios/edit/'.$r['comentario_id']) ?>" class="btn btn-sm btn-warning">Editar</a>
                                <form action="<?= base_url('comentarios/delete/'.$r['comentario_id']) ?>" method="post" class="d-inline"
                                      onsubmit="return confirm('¿Seguro de eliminar este comentario?')">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No hay comentarios.</td></tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="d-flex justify-content-center">
        <?= $pager->links() ?>
    </div>
</div>
<?= $this->endSection() ?>