<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Gestión de Usuarios
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
                <a href="<?= base_url('users/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Usuario
                </a>
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
                    <a href="<?= base_url('users/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Crear Usuario
                    </a>
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
                                    data-search="<?= strtolower($usuario['user_nombre'] . ' ' . $usuario['user_email'] . ' ' . ($usuario['cia_nombre'] ?? '')) ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
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
                                                <span class="fw-medium"><?= esc($usuario['user_nombre']) ?></span>
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
                                        <span><?= esc($usuario['user_email']) ?></span>
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
                                        <?php if (!empty($canToggle) && $canToggle): ?>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle"
                                                type="checkbox"
                                                <?= $usuario['user_habil'] ? 'checked' : '' ?>
                                                data-id="<?= $usuario['user_id'] ?>">
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('users/show/' . $usuario['user_id']) ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('users/edit/' . $usuario['user_id']) ?>" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <!-- Reset password -->
                                            <?php if (!empty($canReset) && $canReset): ?>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-info reset-password-btn"
                                                    data-id="<?= $usuario['user_id'] ?>"
                                                    data-name="<?= esc($usuario['user_nombre']) ?>">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <?php endif; ?>
                                            <!-- Eliminar -->
                                            <?php if (!empty($canDelete) && $canDelete): ?>
                                               <!-- <form method="post" 
                                                    action="<?= base_url('users/delete/' . $usuario['user_id']) ?>" 
                                                    class="delete-user-form" 
                                                    style="display:inline;">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger delete-user-btn"
                                                            data-name="<?= esc($usuario['user_nombre']) ?>">
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
$(document).ready(function() {
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
    const newId = '<?= (int)(session()->getFlashdata('new_user_id') ?? 0) ?>';
        if (Number(newId) > 0) {
        const $row = $('tr[data-id="'+ newId +'"]');
        if ($row.length) {
            $row.addClass('table-success'); // highlight Bootstrap
            $row[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => $row.removeClass('table-success'), 4000);
        }
    }
    console.log('=== Index Users: Document ready ===');
    
    // Debug: verificar si existen los botones
    console.log('Botones reset encontrados:', $('.reset-password-btn').length);
    $('.reset-password-btn').each(function(index) {
        console.log('Botón', index, '- ID:', $(this).data('id'), '- Name:', $(this).data('name'));
    });
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
        
        // Actualizar botones activos
        $('[data-filter]').removeClass('active');
        $(this).addClass('active');
        
        // Filtrar filas
        $('tr[data-tipo]').show();
        
        if (filter !== 'todos') {
            if (filter === 'interno' || filter === 'compania') {
                $('tr[data-tipo]').not(`[data-tipo="${filter}"]`).hide();
            } else if (filter === 'activo' || filter === 'inactivo') {
                $('tr[data-estado]').not(`[data-estado="${filter}"]`).hide();
            }
        }
        
        // Limpiar búsqueda
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

    // Toggle status via AJAX
    $('.status-toggle').on('change', function() {
        const toggle = $(this);
        const id = toggle.data('id');
        const isChecked = toggle.is(':checked');
        
        $.post('<?= base_url('users/toggleStatus') ?>/' + id, {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        })
        .done(function(response) {
            if (response.success) {
                // Actualizar data-estado
                const row = toggle.closest('tr');
                row.attr('data-estado', isChecked ? 'activo' : 'inactivo');
                
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
    $('.delete-user-btn').on('click', function() {  
        const form = this.closest('form');
        const userName = this.dataset.name || 'este usuario';

        Swal.fire({
            title: '¿Eliminar usuario?',
            text: `Se eliminará ${userName}. Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        }); 
    }); 
    // Reset password
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
                $.post('<?= base_url('users/resetPassword') ?>/' + id, {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                })
                .done(function(response) {
                    if (response.success) {
                        // Mostrar la contraseña temporal
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
                .fail(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al resetear la contraseña'
                    });
                });
            }
        });
    });

    // Función para copiar contraseña
    function copyPassword() {
        const passwordField = document.getElementById('tempPasswordField');
        passwordField.select();
        document.execCommand('copy');
        
        // Feedback visual
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

    // Auto-hide alerts
    $('.alert').delay(5000).fadeOut();
});
</script>
<?= $this->endSection() ?>