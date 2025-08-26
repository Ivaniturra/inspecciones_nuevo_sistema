<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Editar Usuario
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Editar Usuario</h1>
                    <p class="text-muted">Modifica los datos de: <strong><?= esc($usuario['user_nombre']) ?></strong></p>
                </div>
                <div class="btn-group">
                    <a href="<?= base_url('users/show/' . $usuario['user_id']) ?>" class="btn btn-outline-info">
                        <i class="fas fa-eye"></i> Ver detalles
                    </a>
                    <a href="<?= base_url('users') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al listado
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Se encontraron los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-edit me-2"></i>
                        Editar Usuario
                    </h5>
                </div>
                
                <div class="card-body">
                    <form action="<?= base_url('users/update/' . $usuario['user_id']) ?>" method="post" enctype="multipart/form-data" id="userForm">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">
                        
                        <div class="row">
                            <!-- Información Personal -->
                            <div class="col-lg-6">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>
                                    Información Personal
                                </h6>
                                
                                <!-- Nombre -->
                                <div class="mb-3">
                                    <label for="user_nombre" class="form-label">
                                        <i class="fas fa-user text-primary me-1"></i>
                                        Nombre Completo *
                                    </label>
                                    <input type="text" 
                                           class="form-control <?= (session('errors.user_nombre')) ? 'is-invalid' : '' ?>" 
                                           id="user_nombre" 
                                           name="user_nombre" 
                                           value="<?= old('user_nombre', $usuario['user_nombre']) ?>"
                                           placeholder="Ej. Juan Pérez García"
                                           required>
                                    <div class="invalid-feedback">
                                        <?= session('errors.user_nombre') ?>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="user_email" class="form-label">
                                        <i class="fas fa-envelope text-info me-1"></i>
                                        Email *
                                    </label>
                                    <input type="email" 
                                           class="form-control <?= (session('errors.user_email')) ? 'is-invalid' : '' ?>" 
                                           id="user_email" 
                                           name="user_email" 
                                           value="<?= old('user_email', $usuario['user_email']) ?>"
                                           placeholder="usuario@ejemplo.com"
                                           required>
                                    <div class="invalid-feedback">
                                        <?= session('errors.user_email') ?>
                                    </div>
                                </div>

                                <!-- Teléfono -->
                                <div class="mb-3">
                                    <label for="user_telefono" class="form-label">
                                        <i class="fas fa-phone text-success me-1"></i>
                                        Teléfono
                                    </label>
                                    <input type="tel" 
                                           class="form-control" 
                                           id="user_telefono" 
                                           name="user_telefono" 
                                           value="<?= old('user_telefono', $usuario['user_telefono']) ?>"
                                           placeholder="+56 9 1234 5678">
                                    <div class="form-text">Opcional - Formato internacional recomendado</div>
                                </div>

                                <!-- Avatar actual y nuevo -->
                                <div class="mb-3">
                                    <!-- Avatar actual -->
                                    <?php if (!empty($usuario['user_avatar'])): ?>
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-image text-warning me-1"></i>
                                                Avatar Actual
                                            </label>
                                            <div class="border rounded p-3 bg-light text-center">
                                                <img src="<?= base_url('uploads/avatars/'.$usuario['user_avatar']) . '?v=' . urlencode($usuario['user_updated_at'] ?? time()) ?>"
                                                alt="<?= esc($usuario['user_nombre']) ?>"
                                                class="rounded-circle"
                                                style="width:100px;height:100px;object-fit:cover;">
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-file me-1"></i>
                                                        <?= $usuario['user_avatar'] ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Nuevo avatar -->
                                    <label for="user_avatar" class="form-label">
                                        <i class="fas fa-upload text-success me-1"></i>
                                        <?= !empty($usuario['user_avatar']) ? 'Cambiar Avatar' : 'Subir Avatar' ?>
                                    </label>
                                    <input type="file" 
                                           class="form-control" 
                                           id="user_avatar" 
                                           name="user_avatar"
                                           accept="image/jpeg,image/jpg,image/png">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        JPG, JPEG, PNG. Máximo 1MB 
                                        <?= !empty($usuario['user_avatar']) ? '(dejar vacío para mantener el actual)' : '(opcional)' ?>
                                    </div>
                                    
                                    <!-- Preview del nuevo avatar -->
                                    <div id="avatarPreview" class="mt-3" style="display: none;">
                                        <div class="border rounded p-3 bg-light text-center">
                                            <label class="form-label mb-2">Vista previa del nuevo avatar:</label>
                                            <div>
                                                <img id="previewImg" src="" alt="Preview" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Configuración del Sistema -->
                            <div class="col-lg-6">
                                <h6 class="text-warning mb-3">
                                    <i class="fas fa-cogs me-2"></i>
                                    Configuración del Sistema
                                </h6>

                                <!-- Perfil -->
                                <div class="mb-3">
                                    <label for="user_perfil" class="form-label">
                                        <i class="fas fa-user-tag text-primary me-1"></i>
                                        Perfil de Usuario *
                                    </label>
                                    <select class="form-select <?= (session('errors.user_perfil')) ? 'is-invalid' : '' ?>" 
                                            id="user_perfil" 
                                            name="user_perfil" 
                                            required>
                                        <option value="">Seleccionar perfil...</option>
                                        <optgroup label="🛡️ Perfiles Internos">
                                            <?php foreach ($perfilesInternos as $perfil): ?>
                                                <option value="<?= $perfil['perfil_id'] ?>" 
                                                        data-tipo="interno"
                                                        <?= old('user_perfil', $usuario['user_perfil']) == $perfil['perfil_id'] ? 'selected' : '' ?>>
                                                    <?= esc($perfil['perfil_nombre']) ?> 
                                                    (Nivel <?= $perfil['perfil_nivel'] ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                        <optgroup label="🏢 Perfiles de Compañía">
                                            <?php foreach ($perfilesCompania as $perfil): ?>
                                                <option value="<?= $perfil['perfil_id'] ?>" 
                                                        data-tipo="compania"
                                                        <?= old('user_perfil', $usuario['user_perfil']) == $perfil['perfil_id'] ? 'selected' : '' ?>>
                                                    <?= esc($perfil['perfil_nombre']) ?> 
                                                    (Nivel <?= $perfil['perfil_nivel'] ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    </select>
                                    <div class="invalid-feedback">
                                        <?= session('errors.user_perfil') ?>
                                    </div>
                                    <div class="form-text">El perfil determina los permisos del usuario</div>
                                </div>

                                <!-- Compañía -->
                                <div class="mb-3" id="cia-container" style="display: none;">
                                    <label for="cia_id" class="form-label">
                                        <i class="fas fa-building text-info me-1"></i>
                                        Compañía *
                                    </label>
                                    <select class="form-select <?= (session('errors.cia_id')) ? 'is-invalid' : '' ?>" 
                                            id="cia_id" 
                                            name="cia_id">
                                        <option value="">Seleccionar compañía...</option>
                                        <?php foreach ($cias as $cia): ?>
                                            <option value="<?= $cia['cia_id'] ?>" 
                                                    <?= old('cia_id', $usuario['cia_id']) == $cia['cia_id'] ? 'selected' : '' ?>>
                                                <?= esc($cia['cia_nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        <?= session('errors.cia_id') ?>
                                    </div>
                                    <div class="form-text">Compañía a la que pertenece el usuario</div>
                                </div>

                                <!-- Info perfil interno -->
                                <div class="mb-3" id="interno-info" style="display: none;">
                                    <div class="alert alert-info">
                                        <i class="fas fa-shield-alt me-2"></i>
                                        <strong>Usuario Interno:</strong> Este usuario pertenece a la organización y no requiere compañía.
                                    </div>
                                </div>

                                <!-- Contraseña (opcional en edición) -->
                                <div class="mb-3">
                                    <label for="user_clave" class="form-label">
                                        <i class="fas fa-lock text-danger me-1"></i>
                                        Nueva Contraseña (opcional)
                                    </label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control <?= (session('errors.user_clave')) ? 'is-invalid' : '' ?>" 
                                               id="user_clave" 
                                               name="user_clave" 
                                               placeholder="Dejar vacío para mantener la actual">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <div class="invalid-feedback">
                                            <?= session('errors.user_clave') ?>
                                        </div>
                                    </div>
                                    <div class="form-text">Solo completa si deseas cambiar la contraseña</div>
                                </div>

                                <!-- Confirmar Contraseña -->
                                <div class="mb-3" id="confirm-password-container" style="display: none;">
                                    <label for="confirmar_clave" class="form-label">
                                        <i class="fas fa-lock text-danger me-1"></i>
                                        Confirmar Nueva Contraseña
                                    </label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control <?= (session('errors.confirmar_clave')) ? 'is-invalid' : '' ?>" 
                                               id="confirmar_clave" 
                                               name="confirmar_clave" 
                                               placeholder="Confirmar nueva contraseña">
                                        <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <div class="invalid-feedback">
                                            <?= session('errors.confirmar_clave') ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Estado -->
                                <div class="mb-3">
                                    <label for="user_habil" class="form-label">
                                        <i class="fas fa-toggle-on text-success me-1"></i>
                                        Estado
                                    </label>
                                    <select class="form-select" id="user_habil" name="user_habil">
                                        <option value="1" <?= old('user_habil', $usuario['user_habil']) == '1' ? 'selected' : '' ?>>
                                            ✅ Activo
                                        </option>
                                        <option value="0" <?= old('user_habil', $usuario['user_habil']) == '0' ? 'selected' : '' ?>>
                                            ❌ Inactivo
                                        </option>
                                    </select>
                                    <div class="form-text">Estado actual del usuario</div>
                                </div>
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-info-circle text-info me-1"></i>
                                            Información del registro
                                        </h6>
                                        <small class="text-muted">
                                            <strong>ID:</strong> <?= $usuario['user_id'] ?><br>
                                            <strong>Registrado:</strong> <?= date('d/m/Y H:i', strtotime($usuario['user_created_at'])) ?><br>
                                            <?php if (!empty($usuario['user_updated_at'])): ?>
                                                <strong>Última modificación:</strong> <?= date('d/m/Y H:i', strtotime($usuario['user_updated_at'])) ?><br>
                                            <?php endif; ?>
                                            <?php if (!empty($usuario['user_ultimo_acceso'])): ?>
                                                <strong>Último acceso:</strong> <?= date('d/m/Y H:i', strtotime($usuario['user_ultimo_acceso'])) ?>
                                            <?php else: ?>
                                                <strong>Último acceso:</strong> <span class="text-warning">Nunca</span>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="<?= base_url('users') ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a> 
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Actualizar Usuario
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    const originalData = {
        nombre:   $('#user_nombre').val(),
        email:    $('#user_email').val(),
        telefono: $('#user_telefono').val(),
        perfil:   $('#user_perfil').val(),
        cia:      $('#cia_id').val(),
        estado:   $('#user_habil').val()
    };

    // ===== Perfil: mostrar/ocultar compañía según tipo =====
    function applyPerfilUI() {
        const tipo = $('#user_perfil').find('option:selected').data('tipo');
        if (tipo === 'compania') {
            $('#cia-container').show();
            $('#cia_id').prop('required', true);
            $('#interno-info').hide();
        } else if (tipo === 'interno') {
            $('#cia-container').hide();
            $('#cia_id').prop('required', false).val('');
            $('#interno-info').show();
        } else {
            $('#cia-container').hide();
            $('#cia_id').prop('required', false).val('');
            $('#interno-info').hide();
        }
    }
    $('#user_perfil').on('change', applyPerfilUI);
    applyPerfilUI(); // inicial

    // ===== Password fuerte (igual al backend) =====
    const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;


    // Mostrar/ocultar confirmar contraseña
    $('#user_clave').on('input', function() {
        const hasVal = $(this).val().length > 0;
        $('#confirm-password-container').toggle(hasVal);
        $('#confirmar_clave').prop('required', hasVal);
        if (!hasVal) {
            $('#confirmar_clave').val('').removeClass('is-invalid');
            $(this).removeClass('is-invalid is-valid');
        }
    });

    // Validar coincidencia en vivo
    $('#confirmar_clave').on('input', function() {
        const password = $('#user_clave').val();
        const confirm  = $(this).val();
        if (confirm && password !== confirm) {
            $(this).addClass('is-invalid').siblings('.invalid-feedback')
                .text('Las contraseñas no coinciden');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    // ===== Preview de avatar =====
    $('#user_avatar').on('change', function() {
        const file = this.files[0];
        const $preview = $('#avatarPreview');
        const $previewImg = $('#previewImg');

        if (file) {
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(file.type)) {
                Swal.fire({ icon:'error', title:'Archivo no válido', text:'Solo JPG, JPEG y PNG' });
                $(this).val(''); $preview.hide(); return;
            }
            if (file.size > 1048576) { // 1MB
                Swal.fire({ icon:'error', title:'Archivo muy grande', text:'Máximo 1MB' });
                $(this).val(''); $preview.hide(); return;
            }
            const reader = new FileReader();
            reader.onload = e => { $previewImg.attr('src', e.target.result); $preview.show(); };
            reader.readAsDataURL(file);
        } else {
            $preview.hide();
        }
    });

    // ===== Detectar cambios =====
    function hasChanges() {
        return (
            $('#user_nombre').val()   !== originalData.nombre ||
            $('#user_email').val()    !== originalData.email  ||
            $('#user_telefono').val() !== originalData.telefono ||
            $('#user_perfil').val()   !== originalData.perfil ||
            $('#cia_id').val()        !== originalData.cia    ||
            $('#user_habil').val()    !== originalData.estado ||
            $('#user_clave').val().length > 0 ||
            ($('#user_avatar')[0].files && $('#user_avatar')[0].files.length > 0)
        );
    }

    // ===== Validación del formulario =====
    $('#userForm').on('submit', function(e) {
        // Sin cambios → evita request innecesario
        if (!hasChanges()) {
            e.preventDefault();
            Swal.fire({ icon:'info', title:'Sin cambios', text:'No se han detectado cambios' });
            return;
        }

        // Nombre
        const nombre = $('#user_nombre').val().trim();
        if (nombre.length < 3) {
            e.preventDefault();
            Swal.fire({ icon:'error', title:'Error de validación', text:'El nombre debe tener al menos 3 caracteres' });
            $('#user_nombre').focus(); return;
        }

        // Email
        const email = $('#user_email').val().trim();
        const emailBasic = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailBasic.test(email)) {
            e.preventDefault();
            Swal.fire({ icon:'error', title:'Error de validación', text:'Debe ingresar un email válido' });
            $('#user_email').focus(); return;
        }

        // Perfil
        const perfil = $('#user_perfil').val();
        if (!perfil) {
            e.preventDefault();
            Swal.fire({ icon:'error', title:'Error de validación', text:'Debe seleccionar un perfil' });
            $('#user_perfil').focus(); return;
        }

        // Compañía si aplica
        const tipo = $('#user_perfil').find('option:selected').data('tipo');
        if (tipo === 'compania' && !$('#cia_id').val()) {
            e.preventDefault();
            Swal.fire({ icon:'error', title:'Error de validación', text:'Debe seleccionar una compañía' });
            $('#cia_id').focus(); return;
        }

        // Password (opcional en edición, pero si viene debe ser fuerte)
        const password = $('#user_clave').val();
        const confirm  = $('#confirmar_clave').val();
        if (password.length > 0) {
            if (!strongRegex.test(password)) {
                e.preventDefault();
                Swal.fire({
                    icon:'error',
                    title:'Contraseña débil',
                    text:'Mínimo 8 caracteres e incluir mayúscula, minúscula, número y símbolo.'
                });
                $('#user_clave').addClass('is-invalid').focus(); return;
            }
            if (password !== confirm) {
                e.preventDefault();
                Swal.fire({ icon:'error', title:'Error', text:'Las contraseñas no coinciden' });
                $('#confirmar_clave').addClass('is-invalid').focus(); return;
            }
        }
    });

    // ===== Restaurar =====
    $('#resetBtn').on('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Restaurar datos originales?',
            text: 'Se perderán todos los cambios',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, restaurar',
            cancelButtonText: 'Cancelar'
        }).then((r) => {
            if (r.isConfirmed) {
                $('#user_nombre').val(originalData.nombre);
                $('#user_email').val(originalData.email);
                $('#user_telefono').val(originalData.telefono);
                $('#user_perfil').val(originalData.perfil).trigger('change');
                $('#cia_id').val(originalData.cia);
                $('#user_habil').val(originalData.estado);
                $('#user_clave').val('').removeClass('is-invalid is-valid');
                $('#confirmar_clave').val('').removeClass('is-invalid is-valid');
                $('#user_avatar').val('');
                $('#avatarPreview').hide();
                $('#confirm-password-container').hide();
                $('.form-control, .form-select').removeClass('is-invalid is-valid');

                Swal.fire({ icon:'success', title:'Datos restaurados', timer: 1500, showConfirmButton: false });
            }
        });
    });

    // ===== Aviso al salir con cambios =====
    $(window).on('beforeunload', function() {
        if (hasChanges()) return 'Tienes cambios sin guardar. ¿Salir igualmente?';
    });
    $('#userForm').on('submit', function() { $(window).off('beforeunload'); });

    // Focus inicial
    $('#user_nombre').focus().select();
    $('#togglePassword').on('click', function() {
    const passwordField = $('#user_clave');
    const icon = $(this).find('i');

    if (passwordField.attr('type') === 'password') {
        passwordField.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        passwordField.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
});
$('#toggleConfirmPassword').on('click', function() {
    const passwordField = $('#confirmar_clave');
    const icon = $(this).find('i');

    if (passwordField.attr('type') === 'password') {
        passwordField.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        passwordField.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
});
});
</script>

<?= $this->endSection() ?>