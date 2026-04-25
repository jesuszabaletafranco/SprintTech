<?php
// ============================================================
// courses/index.php — Catálogo de cursos
// ============================================================
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

$loggedIn = isLoggedIn();
$userId   = (int)($_SESSION['user_id'] ?? 0);
$db       = getDB();

// Obtener todos los cursos
$courses = $db->query('SELECT * FROM courses WHERE activo = 1 ORDER BY orden')->fetchAll();

// Si el usuario está logueado, obtener sus matrículas activas
$enrolledIds = [];
if ($loggedIn && $userId) {
    $enrolled = $db->prepare('SELECT course_id FROM enrollments WHERE user_id = ? AND activo = 1');
    $enrolled->execute([$userId]);
    $enrolledIds = array_column($enrolled->fetchAll(), 'course_id');
}

// Mapa de colores e iconos
$accentColors = [
    '#00d4ff', '#7c3aed', '#06b6d4', '#10b981', '#f59e0b', '#ef4444'
];

$pageTitle = 'Cursos — SprintTech LearningWithIA';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Hero de cursos -->
<section class="courses-hero" style="background: linear-gradient(135deg, #080c18 0%, #0d1a3a 100%); padding: 60px 0 48px; border-bottom: 1px solid var(--clr-border);">
  <div class="container">
    <div class="fade-in">
      <span class="section-label">
        <i class="fa-solid fa-route"></i> Ruta de Formación
      </span>
      <h1 style="margin-top:16px; margin-bottom:16px;">
        Tecnologías <span class="text-gradient">Emergentes</span>
      </h1>
      <p style="color: var(--clr-text-muted); font-size: 1.05rem; max-width: 580px; line-height: 1.7;">
        6 cursos diseñados con metodología <strong style="color:var(--clr-cyan)">ABR (Aprendizaje Basado en Retos)</strong> para que aprendas haciendo y apliques desde el primer día.
      </p>
    </div>

    <!-- Stats rápidos -->
    <div style="display:flex; gap:40px; margin-top:36px; flex-wrap:wrap;">
      <div class="stat-item fade-in fade-in-delay-1">
        <div class="stat-value"><span data-target="6" data-suffix="">0</span></div>
        <div class="stat-label">Cursos disponibles</div>
      </div>
      <div class="stat-item fade-in fade-in-delay-2">
        <div class="stat-value"><span data-target="74" data-suffix="h">0h</span></div>
        <div class="stat-label">Horas de contenido</div>
      </div>
      <div class="stat-item fade-in fade-in-delay-3">
        <div class="stat-value"><span data-target="100" data-suffix="%">0%</span></div>
        <div class="stat-label">Basado en retos reales</div>
      </div>
    </div>
  </div>
</section>

