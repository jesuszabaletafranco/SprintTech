<?php
// ============================================================
// user/unenroll.php — Dar de baja de un curso
// ============================================================
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . baseUrl('/user/my_courses.php'));
    exit;
}

$enrollmentId = (int)($_POST['enrollment_id'] ?? 0);
$userId       = (int)($_SESSION['user_id'] ?? 0);

if ($enrollmentId <= 0) {
    setFlash('error', 'Solicitud inválida.');
    header('Location: ' . baseUrl('/user/my_courses.php'));
    exit;
}

$db = getDB();

// Verificar que la matrícula pertenece al usuario actual
$stmt = $db->prepare('SELECT e.id, c.nombre FROM enrollments e JOIN courses c ON c.id = e.course_id WHERE e.id = ? AND e.user_id = ? AND e.activo = 1');
$stmt->execute([$enrollmentId, $userId]);
$enrollment = $stmt->fetch();

if (!$enrollment) {
    setFlash('error', 'No se encontró la matrícula.');
    header('Location: ' . baseUrl('/user/my_courses.php'));
    exit;
}

// Soft delete
$upd = $db->prepare('UPDATE enrollments SET activo = 0 WHERE id = ?');
$upd->execute([$enrollmentId]);

setFlash('info', "Te retiraste del curso «{$enrollment['nombre']}». Puedes volver a matricularte cuando quieras.");
header('Location: ' . baseUrl('/user/my_courses.php'));
exit;
