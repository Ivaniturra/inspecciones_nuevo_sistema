<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<h1 class="h3 mb-3"><?= esc($title ?? 'Crear usuario') ?></h1>

<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show">
    <i class="fa-solid fa-triangle-exclamation me-2"></i>
    <?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<?php if ($errs = session()->getFlashdata('errors')): ?>
  <div class="alert alert-danger alert-dismissible fade show">
    <i class="fa-solid fa-triangle-exclamation me-2"></i>
    <strong>Corrige los siguientes errores:</strong>
    <ul class="mb-0 mt-2">
      <?php foreach ($errs as $e): ?>
        <li><?= esc($e) ?></li>
      <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<form action="<?= site_url('users/store') ?>" method="post" enctype="multipart/form-data" id="userCreateForm" novalidate>
  <?= csrf_field() ?>

  <div class="row g-3">
    <div class="col-lg-6">
      <div class="mb-3">
        <label class="form-label">Nombre *</label>
        <input type="text" name="user_nombre" class="form-control <?= session('errors.user_nombre') ? 'is-invalid' : '' ?>"
               required maxlength="100" value="<?= esc(old('user_nombre')) ?>">
        <div class="invalid-feedback"><?= session('errors.user_nombre') ?></div>
      </div>

      <div class="mb-3">
        <label class="form-label">Email *</label>
        <input type="email" name="user_email" class="form-control <?= session('errors.user_email') ? 'is-invalid' : '' ?>"
               required maxlength="255" value="<?= esc(old('user_email')) ?>">
        <div class="invalid-feedback"><?= session('errors.user_email') ?></div>
      </div>

      <div class="mb-3">
        <label class="form-label">Teléfono (opcional)</label>
        <input type="text" name="user_telefono" class="form-control" maxlength="50"
               value="<?= esc(old('user_telefono')) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Avatar (opcional)</label>
        <input type="file" name="user_avatar" id="user_avatar" class="form-control" accept=".jpg,.jpeg,.png">
        <div class="form-text">JPG/PNG, máx. 1 MB.</div>
        <div id="avatarPreview" class="mt-3" style="display:none;">
          <img id="previewImg" src="" alt="Preview" class="rounded-circle" style="width:100px;height:100px;object-fit:cover;">
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="mb-3">
        <label class="form-label">Perfil *</label>
        <select name="user_perfil" id="user_perfil" class="form-select <?= session('errors.user_perfil') ? 'is-invalid' : '' ?>" required>
          <option value="">Seleccione…</option>
          
          <optgroup label="🛡️ Perfiles Internos">
            <?php foreach ($perfilesInternos as $p): ?>
              <option
                value="<?= (int)$p['perfil_id'] ?>"
                data-tipo="interno"
                <?= set_select('user_perfil', (string)$p['perfil_id']) ?>>
                <?= esc($p['perfil_nombre']) ?>
                <?php if (isset($p['perfil_nivel'])): ?>(Nivel <?= (int)$p['perfil_nivel'] ?>)<?php endif; ?>
              </option>
            <?php endforeach; ?>
          </optgroup>
          
          <optgroup label="🏢 Perfiles de Compañía">
            <?php foreach ($perfilesCompania as $p): ?>
              <option
                value="<?= (int)$p['perfil_id'] ?>"
                data-tipo="compania"
                <?= set_select('user_perfil', (string)$p['perfil_id']) ?>>
                <?= esc($p['perfil_nombre']) ?>
                <?php if (isset($p['perfil_nivel'])): ?>(Nivel <?= (int)$p['perfil_nivel'] ?>)<?php endif; ?>
              </option>
            <?php endforeach; ?>
          </optgroup>
          
          <optgroup label="🚗 Perfiles de Corredor">
            <?php foreach ($perfilesCorredores as $p): ?>
              <option
                value="<?= (int)$p['perfil_id'] ?>"
                data-tipo="corredor"
                <?= set_select('user_perfil', (string)$p['perfil_id']) ?>>
                <?= esc($p['perfil_nombre']) ?>
                <?php if (isset($p['perfil_nivel'])): ?>(Nivel <?= (int)$p['perfil_nivel'] ?>)<?php endif; ?>
              </option>
            <?php endforeach; ?>
          </optgroup>
          
          <optgroup label="🔍 Perfiles de Inspector">
            <?php foreach ($perfilesInspectores as $p): ?>
              <option
                value="<?= (int)$p['perfil_id'] ?>"
                data-tipo="inspector"
                <?= set_select('user_perfil', (string)$p['perfil_id']) ?>>
                <?= esc($p['perfil_nombre']) ?>
                <?php if (isset($p['perfil_nivel'])): ?>(Nivel <?= (int)$p['perfil_nivel'] ?>)<?php endif; ?>
              </option>
            <?php endforeach; ?>
          </optgroup>
        </select>
        <div class="invalid-feedback"><?= session('errors.user_perfil') ?></div>
      </div>

      <div class="mb-3" id="corredor-container" style="display:none;">
        <label class="form-label">Corredor *</label>
        <select name="corredor_id" id="corredor_id" class="form-select <?= session('errors.corredor_id') ? 'is-invalid' : '' ?>">
          <option value="">Seleccione un corredor…</option>
          <?php foreach ($corredores as $c): ?>
            <option value="<?= (int)$c['corredor_id'] ?>" <?= set_select('corredor_id', (string)$c['corredor_id']) ?>>
              <?= esc($c['corredor_nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback"><?= session('errors.corredor_id') ?></div>
        <div class="form-text">Corredor al que pertenece el usuario</div>
      </div>

      <div class="mb-3" id="cia-container" style="display:none;">
        <label class="form-label">Compañía *</label>
        <select name="cia_id" id="cia_id" class="form-select <?= session('errors.cia_id') ? 'is-invalid' : '' ?>">
          <option value="">Seleccione una compañía…</option>
          <?php foreach ($cias as $c): ?>
            <option value="<?= (int)$c['cia_id'] ?>" <?= set_select('cia_id', (string)$c['cia_id']) ?>>
              <?= esc($c['cia_nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback"><?= session('errors.cia_id') ?></div>
        <div class="form-text">Compañía a la que pertenece el usuario</div>
      </div>

      <!-- Info adicional según tipo de perfil -->
      <div class="mb-3" id="interno-info" style="display:none;">
        <div class="alert alert-info">
          <i class="fas fa-shield-alt me-2"></i>
          <strong>Usuario Interno:</strong> Este usuario pertenece a la organización y no requiere compañía ni corredor.
        </div>
      </div>

      <div class="mb-3" id="inspector-info" style="display:none;">
        <div class="alert alert-success">
          <i class="fas fa-search me-2"></i>
          <strong>Inspector:</strong> Este usuario podrá realizar inspecciones asignadas específicamente, sin depender de compañía o corredor.
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Contraseña *</label>
        <div class="input-group">
          <input type="password" name="user_clave" id="user_clave"
                 class="form-control <?= session('errors.user_clave') ? 'is-invalid' : '' ?>"
                 required autocomplete="new-password" />
          <button class="btn btn-outline-secondary" type="button" id="togglePassword">
            <i class="fa-solid fa-eye"></i>
          </button>
          <div class="invalid-feedback"><?= session('errors.user_clave') ?></div>
        </div>
        <div class="form-text">
          Mínimo 8 caracteres, incluir mayúscula, minúscula, número y símbolo.
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Confirmar contraseña *</label>
        <div class="input-group">
          <input type="password" name="confirmar_clave" id="confirmar_clave"
                 class="form-control <?= session('errors.confirmar_clave') ? 'is-invalid' : '' ?>"
                 required autocomplete="new-password" />
          <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
            <i class="fa-solid fa-eye"></i>
          </button>
          <div class="invalid-feedback"><?= session('errors.confirmar_clave') ?></div>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Estado</label>
        <select name="user_habil" class="form-select">
          <option value="1" <?= set_select('user_habil','1', true) ?>>Activo</option>
          <option value="0" <?= set_select('user_habil','0') ?>>Inactivo</option>
        </select>
      </div>
    </div>
  </div>

  <div class="d-flex gap-2 mt-2">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="<?= site_url('users') ?>" class="btn btn-secondary">Cancelar</a>
  </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(function () {
  // ===== Mostrar/ocultar campos según perfil =====
  function refreshProfileRequirements() {
    const tipo = $('#user_perfil').find('option:selected').data('tipo');
    
    // Ocultar todos los contenedores primero
    $('#cia-container, #corredor-container, #interno-info, #inspector-info').hide();
    $('#cia_id, #corredor_id').prop('required', false).val('');
    
    switch(tipo) {
      case 'compania':
        $('#cia-container').show();
        $('#cia_id').prop('required', true);
        break;
        
      case 'corredor':
        $('#corredor-container').show();
        $('#corredor_id').prop('required', true);
        break;
        
      case 'interno':
        $('#interno-info').show();
        break;
        
      case 'inspector':
        $('#inspector-info').show();
        break;
    }
  }
  
  $('#user_perfil').on('change', refreshProfileRequirements);
  refreshProfileRequirements(); // Ejecutar al cargar

  // ===== Password fuerte (igual al backend) =====
  const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])(?=\S+$).{8,}$/;

  // Mostrar/ocultar password
  $('#togglePassword').on('click', function() {
    const $f = $('#user_clave'), $i = $(this).find('i');
    if ($f.attr('type') === 'password') { $f.attr('type','text');  $i.removeClass('fa-eye').addClass('fa-eye-slash'); }
    else { $f.attr('type','password'); $i.removeClass('fa-eye-slash').addClass('fa-eye'); }
  });
  $('#toggleConfirmPassword').on('click', function() {
    const $f = $('#confirmar_clave'), $i = $(this).find('i');
    if ($f.attr('type') === 'password') { $f.attr('type','text');  $i.removeClass('fa-eye').addClass('fa-eye-slash'); }
    else { $f.attr('type','password'); $i.removeClass('fa-eye-slash').addClass('fa-eye'); }
  });

  // Preview avatar
  $('#user_avatar').on('change', function () {
    const file = this.files[0];
    const $wrap = $('#avatarPreview');
    const $img  = $('#previewImg');
    if (!file) { $wrap.hide(); return; }
    const valid = ['image/jpeg','image/jpg','image/png'];
    if (!valid.includes(file.type)) { this.value=''; $wrap.hide(); return Swal.fire({icon:'error',title:'Archivo no válido',text:'Solo JPG/JPEG/PNG'}); }
    if (file.size > 1048576) { this.value=''; $wrap.hide(); return Swal.fire({icon:'error',title:'Archivo muy grande',text:'Máximo 1MB'}); }
    const r = new FileReader();
    r.onload = e => { $img.attr('src', e.target.result); $wrap.show(); };
    r.readAsDataURL(file);
  });

  // Validación al enviar
  $('#userCreateForm').on('submit', function (e) {
    const email = $('input[name="user_email"]').val().trim();
    const emailBasic = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailBasic.test(email)) {
      e.preventDefault();
      return Swal.fire({icon:'error',title:'Email inválido',text:'Ingrese un email válido.'});
    }

    const pass = $('#user_clave').val();
    const conf = $('#confirmar_clave').val();
    if (!strongRegex.test(pass)) {
      e.preventDefault();
      return Swal.fire({icon:'error',title:'Contraseña débil',text:'Mínimo 8, con mayúscula, minúscula, número y símbolo.'});
    }
    if (pass !== conf) {
      e.preventDefault();
      return Swal.fire({icon:'error',title:'Error',text:'Las contraseñas no coinciden.'});
    }

    const tipo = $('#user_perfil').find('option:selected').data('tipo');
    
    // Validaciones específicas por tipo de perfil
    if (tipo === 'compania' && !$('#cia_id').val()) {
      e.preventDefault();
      return Swal.fire({icon:'error',title:'Falta compañía',text:'Selecciona una compañía para este perfil.'});
    }
    
    if (tipo === 'corredor' && !$('#corredor_id').val()) {
      e.preventDefault();
      return Swal.fire({icon:'error',title:'Falta corredor',text:'Selecciona un corredor para este perfil.'});
    }
  });
});
</script>
<?= $this->endSection() ?>