 <?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'Editar Valor por Comuna') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h1 class="h3 mb-0">
            <i class="fas fa-edit text-warning me-2"></i>
            Editar Valor por Comuna
          </h1>
          <p class="text-muted mb-0">Modificar valor existente para una compañía en una comuna específica</p>
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
        <div class="card-header bg-warning text-dark">
          <h5 class="card-title mb-0">
            <i class="fas fa-clipboard-list me-2"></i>
            Editar Información del Valor
          </h5>
        </div>

        <?php $valorId = $valor['valores_id'] ?? ($valor['id'] ?? ''); ?>
        <form action="<?= base_url('valores-comunas/update/' . $valorId) ?>" method="post" id="valorForm">
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
                        id="cia_id" name="cia_id" required>
                  <option value="">Seleccionar compañía...</option>
                  <?php if (!empty($cias)): ?>
                    <?php foreach ($cias as $id => $nombre): ?>
                      <option value="<?= $id ?>" <?= (old('cia_id', $valor['cia_id'] ?? '') == $id) ? 'selected' : '' ?>>
                        <?= esc($nombre) ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
                <?php if (!empty($validation) && $validation->hasError('cia_id')): ?>
                  <div class="invalid-feedback"><?= esc($validation->getError('cia_id')) ?></div>
                <?php endif; ?>
              </div>

              <!-- Tipo de Usuario -->
              <div class="col-md-6 mb-4">
                <label for="tipo_usuario" class="form-label fw-bold">
                  <i class="fas fa-user-tag text-info me-1"></i>
                  Tipo de Usuario <span class="text-danger">*</span>
                </label>
                <select class="form-select <?= !empty($validation) && $validation->hasError('tipo_usuario') ? 'is-invalid' : '' ?>"
                        id="tipo_usuario" name="tipo_usuario" required>
                  <?php if (!empty($tipos_usuario)): ?>
                    <?php foreach ($tipos_usuario as $key => $nombre): ?>
                      <option value="<?= $key ?>" <?= (old('tipo_usuario', $valor['valores_tipo_usuario'] ?? '') == $key) ? 'selected' : '' ?>>
                        <?= esc($nombre) ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
                <?php if (!empty($validation) && $validation->hasError('tipo_usuario')): ?>
                  <div class="invalid-feedback"><?= esc($validation->getError('tipo_usuario')) ?></div>
                <?php endif; ?>
              </div>

              <!-- Tipo de Inspección -->
              <div class="col-md-6 mb-4">
                  <label for="tipo_inspeccion_id" class="form-label fw-bold">
                      <i class="fas fa-car text-primary me-1"></i>
                      Tipo de Inspección <span class="text-danger">*</span>
                  </label>
                  <select class="form-select <?= !empty($validation) && $validation->hasError('tipo_inspeccion_id') ? 'is-invalid' : '' ?>"
                          id="tipo_inspeccion_id" name="tipo_inspeccion_id" required>
                      <option value="">Seleccionar tipo...</option>
                      <?php if (!empty($tipos_inspeccion)): ?>
                          <?php foreach ($tipos_inspeccion as $id => $nombre): ?>
                              <option value="<?= $id ?>" <?= (old('tipo_inspeccion_id', $valor['tipo_inspeccion_id'] ?? 0) == $id) ? 'selected' : '' ?>>
                                  <?= esc($nombre) ?>
                              </option>
                          <?php endforeach; ?>
                      <?php endif; ?>
                  </select>
                  <?php if (!empty($validation) && $validation->hasError('tipo_inspeccion_id')): ?>
                      <div class="invalid-feedback"><?= esc($validation->getError('tipo_inspeccion_id')) ?></div>
                  <?php endif; ?>
              </div>

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
                      <option value="<?= $id ?>" <?= (old('region_id', $valor['region_id'] ?? '') == $id) ? 'selected' : '' ?>>
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
                  <i class="fas fa-map text-secondary me-1"></i>
                  Provincia <span class="text-danger">*</span>
                </label>
                <select class="form-select <?= !empty($validation) && $validation->hasError('provincias_id') ? 'is-invalid' : '' ?>"
                        id="provincias_id" name="provincias_id" required>
                  <option value="">Seleccionar provincia...</option>
                  <?php if (!empty($provincias)): ?>
                    <?php foreach ($provincias as $provincia): ?>
                      <option value="<?= $provincia['provincias_id'] ?>"
                              <?= (old('provincias_id', $valor['provincias_id'] ?? '') == $provincia['provincias_id']) ? 'selected' : '' ?>>
                        <?= esc($provincia['provincias_nombre']) ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
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
                        id="comunas_id" name="comunas_id" required>
                  <option value="">Seleccionar comuna...</option>
                  <?php if (!empty($comunas)): ?>
                    <?php foreach ($comunas as $comuna): ?>
                      <option value="<?= $comuna['comunas_id'] ?>"
                              <?= (old('comunas_id', $valor['comunas_id'] ?? '') == $comuna['comunas_id']) ? 'selected' : '' ?>>
                        <?= esc($comuna['comunas_nombre']) ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
                <?php if (!empty($validation) && $validation->hasError('comunas_id')): ?>
                  <div class="invalid-feedback"><?= esc($validation->getError('comunas_id')) ?></div>
                <?php endif; ?>
              </div>

              <!-- Valor -->
              <div class="col-md-4 mb-4">
                <label for="valor" class="form-label fw-bold">
                  <i class="fas fa-dollar-sign text-success me-1"></i>
                  Valor <span class="text-danger">*</span>
                </label>
                <?php 
                  $unidadActual = old('unidad_medida', $valor['valores_unidad_medida'] ?? 'CLP');
                  $simbolo      = ($unidadActual === 'UF') ? 'UF' : (($unidadActual === 'UTM') ? 'UTM' : '$');
                ?>
                <div class="input-group">
                  <span class="input-group-text" id="valorSymbol"><?= esc($simbolo) ?></span>
                  <input
                    type="number"
                    class="form-control <?= !empty($validation) && $validation->hasError('valor') ? 'is-invalid' : '' ?>"
                    id="valor" name="valor" step="0.01" min="0"
                    value="<?= esc(old('valor', $valor['valores_valor'] ?? '')) ?>" required
                    aria-describedby="valorSymbol"
                  >
                  <?php if (!empty($validation) && $validation->hasError('valor')): ?>
                    <div class="invalid-feedback"><?= esc($validation->getError('valor')) ?></div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Unidad de Medida -->
              <div class="col-md-4 mb-4">
                <label for="unidad_medida" class="form-label fw-bold">
                  <i class="fas fa-ruler text-info me-1"></i>
                  Unidad de Medida <span class="text-danger">*</span>
                </label>
                <select class="form-select" id="unidad_medida" name="unidad_medida" required>
                  <option value="UF"  <?= ($unidadActual === 'UF')  ? 'selected' : '' ?>>UF - Unidad de Fomento</option>
                  <option value="CLP" <?= ($unidadActual === 'CLP') ? 'selected' : '' ?>>CLP - Peso Chileno</option>
                  <option value="UTM" <?= ($unidadActual === 'UTM') ? 'selected' : '' ?>>UTM - Unidad Tributaria Mensual</option>
                </select>
              </div>

              <!-- Moneda -->
              <div class="col-md-4 mb-4">
                <label for="moneda" class="form-label fw-bold">
                  <i class="fas fa-coins text-warning me-1"></i>
                  Moneda de Referencia
                </label>
                <?php $monedaActual = old('moneda', $valor['valores_moneda'] ?? 'CLP'); ?>
                <select class="form-select" id="moneda" name="moneda">
                  <option value="UF"  <?= ($monedaActual === 'UF')  ? 'selected' : '' ?>>UF</option>
                  <option value="CLP" <?= ($monedaActual === 'CLP') ? 'selected' : '' ?>>CLP</option>
                  <option value="UTM" <?= ($monedaActual === 'UTM') ? 'selected' : '' ?>>UTM</option>
                </select>
              </div>

              <!-- Vigente desde -->
              <div class="col-md-6 mb-4">
                <label for="fecha_vigencia_desde" class="form-label fw-bold">
                  <i class="fas fa-calendar-plus text-success me-1"></i>
                  Vigente desde <span class="text-danger">*</span>
                </label>
                <input type="date"
                       class="form-control <?= !empty($validation) && $validation->hasError('fecha_vigencia_desde') ? 'is-invalid' : '' ?>"
                       id="fecha_vigencia_desde" name="fecha_vigencia_desde"
                       value="<?= esc(old('fecha_vigencia_desde', $valor['valores_fecha_vigencia_desde'] ?? '')) ?>" required>
                <?php if (!empty($validation) && $validation->hasError('fecha_vigencia_desde')): ?>
                  <div class="invalid-feedback"><?= esc($validation->getError('fecha_vigencia_desde')) ?></div>
                <?php endif; ?>
              </div>

              <!-- Vigente hasta -->
              <div class="col-md-6 mb-4">
                <label for="fecha_vigencia_hasta" class="form-label fw-bold">
                  <i class="fas fa-calendar-times text-danger me-1"></i>
                  Vigente hasta
                </label>
                <input type="date"
                       class="form-control <?= !empty($validation) && $validation->hasError('fecha_vigencia_hasta') ? 'is-invalid' : '' ?>"
                       id="fecha_vigencia_hasta" name="fecha_vigencia_hasta"
                       value="<?= esc(old('fecha_vigencia_hasta', $valor['valores_fecha_vigencia_hasta'] ?? '')) ?>">
                <?php if (!empty($validation) && $validation->hasError('fecha_vigencia_hasta')): ?>
                  <div class="invalid-feedback"><?= esc($validation->getError('fecha_vigencia_hasta')) ?></div>
                <?php endif; ?>
                <div class="form-text"><i class="fas fa-info-circle me-1"></i>Dejar vacío para vigencia indefinida</div>
              </div>

              <!-- Descripción -->
              <div class="col-12 mb-4">
                <label for="descripcion" class="form-label fw-bold">
                  <i class="fas fa-align-left text-info me-1"></i>
                  Descripción
                </label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                          placeholder="Descripción del valor o concepto..."><?= esc(old('descripcion', $valor['valores_descripcion'] ?? '')) ?></textarea>
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
                <button type="submit" class="btn btn-warning">
                  <i class="fas fa-save me-2"></i>Actualizar Valor
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
.card{border:none;border-radius:15px}
.card-header{border-radius:15px 15px 0 0!important;font-weight:600}
.form-control,.form-select{border-radius:8px;border:1px solid #dee2e6}
.form-control:focus,.form-select:focus{border-color:#ffc107;box-shadow:0 0 0 0.2rem rgba(255,193,7,.25)}
.btn{border-radius:8px}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(function () {
  // Cascada región -> provincia -> comuna
  const $region    = $('#region_id');
  const $provincia = $('#provincias_id');
  const $comuna    = $('#comunas_id');

  const preRegion    = "<?= esc(old('region_id', $valor['region_id'] ?? ''), 'js') ?>";
  const preProvincia = "<?= esc(old('provincias_id', $valor['provincias_id'] ?? ''), 'js') ?>";
  const preComuna    = "<?= esc(old('comunas_id', $valor['comunas_id'] ?? ''), 'js') ?>";

  function loadProvincias(regionId, selectedId=null) {
    $provincia.html('<option value="">Cargando provincias...</option>').prop('disabled', true);
    $comuna.html('<option value="">Primero selecciona una provincia...</option>').prop('disabled', true);

    if (!regionId) {
      $provincia.html('<option value="">Primero selecciona una región...</option>').prop('disabled', false);
      return $.Deferred().resolve().promise();
    }

    return $.getJSON('<?= site_url('valores-comunas/getProvinciasByRegion') ?>/' + encodeURIComponent(regionId))
      .done(function(rows){
        let opts = '<option value="">Seleccionar provincia...</option>';
        (rows || []).forEach(function(r){
          opts += `<option value="${r.provincias_id}">${r.provincias_nombre}</option>`;
        });
        $provincia.html(opts).prop('disabled', false);
        if (selectedId) { $provincia.val(String(selectedId)); }
      })
      .fail(function(){
        $provincia.html('<option value="">Error al cargar provincias</option>').prop('disabled', false);
      });
  }

  function loadComunas(provId, selectedId=null) {
    $comuna.html('<option value="">Cargando comunas...</option>').prop('disabled', true);
    if (!provId) {
      $comuna.html('<option value="">Primero selecciona una provincia...</option>').prop('disabled', false);
      return $.Deferred().resolve().promise();
    }

    return $.getJSON('<?= site_url('valores-comunas/getComunasByProvincia') ?>/' + encodeURIComponent(provId))
      .done(function(rows){
        let opts = '<option value="">Seleccionar comuna...</option>';
        (rows || []).forEach(function(r){
          opts += `<option value="${r.comunas_id}">${r.comunas_nombre}</option>`;
        });
        $comuna.html(opts).prop('disabled', false);
        if (selectedId) { $comuna.val(String(selectedId)); }
      })
      .fail(function(){
        $comuna.html('<option value="">Error al cargar comunas</option>').prop('disabled', false);
      });
  }

  $region.on('change', function(){
    const rid = $(this).val();
    loadProvincias(rid).then(function(){
      $comuna.html('<option value="">Primero selecciona una provincia...</option>').prop('disabled', false);
    });
  });

  $provincia.on('change', function(){
    const pid = $(this).val();
    loadComunas(pid);
  });

  // Inicialización (modo edición)
  if (preRegion && (!$provincia.find('option').length || $provincia.find('option').length === 1)) {
    $region.val(String(preRegion));
    loadProvincias(preRegion, preProvincia).then(function(){
      if (preProvincia) {
        return loadComunas(preProvincia, preComuna);
      }
    });
  }

  // Unidad => moneda + símbolo
  const $unidad = $('#unidad_medida');
  const $moneda = $('#moneda');
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

  // Validación simple en submit
  $('#valorForm').on('submit', function(e) {
    const valor = parseFloat($('#valor').val());
    if (!(valor > 0)) {
      e.preventDefault();
      if (window.Swal) {
        Swal.fire({ icon: 'error', title: 'Valor incorrecto', text: 'El valor debe ser mayor que 0' });
      } else {
        alert('El valor debe ser mayor que 0');
      }
      $('#valor').focus();
      return false;
    }
    $(this).find('button[type="submit"]').prop('disabled', true);
    if (window.Swal) Swal.fire({ title: 'Actualizando valor...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
  });
});
</script>
<?= $this->endSection() ?>
