<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Gestión de Perfiles
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Gestión de Perfiles</h1>
                    <p class="text-muted">Administra los perfiles de usuario del sistema</p>
                </div>
                <a href="<?= base_url('perfiles/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Perfil
                </a>
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

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-auto">
                            <button class="btn btn-outline-primary active" data-filter="todos">
                                Todos <span class="badge bg-secondary ms-1"><?= count($perfiles) ?></span>
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-info" data-filter="compania">
                                Compañía <span class="badge bg-info ms-1" id="count-compania">0</span>
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-warning" data-filter="interno">
                                Internos <span class="badge bg-warning ms-1" id="count-interno">0</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-user-tag text-primary me-2"></i>
                Listado de Perfiles
            </h5>
        </div>
        
        <div class="card-body p-0">
            <?php if (empty($perfiles)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-user-tag fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay perfiles registrados</h5>
                    <p class="text-muted">Comienza creando tu primer perfil</p>
                    <a href="<?= base_url('perfiles/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Crear Perfil
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="perfilesTable">
                        <thead class="table-light">
                            <tr>
                                <th>Perfil</th>
                                <th>Tipo</th>
                                <th>Nivel</th>
                                <th>Usuarios</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($perfiles as $perfil): ?>
                                <tr data-tipo="<?= $perfil['perfil_tipo'] ?>">
                                    <td>
                                        <div>
                                            <span class="fw-medium"><?= esc($perfil['perfil_nombre']) ?></span>
                                            <br>
                                            <?php if (!empty($perfil['perfil_descripcion'])): ?>
                                                <small class="text-muted"><?= esc($perfil['perfil_descripcion']) ?></small>
                                            <?php else: ?>
                                                <small class="text-muted">Sin descripción</small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($perfil['perfil_tipo'] === 'compania'): ?>
                                            <span class="badge bg-info">
                                                <i class="fas fa-building me-1"></i>
                                                Compañía
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">
                                                <i class="fas fa-shield-alt me-1"></i>
                                                Interno
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php
                                            $nivel = $perfil['perfil_nivel'];
                                            $estrellas = str_repeat('★', $nivel) . str_repeat('☆', 4 - $nivel);
                                            $color = $nivel <= 2 ? 'text-warning' : ($nivel == 3 ? 'text-info' : 'text-danger');
                                            ?>
                                            <span class="<?= $color ?>"><?= $estrellas ?></span>
                                            <small class="text-muted ms-2">Nivel <?= $nivel ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary rounded-pill">
                                            <?= $perfil['total_usuarios'] ?? 0 ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" 
                                                   type="checkbox" 
                                                   <?= $perfil['perfil_habil'] ? 'checked' : '' ?>
                                                   data-id="<?= $perfil['perfil_id'] ?>"
                                                   title="Cambiar estado">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('perfiles/show/' . $perfil['perfil_id']) ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('perfiles/edit/' . $perfil['perfil_id']) ?>" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="post" action="<?= base_url('perfiles/delete/' . $perfil['perfil_id']) ?>" style="display: inline;">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Estás seguro de eliminar este perfil?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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
$(document).ready(function() {
    // Contar por tipo
    let countCompania = 0;
    let countInterno = 0;
    
    $('tr[data-tipo]').each(function() {
        if ($(this).data('tipo') === 'compania') {
            countCompania++;
        } else if ($(this).data('tipo') === 'interno') {
            countInterno++;
        }
    });
    
    $('#count-compania').text(countCompania);
    $('#count-interno').text(countInterno);

    // Filtros
    $('[data-filter]').on('click', function() {
        const filter = $(this).data('filter');
        
        // Actualizar botones activos
        $('[data-filter]').removeClass('active');
        $(this).addClass('active');
        
        // Filtrar filas
        $('tr[data-tipo]').show();
        
        if (filter !== 'todos') {
            $('tr[data-tipo]').not(`[data-tipo="${filter}"]`).hide();
        }
    });

    // Toggle status via AJAX
    $('.status-toggle').on('change', function() {
        const toggle = $(this);
        const id = toggle.data('id');
        const isChecked = toggle.is(':checked');
        
        $.post('<?= base_url('perfiles/toggleStatus') ?>/' + id, {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        })
        .done(function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Estado actualizado',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                toggle.prop('checked', !isChecked); // Revertir
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        })
        .fail(function() {
            toggle.prop('checked', !isChecked); // Revertir
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error de conexión'
            });
        });
    });

    // Auto-hide alerts
    $('.alert').delay(5000).fadeOut();
});
</script>
<?= $this->endSection() ?>