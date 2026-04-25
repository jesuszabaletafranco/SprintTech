<?php
// ============================================================
// payment/install_payments.php — Crear tabla certificate_payments
// Ejecutar UNA VEZ desde el navegador o consola
// ============================================================
require_once __DIR__ . '/../config/database.php';

$db = getDB();

$sqls = [

    // Tabla de pagos PSE
    "CREATE TABLE IF NOT EXISTS certificate_payments (
        id              INT AUTO_INCREMENT PRIMARY KEY,
        user_id         INT NOT NULL,
        course_id       INT NOT NULL,
        banco           VARCHAR(50) NOT NULL,
        tipo_persona    ENUM('natural','juridica') DEFAULT 'natural',
        tipo_doc        VARCHAR(20) NOT NULL,
        numero_doc      VARCHAR(30) NOT NULL,
        monto           DECIMAL(10,2) NOT NULL DEFAULT 15000.00,
        estado          ENUM('pendiente','aprobado','rechazado') DEFAULT 'pendiente',
        referencia_pse  VARCHAR(100),
        fecha_pago      DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
        INDEX idx_user_course (user_id, course_id),
        INDEX idx_estado (estado)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

];

$errors = [];
foreach ($sqls as $sql) {
    try {
        $db->exec($sql);
    } catch (PDOException $e) {
        $errors[] = $e->getMessage();
    }
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Instalación Pasarela PSE — SprintTech</title>
  <style>
    body { font-family: 'Inter', sans-serif; background:#080c18; color:#e2e8f0;
           display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
    .box { background:#0e1428; border:1px solid rgba(255,255,255,0.08); border-radius:16px;
           padding:40px 48px; max-width:520px; width:90%; text-align:center; }
    h1 { font-size:1.5rem; margin-bottom:16px; }
    .ok  { color:#10b981; }
    .err { color:#ef4444; background:rgba(239,68,68,0.1); padding:12px; border-radius:8px; margin-top:12px; font-size:0.85rem; text-align:left; }
    a { display:inline-block; margin-top:20px; background:linear-gradient(135deg,#00d4ff,#7c3aed);
        color:#fff; padding:12px 28px; border-radius:999px; text-decoration:none; font-weight:700; }
  </style>
</head>
<body>
  <div class="box">
    <?php if (empty($errors)): ?>
      <div style="font-size:3rem; margin-bottom:16px;">✅</div>
      <h1 class="ok">¡Pasarela PSE instalada!</h1>
      <p style="color:#64748b; margin-bottom:8px;">La tabla <code>certificate_payments</code> fue creada correctamente.</p>
      <a href="../user/my_courses.php">Ir a Mis Cursos</a>
    <?php else: ?>
      <div style="font-size:3rem; margin-bottom:16px;">❌</div>
      <h1>Error en la instalación</h1>
      <?php foreach ($errors as $err): ?>
        <div class="err"><?= htmlspecialchars($err) ?></div>
      <?php endforeach; ?>
      <a href="../user/my_courses.php">Ir al inicio</a>
    <?php endif; ?>
  </div>
</body>
</html>
