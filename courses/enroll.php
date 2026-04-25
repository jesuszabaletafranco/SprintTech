<?php
// ============================================================
// courses/enroll.php — Procesar matrícula
// ============================================================
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . baseUrl('/courses/index.php'));
    exit;
}

$courseId = (int)($_POST['course_id'] ?? 0);
$userId   = (int)currentUser('user_id') ?? 0;
$userId   = (int)($_SESSION['user_id'] ?? 0);

if ($courseId <= 0) {
    setFlash('error', 'Curso no válido.');
    header('Location: ' . baseUrl('/courses/index.php'));
    exit;
}

$db = getDB();

// Verificar que el curso existe
$stmt = $db->prepare('SELECT id, nombre FROM courses WHERE id = ? AND activo = 1');
$stmt->execute([$courseId]);
$course = $stmt->fetch();

if (!$course) {
    setFlash('error', 'El curso no existe o no está disponible.');
    header('Location: ' . baseUrl('/courses/index.php'));
    exit;
}

// Verificar si ya está matriculado (incluso dado de baja)
$check = $db->prepare('SELECT id, activo FROM enrollments WHERE user_id = ? AND course_id = ?');
$check->execute([$userId, $courseId]);
$existing = $check->fetch();

if ($existing) {
    if ($existing['activo']) {
        setFlash('warning', "Ya estás matriculado en «{$course['nombre']}».");
    } else {
        // Re-activar matrícula
        $reactivate = $db->prepare('UPDATE enrollments SET activo = 1, fecha_matricula = NOW() WHERE id = ?');
        $reactivate->execute([$existing['id']]);
        setFlash('success', "¡Te has vuelto a matricular en «{$course['nombre']}»! 🎉");
    }
} else {
    // Nueva matrícula
    $ins = $db->prepare('INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)');
    $ins->execute([$userId, $courseId]);
    setFlash('success', "¡Felicitaciones! Te matriculaste en «{$course['nombre']}» 🚀");
}

header('Location: ' . baseUrl('/user/my_courses.php'));
exit;
