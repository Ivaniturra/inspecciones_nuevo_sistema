<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar sesión - <?= env('app.title') ?></title>

  <!-- Aplica tema guardado (o el del sistema) ANTES de cargar CSS para evitar FOUC -->
  <script>
  (function(){
    try {
      var saved = localStorage.getItem('theme');
      if (!saved) {
        saved = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
      }
      document.documentElement.setAttribute('data-bs-theme', saved);
    } catch(e) {
      document.documentElement.setAttribute('data-bs-theme','light');
    }
  })();
  </script>

  <!-- Bootstrap 5.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FA6 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    :root{
      --brand-1: #667eea;
      --brand-2: #764ba2;
    }
    .auth-wrap{
      min-height:100vh;
      display:grid;
      grid-template-columns: 1fr 1fr;
    }
    @media (max-width: 991.98px){
      .auth-wrap{ grid-template-columns: 1fr; }
    }
    /* Lado imagen */
    .auth-hero{
      position:relative;
      background: linear-gradient(135deg,var(--brand-1),var(--brand-2));
      overflow:hidden;
    }
    .auth-hero .overlay{
      position:absolute; inset:0;
      background: radial-gradient(ellipse at 20% 20%, rgba(255,255,255,.15), transparent 40%),
                  radial-gradient(ellipse at 80% 30%, rgba(255,255,255,.10), transparent 40%),
                  radial-gradient(ellipse at 50% 80%, rgba(255,255,255,.07), transparent 40%);
    }
    .auth-hero .inner{
      position:relative; z-index:1;
      height:100%;
      display:flex; align-items:center; justify-content:center;
      padding:3rem 2rem;
      text-align:center;
      color:#fff;
    }
    .hero-img{
      max-width: min(520px, 90%);
      border-radius: 1rem;
      box-shadow: 0 12px 30px rgba(0,0,0,.25);
      object-fit: cover;
      aspect-ratio: 16/10;
    }
    .brand-title{
      font-weight:700; letter-spacing:.5px; margin-top:1rem;
    }

    /* Lado formulario */
    .auth-form{
      display:flex; align-items:center; justify-content:center;
      padding: clamp(2rem, 5vw, 4rem);
    }
    .card-login{
      width:100%; max-width: 420px;
      border:0; border-radius:1rem;
      box-shadow: 0 10px 24px rgba(0,0,0,.08);
    }
    .card-login .card-header{
      border:0; border-radius: 1rem 1rem 0 0;
    }
    .form-floating>.form-control:focus~label,
    .form-floating>.form-control:not(:placeholder-shown)~label{
      opacity:.85 !important;
    }

    /* Botón tema */
    .theme-btn{
      position:absolute; top:1rem; right:1rem; z-index:10;
    }
  </style>
</head>
<body>
  <?php
  
  ?>
<?php echo env('app.imagen_nomb_logo2'); ?>
<div class="auth-wrap">

  <!-- Lado izquierdo: imagen / marca -->
  <section class="auth-hero">
    <button class="btn btn-sm btn-light theme-btn" id="themeToggle" title="Cambiar tema" aria-label="Cambiar tema">
      <i class="fa-solid fa-moon"></i>
    </button>
    <div class="overlay"></div>
    <div class="inner">
      <div> 
          <img src="<?= env('app.imagen_nomb_logo') ?>"  style="height:48px" class="mb-3"> 
        <h1 class="brand-title"><?= env('app.title') ?></h1>
        <p class="mb-4 opacity-75">Bienvenido. Ingresa tus credenciales para continuar.</p>
        <!-- Imagen ilustrativa (cámbiala si quieres) -->
        <img class="hero-img" src="<?= env('app.imagen_nomb_logo2') ?>"
             alt="Ilustración de acceso">
      </div>
    </div>
  </section>

  <!-- Lado derecho: login -->
  <section class="auth-form">
    <div class="card card-login">
      <div class="card-header bg-body-tertiary">
        <h5 class="mb-0"><i class="fa-solid fa-right-to-bracket me-2"></i> Iniciar sesión</h5>
      </div>
      <div class="card-body p-4">

        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
          </div>
        <?php endif; ?>

        <?php if ($errs = session()->getFlashdata('errors')): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            <ul class="mb-0 ps-3">
              <?php foreach ($errs as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
          </div>
        <?php endif; ?>

        <form action="<?= base_url('login') ?>" method="post" novalidate autocomplete="on">
          <?= csrf_field() ?>

          <div class="form-floating mb-3">
            <input type="email" class="form-control" id="email" name="email"
                   placeholder="nombre@correo.com" value="<?= old('email') ?>" required
                   spellcheck="false" autocomplete="username">
            <label for="email"><i class="fa-solid fa-envelope me-2"></i>Email</label>
          </div>

          <!-- Wrapper con position-relative para el botón de ojo -->
          <div class="form-floating mb-3 position-relative">
            <input type="password" class="form-control pe-5" id="password" name="password"
                   placeholder="••••••••" required autocomplete="current-password">
            <label for="password"><i class="fa-solid fa-lock me-2"></i>Contraseña</label>
            <button type="button"
                    class="btn btn-sm btn-outline-secondary position-absolute end-0 top-50 translate-middle-y me-3"
                    id="togglePassword" tabindex="-1" aria-label="Mostrar/ocultar contraseña" style="z-index:5">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="remember" name="remember" <?= old('remember')?'checked':'' ?>>
              <label class="form-check-label" for="remember">Recordarme</label>
            </div>
            <a class="small" href="<?= base_url('forgot') ?>">¿Olvidaste tu contraseña?</a>
          </div>

          <button type="submit" class="btn btn-primary w-100">
            <i class="fa-solid fa-right-to-bracket me-2"></i> Entrar
          </button>
        </form>
      </div>
      <div class="card-footer text-center small">
        © <?= date('Y') ?> <?= env('app.title') ?>
      </div>
    </div>
  </section>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Toggle tema (light/dark) con persistencia
(function(){
  const html = document.documentElement;
  const btn  = document.getElementById('themeToggle');
  const cur  = html.getAttribute('data-bs-theme') || 'light';
  btn.innerHTML = (cur === 'dark')
    ? '<i class="fa-solid fa-sun"></i>' : '<i class="fa-solid fa-moon"></i>';
  btn.addEventListener('click', () => {
    const next = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-bs-theme', next);
    try { localStorage.setItem('theme', next); } catch(e){}
    btn.innerHTML = (next === 'dark')
      ? '<i class="fa-solid fa-sun"></i>' : '<i class="fa-solid fa-moon"></i>';
  });
})();

// Mostrar/ocultar contraseña
document.getElementById('togglePassword').addEventListener('click', function(){
  const f = document.getElementById('password');
  const i = this.querySelector('i');
  if (f.type === 'password') { f.type = 'text'; i.classList.replace('fa-eye','fa-eye-slash'); }
  else { f.type = 'password'; i.classList.replace('fa-eye-slash','fa-eye'); }
});
</script>
</body>
</html>
