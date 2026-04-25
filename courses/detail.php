<?php
// ============================================================
// courses/detail.php — Detalle completo del curso
// ============================================================
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

$courseId = (int)($_GET['id'] ?? 0);
if ($courseId <= 0) {
    header('Location: ' . baseUrl('/courses/index.php'));
    exit;
}

$db = getDB();

// Curso
$stmt = $db->prepare('SELECT * FROM courses WHERE id = ? AND activo = 1');
$stmt->execute([$courseId]);
$course = $stmt->fetch();

if (!$course) {
    setFlash('error', 'El curso no existe.');
    header('Location: ' . baseUrl('/courses/index.php'));
    exit;
}

// Módulos
$modStmt = $db->prepare('SELECT * FROM modules WHERE course_id = ? ORDER BY orden');
$modStmt->execute([$courseId]);
$modules = $modStmt->fetchAll();

// Reto
$chalStmt = $db->prepare('SELECT * FROM course_challenges WHERE course_id = ? LIMIT 1');
$chalStmt->execute([$courseId]);
$challenge = $chalStmt->fetch();

// Estado de matrícula
$loggedIn   = isLoggedIn();
$userId     = (int)($_SESSION['user_id'] ?? 0);
$isEnrolled = false;

if ($loggedIn && $userId) {
    $enrStmt = $db->prepare('SELECT id FROM enrollments WHERE user_id = ? AND course_id = ? AND activo = 1');
    $enrStmt->execute([$userId, $courseId]);
    $isEnrolled = (bool)$enrStmt->fetch();
}

$pageTitle = htmlspecialchars($course['nombre']) . ' — SprintTech';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Detail Hero -->
<section class="course-detail-hero" style="background: linear-gradient(135deg, #080c18 0%, #0d1a3a 100%); padding: 56px 0 40px;">
  <div style="position:absolute;inset:0;background:radial-gradient(ellipse at 20% 50%, <?= htmlspecialchars($course['color_accent']) ?>15 0%, transparent 60%);pointer-events:none;"></div>
  <div class="container">
    <div class="detail-header">
      <a href="<?= baseUrl('/courses/index.php') ?>" class="detail-back">
        <i class="fa-solid fa-arrow-left"></i> Volver a todos los cursos
      </a>

      <div class="detail-meta">
        <span class="badge badge-cyan"><i class="fa-solid fa-layer-group"></i> Nivel <?= htmlspecialchars($course['nivel']) ?></span>
        <span class="badge badge-violet"><i class="fa-regular fa-clock"></i> <?= (int)$course['duracion_horas'] ?> horas</span>
        <span class="badge badge-amber"><i class="fa-solid fa-rocket"></i> Metodología ABR</span>
        <?php if ($isEnrolled): ?>
          <span class="badge badge-green"><i class="fa-solid fa-check-circle"></i> Matriculado</span>
        <?php endif; ?>
      </div>

      <h1 class="detail-title">
        <i class="fa-solid <?= htmlspecialchars($course['icono']) ?>" style="color:<?= htmlspecialchars($course['color_accent']) ?>; margin-right:12px;"></i>
        <?= htmlspecialchars($course['nombre']) ?>
      </h1>

      <p class="detail-desc"><?= htmlspecialchars($course['descripcion']) ?></p>
    </div>
  </div>
</section>

