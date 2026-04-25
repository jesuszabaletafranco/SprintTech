<?php
// ============================================================
// auth/register.php — Registro de usuarios
// ============================================================
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

// Si ya está logueado, redirigir
if (isLoggedIn()) {
    header('Location: ' . baseUrl('/user/my_courses.php'));
    exit;
}

$errors = [];
$values = ['nombre' => '', 'apellido' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre']   ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $confirm  = $_POST['confirm']       ?? '';

    $values = compact('nombre', 'apellido', 'email');

    // Validaciones
    if (empty($nombre))   $errors['nombre']   = 'El nombre es requerido.';
    if (empty($apellido)) $errors['apellido'] = 'El apellido es requerido.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email inválido.';
    }
    if (strlen($password) < 8) {
        $errors['password'] = 'La contraseña debe tener al menos 8 caracteres.';
    }
    if ($password !== $confirm) {
        $errors['confirm'] = 'Las contraseñas no coinciden.';
    }

    if (empty($errors)) {
        $db = getDB();
        // Verificar email duplicado
        $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Este correo ya está registrado.';
        } else {
            // Insertar usuario
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $ins  = $db->prepare('INSERT INTO users (nombre, apellido, email, password) VALUES (?, ?, ?, ?)');
            $ins->execute([$nombre, $apellido, $email, $hash]);
            $userId = $db->lastInsertId();

            // Iniciar sesión automáticamente
            $_SESSION['user_id']  = $userId;
            $_SESSION['nombre']   = $nombre;
            $_SESSION['apellido'] = $apellido;
            $_SESSION['email']    = $email;

            setFlash('success', "¡Bienvenido/a a SprintTech, {$nombre}! 🚀");
            header('Location: ' . baseUrl('/courses/index.php'));
            exit;
        }
    }
}

$pageTitle = 'Crear Cuenta — SprintTech';
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
      <h1 class="auth-title">Crear cuenta</h1>
      <p class="auth-subtitle">Únete a miles de estudiantes en Tecnologías 4.0</p>
    </div>

    <!-- Formulario -->
    <form class="auth-form" method="POST" action="" id="registerForm" novalidate>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="nombre">Nombre</label>
          <input
            class="form-control <?= isset($errors['nombre']) ? 'error' : '' ?>"
            type="text"
            id="nombre"
            name="nombre"
            value="<?= htmlspecialchars($values['nombre']) ?>"
            placeholder="Tu nombre"
            autocomplete="given-name"
            required>
          <?php if (isset($errors['nombre'])): ?>
            <span class="form-error"><?= htmlspecialchars($errors['nombre']) ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label class="form-label" for="apellido">Apellido</label>
          <input
            class="form-control <?= isset($errors['apellido']) ? 'error' : '' ?>"
            type="text"
            id="apellido"
            name="apellido"
            value="<?= htmlspecialchars($values['apellido']) ?>"
            placeholder="Tu apellido"
            autocomplete="family-name"
            required>
          <?php if (isset($errors['apellido'])): ?>
            <span class="form-error"><?= htmlspecialchars($errors['apellido']) ?></span>
          <?php endif; ?>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="email">Correo electrónico</label>
        <input
          class="form-control <?= isset($errors['email']) ? 'error' : '' ?>"
          type="email"
          id="email"
          name="email"
          value="<?= htmlspecialchars($values['email']) ?>"
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
            placeholder="Mínimo 8 caracteres"
            autocomplete="new-password"
            required>
          <button type="button" class="input-toggle" data-target="password" aria-label="Mostrar contraseña">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>
        <div class="password-strength" id="passwordStrength">
          <div class="strength-bar"></div>
          <div class="strength-bar"></div>
          <div class="strength-bar"></div>
          <div class="strength-bar"></div>
        </div>
        <?php if (isset($errors['password'])): ?>
          <span class="form-error"><?= htmlspecialchars($errors['password']) ?></span>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label class="form-label" for="confirm">Confirmar contraseña</label>
        <div class="input-wrapper">
          <input
            class="form-control <?= isset($errors['confirm']) ? 'error' : '' ?>"
            type="password"
            id="confirm"
            name="confirm"
            placeholder="Repite tu contraseña"
            autocomplete="new-password"
            required>
          <button type="button" class="input-toggle" data-target="confirm" aria-label="Mostrar contraseña">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>
        <?php if (isset($errors['confirm'])): ?>
          <span class="form-error"><?= htmlspecialchars($errors['confirm']) ?></span>
        <?php endif; ?>
      </div>

      <button type="submit" class="btn btn-primary w-full" id="submitRegister">
        <i class="fa-solid fa-user-plus"></i> Crear mi cuenta
      </button>

      <p class="auth-terms">
        Al registrarte aceptas nuestros <a href="#">Términos de Servicio</a> y
        <a href="#">Política de Privacidad</a>.
      </p>
    </form>

    <div class="auth-footer">
      ¿Ya tienes cuenta? <a href="<?= baseUrl('/auth/login.php') ?>">Inicia sesión</a>
    </div>

    <a href="<?= baseUrl('/') ?>" class="back-home">
      <i class="fa-solid fa-arrow-left"></i> Volver al inicio
    </a>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
