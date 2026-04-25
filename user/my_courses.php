<?php
// ============================================================
// user/my_courses.php — Mis cursos activos
// ============================================================
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

requireLogin();

$userId = (int)($_SESSION['user_id'] ?? 0);
$db     = getDB();

// Cursos activos del usuario con info del curso
$stmt = $db->prepare('
    SELECT e.id AS enrollment_id, e.progreso, e.fecha_matricula,
           c.id AS course_id, c.nombre, c.descripcion, c.duracion_horas,
           c.nivel, c.herramientas, c.color_accent, c.icono,
           (SELECT COUNT(*) FROM modules WHERE course_id = c.id) AS total_modules
    FROM enrollments e
    JOIN courses c ON c.id = e.course_id
    WHERE e.user_id = ? AND e.activo = 1 AND c.activo = 1 AND e.progreso < 100
    ORDER BY e.fecha_matricula DESC
');
$stmt->execute([$userId]);
$myCourses = $stmt->fetchAll();

// Verificar qué cursos ya tienen pago aprobado
$paidSet = [];
if (!empty($myCourses)) {
    $ids = array_column($myCourses, 'course_id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $paidStmt = $db->prepare("
        SELECT course_id FROM certificate_payments
        WHERE user_id = ? AND estado = 'aprobado'
        AND course_id IN ($placeholders)
    ");
    $paidStmt->execute(array_merge([$userId], $ids));
    foreach ($paidStmt->fetchAll() as $row) {
        $paidSet[$row['course_id']] = true;
    }
}

// Capturar base URL antes de require header
$baseUrlJs = rtrim(baseUrl(), '/');

// Contadores
$totalCourses = count($myCourses);
$avgProgress  = $totalCourses > 0 ? round(array_sum(array_column($myCourses, 'progreso')) / $totalCourses) : 0;

$pageTitle = 'Mis Cursos — SprintTech';

// CSS extra del PSE
$extraCss = 'pse.css';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Dashboard hero -->
<div class="dashboard-hero">
  <div class="container">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:24px;">
      <div class="fade-in">
        <h1 class="dashboard-greeting">
          ¡Hola, <?= htmlspecialchars(currentUser('nombre') ?? 'Estudiante') ?>! 👋
        </h1>
        <p class="dashboard-subtext">Aquí están todos tus cursos en progreso</p>
      </div>

      <!-- Stats rápidos -->
      <div style="display:flex; gap:24px; flex-wrap:wrap;" class="fade-in fade-in-delay-1">
        <div style="background:var(--clr-bg-card); border:1px solid var(--clr-border); border-radius:var(--radius-md); padding:16px 24px; text-align:center;">
          <div style="font-size:1.8rem; font-weight:800; color:var(--clr-cyan); font-family: var(--ff-heading);"><?= $totalCourses ?></div>
          <div style="font-size:0.8rem; color:var(--clr-text-muted);">Cursos activos</div>
        </div>
        <div style="background:var(--clr-bg-card); border:1px solid var(--clr-border); border-radius:var(--radius-md); padding:16px 24px; text-align:center;">
          <div style="font-size:1.8rem; font-weight:800; color:var(--clr-violet); font-family: var(--ff-heading);"><?= $avgProgress ?>%</div>
          <div style="font-size:0.8rem; color:var(--clr-text-muted);">Progreso promedio</div>
        </div>
        <a href="<?= baseUrl('/courses/index.php') ?>" class="btn btn-primary" style="align-self:center;">
          <i class="fa-solid fa-plus"></i> Explorar más
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Info PSE banner -->
<div style="background:linear-gradient(135deg,rgba(0,82,204,0.12),rgba(0,212,255,0.08));
            border-top:1px solid rgba(0,82,204,0.2); border-bottom:1px solid rgba(0,212,255,0.1);
            padding:14px 0;">
  <div class="container">
    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
      <div style="display:flex; align-items:center; gap:8px; background:linear-gradient(135deg,#0052cc,#00c2ff);
                  border-radius:8px; padding:5px 12px; font-weight:900; font-size:0.85rem;
                  color:#fff; font-family:'Outfit',sans-serif; letter-spacing:0.05em;">
        PSE
      </div>
      <span style="font-size:0.85rem; color:#94a3b8;">
        <strong style="color:#e2e8f0;">Pasarela de Pago PSE disponible</strong> —
        Paga tu certificado digital por <strong style="color:#00d4ff;">$15.000 COP</strong>
        con Nequi, Bancolombia, Davivienda, Banco de Bogotá, BBVA o Bre-B.
      </span>
      <div style="display:flex; gap:12px; margin-left:auto; flex-wrap:wrap;">
        <span style="font-size:0.75rem; color:#475569; display:flex; align-items:center; gap:5px;">
          <i class="fa-solid fa-shield-halved" style="color:#10b981;"></i> SSL cifrado
        </span>
        <span style="font-size:0.75rem; color:#475569; display:flex; align-items:center; gap:5px;">
          <i class="fa-solid fa-check-circle" style="color:#10b981;"></i> ACH Colombia
        </span>
      </div>
    </div>
  </div>
</div>

<!-- Cursos -->
<section class="section">
  <div class="container">

    <?php if (empty($myCourses)): ?>
    <!-- Estado vacío -->
    <div class="empty-state fade-in">
      <div class="empty-icon"><i class="fa-solid fa-book-open"></i></div>
      <h2 class="empty-title">Aún no estás matriculado en ningún curso</h2>
      <p class="empty-desc">Explora nuestra ruta de formación en Tecnologías Emergentes y empieza tu aprendizaje hoy.</p>
      <a href="<?= baseUrl('/courses/index.php') ?>" class="btn btn-primary btn-lg">
        <i class="fa-solid fa-graduation-cap"></i> Ver cursos disponibles
      </a>
    </div>

    <?php else: ?>

    <div style="display:flex; flex-direction:column; gap:16px;">
      <?php foreach ($myCourses as $i => $course):
        $progress   = (int)$course['progreso'];
        $enrollDate = date('d/m/Y', strtotime($course['fecha_matricula']));
        $hours      = (int)$course['duracion_horas'];
        $delay      = $i % 3;
        $isPaid     = isset($paidSet[$course['course_id']]);
        $courseNameJs = addslashes($course['nombre']);
      ?>
      <div class="my-course-card fade-in fade-in-delay-<?= $delay ?>" id="enrollment-<?= $course['enrollment_id'] ?>">

        <!-- Icono -->
        <div class="my-course-icon"
             style="background: linear-gradient(135deg, <?= htmlspecialchars($course['color_accent']) ?>, #7c3aed);">
          <i class="fa-solid <?= htmlspecialchars($course['icono']) ?>"></i>
        </div>

        <!-- Info -->
        <div class="my-course-info">
          <div class="my-course-name"><?= htmlspecialchars($course['nombre']) ?></div>
          <div class="my-course-meta">
            <span><i class="fa-regular fa-clock"></i> <?= $hours ?>h</span>
            <span><i class="fa-solid fa-layer-group"></i> <?= (int)$course['total_modules'] ?> módulos</span>
            <span><i class="fa-regular fa-calendar"></i> Desde <?= $enrollDate ?></span>
            <span class="badge badge-cyan" style="font-size:0.7rem;">Nivel <?= htmlspecialchars($course['nivel']) ?></span>

            <?php if ($isPaid): ?>
            <span class="badge badge-green" style="font-size:0.7rem;">
              <i class="fa-solid fa-credit-card"></i> Certificado pagado
            </span>
            <?php endif; ?>
          </div>

          <!-- Progreso -->
          <div class="my-course-progress">
            <div class="progress-label">
              <span>Progreso</span>
              <span style="color:var(--clr-cyan); font-weight:600;"><?= $progress ?>%</span>
            </div>
            <div class="progress-wrap">
              <div class="progress-bar" data-width="<?= $progress ?>" style="width:0%;"></div>
            </div>
          </div>
        </div>

        <!-- Acciones -->
        <div class="my-course-actions">

          <!-- Ver curso -->
          <a href="<?= baseUrl('/courses/detail.php?id=' . $course['course_id']) ?>"
             class="btn btn-primary btn-sm"
             id="btn-view-course-<?= $course['course_id'] ?>">
            <i class="fa-solid fa-play"></i> Ver
          </a>

          <!-- Realizar Pago PSE -->
          <?php if ($isPaid): ?>
            <a href="<?= baseUrl('/user/download_cert.php?course_id=' . $course['course_id']) ?>"
               class="btn-pse paid"
               id="btn-pse-<?= $course['course_id'] ?>"
               target="_blank"
               title="Pago completado — descargar certificado">
              <i class="fa-solid fa-circle-check"></i> Descargar
            </a>
          <?php else: ?>
            <button type="button"
                    class="btn-pse"
                    id="btn-pse-<?= $course['course_id'] ?>"
                    data-pse-open="<?= $course['course_id'] ?>"
                    data-course-id="<?= $course['course_id'] ?>"
                    data-course-name="<?= htmlspecialchars($course['nombre'], ENT_QUOTES) ?>"
                    title="Pagar certificado con PSE — $15.000 COP">
              <i class="fa-solid fa-credit-card"></i> Realizar Pago
            </button>
          <?php endif; ?>

          <!-- Dar de baja -->
          <button type="button"
                  class="btn btn-danger btn-sm"
                  id="btn-unenroll-<?= $course['enrollment_id'] ?>"
                  onclick="openModal('modal-unenroll-<?= $course['enrollment_id'] ?>')">
            <i class="fa-solid fa-xmark"></i> Dar de baja
          </button>

        </div>
      </div>

      <!-- Modal confirmación de baja -->
      <div class="modal-overlay" id="modal-unenroll-<?= $course['enrollment_id'] ?>">
        <div class="modal">
          <div class="modal-icon">⚠️</div>
          <h3 class="modal-title">¿Dar de baja?</h3>
          <p class="modal-desc">
            ¿Estás seguro que deseas retirarte del curso
            <strong style="color:var(--clr-white);">"<?= htmlspecialchars($course['nombre']) ?>"</strong>?
            Perderás tu progreso.
          </p>
          <div class="modal-actions">
            <button class="btn btn-ghost" onclick="closeModal('modal-unenroll-<?= $course['enrollment_id'] ?>')">
              Cancelar
            </button>
            <form method="POST" action="<?= baseUrl('/user/unenroll.php') ?>" style="display:inline;">
              <input type="hidden" name="enrollment_id" value="<?= $course['enrollment_id'] ?>">
              <button type="submit" class="btn btn-danger" id="confirm-unenroll-<?= $course['enrollment_id'] ?>">
                <i class="fa-solid fa-xmark"></i> Sí, dar de baja
              </button>
            </form>
          </div>
        </div>
      </div>

      <?php endforeach; ?>
    </div>

    <!-- CTA explorar más cursos -->
    <div style="text-align:center; margin-top:48px;" class="fade-in">
      <p style="color:var(--clr-text-muted); margin-bottom:16px;">¿Quieres aprender más?</p>
      <a href="<?= baseUrl('/courses/index.php') ?>" class="btn btn-outline">
        <i class="fa-solid fa-graduation-cap"></i> Ver todos los cursos
      </a>
    </div>

    <?php endif; ?>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<!-- PSE: base URL disponible para el JS -->
<script>
  window.PSE_BASE_URL = <?= json_encode($baseUrlJs) ?>;
</script>
<!-- PSE JS -->
<script src="<?= baseUrl('/assets/js/pse.js') ?>"></script>
