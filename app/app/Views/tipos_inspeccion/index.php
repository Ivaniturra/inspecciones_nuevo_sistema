 <?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Gestión de Tipos de Vehículo
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Gestión de Tipos de Vehículo</h1>
                    <p class="text-muted">Administra los tipos de vehículos del sistema</p>
                </div>
                <a href="<?= base_url('TipoVehiculos/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Tipo
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
        <div class="col-lg-3 col-md-6 mb-3">
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

        <div class="col-lg-3 col-md-6 mb-3">
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

        <div class="col-lg-3 col-md-6 mb-3">
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
                    <label for="q" class="form-label small text-muted">Buscar</label>
                    <input type="text" name="q" id="q" class="form-control" 
                           placeholder="Buscar por nombre, clave o descripción..." 
                           value="<?= esc($filtros['q']) ?>">
                </div>
                <div class="col-md-3">
                    <label for="estado" class="form-label small text-muted">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">-- Todos los estados --</option>
                        <option value="activo" <?= $filtros['estado'] === 'activo' ? 'selected' : '' ?>>Activos</option>
                        <option value="inactivo" <?= $filtros['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="per_page" class="form-label small text-muted">Por página</label>
                    <select name="per_page" id="per_page" class="form-select">
                        <?php foreach ([10,20,50,100] as $pp): ?>
                            <option value="<?= $pp ?>" <?= $filtros['per_page'] == $pp ? 'selected' : '' ?>>
                                <?= $pp ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="btn-group w-100" role="group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="<?= base_url('TipoVehiculos') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Limpiar
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
                <i class="fas fa-car text-primary me-2"></i>
                Listado de Tipos de Vehículo
                <?php if (!empty($filtros['q']) || !empty($filtros['estado'])): ?>
                    <small class="text-muted">
                        (<?= is_countable($tipos) ? count($tipos) : 0 ?> resultados filtrados)
                    </small>
                <?php endif; ?>
            </h5>
        </div>
        
        <div class="card-body p-0">
            <?php if (empty($tipos)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-car fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay tipos de vehículo registrados</h5>
                    <p class="text-muted">
                        <?php if (!empty($filtros['q']) || !empty($filtros['estado'])): ?>
                            No se encontraron tipos con los filtros aplicados.
                        <?php else: ?>
                            Comienza creando tu primer tipo de vehículo.
                        <?php endif; ?>
                    </p>
                    <?php if (empty($filtros['q']) && empty($filtros['estado'])): ?>
                        <a href="<?= base_url('TipoVehiculos/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Tipo de Vehículo
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
                                                <i class="fas <?= match(strtolower($tipo['tipo_vehiculo_nombre'])) {
                                                    'liviano' => 'fa-car text-primary',
                                                    'pesado' => 'fa-truck text-warning', 
                                                    'motocicleta' => 'fa-motorcycle text-info',
                                                    default => 'fa-car text-secondary'
                                                } ?>"></i>
                                            </div>
                                            <div>
                                                <strong><?= esc($tipo['tipo_vehiculo_nombre']) ?></strong>
                                                <br>
                                                <small class="text-muted">ID: <?= $tipo['tipo_vehiculo_id'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($tipo['tipo_vehiculo_clave'])): ?>
                                            <span class="badge bg-light text-dark"><?= esc($tipo['tipo_vehiculo_clave']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">Sin clave</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($tipo['tipo_vehiculo_descripcion'])): ?>
                                            <span><?= esc($tipo['tipo_vehiculo_descripcion']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">Sin descripción</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input status-toggle"
                                                   type="checkbox"
                                                   <?= $tipo['tipo_vehiculo_activo'] ? 'checked' : '' ?>
                                                   data-id="<?= $tipo['tipo_vehiculo_id'] ?>"
                                                   title="Cambiar estado">
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?= date('d/m/Y', strtotime($tipo['tipo_vehiculo_created_at'])) ?>
                                            <?php if (!empty($tipo['tipo_vehiculo_updated_at']) && $tipo['tipo_vehiculo_updated_at'] !== $tipo['tipo_vehiculo_created_at']): ?>
                                                <br><i class="fas fa-edit me-1"></i>
                                                <?= date('d/m/Y', strtotime($tipo['tipo_vehiculo_updated_at'])) ?>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('TipoVehiculos/show/' . $tipo['tipo_vehiculo_id']) ?>"
                                               class="btn btn-sm btn-outline-info"
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('TipoVehiculos/edit/' . $tipo['tipo_vehiculo_id']) ?>"
                                               class="btn btn-sm btn-outline-warning"
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a> 
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
// Script corregido para app/Views/tipo_vehiculos/index.php
$(function() {
    // Variable para mantener el token CSRF actualizado
    let csrfToken = '<?= csrf_hash() ?>';
    const csrfName = '<?= csrf_token() ?>';

    // Toggle estado via AJAX con manejo correcto de CSRF
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
        
        $.post('<?= base_url('TipoVehiculos/toggleStatus') ?>/' + id, postData)
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
                // Actualizar estadísticas si es necesario
                updateStats(response.newStatus, isChecked);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Estado actualizado',
                    text: response.message || 'El estado del tipo de vehículo se actualizó correctamente',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                $toggle.prop('checked', !isChecked); // Revertir
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo actualizar el estado del tipo de vehículo'
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

    // Función para actualizar estadísticas en tiempo real
    function updateStats(newStatus, wasChecked) {
        const $statsCards = $('.card .d-flex .bg-primary, .card .d-flex .bg-success, .card .d-flex .bg-warning');
        
        if ($statsCards.length >= 3) {
            const $totalCard = $statsCards.eq(0).siblings('div').find('h5');
            const $activosCard = $statsCards.eq(1).siblings('div').find('h5');
            const $inactivosCard = $statsCards.eq(2).siblings('div').find('h5');
            
            if (newStatus === 1 && !wasChecked) {
                // Se activó un tipo
                const activos = parseInt($activosCard.text()) + 1;
                const inactivos = parseInt($inactivosCard.text()) - 1;
                $activosCard.text(activos);
                $inactivosCard.text(Math.max(0, inactivos));
            } else if (newStatus === 0 && wasChecked) {
                // Se desactivó un tipo
                const activos = parseInt($activosCard.text()) - 1;
                const inactivos = parseInt($inactivosCard.text()) + 1;
                $activosCard.text(Math.max(0, activos));
                $inactivosCard.text(inactivos);
            }
        }
    }

    // Confirmación de eliminación mejorada
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const $row = $(this).closest('tr');
        const tipoName = $row.find('strong').first().text().trim();
        const msg = $(this).data('confirm') || '¿Eliminar tipo de vehículo?';

        Swal.fire({
            title: 'Confirmar eliminación',
            html: `
                <div class="text-center">
                    <i class="fas fa-car fa-3x text-danger mb-3"></i>
                    <p>¿Estás seguro de que deseas eliminar el tipo de vehículo:</p>
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
                    text: 'Por favor espera',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                
                form.off('click').submit(); // Evitar bucle infinito
            }
        });
    });

    // Mejorar el filtrado en tiempo real
    const $searchInput = $('#q');
    let searchTimeout;
    
    $searchInput.on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val().toLowerCase().trim();
        
        searchTimeout = setTimeout(() => {
            if (query === '') {
                $('tbody tr').show();
                return;
            }
            
            $('tbody tr').each(function() {
                const $row = $(this);
                const nombre = $row.find('strong').text().toLowerCase();
                const clave = $row.find('.badge').text().toLowerCase();
                const descripcion = $row.find('td').eq(2).text().toLowerCase();
                
                const matches = nombre.includes(query) || 
                              clave.includes(query) || 
                              descripcion.includes(query);
                
                $row.toggle(matches);
            });
        }, 300);
    });

    // Auto-hide alerts con mejor timing
    $('.alert-dismissible').delay(5000).slideUp();
    
    // Tooltips para botones de acción
    $('[title]').tooltip();
    
    // Inicializar contador de resultados visibles
    function updateResultCount() {
        const visibleRows = $('tbody tr:visible').length;
        const $cardTitle = $('.card-title');
        
        if ($cardTitle.find('.result-count').length === 0) {
            $cardTitle.append(' <span class="result-count badge bg-secondary"></span>');
        }
        
        $cardTitle.find('.result-count').text(`${visibleRows} visible${visibleRows !== 1 ? 's' : ''}`);
    }
    
    // Actualizar contador al filtrar
    $searchInput.on('input', () => {
        setTimeout(updateResultCount, 350);
    });
    
    // Mejorar selects de filtro
    $('#estado, #per_page').on('change', function() {
        const $form = $(this).closest('form');
        // Auto-submit si hay búsqueda activa
        if ($('#q').val().trim() !== '') {
            $form.submit();
        }
    });
    
    // Indicador de filtros activos
    function showActiveFilters() {
        const activeFilters = [];
        
        if ($('#q').val().trim()) {
            activeFilters.push(`Búsqueda: "${$('#q').val()}"`);
        }
        
        if ($('#estado').val()) {
            const estadoText = $('#estado option:selected').text();
            activeFilters.push(`Estado: ${estadoText}`);
        }
        
        if (activeFilters.length > 0) {
            const $filtersDiv = $('.active-filters');
            if ($filtersDiv.length === 0) {
                $('.card.shadow-sm.mb-4 .card-body').append(`
                    <div class="active-filters mt-2">
                        <small class="text-muted">
                            <i class="fas fa-filter me-1"></i>
                            Filtros activos: ${activeFilters.join(' | ')}
                        </small>
                    </div>
                `);
            } else {
                $filtersDiv.find('small').html(`
                    <i class="fas fa-filter me-1"></i>
                    Filtros activos: ${activeFilters.join(' | ')}
                `);
            }
        } else {
            $('.active-filters').remove();
        }
    }
    
    // Mostrar filtros activos al cargar
    showActiveFilters();
    
    // Contador inicial
    updateResultCount();
});
</script>
<?= $this->endSection() ?>