<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($title ?? 'Nueva contraseña') ?> - <?= esc($appTitle ?? 'InspectZu') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary">

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-sm-10 col-md-8 col-lg-5">
      <div class="card shadow-sm">
        <div class="card-header bg-body-tertiary">
          <h5 class="mb-0">Crear nueva contraseña</h5>
        </div>
        <div class="card-body">
          <?php if ($errs = session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
                <?php foreach ($errs as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <form method="post" action="<?= base_url('reset') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= esc($token) ?>">

            <div class="mb-3">
              <label class="form-label">Nueva contraseña</label>
              <input type="password" name="password" class="form-control" required minlength="8" autocomplete="new-password">
              <div class="form-text">Mínimo 8 caracteres (recomendado: mayúscula, minúscula, número y símbolo)</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Confirmar contraseña</label>
              <input type="password" name="confirm_password" class="form-control" required minlength="8" autocomplete="new-password">
            </div>

            <button class="btn btn-primary w-100" type="submit">Actualizar contraseña</button>
          </form>

          <div class="text-center mt-3">
            <a href="<?= base_url('/') ?>">Volver al login</a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
