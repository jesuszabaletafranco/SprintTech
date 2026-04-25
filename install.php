<?php
// ============================================================
// install.php — Instalador automático de la base de datos
// Accede a: http://localhost/SprintTech/SprintTech/install.php
// ELIMINA ESTE ARCHIVO DESPUÉS DE LA INSTALACIÓN
// ============================================================

// Configuración de conexión directa (sin BD)
$host    = 'localhost';
$user    = 'root';
$pass    = '';
$dbName  = 'sprinttech';
$sqlFile = __DIR__ . '/database/schema.sql';

$errors   = [];
$messages = [];
$success  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. Conectar sin seleccionar BD
        $pdo = new PDO(
            "mysql:host={$host};charset=utf8mb4",
            $user,
            $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $messages[] = "✅ Conexión a MySQL exitosa.";

        // 2. Leer el archivo SQL
        if (!file_exists($sqlFile)) {
            throw new Exception("No se encontró el archivo: {$sqlFile}");
        }

        $sql = file_get_contents($sqlFile);
        $messages[] = "✅ Archivo SQL leído correctamente.";

        // 3. Ejecutar sentencias
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            fn($s) => !empty($s)
        );

        foreach ($statements as $stmt) {
            $pdo->exec($stmt);
        }
        $messages[] = "✅ Base de datos <strong>{$dbName}</strong> creada e instalada.";
        $messages[] = "✅ Tablas creadas: users, courses, modules, course_challenges, enrollments, certifications.";
        $messages[] = "✅ Datos semilla insertados: 6 cursos, módulos y retos.";

        $success = true;
    } catch (PDOException $e) {
        $errors[] = "❌ Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        $errors[] = "❌ Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Instalador — SprintTech</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', sans-serif;
      background: #080c18;
      color: #e2e8f0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 24px;
    }
    .card {
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 24px;
      padding: 48px;
      max-width: 600px;
      width: 100%;
      box-shadow: 0 32px 80px rgba(0,0,0,0.6);
    }
    .logo {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 32px;
      justify-content: center;
    }
    .logo-icon {
      width: 48px; height: 48px;
      background: linear-gradient(135deg, #00d4ff, #7c3aed);
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 22px; color: white;
    }
    .logo-text {
      font-family: 'Outfit', sans-serif;
      font-size: 1.6rem;
      font-weight: 800;
      color: white;
    }
    .logo-text span { color: #00d4ff; }
    h1 { font-family: 'Outfit', sans-serif; font-size: 1.8rem; font-weight: 700; text-align: center; margin-bottom: 8px; }
    .subtitle { text-align: center; color: #94a3b8; margin-bottom: 32px; font-size: 0.95rem; line-height: 1.6; }
    .info-box {
      background: rgba(0,212,255,0.08);
      border: 1px solid rgba(0,212,255,0.3);
      border-radius: 12px;
      padding: 16px 20px;
      margin-bottom: 24px;
      font-size: 0.88rem;
      line-height: 1.6;
    }
    .info-box strong { color: #00d4ff; }
    .info-row { display: flex; justify-content: space-between; padding: 6px 0; }
    .info-row span:first-child { color: #94a3b8; }
    .info-row span:last-child { color: white; font-weight: 600; }
    .btn {
      display: block;
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, #00d4ff, #7c3aed);
      border: none;
      border-radius: 100px;
      color: white;
      font-family: 'Outfit', sans-serif;
      font-size: 1rem;
      font-weight: 700;
      cursor: pointer;
      transition: filter 0.2s, transform 0.2s;
      text-decoration: none;
      text-align: center;
    }
    .btn:hover { filter: brightness(1.1); transform: translateY(-1px); }
    .btn-outline {
      background: transparent;
      border: 2px solid #00d4ff;
      color: #00d4ff;
      margin-top: 12px;
    }
    .msg { padding: 10px 16px; border-radius: 8px; font-size: 0.88rem; margin-bottom: 8px; }
    .msg-ok  { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); color: #10b981; }
    .msg-err { background: rgba(239,68,68,0.1);  border: 1px solid rgba(239,68,68,0.3);  color: #ef4444; }
    .warning {
      background: rgba(245,158,11,0.1);
      border: 1px solid rgba(245,158,11,0.3);
      border-radius: 10px;
      padding: 14px 18px;
      font-size: 0.85rem;
      color: #f59e0b;
      margin-top: 20px;
      text-align: center;
    }
    .messages { margin: 20px 0; }
  </style>
</head>
<body>
  <div class="card">
    <div class="logo">
      <div class="logo-icon">⚡</div>
      <div class="logo-text">Sprint<span>Tech</span></div>
    </div>

    <h1>Instalador de la Base de Datos</h1>
    <p class="subtitle">Este script crea la base de datos, tablas y datos iniciales para SprintTech LearningWithIA.</p>

    <?php if (!$success): ?>

    <div class="info-box">
      <strong>Configuración de conexión:</strong>
      <div class="info-row">
        <span>Host:</span><span><?= htmlspecialchars($host) ?></span>
      </div>
      <div class="info-row">
        <span>Usuario:</span><span><?= htmlspecialchars($user) ?></span>
      </div>
      <div class="info-row">
        <span>Base de datos:</span><span><?= htmlspecialchars($dbName) ?></span>
      </div>
      <div class="info-row">
        <span>Stock de cursos:</span><span>6 cursos + módulos + retos</span>
      </div>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="messages">
      <?php foreach ($errors as $err): ?>
        <div class="msg msg-err"><?= $err ?></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST">
      <button type="submit" class="btn" id="installBtn">
        ⚡ Instalar Base de Datos
      </button>
    </form>

    <?php else: ?>

    <div class="messages">
      <?php foreach ($messages as $m): ?>
        <div class="msg msg-ok"><?= $m ?></div>
      <?php endforeach; ?>
    </div>

    <a href="<?= rtrim(dirname($_SERVER['PHP_SELF']), '/') ?>/" class="btn">
      🚀 Ir a SprintTech
    </a>

    <div class="warning">
      ⚠️ <strong>Importante:</strong> Elimina este archivo <code>install.php</code> después de la instalación por seguridad.
    </div>

    <?php endif; ?>
  </div>
</body>
</html>
