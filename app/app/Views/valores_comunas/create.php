 <?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'Nuevo Valor por Comuna') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-plus-circle text-success me-2"></i>
                        Nuevo Valor por Comuna
                    </h1>
                    <p class="text-muted mb-0">Crear un nuevo valor para una compañía en una comuna específica</p>
                </div>
                <div>
                    <a href="<?= base_url('valores-comunas') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Valores
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($validation) && $validation->getErrors()): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Errores de validación:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($validation->getErrors() as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Formulario -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Información del Valor
                    </h5>
                </div>
                
                <form action="<?= base_url('valores-comunas/store') ?>" method="post" id="valorForm">
                    <?= csrf_field() ?>
                    
                    <div class="card-body">
                        <div class="row">
                            <!-- Compañía -->
                            <div class="col-md-6 mb-4">
                                <label for="cia_id" class="form-label fw-bold">
                                    <i class="fas fa-building text-primary me-1"></i>
                                    Compañía <span class="text-danger">*</span>
                                </label>
                                <select class="form-select <?= !empty($validation) && $validation->hasError('cia_id') ? 'is-invalid' : '' ?>" 
                                        id="cia_id" 
                                        name="cia_id" 
                                        required>
                                    <option value="">Seleccionar compañía...</option>
                                    <?php if (!empty($cias)): ?>
                                        <?php foreach ($cias as $id => $nombre): ?>
                                            <option value="<?= $id ?>" <?= old('cia_id') == $id ? 'selected' : '' ?>>
                                                <?= esc($nombre) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?php if (!empty($validation) && $validation->hasError('cia_id')): ?>
                                    <div class="invalid-feedback">
                                        <?= esc($validation->getError('cia_id')) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Tipo de Usuario -->
                            <div class="col-md-6 mb-4">
                                <label for="tipo_usuario" class="form-label fw-bold">
                                    <i class="fas fa-user-tag text-info me-1"></i>
                                    Tipo de Usuario <span class="text-danger">*</span>
                                </label>
                                <select class="form-select <?= !empty($validation) && $validation->hasError('tipo_usuario') ? 'is-invalid' : '' ?>" 
                                        id="tipo_usuario" 
                                        name="tipo_usuario" 
                                        required>
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="inspector"     <?= old('tipo_usuario') == 'inspector' ? 'selected' : '' ?>>Inspector</option>
                                    <option value="compania"      <?= old('tipo_usuario') == 'compania' ? 'selected' : '' ?>>Compañía</option> 
                                </select>
                                <?php if (!empty($validation) && $validation->hasError('tipo_usuario')): ?>
                                    <div class="invalid-feedback">
                                        <?= esc($validation->getError('tipo_usuario')) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Inspector: Lo que cobra el inspector | Compañía: Lo que cobra al cliente final
                                </div>
                            </div>

                            <!-- Tipo de Vehículo -->
                            <!-- Región -->
                            <div class="col-md-6 mb-4">
                            <label for="region_id" class="form-label fw-bold">
                                <i class="fas fa-globe-americas text-warning me-1"></i>
                                Región <span class="text-danger">*</span>
                            </label>
                            <select class="form-select <?= !empty($validation) && $validation->hasError('region_id') ? 'is-invalid' : '' ?>"
                                    id="region_id" name="region_id" required>
                                <option value="">Seleccionar región...</option>
                                <?php if (!empty($regiones)): ?>
                                <?php foreach ($regiones as $id => $nombre): ?>
                                    <option value="<?= $id ?>" <?= old('region_id') == $id ? 'selected' : '' ?>>
                                    <?= esc($nombre) ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if (!empty($validation) && $validation->hasError('region_id')): ?>
                                <div class="invalid-feedback"><?= esc($validation->getError('region_id')) ?></div>
                            <?php endif; ?>
                            </div>

                            <!-- Provincia -->
                            <div class="col-md-6 mb-4">
                            <label for="provincias_id" class="form-label fw-bold">
                                <i class="fas fa-map text-primary me-1"></i>
                                Provincia <span class="text-danger">*</span>
                            </label>
                            <select class="form-select <?= !empty($validation) && $validation->hasError('provincias_id') ? 'is-invalid' : '' ?>"
                                    id="provincias_id" name="provincias_id" required disabled>
                                <option value="">Primero selecciona una región...</option>
                            </select>
                            <?php if (!empty($validation) && $validation->hasError('provincias_id')): ?>
                                <div class="invalid-feedback"><?= esc($validation->getError('provincias_id')) ?></div>
                            <?php endif; ?>
                            </div>

                            <!-- Comuna -->
                            <div class="col-md-6 mb-4">
                            <label for="comunas_id" class="form-label fw-bold">
                                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                Comuna <span class="text-danger">*</span>
                            </label>
                            <select class="form-select <?= !empty($validation) && $validation->hasError('comunas_id') ? 'is-invalid' : '' ?>"
                                    id="comunas_id" name="comunas_id" required disabled>
                                <option value="">Primero selecciona una provincia...</option>
                            </select>
                            <?php if (!empty($validation) && $validation->hasError('comunas_id')): ?>
                                <div class="invalid-feedback"><?= esc($validation->getError('comunas_id')) ?></div>
                            <?php endif; ?>
                            </div>

                            <!-- Tipo de Vehículo -->
                            <div class="col-md-6 mb-4">
                            <label for="tipo_vehiculo_id" class="form-label fw-bold">
                                <i class="fas fa-car text-primary me-1"></i>
                                Tipo de Vehículo <span class="text-danger">*</span>
                            </label>
                            <select class="form-select <?= !empty($validation) && $validation->hasError('tipo_vehiculo_id') ? 'is-invalid' : '' ?>"
                                    id="tipo_vehiculo_id" name="tipo_vehiculo_id" required>
                                <option value="">Seleccionar tipo...</option>
                                <?php foreach (($tiposVehiculo ?? []) as $id => $nombre): ?>
                                <option value="<?= $id ?>" <?= old('tipo_vehiculo_id') == $id ? 'selected' : '' ?>>
                                    <?= esc($nombre) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($validation) && $validation->hasError('tipo_vehiculo_id')): ?>
                                <div class="invalid-feedback"><?= esc($validation->getError('tipo_vehiculo_id')) ?></div>
                            <?php endif; ?>
                            </div>

                            <!-- Valor -->
                            <div class="col-md-4 mb-4">
                                <label for="valor" class="form-label fw-bold">
                                    <i class="fas fa-dollar-sign text-success me-1"></i>
                                    Valor <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" id="valorSymbol">$</span>
                                    <input type="number" 
                                           class="form-control <?= !empty($validation) && $validation->hasError('valor') ? 'is-invalid' : '' ?>" 
                                           id="valor" 
                                           name="valor" 
                                           value="<?= esc(old('valor')) ?>"
                                           step="0.01"
                                           min="0"
                                           placeholder="1.50"
                                           required>
                                </div>
                                <?php if (!empty($validation) && $validation->hasError('valor')): ?>
                                    <div class="invalid-feedback">
                                        <?= esc($validation->getError('valor')) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Para UF usar decimales (ej: 1.50)
                                </div>
                            </div>

                            <!-- Unidad de Medida -->
                            <div class="col-md-4 mb-4">
                                <label for="unidad_medida" class="form-label fw-bold">
                                    <i class="fas fa-ruler text-info me-1"></i>
                                    Unidad de Medida <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="unidad_medida" name="unidad_medida" required>
                                    <option value="UF"  <?= old('unidad_medida', 'UF') == 'UF'  ? 'selected' : '' ?>>UF - Unidad de Fomento</option>
                                    <option value="CLP" <?= old('unidad_medida') == 'CLP' ? 'selected' : '' ?>>CLP - Peso Chileno</option>
                                    <option value="UTM" <?= old('unidad_medida') == 'UTM' ? 'selected' : '' ?>>UTM - Unidad Tributaria Mensual</option>
                                </select>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Unidad en que se expresa el valor
                                </div>
                            </div>

                            <!-- Moneda -->
                            <div class="col-md-4 mb-4">
                                <label for="moneda" class="form-label fw-bold">
                                    <i class="fas fa-coins text-warning me-1"></i>
                                    Moneda de Referencia
                                </label>
                                <select class="form-select" id="moneda" name="moneda">
                                    <option value="UF"  <?= old('moneda') == 'UF'  ? 'selected' : '' ?>>UF</option>
                                    <option value="CLP" <?= old('moneda') == 'CLP' ? 'selected' : '' ?>>CLP</option>
                                    <option value="UTM" <?= old('moneda') == 'UTM' ? 'selected' : '' ?>>UTM</option>
                                </select>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Normalmente igual a la unidad de medida
                                </div>
                            </div>

                            <!-- Fecha de vigencia desde -->
                            <div class="col-md-6 mb-4">
                                <label for="fecha_vigencia_desde" class="form-label fw-bold">
                                    <i class="fas fa-calendar-plus text-success me-1"></i>
                                    Vigente desde <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control <?= !empty($validation) && $validation->hasError('fecha_vigencia_desde') ? 'is-invalid' : '' ?>" 
                                       id="fecha_vigencia_desde" 
                                       name="fecha_vigencia_desde" 
                                       value="<?= esc(old('fecha_vigencia_desde', date('Y-m-d'))) ?>"
                                       required>
                                <?php if (!empty($validation) && $validation->hasError('fecha_vigencia_desde')): ?>
                                    <div class="invalid-feedback">
                                        <?= esc($validation->getError('fecha_vigencia_desde')) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Fecha de vigencia hasta -->
                            <div class="col-md-6 mb-4">
                                <label for="fecha_vigencia_hasta" class="form-label fw-bold">
                                    <i class="fas fa-calendar-times text-danger me-1"></i>
                                    Vigente hasta
                                </label>
                                <input type="date" 
                                       class="form-control <?= !empty($validation) && $validation->hasError('fecha_vigencia_hasta') ? 'is-invalid' : '' ?>" 
                                       id="fecha_vigencia_hasta" 
                                       name="fecha_vigencia_hasta" 
                                       value="<?= esc(old('fecha_vigencia_hasta')) ?>">
                                <?php if (!empty($validation) && $validation->hasError('fecha_vigencia_hasta')): ?>
                                    <div class="invalid-feedback">
                                        <?= esc($validation->getError('fecha_vigencia_hasta')) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Dejar vacío para vigencia indefinida
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="col-12 mb-4">
                                <label for="descripcion" class="form-label fw-bold">
                                    <i class="fas fa-align-left text-info me-1"></i>
                                    Descripción
                                </label>
                                <textarea class="form-control" 
                                          id="descripcion" 
                                          name="descripcion" 
                                          rows="3"
                                          placeholder="Descripción del valor o concepto..."><?= esc(old('descripcion')) ?></textarea>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Información adicional sobre este valor (opcional)
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                <small>
                                    <i class="fas fa-asterisk text-danger me-1"></i>
                                    Los campos marcados con asterisco son obligatorios
                                </small>
                            </div>
                            <div class="btn-group">
                                <a href="<?= base_url('valores-comunas') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Crear Valor
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.card { 
    border: none; 
    border-radius: 15px; 
}
.card-header { 
    border-radius: 15px 15px 0 0 !important; 
    font-weight: 600; 
}
.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #dee2e6;
}
.form-control:focus, .form-select:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}
.btn { 
    border-radius: 8px; 
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script> 
$(function () {
  const $region   = $('#region_id');
  const $prov     = $('#provincias_id');
  const $comuna   = $('#comunas_id');
  const $unidad   = $('#unidad_medida');
  const $moneda   = $('#moneda');
  const $valor    = $('#valor');
  const $form     = $('#valorForm');
  const $btnSave  = $form.find('button[type="submit"]');

  const oldRegion = "<?= esc(old('region_id') ?? '', 'js') ?>";
  const oldProv   = "<?= esc(old('provincias_id') ?? '', 'js') ?>";
  const oldComuna = "<?= esc(old('comunas_id') ?? '', 'js') ?>";

  // Región => Provincias
  $region.on('change', function () {
    const regionId = $(this).val();
    $prov.html('<option value="">Cargando provincias...</option>').prop('disabled', true);
    $comuna.html('<option value="">Primero selecciona una provincia...</option>').prop('disabled', true);

    if (!regionId) {
      $prov.html('<option value="">Primero selecciona una región...</option>').prop('disabled', false);
      return;
    }

    $.getJSON('<?= site_url('valores-comunas/getProvinciasByRegion') ?>/' + encodeURIComponent(regionId))
      .done(function (rows) {
        let options = '<option value="">Seleccionar provincia...</option>';
        if (Array.isArray(rows) && rows.length) {
          rows.forEach(function (r) {
            options += `<option value="${r.provincias_id}">${r.provincias_nombre}</option>`;
          });
        } else {
          options = '<option value="">No hay provincias disponibles</option>';
        }
        $prov.html(options).prop('disabled', false);

        if (oldProv) {
          $prov.val(oldProv).trigger('change'); // cargar comunas
        }
      })
      .fail(function () {
        $prov.html('<option value="">Error al cargar provincias</option>').prop('disabled', false);
      });
  });

  // Provincia => Comunas
  $prov.on('change', function () {
    const provId = $(this).val();
    $comuna.html('<option value="">Cargando comunas...</option>').prop('disabled', true);

    if (!provId) {
      $comuna.html('<option value="">Primero selecciona una provincia...</option>').prop('disabled', false);
      return;
    }

    $.getJSON('<?= site_url('valores-comunas/getComunasByProvincia') ?>/' + encodeURIComponent(provId))
      .done(function (rows) {
        let options = '<option value="">Seleccionar comuna...</option>';
        if (Array.isArray(rows) && rows.length) {
          rows.forEach(function (r) {
            options += `<option value="${r.comunas_id}">${r.comunas_nombre}</option>`;
          });
        } else {
          options = '<option value="">No hay comunas disponibles</option>';
        }
        $comuna.html(options).prop('disabled', false);

        if (oldComuna) {
          $comuna.val(oldComuna);
        }
      })
      .fail(function () {
        $comuna.html('<option value="">Error al cargar comunas</option>').prop('disabled', false);
      });
  });

  // Repoblar si hay old region
  if (oldRegion) {
    $region.val(oldRegion).trigger('change');
  }

  // Unidad => moneda + símbolo
  function applySymbolAndCurrency() {
    const unidad = $unidad.val();
    $moneda.val(unidad);
    let symbol = '$';
    if (unidad === 'UF') symbol = 'UF';
    else if (unidad === 'UTM') symbol = 'UTM';
    $('#valorSymbol').text(symbol);
  }
  $unidad.on('change', applySymbolAndCurrency);
  applySymbolAndCurrency();

  // Sanitizar número
  $valor.on('input', function () {
    let v = $(this).val().replace(',', '.').replace(/[^0-9.]/g, '');
    const parts = v.split('.');
    if (parts.length > 2) v = parts[0] + '.' + parts.slice(1).join('');
    $(this).val(v);
  });

  // Validar fechas
  $('#fecha_vigencia_hasta').on('change', function () {
    const desde = $('#fecha_vigencia_desde').val();
    const hasta = $(this).val();
    if (desde && hasta && hasta <= desde) {
      Swal.fire({ icon: 'warning', title: 'Fechas incorrectas', text: 'La fecha de vigencia hasta debe ser posterior a la fecha desde' });
      $(this).val('');
    }
  });

  // Validación final
  $form.on('submit', function (e) {
    const n = parseFloat($valor.val());
    if (!(n > 0)) {
      e.preventDefault();
      Swal.fire({ icon: 'error', title: 'Valor incorrecto', text: 'El valor debe ser mayor que 0' });
      $valor.focus();
      return false;
    }
    if (!$region.val() || !$prov.val() || !$comuna.val()) {
      e.preventDefault();
      Swal.fire({ icon: 'error', title: 'Faltan datos', text: 'Selecciona región, provincia y comuna' });
      return false;
    }

    $btnSave.prop('disabled', true);
    Swal.fire({ title: 'Creando valor...', text: 'Por favor espera', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
  });
}); 
</script>
<?= $this->endSection() ?>