<!-- Course Layout -->
<div class="container">
  <div class="course-layout">

    <!-- Main content -->
    <div class="course-main">

      <!-- Objetivo -->
      <section style="margin-bottom:40px;" class="fade-in">
        <h2 style="font-size:1.4rem; margin-bottom:16px; display:flex; align-items:center; gap:10px;">
          <span style="color:<?= htmlspecialchars($course['color_accent']) ?>;"><i class="fa-solid fa-bullseye"></i></span>
          Objetivo del Curso
        </h2>
        <div style="background:var(--clr-bg-card); border:1px solid var(--clr-border); border-left:3px solid <?= htmlspecialchars($course['color_accent']) ?>; border-radius:var(--radius-md); padding:20px 24px;">
          <p style="color:var(--clr-text-muted); line-height:1.7;"><?= htmlspecialchars($course['objetivo']) ?></p>
        </div>
      </section>

      <!-- Módulos -->
      <section style="margin-bottom:40px;" class="fade-in">
        <h2 style="font-size:1.4rem; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
          <span style="color:<?= htmlspecialchars($course['color_accent']) ?>;"><i class="fa-solid fa-list-check"></i></span>
          Módulos del Curso
        </h2>
        <div style="display:flex; flex-direction:column; gap:12px;">
          <?php foreach ($modules as $mod): ?>
          <div class="module-item">
            <div class="module-header">
              <div class="module-number" style="background:linear-gradient(135deg, <?= htmlspecialchars($course['color_accent']) ?>, #7c3aed);">
                <?= (int)$mod['orden'] ?>
              </div>
              <span class="module-title"><?= htmlspecialchars($mod['titulo']) ?></span>
              <i class="fa-solid fa-chevron-down" style="color:var(--clr-text-dim); font-size:0.8rem;"></i>
            </div>
            <?php if (!empty($mod['descripcion'])): ?>
            <p class="module-desc"><?= htmlspecialchars($mod['descripcion']) ?></p>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- Evaluación general -->
      <section style="margin-bottom:40px;" class="fade-in">
        <h2 style="font-size:1.4rem; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
          <span style="color:<?= htmlspecialchars($course['color_accent']) ?>;"><i class="fa-solid fa-chart-bar"></i></span>
          Sistema de Evaluación
        </h2>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:16px;">
          <?php
          $evalTypes = [
            ['fa-stethoscope', 'Diagnóstica', '¿Qué sabe el estudiante?', 'cyan'],
            ['fa-pen-to-square', 'Formativa', 'Actividades por módulo', 'violet'],
            ['fa-trophy', 'Final', 'Reto práctico', 'amber'],
            ['fa-comments', 'Autoevaluación', '¿Qué aprendí? ¿Cómo lo aplico?', 'green'],
          ];
          foreach ($evalTypes as $ev): ?>
          <div style="background:var(--clr-bg-card); border:1px solid var(--clr-border); border-radius:var(--radius-md); padding:20px; text-align:center;">
            <i class="fa-solid <?= $ev[0] ?>" style="font-size:1.6rem; color:var(--clr-<?= $ev[3] ?>); margin-bottom:12px; display:block;"></i>
            <h4 style="font-size:0.95rem; margin-bottom:6px;"><?= $ev[1] ?></h4>
            <p style="font-size:0.82rem; color:var(--clr-text-muted);"><?= $ev[2] ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- Reto final -->
      <?php if ($challenge): ?>
      <section class="fade-in">
        <div class="challenge-box">
          <div class="challenge-title">
            <i class="fa-solid fa-fire" style="color:#f59e0b;"></i>
            Reto Final: <?= htmlspecialchars($challenge['titulo']) ?>
          </div>
          <p class="challenge-desc"><?= htmlspecialchars($challenge['descripcion']) ?></p>

          <?php if (!empty($challenge['actividad'])): ?>
          <div style="background:rgba(0,0,0,0.3); border-radius:var(--radius-md); padding:16px 20px; margin-bottom:20px;">
            <h5 style="font-size:0.88rem; color:var(--clr-text-muted); margin-bottom:8px; text-transform:uppercase; letter-spacing:0.08em;">
              <i class="fa-solid fa-flask"></i> Actividad
            </h5>
            <p style="font-size:0.9rem; line-height:1.6;"><?= nl2br(htmlspecialchars($challenge['actividad'])) ?></p>
          </div>
          <?php endif; ?>

          <h5 style="font-size:0.88rem; color:var(--clr-text-muted); margin-bottom:12px; text-transform:uppercase; letter-spacing:0.08em;">
            <i class="fa-solid fa-clipboard-check"></i> Criterios de Evaluación
          </h5>
          <div class="criteria-list">
            <?php foreach (['criterio_1', 'criterio_2', 'criterio_3'] as $crit): ?>
              <?php if (!empty($challenge[$crit])): ?>
              <div class="criteria-item">
                <i class="fa-solid fa-check-circle"></i>
                <?= htmlspecialchars($challenge[$crit]) ?>
              </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
      <?php endif; ?>

    </div><!-- /course-main -->

    <!-- Sidebar -->
    <aside class="course-sidebar fade-in fade-in-delay-2">
      <div class="sidebar-card">

        <!-- Icono grande -->
        <div style="width:100%; height:140px; border-radius:var(--radius-md); background:linear-gradient(135deg, <?= htmlspecialchars($course['color_accent']) ?>22, <?= htmlspecialchars($course['color_accent']) ?>55); display:flex; align-items:center; justify-content:center; font-size:3.5rem; color:white; position:relative; overflow:hidden;">
          <div style="position:absolute;inset:0;background:radial-gradient(ellipse at center, <?= htmlspecialchars($course['color_accent']) ?>33 0%, transparent 70%);"></div>
          <i class="fa-solid <?= htmlspecialchars($course['icono']) ?>" style="position:relative;z-index:1;filter:drop-shadow(0 0 20px <?= htmlspecialchars($course['color_accent']) ?>);"></i>
        </div>

        <!-- Precio (gratis) -->
        <div class="sidebar-price">
          <strong style="color:var(--clr-green);">¡GRATIS!</strong>
          Acceso completo incluido
        </div>

        <!-- Botón de matrícula -->
        <?php if ($isEnrolled): ?>
          <div style="text-align:center;">
            <span class="badge badge-green" style="padding:10px 20px; font-size:0.9rem; width:100%; justify-content:center;">
              <i class="fa-solid fa-check-circle"></i> Ya estás matriculado
            </span>
            <a href="<?= baseUrl('/user/my_courses.php') ?>" class="btn btn-ghost w-full" style="margin-top:10px;">
              <i class="fa-solid fa-arrow-right"></i> Ir a Mis Cursos
            </a>
          </div>
        <?php elseif ($loggedIn): ?>
          <form method="POST" action="<?= baseUrl('/courses/enroll.php') ?>">
            <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
            <button type="submit" class="btn btn-primary w-full btn-lg" id="btn-enroll-detail-<?= $course['id'] ?>">
              <i class="fa-solid fa-graduation-cap"></i> Matricularme ahora
            </button>
          </form>
        <?php else: ?>
          <a href="<?= baseUrl('/auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI'])) ?>"
             class="btn btn-primary w-full btn-lg" id="btn-login-to-enroll">
            <i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión para matricularse
          </a>
          <a href="<?= baseUrl('/auth/register.php') ?>" class="btn btn-outline w-full">
            <i class="fa-solid fa-user-plus"></i> Crear cuenta gratis
          </a>
        <?php endif; ?>

        <div class="divider" style="margin:4px 0;"></div>

        <!-- Info del curso -->
        <div class="course-info-list">
          <div class="course-info-item">
            <i class="fa-regular fa-clock"></i>
            <span><strong><?= (int)$course['duracion_horas'] ?> horas</strong> de contenido</span>
          </div>
          <div class="course-info-item">
            <i class="fa-solid fa-layer-group"></i>
            <span>Nivel <strong><?= htmlspecialchars($course['nivel']) ?></strong></span>
          </div>
          <div class="course-info-item">
            <i class="fa-solid fa-book-open"></i>
            <span><strong><?= count($modules) ?> módulos</strong> de aprendizaje</span>
          </div>
          <?php if (!empty($course['herramientas'])): ?>
          <div class="course-info-item" style="align-items:flex-start;">
            <i class="fa-solid fa-wrench" style="margin-top:2px;"></i>
            <span><strong>Herramientas:</strong> <?= htmlspecialchars($course['herramientas']) ?></span>
          </div>
          <?php endif; ?>
          <div class="course-info-item">
            <i class="fa-solid fa-certificate"></i>
            <span>Certificado al <strong>completar</strong></span>
          </div>
          <div class="course-info-item">
            <i class="fa-solid fa-infinity"></i>
            <span>Acceso <strong>ilimitado</strong></span>
          </div>
        </div>

        <div class="divider" style="margin:4px 0;"></div>

        <!-- ABR Cycle -->
        <h4 style="font-size:0.85rem; color:var(--clr-text-muted); text-transform:uppercase; letter-spacing:0.1em; margin-bottom:8px;">
          Ciclo ABR
        </h4>
        <div class="abr-cycle">
          <?php
          $abrItems = [
            ['🎯', 'Problema real', 'Reto auténtico del mundo'],
            ['⚙️', 'Tecnología', 'Useás herramientas reales'],
            ['🔄', 'Prueba y error', 'Iteras y mejoras'],
            ['✅', 'Solución', 'Presentás tu propuesta'],
            ['💡', 'Reflexión', 'Consolidás el aprendizaje'],
          ];
          foreach ($abrItems as $abr): ?>
          <div class="abr-step-item">
            <div class="abr-step-dot" style="background: linear-gradient(135deg, <?= htmlspecialchars($course['color_accent']) ?>, #7c3aed);">
              <?= $abr[0] ?>
            </div>
            <div class="abr-step-content">
              <h5><?= $abr[1] ?></h5>
              <p><?= $abr[2] ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

      </div>
    </aside>

  </div><!-- /course-layout -->
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
