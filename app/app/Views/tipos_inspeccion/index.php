<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Gestión de Tipos de Inspección
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-car text-primary me-2"></i>
                        Gestión de Tipos de Inspección
                    </h1>
                    <p class="text-muted mb-0">Administra los tipos de inspecciones del sistema</p>
                </div>
                <a href="<?= base_url('tipos-inspeccion/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nuevo Tipo
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

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 48px; height: 48px;">
                                <i class="fas fa-car"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-primary mb-0"><?= $estadisticas['total'] ?? 0 ?></h5>
                            <small class="text-muted">Total Tipos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 48px; height: 48px;">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-success mb-0"><?= $estadisticas['activos'] ?? 0 ?></h5>
                            <small class="text-muted">Activos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 48px; height: 48px;">
                                <i class="fas fa-pause"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-warning mb-0"><?= $estadisticas['inactivos'] ?? 0 ?></h5>
                            <small class="text-muted">Inactivos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="q" class="form-label small text-muted">
                        <i class="fas fa-search me-1"></i>Buscar
                    </label>
                    <input type="text" name="q" id="q" class="form-control" 
                           placeholder="Buscar por nombre, clave o descripción..." 
                           value="<?= esc($filtros['q'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label for="estado" class="form-label small text-muted">
                        <i class="fas fa-filter me-1"></i>Estado
                    </label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="activo" <?= ($filtros['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>Activos</option>
                        <option value="inactivo" <?= ($filtros['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="per_page" class="form-label small text-muted">
                        <i class="fas fa-list me-1"></i>Por página
                    </label>
                    <select name="per_page" id="per_page" class="form-select">
                        <?php foreach ([10,20,50,100] as $pp): ?>
                            <option value="<?= $pp ?>" <?= ($filtros['per_page'] ?? 10) == $pp ? 'selected' : '' ?>>
                                <?= $pp ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="btn-group w-100" role="group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>Filtrar
                        </button>
                        <a href="<?= base_url('tipos-inspeccion') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-list text-primary me-2"></i>
                Listado de Tipos de Inspección
                <?php if (!empty($filtros['q']) || !empty($filtros['estado'])): ?>
                    <small class="text-muted">
                        (<?= is_countable($tipos) ? count($tipos) : 0 ?> resultados)
                    </small>
                <?php endif; ?>
            </h5>
        </div>
        
        <div class="card-body p-0">
            <?php if (empty($tipos)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-car fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay tipos de inspección registrados</h5>
                    <p class="text-muted">
                        <?php if (!empty($filtros['q']) || !empty($filtros['estado'])): ?>
                            No se encontraron tipos con los filtros aplicados.
                        <?php else: ?>
                            Comienza creando tu primer tipo de inspección.
                        <?php endif; ?>
                    </p>
                    <?php if (empty($filtros['q']) && empty($filtros['estado'])): ?>
                        <a href="<?= base_url('tipos-inspeccion/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Crear Tipo de Inspección
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tipo</th>
                                <th>Clave</th>
                                <th>Descripción</th>
                                <th class="text-center">Estado</th>
                                <th>Fechas</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tipos as $tipo): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <i class="fas <?= match(strtolower($tipo['tipo_inspeccion_nombre'] ?? '')) {
                                                    'liviano' => 'fa-car text-primary',
                                                    'pesado' => 'fa-truck text-warning', 
                                                    'motocicleta', 'moto' => 'fa-motorcycle text-info',
                                                    default => 'fa-car text-secondary'
                                                } ?>"></i>
                                            </div>
                                            <div>
                                                <strong><?= esc($tipo['tipo_inspeccion_nombre']) ?></strong>
                                                <br>
                                                <small class="text-muted">ID: <?= $tipo['tipo_inspeccion_id'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($tipo['tipo_inspeccion_clave'])): ?>
                                            <span class="badge bg-light text-dark"><?= esc($tipo['tipo_inspeccion_clave']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">Sin clave</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($tipo['tipo_inspeccion_descripcion'])): ?>
                                            <span><?= esc($tipo['tipo_inspeccion_descripcion']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">Sin descripción</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input status-toggle"
                                                   type="checkbox"
                                                   <?= $tipo['tipo_inspeccion_activo'] ? 'checked' : '' ?>
                                                   data-id="<?= $tipo['tipo_inspeccion_id'] ?>"
                                                   title="Cambiar estado">
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?= date('d/m/Y', strtotime($tipo['tipo_inspeccion_created_at'])) ?>
                                            <?php if (!empty($tipo['tipo_inspeccion_updated_at']) && $tipo['tipo_inspeccion_updated_at'] !== $tipo['tipo_inspeccion_created_at']): ?>
                                                <br><i class="fas fa-edit me-1"></i>
                                                <?= date('d/m/Y', strtotime($tipo['tipo_inspeccion_updated_at'])) ?>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('tipos-inspeccion/show/' . $tipo['tipo_inspeccion_id']) ?>"
                                               class="btn btn-sm btn-outline-info"
                                               data-bs-toggle="tooltip"
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('tipos-inspeccion/edit/' . $tipo['tipo_inspeccion_id']) ?>"
                                               class="btn btn-sm btn-outline-warning"
                                               data-bs-toggle="tooltip"
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="<?= base_url('tipos-inspeccion/delete/' . $tipo['tipo_inspeccion_id']) ?>" 
                                                  method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger btn-delete"
                                                        data-bs-toggle="tooltip"
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Paginación -->
    <?php if (!empty($tipos) && isset($pager)): ?>
        <div class="d-flex justify-content-center mt-4">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(function() {
    // Variable para mantener el token CSRF actualizado
    let csrfToken = '<?= csrf_hash() ?>';
    const csrfName = '<?= csrf_token() ?>';

    // Toggle estado via AJAX
    $('.status-toggle').on('change', function() {
        const $toggle = $(this);
        const id = $toggle.data('id');
        const isChecked = $toggle.is(':checked');
        const $row = $toggle.closest('tr');
        
        // Mostrar estado de carga
        $row.addClass('table-warning');
        
        // Preparar data con token CSRF
        const postData = {};
        postData[csrfName] = csrfToken;
        
        $.post('<?= base_url('tipos-inspeccion/toggleStatus') ?>/' + id, postData)
        .done(function(response, textStatus, xhr) {
            $row.removeClass('table-warning');
            
            // Actualizar el token CSRF
            const newToken = xhr.getResponseHeader('X-CSRF-TOKEN');
            if (newToken) {
                csrfToken = newToken;
                $('input[name="' + csrfName + '"]').val(newToken);
            }
            
            if (response && response.success) {
                updateStats(response.newStatus, isChecked);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Estado actualizado',
                    text: response.message || 'El estado se actualizó correctamente',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                $toggle.prop('checked', !isChecked);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo actualizar el estado'
                });
            }
        })
        .fail(function(xhr) {
            $row.removeClass('table-warning');
            $toggle.prop('checked', !isChecked);
            
            let errorMsg = 'Error de conexión con el servidor';
            if (xhr.status === 403) {
                errorMsg = 'Token de seguridad expirado. Recarga la página.';
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMsg
            });
        });
    });

    // Actualizar estadísticas
    function updateStats(newStatus, wasChecked) {
        const $activosCard = $('.bg-success').siblings('div').find('h5');
        const $inactivosCard = $('.bg-warning').siblings('div').find('h5');
        
        if (newStatus === 1 && !wasChecked) {
            const activos = parseInt($activosCard.text()) + 1;
            const inactivos = parseInt($inactivosCard.text()) - 1;
            $activosCard.text(activos);
            $inactivosCard.text(Math.max(0, inactivos));
        } else if (newStatus === 0 && wasChecked) {
            const activos = parseInt($activosCard.text()) - 1;
            const inactivos = parseInt($inactivosCard.text()) + 1;
            $activosCard.text(Math.max(0, activos));
            $inactivosCard.text(inactivos);
        }
    }

    // Confirmación de eliminación
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const $row = $(this).closest('tr');
        const tipoName = $row.find('strong').first().text().trim();

        Swal.fire({
            title: 'Confirmar eliminación',
            html: `
                <div class="text-center">
                    <i class="fas fa-car fa-3x text-danger mb-3"></i>
                    <p>¿Eliminar el tipo de inspección:</p>
                    <strong>"${tipoName}"</strong>
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
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                form.off('click').submit();
            }
        });
    });

    // Auto-hide alerts
    $('.alert-dismissible').delay(5000).slideUp();
    
    // Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
<?= $this->endSection() ?>