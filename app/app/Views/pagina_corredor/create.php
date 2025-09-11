<?= $this->extend('layouts/maincorredor') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
    .form-floating {
        margin-bottom: 1rem;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .required::after {
        content: " *";
        color: #dc3545;
    }
    
    .rut-input {
        text-transform: uppercase;
    }
    
    .patente-input {
        text-transform: uppercase;
    }
    
    .preview-section {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-plus-circle me-2 text-primary"></i>
                        <?= esc($title) ?>
                    </h1>
                    <p class="text-muted mb-0">Complete todos los campos obligatorios</p>
                </div>
                <div>
                    <a href="<?= base_url('corredor') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensajes de error -->
    <?php if (session('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Errores encontrados:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach (session('errors') as $error): ?>
            <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Formulario -->
    <form action="<?= base_url('corredor/store') ?>" method="post" id="inspeccionForm">
        <?= csrf_field() ?>
        
        <div class="row">
            <!-- Información del Asegurado -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>
                            Información del Asegurado
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-floating">
                            <input type="text" 
                                   class="form-control" 
                                   id="asegurado" 
                                   name="asegurado" 
                                   placeholder="Nombre completo del asegurado"
                                   value="<?= old('asegurado') ?>"
                                   required>
                            <label for="asegurado" class="required">Nombre del Asegurado</label>
                        </div>

                        <div class="form-floating">
                            <input type="text" 
                                   class="form-control rut-input" 
                                   id="inspecciones_rut" 
                                   name="inspecciones_rut" 
                                   placeholder="12345678-9"
                                   value="<?= old('inspecciones_rut') ?>"
                                   maxlength="12"
                                   required>
                            <label for="inspecciones_rut" class="required">RUT</label>
                            <div class="form-text">Formato: 12345678-9</div>
                        </div>

                        <div class="form-floating">
                            <input type="text" 
                                   class="form-control" 
                                   id="inspecciones_direccion" 
                                   name="inspecciones_direccion" 
                                   placeholder="Dirección completa"
                                   value="<?= old('inspecciones_direccion') ?>"
                                   required>
                            <label for="inspecciones_direccion" class="required">Dirección</label>
                        </div>

                        <div class="form-floating">
                            <select class="form-select" id="comunas_id" name="comunas_id" required>
                                <option value="">Seleccione una comuna</option>
                                <?php foreach ($comunas as $comuna): ?>
                                <option value="<?= $comuna['comunas_id'] ?>" 
                                        <?= old('comunas_id') == $comuna['comunas_id'] ? 'selected' : '' ?>>
                                    <?= esc($comuna['comunas_nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="comunas_id" class="required">Comuna</label>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" 
                                           class="form-control" 
                                           id="celular" 
                                           name="celular" 
                                           placeholder="+56 9 1234 5678"
                                           value="<?= old('celular') ?>"
                                           required>
                                    <label for="celular" class="required">Celular</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" 
                                           class="form-control" 
                                           id="telefono" 
                                           name="telefono" 
                                           placeholder="+56 2 1234 5678"
                                           value="<?= old('telefono') ?>">
                                    <label for="telefono">Teléfono (Opcional)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Vehículo -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-car me-2"></i>
                            Información del Vehículo
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-floating">
                            <input type="text" 
                                   class="form-control patente-input" 
                                   id="patente" 
                                   name="patente" 
                                   placeholder="ABC123 o ABCD12"
                                   value="<?= old('patente') ?>"
                                   maxlength="8"
                                   required>
                            <label for="patente" class="required">Patente</label>
                            <div class="form-text">Formato antiguo: ABCD-12 | Formato nuevo: ABCD-12</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control" 
                                           id="marca" 
                                           name="marca" 
                                           placeholder="Toyota, Chevrolet, etc."
                                           value="<?= old('marca') ?>"
                                           required>
                                    <label for="marca" class="required">Marca</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control" 
                                           id="modelo" 
                                           name="modelo" 
                                           placeholder="Corolla, Aveo, etc."
                                           value="<?= old('modelo') ?>"
                                           required>
                                    <label for="modelo" class="required">Modelo</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-floating">
                            <select class="form-select" id="cia_id" name="cia_id" required>
                                <option value="">Seleccione una compañía</option>
                                <?php foreach ($companias as $compania): ?>
                                <option value="<?= $compania['cia_id'] ?>" 
                                        <?= old('cia_id') == $compania['cia_id'] ? 'selected' : '' ?>>
                                    <?= esc($compania['cia_nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="cia_id" class="required">Compañía de Seguros</label>
                        </div>

                        <div class="form-floating">
                            <input type="text" 
                                   class="form-control" 
                                   id="n_poliza" 
                                   name="n_poliza" 
                                   placeholder="Número de póliza"
                                   value="<?= old('n_poliza') ?>"
                                   required>
                            <label for="n_poliza" class="required">Número de Póliza</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Section -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-eye me-2"></i>
                            Resumen de la Inspección
                        </h5>
                    </div>
                    <div class="card-body preview-section">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Datos del Asegurado</h6>
                                <p class="mb-1"><strong>Nombre:</strong> <span id="preview-asegurado">-</span></p>
                                <p class="mb-1"><strong>RUT:</strong> <span id="preview-rut">-</span></p>
                                <p class="mb-1"><strong>Dirección:</strong> <span id="preview-direccion">-</span></p>
                                <p class="mb-1"><strong>Celular:</strong> <span id="preview-celular">-</span></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Datos del Vehículo</h6>
                                <p class="mb-1"><strong>Patente:</strong> <span id="preview-patente">-</span></p>
                                <p class="mb-1"><strong>Vehículo:</strong> <span id="preview-vehiculo">-</span></p>
                                <p class="mb-1"><strong>Compañía:</strong> <span id="preview-compania">-</span></p>
                                <p class="mb-1"><strong>Póliza:</strong> <span id="preview-poliza">-</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-save me-2"></i>
                            Crear Inspección
                        </button>
                        <a href="<?= base_url('corredor') ?>" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
<?= $this->section('js') ?>
<script>
$(function () {
  // ---------- Helpers ----------
  function normalizarRutInput(val) {
    return (val || '').replace(/[^0-9kK]/g, '');
  }
  function formatearRutVisual(rutRaw) {
    const rut = normalizarRutInput(rutRaw);
    if (rut.length < 2) return rutRaw;
    const dv = rut.slice(-1).toUpperCase();
    const num = rut.slice(0, -1);
    const conPuntos = num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    return conPuntos + '-' + dv;
  }
  function validarRUT(rutRaw) {
    const rut = normalizarRutInput(rutRaw);
    if (rut.length < 8 || rut.length > 9) return false;
    const dv = rut.slice(-1).toUpperCase();
    const num = rut.slice(0, -1);
    let suma = 0, mult = 2;
    for (let i = num.length - 1; i >= 0; i--) {
      suma += parseInt(num[i], 10) * mult;
      mult = (mult === 7) ? 2 : mult + 1;
    }
    const resto = suma % 11;
    let dvCalc = 11 - resto;
    if (dvCalc === 11) dvCalc = '0';
    else if (dvCalc === 10) dvCalc = 'K';
    else dvCalc = String(dvCalc);
    return dv === dvCalc;
  }
  function limpiarPatente(val) {
    return (val || '').toUpperCase().replace(/[^A-Z0-9]/g, '');
  }
  function validarPatente(val) {
    // Acepta con o sin guion (lo ignoramos al validar)
    const p = limpiarPatente(val);
    const nuevo = /^[A-Z]{4}[0-9]{2}$/;  // ABCD12
    const antiguo = /^[A-Z]{2}[0-9]{4}$/; // AB1234
    return nuevo.test(p) || antiguo.test(p);
  }
  function formatearTelefonoCL(input) {
    let n = (input || '').replace(/[^0-9]/g, '');
    if (n.startsWith('56')) n = n.slice(2);
    // Celular de 9 dígitos partiendo con 9 → +56 9 1234 5678
    if (n.length === 9 && n.startsWith('9')) {
      return '+56 9 ' + n.slice(1,5) + ' ' + n.slice(5);
    }
    // Fijo Stgo 9 dígitos partiendo con 2 → +56 2 1234 5678
    if (n.length === 9 && n.startsWith('2')) {
      return '+56 2 ' + n.slice(1,5) + ' ' + n.slice(5);
    }
    // 8 dígitos asume celular sin 9 -> antepone 9
    if (n.length === 8) {
      return '+56 9 ' + n.slice(0,4) + ' ' + n.slice(4);
    }
    return input;
  }

  // ---------- Formateos en vivo ----------
  $('#inspecciones_rut, #inspecciones_rut'.replace(/inspecciones_/g,'')); // no-op guard

  // RUT (input id correcto en tu HTML: #inspecciones_rut)
  $('#inspecciones_rut').on('input', function () {
    const pos = this.selectionStart;
    $(this).val(formatearRutVisual($(this).val()));
    this.setSelectionRange(pos, pos);
  });

  // Patente (id correcto en tu HTML: #patente)
  $('#patente').on('input', function () {
    $(this).val($(this).val().toUpperCase().replace(/[^A-Z0-9-]/g, ''));
  });

  // Teléfonos (ids correctos: #celular, #telefono)
  $('#celular, #telefono').on('blur', function () {
    $(this).val(formatearTelefonoCL($(this).val()));
  });

  // ---------- Preview ----------
  function updatePreview() {
    $('#preview-asegurado').text($('#asegurado').val() || '-');
    $('#preview-rut').text($('#inspecciones_rut').val() || '-');
    $('#preview-direccion').text($('#inspecciones_direccion').val() || '-');
    $('#preview-celular').text($('#celular').val() || '-');
    $('#preview-patente').text($('#patente').val() || '-');

    const marca = $('#marca').val();
    const modelo = $('#modelo').val();
    $('#preview-vehiculo').text((marca && modelo) ? (marca + ' ' + modelo) : '-');

    const ciaText = $('#cia_id option:selected').text();
    $('#preview-compania').text(ciaText && ciaText !== 'Seleccione una compañía' ? ciaText : '-');

    $('#preview-poliza').text($('#n_poliza').val() || '-');
  }
  $('input, select').on('input change', updatePreview);
  updatePreview();

  // ---------- Validación Submit ----------
  $('#inspeccionForm').on('submit', function (e) {
    const errores = [];

    // RUT
    if (!validarRUT($('#inspecciones_rut').val())) {
      errores.push('El RUT ingresado no es válido.');
    }

    // Patente
    if (!validarPatente($('#patente').val())) {
      errores.push('La patente debe tener formato AB1234 o ABCD12 (con o sin guion).');
    }

    // Campos obligatorios (usa IDs reales del HTML)
    const obligatorios = [
      ['#asegurado', 'Nombre del asegurado'],
      ['#inspecciones_direccion', 'Dirección'],
      ['#comunas_id', 'Comuna'],
      ['#celular', 'Celular'],
      ['#marca', 'Marca del vehículo'],
      ['#modelo', 'Modelo del vehículo'],
      ['#cia_id', 'Compañía de seguros'],
      ['#n_poliza', 'Número de póliza'],
      ['#patente', 'Patente']
    ];
    obligatorios.forEach(([sel, nombre]) => {
      const $el = $(sel);
      const val = ($el.is('select') ? $el.find('option:selected').val() : $el.val()) || '';
      if (!val.toString().trim()) errores.push(`${nombre} es obligatorio.`);
    });

    if (errores.length) {
      e.preventDefault();
      alert('Errores encontrados:\n\n' + errores.join('\n'));
      return;
    }

    if (!confirm('¿Estás seguro de que deseas crear esta inspección?')) {
      e.preventDefault();
    }
  });
});
</script>
<?= $this->endSection() ?>
