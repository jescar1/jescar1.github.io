<?php
session_start();

// CSRF token
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf'];

$tab = $_GET['tab'] ?? 'login';
$msg = $_GET['msg'] ?? '';
$type = $_GET['type'] ?? 'info';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Acceso | Proyecto Recetas</title>
  <link rel="stylesheet" href="formulario.css">
</head>
<body>
  <main class="page">
    <section class="auth-card">
      <header class="brand">
        <div class="mark" aria-hidden="true">🍽</div>
        <div class="brand-copy">
          <h1>Proyecto Recetas</h1>
          <p>Accede o crea tu cuenta para continuar</p>
        </div>
      </header>

      <?php if ($msg): ?>
      <div class="alert <?php echo htmlspecialchars($type); ?>">
        <?php echo htmlspecialchars($msg); ?>
      </div>
      <?php endif; ?>

      <nav class="tabs" role="tablist" aria-label="Selector de formulario">
        <button id="t-login" class="tab" role="tab" aria-controls="p-login">Iniciar sesión</button>
        <button id="t-register" class="tab" role="tab" aria-controls="p-register">Registrarse</button>
        <span class="underline" aria-hidden="true"></span>
      </nav>

      <div class="panels">
        <!-- Login -->
        <section id="p-login" class="panel" role="tabpanel" aria-labelledby="t-login">
          <form class="form" method="post" action="auth/login.php" autocomplete="on" novalidate>
            <div class="field">
  <input type="email" name="email" id="email" placeholder="Correo electrónico" required>
  <label for="email" class="sr-only">Correo electrónico</label>
</div>
<div class="field">
  <input type="password" name="password" id="password" placeholder="Contraseña" required minlength="6">
  <label for="password" class="sr-only">Contraseña</label>
</div>

            <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
            <button class="btn primary" type="submit">Ingresar</button>
            <button class="btn ghost" type="button" data-switch="register">Crear cuenta</button>
          </form>
        </section>

        <!-- Register -->
        <section id="p-register" class="panel" role="tabpanel" aria-labelledby="t-register">
          <form class="form" method="post" action="auth/register.php" autocomplete="on" novalidate>
            <div class="field">
  <input type="text" name="nombre" id="nombre" placeholder="Nombre completo" required>
  <label for="nombre" class="sr-only">Nombre completo</label>
</div>
<div class="field">
  <input type="email" name="email" id="reg_email" placeholder="Correo electrónico" required>
  <label for="reg_email" class="sr-only">Correo electrónico</label>
</div>
<div class="field">
  <input type="password" name="password" id="reg_password" placeholder="Contraseña" required minlength="6">
  <label for="reg_password" class="sr-only">Contraseña</label>
</div>

            <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
            <button class="btn primary" type="submit">Crear cuenta</button>
            <button class="btn ghost" type="button" data-switch="login">Volver a iniciar sesión</button>
          </form>
        </section>
      </div>
      <footer class="small">© <?php echo date('Y'); ?> Proyecto Recetas</footer>
    </section>
  </main>

  <script>
  const $ = s => document.querySelector(s);
  const loginBtn = $("#t-login"), regBtn = $("#t-register");
  const underline = $(".underline");
  const panels = document.querySelectorAll(".panel");
  const tabs = document.querySelectorAll(".tab");
  const urlTab = (new URLSearchParams(location.search)).get("tab") || "<?php echo $tab; ?>";

  function setTab(which){
    tabs.forEach(t => t.classList.remove("active"));
    panels.forEach(p => p.classList.remove("active"));
    if(which === "register"){
      $("#t-register").classList.add("active");
      $("#p-register").classList.add("active");
      underline.style.transform = "translateX(100%)";
      history.replaceState(null, "", "?tab=register");
    }else{
      $("#t-login").classList.add("active");
      $("#p-login").classList.add("active");
      underline.style.transform = "translateX(0)";
      history.replaceState(null, "", "?tab=login");
    }
  }

  loginBtn.addEventListener("click", ()=> setTab("login"));
  regBtn.addEventListener("click", ()=> setTab("register"));
  document.querySelectorAll("[data-switch]").forEach(el=>{
    el.addEventListener("click", ()=> setTab(el.getAttribute("data-switch")));
  });

  // Primer pintado
  setTab(urlTab === "register" ? "register" : "login");
  </script>
</body>
</html>
