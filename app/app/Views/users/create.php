<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<h1><?= esc($title ?? 'Crear usuario') ?></h1>

 
<form action="<?= site_url('users/store') ?>" method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>

  <div class="mb-3">
    <label class="form-label">Nombre</label>
    <input type="text" name="user_nombre" class="form-control" required maxlength="100"
           value="<?= esc(old('user_nombre')) ?>">
  </div>

  <div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="user_email" class="form-control" required maxlength="255"
           value="<?= esc(old('user_email')) ?>">
  </div>

  <div class="mb-3">
    <label class="form-label">Teléfono (opcional)</label>
    <input type="text" name="user_telefono" class="form-control" maxlength="50"
           value="<?= esc(old('user_telefono')) ?>">
  </div>

  <div class="mb-3">
    <label class="form-label">Perfil</label>
    <select name="user_perfil" class="form-select" required>
      <option value="">Seleccione…</option>
      <?php foreach ($perfiles as $p): ?>
        <option value="<?= (int)$p['perfil_id'] ?>" <?= set_select('user_perfil', (string)$p['perfil_id']) ?>>
          <?= esc($p['perfil_nombre']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Compañía (opcional)</label>
    <select name="cia_id" class="form-select">
      <option value="">—</option>
      <?php foreach ($cias as $c): ?>
        <option value="<?= (int)$c['cia_id'] ?>" <?= set_select('cia_id', (string)$c['cia_id']) ?>>
          <?= esc($c['cia_nombre']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Contraseña</label>
    <input type="password" name="user_clave" class="form-control" required autocomplete="new-password">
    <div class="form-text">
      Mínimo 8 caracteres, incluyendo: mayúscula, minúscula, número y símbolo.
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">Confirmar contraseña</label>
    <input type="password" name="confirmar_clave" class="form-control" required autocomplete="new-password">
  </div>

  <div class="mb-3">
    <label class="form-label">Avatar (opcional)</label>
    <input type="file" name="user_avatar" class="form-control" accept=".jpg,.jpeg,.png">
    <div class="form-text">JPG/PNG, máx. 1MB.</div>
  </div>

  <div class="mb-3">
    <label class="form-label">Estado</label>
    <select name="user_habil" class="form-select">
      <option value="1" <?= set_select('user_habil','1', true) ?>>Activo</option>
      <option value="0" <?= set_select('user_habil','0') ?>>Inactivo</option>
    </select>
  </div>

  <div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="<?= site_url('users') ?>" class="btn btn-secondary">Cancelar</a>
  </div>
</form>

<?= $this->endSection() ?>