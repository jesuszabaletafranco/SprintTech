<?php
// ============================================================
// user/certifications.php — Mis certificaciones
// ============================================================
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

requireLogin();

$userId = (int)($_SESSION['user_id'] ?? 0);
$db     = getDB();

// Obtener certificaciones existentes
$certStmt = $db->prepare('
    SELECT cert.*, c.nombre AS course_nombre, c.descripcion, c.icono, c.color_accent,
           c.duracion_horas
    FROM certifications cert
    JOIN courses c ON c.id = cert.course_id
    WHERE cert.user_id = ?
    ORDER BY cert.fecha_emision DESC
');
$certStmt->execute([$userId]);
$certifications = $certStmt->fetchAll();

// ────────────────────────────────────────────────────────────
// Auto-generar certificaciones para cursos con progreso = 100%
// (Simulación: si el curso lleva ≥ 80% de progreso se certifica)
// ────────────────────────────────────────────────────────────
$compStmt = $db->prepare('
    SELECT e.course_id, e.progreso, c.nombre
    FROM enrollments e
    JOIN courses c ON c.id = e.course_id
    WHERE e.user_id = ? AND e.activo = 1 AND e.progreso >= 100
');
$compStmt->execute([$userId]);
$completed = $compStmt->fetchAll();

foreach ($completed as $comp) {
    // Verificar si ya existe certificación para este curso
    $exists = $db->prepare('SELECT id FROM certifications WHERE user_id = ? AND course_id = ?');
    $exists->execute([$userId, $comp['course_id']]);
    if (!$exists->fetch()) {
        $code = 'SPRT-' . strtoupper(substr(md5($userId . $comp['course_id'] . time()), 0, 8));
        $ins  = $db->prepare('INSERT INTO certifications (user_id, course_id, codigo_cert) VALUES (?, ?, ?)');
        $ins->execute([$userId, $comp['course_id'], $code]);
    }
}

// Recargar certificaciones
$certStmt->execute([$userId]);
$certifications = $certStmt->fetchAll();

// Cursos completados al 100% para mostrar en "próximas"
$nearStmt = $db->prepare('
    SELECT e.course_id, e.progreso, c.nombre, c.icono, c.color_accent
    FROM enrollments e
    JOIN courses c ON c.id = e.course_id
    LEFT JOIN certifications cert ON cert.user_id = e.user_id AND cert.course_id = e.course_id
    WHERE e.user_id = ? AND e.activo = 1 AND e.progreso < 100 AND cert.id IS NULL
    ORDER BY e.progreso DESC
    LIMIT 3
');
$nearStmt->execute([$userId]);
$nearCerts = $nearStmt->fetchAll();

$pageTitle = 'Mis Certificaciones — SprintTech';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Hero -->
<div class="dashboard-hero">
  <div class="container">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:24px;">
      <div class="fade-in">
        <h1 class="dashboard-greeting">
          <i class="fa-solid fa-certificate" style="color:var(--clr-amber);"></i> Mis Certificaciones
        </h1>
        <p class="dashboard-subtext">Reconocimientos por tus logros en SprintTech</p>
      </div>
      <div style="display:flex; gap:24px; flex-wrap:wrap;" class="fade-in fade-in-delay-1">
        <div style="background:var(--clr-bg-card); border:1px solid var(--clr-border); border-radius:var(--radius-md); padding:16px 24px; text-align:center;">
          <div style="font-size:1.8rem; font-weight:800; color:var(--clr-amber); font-family:var(--ff-heading);"><?= count($certifications) ?></div>
          <div style="font-size:0.8rem; color:var(--clr-text-muted);">Certificaciones</div>
        </div>
        <a href="<?= baseUrl('/courses/index.php') ?>" class="btn btn-primary" style="align-self:center;">
          <i class="fa-solid fa-graduation-cap"></i> Seguir aprendiendo
        </a>
      </div>
    </div>
  </div>
</div>

<section class="section">
  <div class="container">

    <?php if (empty($certifications)): ?>
    <!-- Estado vacío -->
    <div class="empty-state fade-in">
      <div class="empty-icon"><i class="fa-solid fa-certificate"></i></div>
      <h2 class="empty-title">Aún no tienes certificaciones</h2>
      <p class="empty-desc">
        Completa los retos de tus cursos al 100% para obtener tu certificado digital de SprintTech.
      </p>
      <a href="<?= baseUrl('/user/my_courses.php') ?>" class="btn btn-primary btn-lg">
        <i class="fa-solid fa-book-open"></i> Ver mis cursos
      </a>
    </div>

    <?php else: ?>

    <!-- Grid de certificaciones -->
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(350px, 1fr)); gap:24px; margin-bottom:56px;">
      <?php foreach ($certifications as $i => $cert): ?>
      <div class="cert-card fade-in fade-in-delay-<?= $i % 3 ?>" id="cert-<?= $cert['id'] ?>">

        <!-- Decoración de diploma -->
        <div style="position:absolute; top:16px; right:16px; opacity:0.08; font-size:4rem; pointer-events:none;">
          <i class="fa-solid <?= htmlspecialchars($cert['icono']) ?>"></i>
        </div>

        <div class="cert-header">
          <div class="cert-medal">
            <i class="fa-solid fa-award"></i>
          </div>
          <div>
            <div class="cert-course-name"><?= htmlspecialchars($cert['course_nombre']) ?></div>
            <div class="cert-date">
              <i class="fa-regular fa-calendar"></i>
              Emitido el <?= date('d \d\e F \d\e Y', strtotime($cert['fecha_emision'])) ?>
            </div>
          </div>
        </div>

        <!-- Nombre del estudiante -->
        <div style="background:rgba(0,0,0,0.2); border-radius:var(--radius-md); padding:14px 18px; margin-bottom:16px;">
          <div style="font-size:0.75rem; color:var(--clr-text-dim); margin-bottom:4px; text-transform:uppercase; letter-spacing:0.08em;">Certificado a</div>
          <div style="font-size:1.1rem; font-weight:700; color:var(--clr-white);">
            <?= htmlspecialchars(($_SESSION['nombre'] ?? '') . ' ' . ($_SESSION['apellido'] ?? '')) ?>
          </div>
          <div style="font-size:0.8rem; color:var(--clr-text-muted); margin-top:4px;">
            Por completar exitosamente el curso con metodología ABR
          </div>
        </div>

        <!-- Código del certificado -->
        <div class="cert-code-block">
          <span class="cert-code" id="cert-code-<?= $cert['id'] ?>"><?= htmlspecialchars($cert['codigo_cert']) ?></span>
          <button class="btn-copy-cert"
                  data-code="<?= htmlspecialchars($cert['codigo_cert']) ?>"
                  id="btn-copy-<?= $cert['id'] ?>"
                  title="Copiar código"
                  style="background:none; border:none; cursor:pointer; color:var(--clr-text-muted); font-size:0.85rem; padding:4px 8px; border-radius:4px; transition:color 0.2s;">
            <i class="fa-regular fa-copy"></i>
          </button>
        </div>

        <!-- Badges -->
        <div style="display:flex; gap:8px; margin-top:16px; flex-wrap:wrap;">
          <span class="badge badge-green"><i class="fa-solid fa-check"></i> Completado</span>
          <span class="badge badge-amber"><i class="fa-regular fa-clock"></i> <?= (int)$cert['duracion_horas'] ?>h</span>
          <span class="badge badge-cyan"><i class="fa-solid fa-rocket"></i> ABR</span>
        </div>

      </div>
      <?php endforeach; ?>
    </div>

    <?php endif; ?>

    <!-- Próximas certificaciones -->
    <?php if (!empty($nearCerts)): ?>
    <div class="fade-in">
      <div class="section-header" style="text-align:left; margin-bottom:24px;">
        <h3 style="font-size:1.3rem; display:flex; align-items:center; gap:10px;">
          <i class="fa-solid fa-hourglass-half" style="color:var(--clr-amber);"></i>
          En progreso — próximas certificaciones
        </h3>
        <p style="color:var(--clr-text-muted); font-size:0.9rem; margin-top:6px;">
          Completa estos cursos al 100% para obtener tu certificado
        </p>
      </div>

      <div style="display:flex; flex-direction:column; gap:12px;">
        <?php foreach ($nearCerts as $near): ?>
        <div style="background:var(--clr-bg-card); border:1px solid var(--clr-border); border-radius:var(--radius-md); padding:16px 20px; display:flex; align-items:center; gap:16px;">
          <div style="width:44px; height:44px; border-radius:10px; background:linear-gradient(135deg, <?= htmlspecialchars($near['color_accent']) ?>, #7c3aed); display:flex; align-items:center; justify-content:center; color:white; font-size:1.2rem; flex-shrink:0;">
            <i class="fa-solid <?= htmlspecialchars($near['icono']) ?>"></i>
          </div>
          <div style="flex:1; min-width:0;">
            <div style="font-size:0.95rem; font-weight:600; color:var(--clr-white); margin-bottom:8px;">
              <?= htmlspecialchars($near['nombre']) ?>
            </div>
            <div class="progress-wrap">
              <div class="progress-bar" data-width="<?= (int)$near['progreso'] ?>" style="width:0%;"></div>
            </div>
            <div style="font-size:0.75rem; color:var(--clr-text-muted); margin-top:4px;">
              <?= (int)$near['progreso'] ?>% completado — faltan <?= 100 - (int)$near['progreso'] ?>%
            </div>
          </div>
          <a href="<?= baseUrl('/courses/detail.php?id=' . $near['course_id']) ?>" class="btn btn-ghost btn-sm">
            Continuar <i class="fa-solid fa-arrow-right"></i>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
