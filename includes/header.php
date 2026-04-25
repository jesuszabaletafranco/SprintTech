<?php
// ============================================================
// includes/header.php — Navbar dinámica
// ============================================================
require_once __DIR__ . '/../config/session.php';
$loggedIn    = isLoggedIn();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$basePath    = baseUrl();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="SprintTech LearningWithIA — Plataforma de Aprendizaje Interactivo en Tecnologías 4.0. Capacítate en IA, IoT, Apps móviles y más.">
  <title><?= $pageTitle ?? 'SprintTech — Aprende. Compite. Gana.' ?></title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Main CSS -->
  <link rel="stylesheet" href="<?= $basePath ?>/assets/css/main.css">
  <?php if (isset($extraCss)): ?>
    <link rel="stylesheet" href="<?= $basePath ?>/assets/css/<?= $extraCss ?>">
  <?php endif; ?>

  <!-- Page icon -->
  <link rel="icon" href="<?= $basePath ?>/assets/img/favicon.svg" type="image/svg+xml">
</head>
<body>

<!-- ═══════════════════ NAVBAR ═══════════════════ -->
<nav class="navbar" id="navbar">
  <div class="nav-container">

    <!-- Logo -->
    <a href="<?= $basePath ?>/" class="nav-logo">
      <span class="logo-icon"><i class="fa-solid fa-bolt-lightning"></i></span>
      <span class="logo-text">Sprint<span class="logo-accent">Tech</span></span>
    </a>

    <!-- Desktop Menu -->
    <ul class="nav-menu" id="navMenu">
      <li class="nav-item">
        <a href="<?= $basePath ?>/" class="nav-link <?= ($currentPage === 'index') ? 'active' : '' ?>">
          <i class="fa-solid fa-house"></i> Inicio
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= $basePath ?>/courses/index.php" class="nav-link <?= ($currentPage === 'index' && strpos($_SERVER['PHP_SELF'], 'courses') !== false) ? 'active' : '' ?>">
          <i class="fa-solid fa-graduation-cap"></i> Cursos
        </a>
      </li>

      <?php if ($loggedIn): ?>
        <!-- Menú de usuario autenticado -->
        <li class="nav-item">
          <a href="<?= $basePath ?>/user/my_courses.php" class="nav-link <?= ($currentPage === 'my_courses') ? 'active' : '' ?>">
            <i class="fa-solid fa-book-open"></i> Mis Cursos
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= $basePath ?>/user/certifications.php" class="nav-link <?= ($currentPage === 'certifications') ? 'active' : '' ?>">
            <i class="fa-solid fa-certificate"></i> Mis Certificaciones
          </a>
        </li>
        <li class="nav-item nav-user-menu">
          <button class="nav-user-btn" id="userMenuBtn" aria-expanded="false">
            <span class="user-avatar"><?= strtoupper(substr(currentUser('nombre') ?? 'U', 0, 1)) ?></span>
            <span class="user-name"><?= htmlspecialchars(currentUser('nombre') ?? '') ?></span>
            <i class="fa-solid fa-chevron-down"></i>
          </button>
          <div class="user-dropdown" id="userDropdown">
            <div class="dropdown-header">
              <span><?= htmlspecialchars((currentUser('nombre') ?? '') . ' ' . (currentUser('apellido') ?? '')) ?></span>
              <small><?= htmlspecialchars(currentUser('email') ?? '') ?></small>
            </div>
            <a href="<?= $basePath ?>/auth/logout.php" class="dropdown-item dropdown-item--danger">
              <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
            </a>
          </div>
        </li>
      <?php else: ?>
        <!-- Botón de login -->
        <li class="nav-item">
          <a href="<?= $basePath ?>/auth/login.php" class="btn btn-outline btn-sm">
            <i class="fa-solid fa-right-to-bracket"></i> Iniciar Sesión
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= $basePath ?>/auth/register.php" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-user-plus"></i> Registrarse
          </a>
        </li>
      <?php endif; ?>
    </ul>

    <!-- Hamburger -->
    <button class="nav-hamburger" id="navHamburger" aria-label="Abrir menú">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<!-- Flash message -->
<div class="flash-container" id="flashContainer">
  <?php renderFlash(); ?>
</div>

<main class="main-content">
