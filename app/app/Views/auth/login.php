 <!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar sesión - <?= env('app.title', 'InspectZu') ?></title>

  <!-- resto del head... -->
</head>
<body>

<!-- Debug temporal - puedes quitarlo después -->
<?php
echo "<!-- Debug: ";
echo "title: '" . env('app.title') . "', ";
echo "logo_path: '" . env('app.ubicacion_logo_pagina') . "', ";
echo "logo2: '" . env('app.imagen_nomb_logo2') . "'";
echo " -->";
?>

<div class="auth-wrap">
  <section class="auth-hero">
    <button class="btn btn-sm btn-light theme-btn" id="themeToggle" title="Cambiar tema">
      <i class="fa-solid fa-moon"></i>
    </button>
    <div class="overlay"></div>
    <div class="inner">
      <div> 
        <!-- Logo pequeño -->
        <img src="<?= base_url((env('app.ubicacion_logo_pagina') ?? 'assets/img/') . (env('app.imagen_nomb_logo2') ?? 'login-hero.jpg')) ?>" 
             style="height:48px" class="mb-3" alt="Logo"> 
        
        <!-- CORREGIDO: Solo el texto, sin base_url() -->
        <h1 class="brand-title"><?= env('app.title', 'InspectZu') ?></h1>
        
        <p class="mb-4 opacity-75">Bienvenido. Ingresa tus credenciales para continuar.</p>
        
        <!-- Imagen hero -->
        <img class="hero-img" 
             src="<?= base_url((env('app.ubicacion_logo_pagina') ?? 'assets/img/') . (env('app.imagen_nomb_logo') ?? 'logo.jpg')) ?>"
             alt="Ilustración de acceso">
      </div>
    </div>
  </section>

  <section class="auth-form">
    <div class="card card-login">
      <!-- resto del formulario... -->
      <div class="card-footer text-center small">
        © <?= date('Y') ?> <?= env('app.title', 'InspectZu') ?>
      </div>
    </div>
  </section>
</div>

<!-- resto del HTML... -->
</body>
</html>