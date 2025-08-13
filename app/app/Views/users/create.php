<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<h1><?= esc($title ?? 'Crear usuario') ?></h1>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
  </div>
<?php endif; ?>

<form action="<?= site_url('users/store') ?>" method="post" novalidate>
  <?= csrf_field() ?>
  <div class="mb-3">
    <label class="form-label">Nombre</label>
    <input type="text" name="user_nombre" class="form-control" required maxlength="100" value="<?= esc(old('user_nombre')) ?>">
  </div>

  <div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="user_email" class="form-control" required maxlength="255" value="<?= esc(old('user_email')) ?>">
  </div>

  <div class="mb-3">
    <label class="form-label">Perfil</label>
    <select name="user_perfil" class="form-select" required>
      <option value="">Seleccione…</option>
      <?php foreach ($perfiles as $p): ?>
        <option value="<?= (int)$p['perfil_id'] ?>" <?= set_select('user_perfil', (string)$p['perfil_id']) ?>><?= esc($p['perfil_nombre']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Compañía (opcional)</label>
    <select name="cia_id" class="form-select">
      <option value="">—</option>
      <?php foreach ($cias as $c): ?>
        <option value="<?= (int)$c['cia_id'] ?>" <?= set_select('cia_id', (string)$c['cia_id']) ?>><?= esc($c['cia_nombre']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Contraseña</label>
    <input type="password" name="user_clave" class="form-control" minlength="6">
    <div class="form-text">Mínimo 6 caracteres.</div>
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
