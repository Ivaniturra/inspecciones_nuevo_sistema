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
                                           <!-- <form method="post" action="<?= base_url('perfiles/delete/' . $perfil['perfil_id']) ?>" style="display: inline;">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Estás seguro de eliminar este perfil?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>-->
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
    // Variable para mantener el token CSRF actualizado
    let csrfToken = '<?= csrf_hash() ?>';
    const csrfName = '<?= csrf_token() ?>';

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

    // Toggle status via AJAX con manejo correcto de CSRF
    $('.status-toggle').on('change', function() {
        const $toggle = $(this);
        const id = $toggle.data('id');
        const isChecked = $toggle.is(':checked');
        const $row = $toggle.closest('tr');
        
        // Mostrar estado de carga
        $row.addClass('table-warning');
        
        // Preparar data con token CSRF actualizado
        const postData = {};
        postData[csrfName] = csrfToken;
        
        $.post('<?= base_url('perfiles/toggleStatus') ?>/' + id, postData)
        .done(function(response, textStatus, xhr) {
            $row.removeClass('table-warning');
            
            // CRÍTICO: Actualizar el token CSRF para la siguiente petición
            const newToken = xhr.getResponseHeader('X-CSRF-TOKEN');
            if (newToken) {
                csrfToken = newToken;
                // También actualizar el token en cualquier input hidden del DOM si existe
                $('input[name="' + csrfName + '"]').val(newToken);
            }
            
            if (response && response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Estado actualizado',
                    text: response.message || 'El estado del perfil se actualizó correctamente',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                $toggle.prop('checked', !isChecked); // Revertir
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo actualizar el estado del perfil'
                });
            }
        })
        .fail(function(xhr) {
            $row.removeClass('table-warning');
            $toggle.prop('checked', !isChecked); // Revertir
            
            let errorMsg = 'Error de conexión con el servidor';
            if (xhr.status === 403) {
                errorMsg = 'Token de seguridad expirado. Por favor, recarga la página.';
            } else if (xhr.status === 404) {
                errorMsg = 'La funcionalidad de cambio de estado no está disponible.';
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMsg
            });
        });
    });

    // Mejorar confirmación de eliminación de perfiles
    $('form[action*="/delete/"]').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const perfilRow = form.closest('tr');
        const perfilName = perfilRow.find('.fw-medium').text().trim();
        
        Swal.fire({
            title: '¿Eliminar perfil?',
            html: `
                <div class="text-center">
                    <i class="fas fa-user-tag fa-3x text-danger mb-3"></i>
                    <p>¿Estás seguro de que deseas eliminar el perfil:</p>
                    <strong>"${perfilName}"</strong>
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Esta acción no se puede deshacer.
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Eliminando...',
                    text: 'Por favor espera',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                
                form.off('submit').submit(); // Evitar bucle infinito
            }
        });
    });

    // Auto-hide alerts con mejor timing
    $('.alert-dismissible').delay(5000).slideUp();
    
    // Tooltips para botones de acción
    $('[title]').tooltip();
});
</script>
<?= $this->endSection() ?>