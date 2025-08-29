<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Gestión de Corredores
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Gestión de Corredores</h1>
                    <p class="text-muted">Administra los corredores del sistema</p>
                </div>

                <?php if (function_exists('can') ? can('gestionar_corredores') : true): ?>
                <a href="<?= base_url('corredores/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Corredor
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           value="<?= esc($search) ?>" 
                           placeholder="Nombre, email o RUT...">
                </div>
                <div class="col-md-3">
                    <label for="cia_id" class="form-label">Compañía</label>
                    <select class="form-select" id="cia_id" name="cia_id">
                        <option value="">Todas las compañías</option>
                        <?php foreach ($cias as $cia): ?>
                            <option value="<?= $cia['cia_id'] ?>" <?= $ciaId == $cia['cia_id'] ? 'selected' : '' ?>>
                                <?= esc($cia['cia_display_name'] ?: $cia['cia_nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <a href="<?= base_url('corredores') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>
            </form>
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
                <i class="fas fa-user-tie text-primary me-2"></i>
                Listado de Corredores 
                <span class="badge bg-light text-dark ms-2"><?= count($corredores) ?></span>
            </h5>
        </div>

        <div class="card-body p-0">
            <?php if (empty($corredores)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay corredores registrados</h5>
                    <p class="text-muted">Comienza creando tu primer corredor</p>
                    <?php if (function_exists('can') ? can('gestionar_corredores') : true): ?>
                    <a href="<?= base_url('corredores/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Crear Corredor
                    </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Logo</th>
                                <th>Nombre/Email</th>
                                <th>RUT</th>
                                <th>Teléfono</th>
                                <th>Compañías</th>
                                <th class="text-center">Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($corredores as $corredor): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($corredor['corredor_logo'])): ?>
                                            <img src="<?= base_url('uploads/corredores/' . $corredor['corredor_logo']) ?>"
                                                 alt="<?= esc($corredor['corredor_nombre']) ?>"
                                                 class="rounded"
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-user-tie text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div>
                                            <a class="fw-medium text-decoration-none" href="<?= base_url('corredores/show/' . $corredor['corredor_id']) ?>">
                                                <?= esc($corredor['corredor_nombre']) ?>
                                            </a>
                                            <?php if (!empty($corredor['corredor_display_name'])): ?>
                                                <br><small class="text-muted"><?= esc($corredor['corredor_display_name']) ?></small>
                                            <?php endif; ?>
                                            <?php if (!empty($corredor['corredor_email'])): ?>
                                                <br><small class="text-info">
                                                    <i class="fas fa-envelope me-1"></i><?= esc($corredor['corredor_email']) ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($corredor['corredor_rut'])): ?>
                                            <span class="font-monospace"><?= esc($corredor['corredor_rut']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Sin RUT</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($corredor['corredor_telefono'])): ?>
                                            <span class="font-monospace"><?= esc($corredor['corredor_telefono']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Sin teléfono</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                             <?php
                                            // $corredor['companias'] puede venir como string "A|B|C" o como null
                                            $lista = [];
                                            if (!empty($corredor['cias'])) {
                                                $lista = is_array($corredor['cias'])
                                                        ? $corredor['cias']
                                                        : array_filter(explode('|', $corredor['cias']));
                                            } 
                                            $max = 3; // mostrar hasta 3
                                            $total = count($lista);
                                            foreach (array_slice($lista, 0, $max) as $nom): ?>
                                                <span class="badge bg-primary"><?= esc($nom) ?></span>
                                            <?php endforeach; ?>
                                            <?php if ($total > $max): ?>
                                            <span class="badge bg-secondary">+<?= $total - $max ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <!-- Estado con switch AJAX -->
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input corredor-status-toggle"
                                                   type="checkbox"
                                                   <?= $corredor['corredor_habil'] ? 'checked' : '' ?>
                                                   data-id="<?= $corredor['corredor_id'] ?>"
                                                   title="Cambiar estado">
                                        </div>
                                    </td>

                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('corredores/show/' . $corredor['corredor_id']) ?>"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <?php if (function_exists('can') ? can('gestionar_corredores') : true): ?>
                                            <a href="<?= base_url('corredores/edit/' . $corredor['corredor_id']) ?>"
                                               class="btn btn-sm btn-outline-warning"
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!--<form method="post" action="<?= base_url('corredores/delete/' . $corredor['corredor_id']) ?>" class="d-inline-block">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger btn-delete"
                                                        data-confirm="¿Estás seguro de eliminar este corredor?"
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>-->
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
$('.corredor-status-toggle').on('change', function () {
  const $toggle = $(this);
  const id = $toggle.data('id');
  const checked = $toggle.is(':checked');

  $.post('<?= base_url('corredores/toggleStatus') ?>/' + id)
    .done(function (resp) {
      if (!resp || !resp.success) {
        $toggle.prop('checked', !checked);
        Swal.fire({ icon:'error', title:'Error', text: resp?.message ?? 'No se pudo actualizar el estado' });
      } else {
        Swal.fire({ icon:'success', title:'Estado actualizado', text: resp.message, timer: 1600, showConfirmButton:false });
      }
    })
    .fail(function () {
      $toggle.prop('checked', !checked);
      Swal.fire({ icon:'error', title:'Error de conexión', text:'No se pudo conectar con el servidor' });
    });
});

    // Confirmación de eliminación
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
</script>
<?= $this->endSection() ?>