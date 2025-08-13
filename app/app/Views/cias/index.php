 <?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Gestión de Compañías
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Gestión de Compañías</h1>
                    <p class="text-muted">Administra las compañías del sistema</p>
                </div>
                <a href="<?= base_url('cias/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Compañía
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

    <!-- Table Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-building text-primary me-2"></i>
                Listado de Compañías
            </h5>
        </div>
        
        <div class="card-body p-0">
            <?php if (empty($cias)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay compañías registradas</h5>
                    <p class="text-muted">Comienza creando tu primera compañía</p>
                    <a href="<?= base_url('cias/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Crear Compañía
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Logo</th>
                                <th>Nombre</th>
                                <th>Dirección</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cias as $cia): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($cia['cia_logo'])): ?>
                                            <img src="<?= base_url('uploads/logos/' . $cia['cia_logo']) ?>" 
                                                 alt="<?= esc($cia['cia_nombre']) ?>" 
                                                 class="rounded" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-building text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="fw-medium"><?= esc($cia['cia_nombre']) ?></span>
                                            <br>
                                            <small class="text-muted">ID: <?= $cia['cia_id'] ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($cia['cia_direccion'])): ?>
                                            <span><?= esc($cia['cia_direccion']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Sin dirección</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($cia['cia_habil']): ?>
                                            <span class="badge bg-success">Activa</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactiva</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('cias/show/' . $cia['cia_id']) ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('cias/edit/' . $cia['cia_id']) ?>" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="post" action="<?= base_url('cias/delete/' . $cia['cia_id']) ?>" style="display: inline;">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Estás seguro de eliminar esta compañía?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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