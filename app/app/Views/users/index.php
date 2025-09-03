<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Gestión de Usuarios
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
/* Estilos para usuarios deshabilitados */
.user-disabled {
    opacity: 0.7;
    background-color: #f8f9fa !important;
}

.user-disabled .user-name {
    text-decoration: line-through;
    color: #6c757d !important;
}

.user-disabled .badge {
    filter: grayscale(50%);
}

/* Toggle switch mejorado */
.status-toggle {
    width: 50px;
    height: 25px;
    cursor: pointer;
}

.status-toggle:checked {
    background-color: #198754;
    border-color: #198754;
}

.status-toggle:not(:checked) {
    background-color: #dc3545;
    border-color: #dc3545;
}

/* Indicador visual adicional */
.status-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 5px;
}

.status-active {
    background-color: #28a745;
    box-shadow: 0 0 5px rgba(40, 167, 69, 0.5);
}

.status-inactive {
    background-color: #dc3545;
    box-shadow: 0 0 5px rgba(220, 53, 69, 0.5);
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Gestión de Usuarios</h1>
                    <p class="text-muted">Administra los usuarios del sistema</p>
                </div>
                <?php if (!empty($canCreate) && $canCreate): ?>
                <a href="<?= base_url('users/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Usuario
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= $stats['total'] ?></h4>
                            <span>Total Usuarios</span>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= $stats['activos'] ?></h4>
                            <span>Activos</span>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= $stats['internos'] ?></h4>
                            <span>Internos</span>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shield-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= $stats['externos'] ?></h4>
                            <span>Externos</span>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                    </div>
                </div>
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
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <button class="btn btn-outline-primary active" data-filter="todos">
                                Todos <span class="badge bg-secondary ms-1"><?= count($usuarios) ?></span>
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-warning" data-filter="interno">
                                Internos <span class="badge bg-warning ms-1" id="count-interno">0</span>
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-info" data-filter="compania">
                                Compañías <span class="badge bg-info ms-1" id="count-compania">0</span>
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-success" data-filter="activo">
                                Activos <span class="badge bg-success ms-1" id="count-activo">0</span>
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-danger" data-filter="inactivo">
                                Inactivos <span class="badge bg-danger ms-1" id="count-inactivo">0</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <input type="text" class="form-control" id="searchInput" placeholder="Buscar usuarios...">
                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-users text-primary me-2"></i>
                Listado de Usuarios
            </h5>
        </div>
        
        <div class="card-body p-0">
            <?php if (empty($usuarios)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay usuarios registrados</h5>
                    <p class="text-muted">Comienza creando tu primer usuario</p>
                    <?php if (!empty($canCreate) && $canCreate): ?>
                    <a href="<?= base_url('users/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Crear Usuario
                    </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="usersTable">
                        <thead class="table-light">
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Perfil</th>
                                <th>Compañía</th>
                                <th>Último Acceso</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr data-id="<?= (int)$usuario['user_id'] ?>"
                                    data-tipo="<?= $usuario['perfil_tipo'] ?>" 
                                    data-estado="<?= $usuario['user_habil'] ? 'activo' : 'inactivo' ?>"
                                    data-search="<?= strtolower($usuario['user_nombre'] . ' ' . $usuario['user_email'] . ' ' . ($usuario['cia_nombre'] ?? '')) ?>"
                                    class="<?= !$usuario['user_habil'] ? 'user-disabled' : '' ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <!-- Indicador de estado -->
                                            <span class="status-indicator <?= $usuario['user_habil'] ? 'status-active' : 'status-inactive' ?>"></span>
                                            
                                            <!-- Avatar -->
                                            <?php if (!empty($usuario['user_avatar'])): ?>
                                                <img src="<?= base_url('uploads/avatars/' . $usuario['user_avatar']) ?>" 
                                                     alt="<?= esc($usuario['user_nombre']) ?>" 
                                                     class="rounded-circle me-3" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                     style="width: 40px; height: 40px;">
                                                    <span class="text-white fw-bold">
                                                        <?= strtoupper(substr($usuario['user_nombre'], 0, 2)) ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div>
                                                <span class="fw-medium user-name <?= !$usuario['user_habil'] ? 'text-muted' : '' ?>">
                                                    <?= esc($usuario['user_nombre']) ?>
                                                    <?php if (!$usuario['user_habil']): ?>
                                                        <small class="text-danger">
                                                            <i class="fas fa-ban ms-1"></i>
                                                        </small>
                                                    <?php endif; ?>
                                                </span>
                                                <br>
                                                <?php if (!empty($usuario['user_telefono'])): ?>
                                                    <small class="text-muted">
                                                        <i class="fas fa-phone me-1"></i>
                                                        <?= esc($usuario['user_telefono']) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="<?= !$usuario['user_habil'] ? 'text-muted' : '' ?>">
                                            <?= esc($usuario['user_email']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($usuario['perfil_tipo'] === 'interno'): ?>
                                                <span class="badge bg-warning me-2">
                                                    <i class="fas fa-shield-alt"></i>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-info me-2">
                                                    <i class="fas fa-building"></i>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <div>
                                                <span class="fw-medium"><?= esc($usuario['perfil_nombre']) ?></span>
                                                <br>
                                                <small class="text-muted">
                                                    <?php
                                                    $nivel = $usuario['perfil_nivel'];
                                                    $estrellas = str_repeat('★', $nivel);
                                                    echo $estrellas;
                                                    ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($usuario['cia_nombre'])): ?>
                                            <span class="badge bg-light text-dark">
                                                <?= esc($usuario['cia_nombre']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-shield-alt me-1"></i>
                                                Interno
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($usuario['user_ultimo_acceso'])): ?>
                                            <small class="text-muted">
                                                <?= date('d/m/Y H:i', strtotime($usuario['user_ultimo_acceso'])) ?>
                                            </small>
                                        <?php else: ?>
                                            <small class="text-muted">Nunca</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <!-- Badge de estado -->
                                            <span class="badge me-2 <?= $usuario['user_habil'] ? 'bg-success' : 'bg-danger' ?>">
                                                <i class="fas <?= $usuario['user_habil'] ? 'fa-check' : 'fa-times' ?> me-1"></i>
                                                <?= $usuario['user_habil'] ? 'Activo' : 'Inactivo' ?>
                                            </span>
                                            
                                            <!-- Toggle switch -->
                                            <?php if (!empty($canToggle) && $canToggle): ?>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input status-toggle"
                                                    type="checkbox"
                                                    <?= $usuario['user_habil'] ? 'checked' : '' ?>
                                                    data-id="<?= $usuario['user_id'] ?>"
                                                    data-name="<?= esc($usuario['user_nombre']) ?>"
                                                    title="<?= $usuario['user_habil'] ? 'Desactivar usuario' : 'Activar usuario' ?>">
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('users/show/' . $usuario['user_id']) ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if (!empty($canEdit) && $canEdit): ?>
                                            <a href="<?= base_url('users/edit/' . $usuario['user_id']) ?>" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php endif; ?>
                                            
                                            <!-- Reset password -->
                                            <?php if (!empty($canReset) && $canReset): ?>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-info reset-password-btn"
                                                    data-id="<?= $usuario['user_id'] ?>"
                                                    data-name="<?= esc($usuario['user_nombre']) ?>"
                                                    title="Resetear contraseña">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <?php endif; ?>
                                            
                                            <!-- Delete button -->
                                            <?php if (!empty($canDelete) && $canDelete): ?>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger delete-user-btn"
                                                    data-id="<?= $usuario['user_id'] ?>"
                                                    data-name="<?= esc($usuario['user_nombre']) ?>"
                                                    title="Eliminar usuario">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
 // ✅ SOLUCIÓN PARA EL ERROR CSRF EN TOGGLE USUARIOS

$(document).ready(function() {
    console.log('=== Index Users: Document ready ===');
    
    // Token CSRF global que se actualiza
    let CSRF = { 
        name: '<?= csrf_token() ?>', 
        hash: '<?= csrf_hash() ?>' 
    };
    
    // Flash messages
    const flashSuccess = `<?= addslashes(session()->getFlashdata('success') ?? '') ?>`;
    if (flashSuccess) {
        Swal.fire({
            icon: 'success',
            title: flashSuccess,
            timer: 2500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }
    
    // Highlight new user
    const newId = '<?= (int)(session()->getFlashdata('new_user_id') ?? 0) ?>';
    if (Number(newId) > 0) {
        const $row = $('tr[data-id="'+ newId +'"]');
        if ($row.length) {
            $row.addClass('table-success');
            $row[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => $row.removeClass('table-success'), 4000);
        }
    }
    
    // Contar por categorías
    let counts = {
        interno: 0,
        compania: 0,
        activo: 0,
        inactivo: 0
    };
    
    $('tr[data-tipo]').each(function() {
        const tipo = $(this).data('tipo');
        const estado = $(this).data('estado');
        
        if (tipo === 'interno') counts.interno++;
        if (tipo === 'compania') counts.compania++;
        if (estado === 'activo') counts.activo++;
        if (estado === 'inactivo') counts.inactivo++;
    });
    
    $('#count-interno').text(counts.interno);
    $('#count-compania').text(counts.compania);
    $('#count-activo').text(counts.activo);
    $('#count-inactivo').text(counts.inactivo);

    // Filtros
    $('[data-filter]').on('click', function() {
        const filter = $(this).data('filter');
        
        $('[data-filter]').removeClass('active');
        $(this).addClass('active');
        
        $('tr[data-tipo]').show();
        
        if (filter !== 'todos') {
            if (filter === 'interno' || filter === 'compania') {
                $('tr[data-tipo]').not(`[data-tipo="${filter}"]`).hide();
            } else if (filter === 'activo' || filter === 'inactivo') {
                $('tr[data-estado]').not(`[data-estado="${filter}"]`).hide();
            }
        }
        
        $('#searchInput').val('');
    });

    // Búsqueda
    $('#searchInput').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        $('tr[data-search]').each(function() {
            const searchData = $(this).data('search');
            if (searchData.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Limpiar búsqueda
    $('#clearSearch').on('click', function() {
        $('#searchInput').val('');
        $('tr[data-search]').show();
    });

    // ✅ TOGGLE STATUS ARREGLADO - Sin error CSRF
    $('.status-toggle').on('change', function() {
        const toggle = $(this);
        const id = toggle.data('id');
        const name = toggle.data('name') || 'usuario';
        const isChecked = toggle.is(':checked');
        const action = isChecked ? 'activar' : 'desactivar';
        const row = toggle.closest('tr');
        
        // Confirmación antes del cambio
        Swal.fire({
            title: `¿${action.charAt(0).toUpperCase() + action.slice(1)} usuario?`,
            text: `¿Deseas ${action} a ${name}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: isChecked ? '#28a745' : '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Sí, ${action}`,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Deshabilitar el toggle durante la petición
                toggle.prop('disabled', true);
                
                // ✅ AJAX con manejo correcto de CSRF
                $.ajax({
                    url: '<?= base_url('users/toggleStatus') ?>/' + id,
                    type: 'POST',
                    data: {
                        [CSRF.name]: CSRF.hash
                    },
                    headers: {
                        'X-CSRF-TOKEN': CSRF.hash,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .done(function(response, textStatus, xhr) {
                    // ✅ Actualizar token CSRF si viene en la respuesta
                    const newToken = xhr.getResponseHeader('X-CSRF-TOKEN');
                    if (newToken) {
                        CSRF.hash = newToken;
                        // Actualizar también cualquier meta tag CSRF si existe
                        $('meta[name="csrf-token"]').attr('content', newToken);
                    }
                    
                    if (response.success) {
                        // ✅ ACTUALIZAR VISUALMENTE LA FILA
                        updateRowStatus(row, isChecked);
                        
                        // Actualizar data-estado para filtros
                        row.attr('data-estado', isChecked ? 'activo' : 'inactivo');
                        
                        // Actualizar contadores
                        updateCounters();
                        
                        // Mostrar mensaje de éxito
                        Swal.fire({
                            icon: 'success',
                            title: 'Estado actualizado',
                            text: `Usuario ${isChecked ? 'activado' : 'desactivado'} correctamente`,
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    } else {
                        // Revertir el toggle
                        toggle.prop('checked', !isChecked);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'No se pudo cambiar el estado'
                        });
                    }
                })
                .fail(function(xhr) {
                    // ✅ Intentar actualizar token incluso en error
                    const newToken = xhr.getResponseHeader('X-CSRF-TOKEN');
                    if (newToken) {
                        CSRF.hash = newToken;
                        $('meta[name="csrf-token"]').attr('content', newToken);
                    }
                    
                    // Revertir el toggle
                    toggle.prop('checked', !isChecked);
                    
                    let errorMsg = 'Error de conexión';
                    if (xhr.status === 403) {
                        errorMsg = 'Error de permisos. Recarga la página.';
                    } else if (xhr.status === 419) {
                        errorMsg = 'Sesión expirada. Recarga la página.';
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg
                    });
                })
                .always(function() {
                    // Re-habilitar el toggle
                    toggle.prop('disabled', false);
                });
            } else {
                // Usuario canceló - revertir el toggle
                toggle.prop('checked', !isChecked);
            }
        });
    });
    
    // ✅ FUNCIÓN PARA ACTUALIZAR VISUALMENTE UNA FILA
    function updateRowStatus(row, isActive) {
        const statusIndicator = row.find('.status-indicator');
        const statusBadge = row.find('.badge:contains("Activo"), .badge:contains("Inactivo")');
        const userName = row.find('.user-name');
        const toggle = row.find('.status-toggle');
        
        if (isActive) {
            // Activar usuario
            row.removeClass('user-disabled');
            statusIndicator.removeClass('status-inactive').addClass('status-active');
            statusBadge.removeClass('bg-danger').addClass('bg-success')
                .html('<i class="fas fa-check me-1"></i>Activo');
            userName.removeClass('text-muted');
            userName.find('.fa-ban').remove();
            toggle.attr('title', 'Desactivar usuario');
        } else {
            // Desactivar usuario
            row.addClass('user-disabled');
            statusIndicator.removeClass('status-active').addClass('status-inactive');
            statusBadge.removeClass('bg-success').addClass('bg-danger')
                .html('<i class="fas fa-times me-1"></i>Inactivo');
            userName.addClass('text-muted');
            if (!userName.find('.fa-ban').length) {
                userName.append('<small class="text-danger"><i class="fas fa-ban ms-1"></i></small>');
            }
            toggle.attr('title', 'Activar usuario');
        }
    }
    
    // ✅ FUNCIÓN PARA ACTUALIZAR CONTADORES
    function updateCounters() {
        let counts = { interno: 0, compania: 0, activo: 0, inactivo: 0 };
        
        $('tr[data-tipo]').each(function() {
            const tipo = $(this).data('tipo');
            const estado = $(this).data('estado');
            
            if (tipo === 'interno') counts.interno++;
            if (tipo === 'compania') counts.compania++;
            if (estado === 'activo') counts.activo++;
            if (estado === 'inactivo') counts.inactivo++;
        });
        
        $('#count-interno').text(counts.interno);
        $('#count-compania').text(counts.compania);
        $('#count-activo').text(counts.activo);
        $('#count-inactivo').text(counts.inactivo);
    }

    // Reset password con CSRF arreglado
    $('.reset-password-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        Swal.fire({
            title: 'Resetear contraseña',
            text: `¿Generar nueva contraseña temporal para ${name}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, resetear',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('users/resetPassword') ?>/' + id,
                    type: 'POST',
                    data: {
                        [CSRF.name]: CSRF.hash
                    },
                    headers: {
                        'X-CSRF-TOKEN': CSRF.hash,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .done(function(response, textStatus, xhr) {
                    // Actualizar token CSRF
                    const newToken = xhr.getResponseHeader('X-CSRF-TOKEN');
                    if (newToken) {
                        CSRF.hash = newToken;
                    }
                    
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Contraseña Reseteada',
                            html: `
                                <div class="text-start">
                                    <p><strong>Usuario:</strong> ${response.userName}</p>
                                    <p><strong>Email:</strong> ${response.userEmail}</p>
                                    <div class="alert alert-warning mt-3">
                                        <strong>Nueva contraseña temporal:</strong>
                                        <div class="input-group mt-2">
                                            <input type="text" class="form-control" 
                                                value="${response.tempPassword}" 
                                                id="tempPasswordField" readonly>
                                            <button class="btn btn-outline-secondary" 
                                                    type="button" 
                                                    onclick="copyPassword()">
                                                <i class="fas fa-copy"></i> Copiar
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        El usuario deberá cambiar esta contraseña en su próximo inicio de sesión.
                                    </small>
                                </div>
                            `,
                            showConfirmButton: true,
                            confirmButtonText: 'Entendido',
                            width: 600
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                })
                .fail(function(xhr) {
                    const newToken = xhr.getResponseHeader('X-CSRF-TOKEN');
                    if (newToken) {
                        CSRF.hash = newToken;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al resetear la contraseña'
                    });
                });
            }
        });
    });

    // Delete user con CSRF arreglado
    $('.delete-user-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        Swal.fire({
            title: '¿Eliminar usuario?',
            text: `Se eliminará permanentemente a ${name}. Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Crear formulario dinámico para enviar DELETE
                const form = $('<form>', {
                    method: 'POST',
                    action: '<?= base_url('users/delete') ?>/' + id
                });
                
                // ✅ Usar el token CSRF actualizado
                form.append($('<input>', {
                    type: 'hidden',
                    name: CSRF.name,
                    value: CSRF.hash
                }));
                
                form.append($('<input>', {
                    type: 'hidden',
                    name: '_method',
                    value: 'DELETE'
                }));
                
                $('body').append(form);
                form.submit();
            }
        });
    });

    // Auto-hide alerts
    $('.alert').delay(5000).fadeOut();
});

// Función global para copiar contraseña
window.copyPassword = function() {
    const passwordField = document.getElementById('tempPasswordField');
    passwordField.select();
    document.execCommand('copy');
    
    Swal.fire({
        icon: 'success',
        title: 'Copiado',
        text: 'Contraseña copiada al portapapeles',
        timer: 1500,
        showConfirmButton: false,
        position: 'top-end',
        toast: true
    });
}
</script>
<?= $this->endSection() ?>