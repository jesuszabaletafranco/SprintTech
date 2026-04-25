<?php
// ============================================================
// user/download_cert.php — Descarga / Vista del certificado
// Solo accesible si el pago fue aprobado
// ============================================================
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

requireLogin();

$userId   = (int)($_SESSION['user_id'] ?? 0);
$courseId = (int)($_GET['course_id'] ?? 0);
$db       = getDB();

if (!$courseId) {
    header('Location: ' . baseUrl('/user/certifications.php'));
    exit;
}

// Verificar que el pago esté aprobado
$payStmt = $db->prepare('
    SELECT cp.referencia_pse, cp.fecha_pago, cp.banco
    FROM certificate_payments cp
    WHERE cp.user_id = ? AND cp.course_id = ? AND cp.estado = "aprobado"
    ORDER BY cp.fecha_pago DESC
    LIMIT 1
');
$payStmt->execute([$userId, $courseId]);
$payment = $payStmt->fetch();

if (!$payment) {
    // No hay pago aprobado — redirigir con mensaje
    setFlash('error', 'Debes realizar el pago PSE antes de descargar tu certificado.');
    header('Location: ' . baseUrl('/user/my_courses.php'));
    exit;
}

// Obtener datos del certificado
$certStmt = $db->prepare('
    SELECT cert.codigo_cert, cert.fecha_emision,
           c.nombre AS course_nombre, c.duracion_horas, c.icono, c.color_accent
    FROM certifications cert
    JOIN courses c ON c.id = cert.course_id
    WHERE cert.user_id = ? AND cert.course_id = ?
');
$certStmt->execute([$userId, $courseId]);
$cert = $certStmt->fetch();

// Si el certificado no existe aún pero ya pagó, generarlo
if (!$cert) {
    $code = 'SPRT-' . strtoupper(substr(md5($userId . $courseId . time()), 0, 8));
    $db->prepare('INSERT IGNORE INTO certifications (user_id, course_id, codigo_cert) VALUES (?, ?, ?)')
       ->execute([$userId, $courseId, $code]);
    $certStmt->execute([$userId, $courseId]);
    $cert = $certStmt->fetch();
}

// Obtener nombre del usuario
$userStmt = $db->prepare('SELECT nombre, apellido, email FROM users WHERE id = ?');
$userStmt->execute([$userId]);
$user = $userStmt->fetch();

$nombreCompleto = htmlspecialchars(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? ''));
$email          = htmlspecialchars($user['email'] ?? '');
$courseName     = htmlspecialchars($cert['course_nombre'] ?? '');
$codigo         = htmlspecialchars($cert['codigo_cert'] ?? '');
$fechaEmision   = date('d \d\e F \d\e Y', strtotime($cert['fecha_emision'] ?? 'now'));
$duracion       = (int)($cert['duracion_horas'] ?? 0);
$colorAccent    = htmlspecialchars($cert['color_accent'] ?? '#00d4ff');
$icon           = htmlspecialchars($cert['icono'] ?? 'fa-brain');
$referencia     = htmlspecialchars($payment['referencia_pse'] ?? '');
$fechaPago      = date('d/m/Y H:i', strtotime($payment['fecha_pago'] ?? 'now'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Certificado — <?= $courseName ?> · SprintTech</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    /* ── Variables ── */
    :root {
      --accent: <?= $colorAccent ?>;
      --bg-dark: #080c18;
      --bg-card: #0e1428;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #080c18 0%, #0d1a3a 50%, #1a0d2e 100%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 40px 20px;
      color: #e2e8f0;
    }

    /* ── Top bar ── */
    .top-bar {
      width: 100%;
      max-width: 900px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 32px;
      gap: 16px;
      flex-wrap: wrap;
    }

    .top-bar a {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: #94a3b8;
      text-decoration: none;
      font-size: 0.88rem;
      transition: color 0.2s;
    }

    .top-bar a:hover { color: #e2e8f0; }

    .btn-print {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 24px;
      background: linear-gradient(135deg, #00d4ff, #7c3aed);
      color: white;
      border: none;
      border-radius: 999px;
      font-size: 0.9rem;
      font-weight: 700;
      font-family: 'Outfit', sans-serif;
      cursor: pointer;
      transition: all 0.25s;
      box-shadow: 0 4px 20px rgba(0,212,255,0.3);
    }

    .btn-print:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 28px rgba(0,212,255,0.4);
    }

    /* ── Certificado ── */
    .cert-wrapper {
      width: 100%;
      max-width: 860px;
      background: linear-gradient(145deg, #0e1428, #121a35);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 40px 100px rgba(0,0,0,0.7), 0 0 80px rgba(0,212,255,0.06);
      position: relative;
    }

    /* ── Borde decorativo superior ── */
    .cert-top-band {
      height: 8px;
      background: linear-gradient(90deg, #00d4ff, var(--accent), #7c3aed);
    }

    /* ── Fondo con patrón geométrico sutil ── */
    .cert-bg-pattern {
      position: absolute;
      inset: 0;
      opacity: 0.03;
      background-image:
        repeating-linear-gradient(45deg, #fff 0, #fff 1px, transparent 0, transparent 50%);
      background-size: 20px 20px;
      pointer-events: none;
    }

    /* ── Orbes de fondo ── */
    .cert-glow-1 {
      position: absolute;
      top: -60px; left: -60px;
      width: 280px; height: 280px;
      background: radial-gradient(circle, rgba(0,212,255,0.12) 0%, transparent 70%);
      pointer-events: none;
    }

    .cert-glow-2 {
      position: absolute;
      bottom: -60px; right: -60px;
      width: 280px; height: 280px;
      background: radial-gradient(circle, rgba(124,58,237,0.14) 0%, transparent 70%);
      pointer-events: none;
    }

    /* ── Contenido ── */
    .cert-content {
      position: relative;
      z-index: 1;
      padding: 52px 60px;
      text-align: center;
    }

    @media (max-width: 600px) {
      .cert-content { padding: 32px 24px; }
    }

    /* Header */
    .cert-brand {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      margin-bottom: 36px;
    }

    .cert-brand-icon {
      width: 44px; height: 44px;
      background: linear-gradient(135deg, #00d4ff, #7c3aed);
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      color: #fff; font-size: 1.2rem;
    }

    .cert-brand-name {
      font-family: 'Outfit', sans-serif;
      font-size: 1.5rem;
      font-weight: 900;
      color: #fff;
      letter-spacing: -0.02em;
    }

    .cert-brand-name span { color: #00d4ff; }

    /* Distinción */
    .cert-label {
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.25em;
      text-transform: uppercase;
      color: #64748b;
      margin-bottom: 8px;
    }

    .cert-headline {
      font-family: 'Outfit', sans-serif;
      font-size: clamp(1.6rem, 4vw, 2.4rem);
      font-weight: 900;
      color: #fff;
      margin-bottom: 32px;
      line-height: 1.2;
    }

    /* Medalla */
    .cert-medal {
      width: 90px; height: 90px;
      margin: 0 auto 28px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 2.4rem;
      background: linear-gradient(135deg, var(--accent), #7c3aed);
      box-shadow: 0 0 40px rgba(0,212,255,0.3);
      position: relative;
    }

    .cert-medal::after {
      content: '';
      position: absolute;
      inset: -4px;
      border-radius: 50%;
      border: 2px solid rgba(0,212,255,0.3);
    }

    /* Nombre del graduado */
    .cert-person-box {
      background: rgba(0,0,0,0.25);
      border: 1px solid rgba(255,255,255,0.07);
      border-radius: 16px;
      padding: 20px 32px;
      margin-bottom: 28px;
      display: inline-block;
      min-width: 300px;
    }

    .cert-person-label {
      font-size: 0.72rem;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.12em;
      margin-bottom: 8px;
    }

    .cert-person-name {
      font-family: 'Outfit', sans-serif;
      font-size: clamp(1.4rem, 3vw, 1.9rem);
      font-weight: 800;
      color: #fff;
      letter-spacing: -0.02em;
    }

    .cert-person-sub {
      font-size: 0.8rem;
      color: #64748b;
      margin-top: 6px;
    }

    /* Curso */
    .cert-course-label {
      font-size: 0.78rem;
      color: #64748b;
      margin-bottom: 6px;
    }

    .cert-course-name {
      font-family: 'Outfit', sans-serif;
      font-size: clamp(1rem, 2.5vw, 1.4rem);
      font-weight: 700;
      color: #00d4ff;
      margin-bottom: 28px;
      line-height: 1.3;
    }

    /* Badges */
    .cert-badges {
      display: flex;
      justify-content: center;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 36px;
    }

    .cert-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 16px;
      border-radius: 999px;
      font-size: 0.78rem;
      font-weight: 600;
    }

    .cert-badge-green  { background: rgba(16,185,129,0.12); color: #10b981; border: 1px solid rgba(16,185,129,0.3); }
    .cert-badge-amber  { background: rgba(245,158,11,0.12); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3); }
    .cert-badge-cyan   { background: rgba(0,212,255,0.1);   color: #00d4ff; border: 1px solid rgba(0,212,255,0.3); }
    .cert-badge-violet { background: rgba(124,58,237,0.12); color: #a78bfa; border: 1px solid rgba(124,58,237,0.3); }

    /* Separador con línea decorativa */
    .cert-divider {
      display: flex;
      align-items: center;
      gap: 16px;
      margin-bottom: 32px;
    }

    .cert-divider-line {
      flex: 1;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
    }

    .cert-divider-icon { color: #f59e0b; font-size: 1rem; }

    /* Código del certificado */
    .cert-code-section {
      background: rgba(0,0,0,0.3);
      border: 1px solid rgba(0,212,255,0.15);
      border-radius: 12px;
      padding: 16px 24px;
      margin-bottom: 32px;
      display: inline-block;
    }

    .cert-code-label {
      font-size: 0.7rem;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.12em;
      margin-bottom: 6px;
    }

    .cert-code-value {
      font-family: 'Courier New', monospace;
      font-size: 1.05rem;
      font-weight: 700;
      color: #00d4ff;
      letter-spacing: 0.1em;
    }

    /* Referencia PSE */
    .cert-pse-ref {
      font-size: 0.72rem;
      color: #475569;
      margin-top: 6px;
    }

    /* Footer del cert */
    .cert-footer {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      padding-top: 28px;
      border-top: 1px solid rgba(255,255,255,0.06);
      flex-wrap: wrap;
      gap: 16px;
      text-align: left;
    }

    .cert-footer-item h5 {
      font-family: 'Outfit', sans-serif;
      font-size: 0.95rem;
      font-weight: 700;
      color: #fff;
      margin-bottom: 3px;
    }

    .cert-footer-item p {
      font-size: 0.75rem;
      color: #64748b;
    }

    .cert-signature-line {
      width: 120px;
      height: 2px;
      background: linear-gradient(90deg, #00d4ff, #7c3aed);
      margin-bottom: 8px;
      border-radius: 2px;
    }

    /* ── Print styles ── */
    @media print {
      body { background: white !important; padding: 0; }
      .top-bar { display: none; }
      .cert-wrapper {
        box-shadow: none;
        border: 2px solid #ddd;
        border-radius: 0;
        max-width: 100%;
      }
      .cert-content { padding: 40px; }
      /* Colores para impresión */
      .cert-headline { color: #1a1a2e !important; }
      .cert-person-name { color: #1a1a2e !important; }
    }
  </style>
</head>
<body>

  <!-- Top bar de navegación -->
  <div class="top-bar no-print">
    <a href="<?= baseUrl('/user/my_courses.php') ?>">
      <i class="fa-solid fa-arrow-left"></i> Volver a Mis Cursos
    </a>
    <button class="btn-print" onclick="window.print()">
      <i class="fa-solid fa-print"></i> Imprimir / Guardar PDF
    </button>
  </div>

  <!-- Certificado -->
  <div class="cert-wrapper">
    <div class="cert-top-band"></div>
    <div class="cert-bg-pattern"></div>
    <div class="cert-glow-1"></div>
    <div class="cert-glow-2"></div>

    <div class="cert-content">

      <!-- Marca -->
      <div class="cert-brand">
        <div class="cert-brand-icon"><i class="fa-solid fa-bolt-lightning"></i></div>
        <div class="cert-brand-name">Sprint<span>Tech</span></div>
      </div>

      <div class="cert-label">Certificado oficial de aprobación</div>
      <h1 class="cert-headline">Certificado de Logro</h1>

      <!-- Medalla con el ícono del curso -->
      <div class="cert-medal">
        <i class="fa-solid <?= $icon ?>"></i>
      </div>

      <!-- Se otorga a -->
      <div class="cert-course-label">Este certificado se otorga a</div>
      <div class="cert-person-box">
        <div class="cert-person-label">Estudiante certificado</div>
        <div class="cert-person-name"><?= $nombreCompleto ?></div>
        <div class="cert-person-sub"><?= $email ?></div>
      </div>

      <!-- Curso -->
      <div class="cert-course-label">Por completar exitosamente el curso</div>
      <div class="cert-course-name"><?= $courseName ?></div>

      <!-- Badges informativos -->
      <div class="cert-badges">
        <span class="cert-badge cert-badge-green">
          <i class="fa-solid fa-check"></i> Curso completado
        </span>
        <span class="cert-badge cert-badge-amber">
          <i class="fa-regular fa-clock"></i> <?= $duracion ?>h de formación
        </span>
        <span class="cert-badge cert-badge-cyan">
          <i class="fa-solid fa-rocket"></i> Metodología ABR
        </span>
        <span class="cert-badge cert-badge-violet">
          <i class="fa-solid fa-calendar-check"></i> <?= $fechaEmision ?>
        </span>
      </div>

      <!-- Divider -->
      <div class="cert-divider">
        <div class="cert-divider-line"></div>
        <div class="cert-divider-icon"><i class="fa-solid fa-award"></i></div>
        <div class="cert-divider-line"></div>
      </div>

      <!-- Código de verificación -->
      <div class="cert-code-section">
        <div class="cert-code-label">Código de verificación</div>
        <div class="cert-code-value"><?= $codigo ?></div>
        <?php if ($referencia): ?>
        <div class="cert-pse-ref">Ref. PSE: <?= $referencia ?> · Pagado <?= $fechaPago ?></div>
        <?php endif; ?>
      </div>

      <!-- Footer con firmas -->
      <div class="cert-footer">
        <div class="cert-footer-item">
          <div class="cert-signature-line"></div>
          <h5>Director Académico</h5>
          <p>SprintTech LearningWithIA</p>
        </div>
        <div class="cert-footer-item" style="text-align:center;">
          <div style="font-size:2rem; color:#f59e0b; margin-bottom:4px;">
            <i class="fa-solid fa-certificate"></i>
          </div>
          <p style="font-size:0.7rem; color:#475569;">Verificado digitalmente</p>
        </div>
        <div class="cert-footer-item" style="text-align:right;">
          <div class="cert-signature-line" style="margin-left:auto;"></div>
          <h5>Coordinadora de Certificaciones</h5>
          <p>SprintTech LearningWithIA</p>
        </div>
      </div>

    </div><!-- /cert-content -->
  </div><!-- /cert-wrapper -->

</body>
</html>
