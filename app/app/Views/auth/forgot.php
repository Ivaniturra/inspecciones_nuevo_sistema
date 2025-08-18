<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($title ?? 'Recuperar contraseña') ?> - <?= esc($appTitle ?? 'InspectZu') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary">

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-sm-10 col-md-8 col-lg-5">
      <div class="card shadow-sm">
        <div class="card-header bg-body-tertiary">
          <h5 class="mb-0">Recuperar contraseña</h5>
        </div>
        <div class="card-body">
          <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
          <?php endif; ?>
          <?php if ($errs = session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
                <?php foreach ($errs as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <form method="post" action="<?= base_url('forgot') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required value="<?= old('email') ?>">
            </div>
            <button class="btn btn-primary w-100" type="submit">Enviar enlace</button>
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
