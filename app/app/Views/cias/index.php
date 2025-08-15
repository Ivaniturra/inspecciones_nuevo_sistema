 <?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Gestión de Compañías
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Gestión de Compañías</h1>
                    <p class="text-muted">Administra las compañías del sistema</p>
                </div>

                <?php if (function_exists('can') ? can('gestionar_companias') : true): ?>
                <a href="<?= base_url('cias/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Compañía
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Table Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-building text-primary me-2"></i>
                Listado de Compañías
            </h5>
        </div>

        <div class="card-body p-0">
            <?php if (empty($cias)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay compañías registradas</h5>
                    <p class="text-muted">Comienza creando tu primera compañía</p>
                    <?php if (function_exists('can') ? can('gestionar_companias') : true): ?>
                    <a href="<?= base_url('cias/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Crear Compañía
                    </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Logo</th>
                                <th>Nombre</th>
                                <th>Dirección</th>
                                <th class="text-center">Usuarios</th>
                                <th class="text-center">Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cias as $cia): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($cia['cia_logo'])): ?>
                                            <img src="<?= base_url('uploads/logos/' . $cia['cia_logo']) ?>"
                                                 alt="<?= esc($cia['cia_nombre']) ?>"
                                                 class="rounded"
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-building text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div>
                                            <a class="fw-medium text-decoration-none" href="<?= base_url('cias/show/' . $cia['cia_id']) ?>">
                                                <?= esc($cia['cia_nombre']) ?>
                                            </a>
                                            <br>
                                            <small class="text-muted">ID: <?= $cia['cia_id'] ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($cia['cia_direccion'])): ?>
                                            <span><?= esc($cia['cia_direccion']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Sin dirección</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Total usuarios (si viene desde getCiasWithUserCount) -->
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark">
                                            <?= isset($cia['total_usuarios']) ? (int)$cia['total_usuarios'] : 0 ?>
                                        </span>
                                    </td>

                                    <!-- Estado con switch AJAX -->
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input cia-status-toggle"
                                                   type="checkbox"
                                                   <?= $cia['cia_habil'] ? 'checked' : '' ?>
                                                   data-id="<?= $cia['cia_id'] ?>"
                                                   title="Cambiar estado">
                                        </div>
                                    </td>

                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('cias/show/' . $cia['cia_id']) ?>"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <?php if (function_exists('can') ? can('gestionar_companias') : true): ?>
                                            <a href="<?= base_url('cias/edit/' . $cia['cia_id']) ?>"
                                               class="btn btn-sm btn-outline-warning"
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form method="post" action="<?= base_url('cias/delete/' . $cia['cia_id']) ?>" class="d-inline-block">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger btn-delete"
                                                        data-confirm="¿Estás seguro de eliminar esta compañía?"
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(function() {
    // Toggle estado compañía (AJAX)
    $('.cia-status-toggle').on('change', function() {
        const $toggle = $(this);
        const id = $toggle.data('id');
        const checked = $toggle.is(':checked');

        $.post('<?= base_url('cias/toggleStatus') ?>/' + id, {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        })
        .done(function(resp) {
            if (!resp || !resp.success) {
                $toggle.prop('checked', !checked); // revertir
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: (resp && resp.message) ? resp.message : 'No se pudo actualizar el estado'
                });
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Estado actualizado',
                    text: resp.message,
                    timer: 1600,
                    showConfirmButton: false
                });
            }
        })
        .fail(function() {
            $toggle.prop('checked', !checked); // revertir
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor'
            });
        });
    });

    // Confirmación de eliminación (aprovecha script global si ya lo tienes)
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const msg  = $(this).data('confirm') || '¿Eliminar registro?';

        Swal.fire({
            title: 'Confirmar eliminación',
            text: msg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((r) => {
            if (r.isConfirmed) form.submit();
        });
    });
});
</script>
<?= $this->endSection() ?>
