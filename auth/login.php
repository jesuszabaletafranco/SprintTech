<?php
// ============================================================
// auth/login.php — Inicio de sesión
// ============================================================
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

if (isLoggedIn()) {
    header('Location: ' . baseUrl('/user/my_courses.php'));
    exit;
}

$errors   = [];
$email    = '';
$redirect = $_GET['redirect'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $redirect = $_POST['redirect']      ?? '';

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Ingresa un correo válido.';
    }
    if (empty($password)) {
        $errors['password'] = 'Ingresa tu contraseña.';
    }

    if (empty($errors)) {
        $db   = getDB();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Renovar ID de sesión por seguridad
            session_regenerate_id(true);

            $_SESSION['user_id']  = $user['id'];
            $_SESSION['nombre']   = $user['nombre'];
            $_SESSION['apellido'] = $user['apellido'];
            $_SESSION['email']    = $user['email'];
            $_SESSION['rol']      = $user['rol'];

            setFlash('success', "¡Bienvenido/a de vuelta, {$user['nombre']}! 👋");

            // Redirigir
            $dest = !empty($redirect) ? $redirect : baseUrl('/user/my_courses.php');
            header('Location: ' . $dest);
            exit;
        } else {
            $errors['general'] = 'Correo o contraseña incorrectos. Intenta de nuevo.';
        }
    }
}

$pageTitle = 'Iniciar Sesión — SprintTech';
$extraCss  = 'auth.css';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-page">
  <div class="auth-card">

    <!-- Logo -->
    <a href="<?= baseUrl('/') ?>" class="auth-logo">
      <div class="auth-logo-icon"><i class="fa-solid fa-bolt-lightning"></i></div>
      <span class="auth-logo-text">Sprint<span>Tech</span></span>
    </a>

    <!-- Header -->
    <div class="auth-header">
      <h1 class="auth-title">Iniciar sesión</h1>
      <p class="auth-subtitle">Continúa tu camino de aprendizaje en Tecnologías 4.0</p>
    </div>

    <?php if (isset($errors['general'])): ?>
      <div class="flash flash--error" style="margin-bottom:16px;">
        <i class="fa-solid fa-circle-exclamation"></i>
        <?= htmlspecialchars($errors['general']) ?>
      </div>
    <?php endif; ?>

    <!-- Formulario -->
    <form class="auth-form" method="POST" action="" novalidate>
      <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

      <div class="form-group">
        <label class="form-label" for="email">Correo electrónico</label>
        <input
          class="form-control <?= isset($errors['email']) ? 'error' : '' ?>"
          type="email"
          id="email"
          name="email"
          value="<?= htmlspecialchars($email) ?>"
          placeholder="correo@ejemplo.com"
          autocomplete="email"
          required>
        <?php if (isset($errors['email'])): ?>
          <span class="form-error"><?= htmlspecialchars($errors['email']) ?></span>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Contraseña</label>
        <div class="input-wrapper">
          <input
            class="form-control <?= isset($errors['password']) ? 'error' : '' ?>"
            type="password"
            id="password"
            name="password"
            placeholder="Tu contraseña"
            autocomplete="current-password"
            required>
          <button type="button" class="input-toggle" data-target="password" aria-label="Mostrar contraseña">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>
        <?php if (isset($errors['password'])): ?>
          <span class="form-error"><?= htmlspecialchars($errors['password']) ?></span>
        <?php endif; ?>
      </div>

      <button type="submit" class="btn btn-primary w-full" id="submitLogin">
        <i class="fa-solid fa-right-to-bracket"></i> Ingresar a la plataforma
      </button>
    </form>

    <div class="auth-divider">o</div>

    <div class="auth-footer">
      ¿No tienes cuenta? <a href="<?= baseUrl('/auth/register.php') ?>">Regístrate gratis</a>
    </div>

    <a href="<?= baseUrl('/') ?>" class="back-home">
      <i class="fa-solid fa-arrow-left"></i> Volver al inicio
    </a>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
