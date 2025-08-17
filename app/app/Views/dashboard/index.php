<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h3 mb-1">Panel Principal</h1>
    <div class="text-body-secondary">Resumen general del sistema</div>
  </div>
</div>

<div class="row g-3">
  <div class="col-md-4">
    <div class="card card-kpi">
      <div class="card-body text-center">
        <div class="text-body-secondary mb-1">Usuarios</div>
        <div class="kpi-value"><?= esc($stats['total_users'] ?? 0) ?></div>
        <a href="<?= base_url('users') ?>" class="btn btn-primary btn-sm mt-2">
          <i class="fa-solid fa-users me-1"></i> Gestionar
        </a>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card card-kpi">
      <div class="card-body text-center">
        <div class="text-body-secondary mb-1">Compañías activas</div>
        <div class="kpi-value"><?= esc($stats['active_cias'] ?? 0) ?></div>
        <a href="<?= base_url('cias') ?>" class="btn btn-success btn-sm mt-2">
          <i class="fa-solid fa-building me-1"></i> Ver compañías
        </a>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card card-kpi">
      <div class="card-body text-center">
        <div class="text-body-secondary mb-1">Tareas pendientes</div>
        <div class="kpi-value"><?= esc($stats['pending_tasks'] ?? 0) ?></div>
        <a href="#" class="btn btn-warning btn-sm mt-2">
          <i class="fa-solid fa-list-check me-1"></i> Ver tareas
        </a>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

