 <?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Gestión de Comentarios
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Gestión de Comentarios</h1>
                    <p class="text-muted">Administra los comentarios del sistema organizados por compañía y perfil</p>
                </div>
                <a href="<?= base_url('comentarios/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Comentario
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

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="cia_id" class="form-label small text-muted">Compañía</label>
                    <select name="cia_id" id="cia_id" class="form-select">
                        <option value="">-- Todas las compañías --</option>
                        <?php foreach ($cias as $id => $nombre): ?>
                            <option value="<?= esc($id) ?>" <?= $filtros['cia_id']==$id ? 'selected' : '' ?>>
                                <?= esc($nombre) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="perfil_id" class="form-label small text-muted">Perfil</label>
                    <select name="perfil_id" id="perfil_id" class="form-select">
                        <option value="">-- Todos los perfiles --</option>
                        <?php foreach ($perfiles as $id => $nombre): ?>
                            <option value="<?= esc($id) ?>" <?= $filtros['perfil_id']==$id ? 'selected' : '' ?>>
                                <?= esc($nombre) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="q" class="form-label small text-muted">Buscar</label>
                    <input type="text" name="q" id="q" class="form-control" 
                           placeholder="Buscar en comentarios..." 
                           value="<?= esc($filtros['q']) ?>">
                </div>
                <div class="col-md-1">
                    <label for="per_page" class="form-label small text-muted">Por página</label>
                    <select name="per_page" id="per_page" class="form-select">
                        <?php foreach ([10,20,50,100] as $pp): ?>
                            <option value="<?= $pp ?>" <?= $filtros['per_page']==$pp ? 'selected' : '' ?>>
                                <?= $pp ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="btn-group w-100" role="group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="<?= base_url('comentarios') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-comments text-primary me-2"></i>
                Listado de Comentarios
                <?php if (!empty($filtros['cia_id']) || !empty($filtros['perfil_id']) || !empty($filtros['q'])): ?>
                    <small class="text-muted">
                        (<?= is_countable($rows) ? count($rows) : 0 ?> resultados filtrados)
                    </small>
                <?php endif; ?>
            </h5>
        </div>
        
        <div class="card-body p-0">
            <?php if (empty($rows)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay comentarios registrados</h5>
                    <p class="text-muted">
                        <?php if (!empty($filtros['cia_id']) || !empty($filtros['perfil_id']) || !empty($filtros['q'])): ?>
                            No se encontraron comentarios con los filtros aplicados.
                        <?php else: ?>
                            Comienza creando tu primer comentario.
                        <?php endif; ?>
                    </p>
                    <?php if (empty($filtros['cia_id']) && empty($filtros['perfil_id']) && empty($filtros['q'])): ?>
                        <a href="<?= base_url('comentarios/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Comentario
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:80px">ID</th>
                                <th style="width:160px">Compañía</th>
                                <th style="width:140px">Perfil</th>
                                <th>Comentario</th>
                                <th style="width:100px" class="text-center">ID Interno</th>
                                <th style="width:250px" class="text-center">Flags</th>
                                <th style="width:120px" class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $r): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark"><?= esc($r['comentario_id']) ?></span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= esc($r['cia_nombre'] ?? 'Compañía #' . $r['cia_id']) ?></strong>
                                            <br>
                                            <small class="text-muted">ID: <?= esc($r['cia_id']) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($r['perfil_nombre'])): ?>
                                            <span class="badge bg-info"><?= esc($r['perfil_nombre']) ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Todos</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 250px;" title="<?= esc($r['comentario_nombre']) ?>">
                                            <?= esc($r['comentario_nombre']) ?>
                                        </div>
                                        <?php if (!empty($r['comentario_created_at'])): ?>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                <?= date('d/m/Y H:i', strtotime($r['comentario_created_at'])) ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($r['comentario_id_cia_interno'])): ?>
                                            <span class="badge bg-warning text-dark"><?= esc($r['comentario_id_cia_interno']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-wrap justify-content-center gap-1">
                                            <?php if ($r['comentario_devuelve']): ?>
                                                <span class="badge bg-warning text-dark" title="Requiere devolución">
                                                    <i class="fas fa-undo me-1"></i>Dev
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($r['comentario_elimina']): ?>
                                                <span class="badge bg-danger" title="Sugiere eliminación">
                                                    <i class="fas fa-trash me-1"></i>Del
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($r['comentario_envia_correo']): ?>
                                                <span class="badge bg-success" title="Envía notificación por correo">
                                                    <i class="fas fa-envelope me-1"></i>Mail
                                                </span>
                                            <?php endif; ?>
                                            <?php if (!$r['comentario_devuelve'] && !$r['comentario_elimina'] && !$r['comentario_envia_correo']): ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('comentarios/edit/'.$r['comentario_id']) ?>" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Editar comentario">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="<?= base_url('comentarios/delete/'.$r['comentario_id']) ?>" 
                                                  method="post" 
                                                  class="d-inline-block">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger btn-delete"
                                                        data-confirm="¿Estás seguro de eliminar este comentario?"
                                                        title="Eliminar comentario">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Paginación -->
    <?php if (!empty($rows) && isset($pager)): ?>
        <div class="d-flex justify-content-center mt-4">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(function() {
    // Confirmación de eliminación
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const msg  = $(this).data('confirm') || '¿Eliminar comentario?';

        Swal.fire({
            title: 'Confirmar eliminación',
            text: msg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((r) => {
            if (r.isConfirmed) form.submit();
        });
    });

    // Auto-hide alerts
    $('.alert').delay(5000).fadeOut();

    // Tooltip para comentarios truncados
    $('[title]').tooltip();
});
</script>
<?= $this->endSection() ?>