<!-- Listado de cursos -->
<section class="section">
  <div class="container">
    <div class="courses-grid">
      <?php foreach ($courses as $i => $course):
        $isEnrolled = in_array($course['id'], $enrolledIds);
        $delay = $i % 3;
      ?>
      <article class="course-card fade-in fade-in-delay-<?= $delay ?>"
               style="--card-accent: <?= htmlspecialchars($course['color_accent']) ?>;"
               id="course-card-<?= $course['id'] ?>">

        <!-- Banner con gradiente -->
        <div class="course-card-banner" style="background: linear-gradient(135deg, <?= htmlspecialchars($course['color_accent']) ?>22, <?= htmlspecialchars($course['color_accent']) ?>55);">
          <div style="position:absolute;inset:0;background:radial-gradient(ellipse at center, <?= htmlspecialchars($course['color_accent']) ?>33 0%, transparent 70%);"></div>
          <i class="fa-solid <?= htmlspecialchars($course['icono']) ?> course-card-icon"></i>
          <div style="position:absolute;top:12px;left:12px;">
            <span class="badge" style="background:rgba(0,0,0,0.4);color:white;border-color:rgba(255,255,255,0.2);">
              <i class="fa-solid fa-layer-group"></i> Nivel <?= htmlspecialchars($course['nivel']) ?>
            </span>
          </div>
          <div style="position:absolute;top:12px;right:12px;">
            <span class="badge" style="background:rgba(0,0,0,0.4);color:white;border-color:rgba(255,255,255,0.2);">
              <i class="fa-regular fa-clock"></i> <?= (int)$course['duracion_horas'] ?>h
            </span>
          </div>
          <?php if ($isEnrolled): ?>
          <div style="position:absolute;bottom:12px;right:12px;">
            <span class="badge badge-green">
              <i class="fa-solid fa-check"></i> Matriculado
            </span>
          </div>
          <?php endif; ?>
        </div>

        <!-- Cuerpo -->
        <div class="course-card-body">
          <h3 style="font-size:1.1rem; color:var(--clr-white); line-height:1.3;">
            <?= htmlspecialchars($course['nombre']) ?>
          </h3>
          <p style="font-size:0.88rem; color:var(--clr-text-muted); line-height:1.6; flex:1;">
            <?= htmlspecialchars(mb_substr($course['descripcion'], 0, 120)) ?>...
          </p>

          <!-- Herramientas -->
          <?php if (!empty($course['herramientas'])): ?>
          <div style="display:flex; gap:6px; flex-wrap:wrap; margin-top:4px;">
            <?php foreach (explode(',', $course['herramientas']) as $tool): ?>
              <span style="font-size:0.72rem; padding:3px 10px; background:rgba(255,255,255,0.05); border:1px solid var(--clr-border); border-radius:100px; color:var(--clr-text-muted);">
                <?= trim(htmlspecialchars($tool)) ?>
              </span>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="course-card-footer">
          <a href="<?= baseUrl('/courses/detail.php?id=' . $course['id']) ?>"
             class="btn btn-outline btn-sm"
             id="btn-detail-<?= $course['id'] ?>">
            <i class="fa-solid fa-eye"></i> Ver detalles
          </a>

          <?php if ($isEnrolled): ?>
            <a href="<?= baseUrl('/user/my_courses.php') ?>" class="btn btn-success btn-sm">
              <i class="fa-solid fa-book-open"></i> Ir al curso
            </a>
          <?php elseif ($loggedIn): ?>
            <form method="POST" action="<?= baseUrl('/courses/enroll.php') ?>" style="display:inline;">
              <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
              <button type="submit" class="btn btn-primary btn-sm" id="btn-enroll-<?= $course['id'] ?>">
                <i class="fa-solid fa-graduation-cap"></i> Matricularme
              </button>
            </form>
          <?php else: ?>
            <a href="<?= baseUrl('/auth/login.php?redirect=' . urlencode(baseUrl('/courses/detail.php?id=' . $course['id']))) ?>"
               class="btn btn-primary btn-sm">
              <i class="fa-solid fa-graduation-cap"></i> Matricularme
            </a>
          <?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <!-- Metodología ABR -->
    <div class="fade-in" style="margin-top:72px;">
      <div class="section-header">
        <span class="section-label"><i class="fa-solid fa-arrows-spin"></i> Metodología</span>
        <h2 class="section-title mt-24">Aprendizaje Basado en <span class="text-gradient">Retos (ABR)</span></h2>
        <p class="section-subtitle">Cada curso sigue un ciclo de aprendizaje activo y significativo</p>
      </div>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:20px; max-width:900px; margin:0 auto;">
        <?php
        $abrSteps = [
          ['🎯', 'Problema real',       'Partes de un desafío auténtico del mundo actual'],
          ['⚙️',  'Uso de tecnología',  'Aplicas herramientas digitales reales'],
          ['🔄', 'Prueba y error',       'Iterás sobre tu solución y aprendes del proceso'],
          ['✅', 'Solución',             'Presentas un producto o propuesta funcional'],
          ['💡', 'Reflexión',            'Consolidas lo aprendido y proyectas su impacto'],
        ];
        foreach ($abrSteps as $idx => $step): ?>
        <div class="feature-card fade-in fade-in-delay-<?= $idx % 3 ?>" style="text-align:center; padding:24px 20px;">
          <div style="font-size:2.2rem; margin-bottom:12px;"><?= $step[0] ?></div>
          <h4 style="font-size:0.95rem; margin-bottom:8px;"><?= $step[1] ?></h4>
          <p style="font-size:0.8rem; color:var(--clr-text-muted);"><?= $step[2] ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
