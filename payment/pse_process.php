<?php
// ============================================================
// payment/pse_process.php — Procesar pago PSE (simulado)
// ============================================================
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

$userId    = (int)($_SESSION['user_id'] ?? 0);
$courseId  = (int)($_POST['course_id'] ?? 0);
$banco     = trim($_POST['banco'] ?? '');
$tipoPers  = trim($_POST['tipo_persona'] ?? 'natural');
$tipoDoc   = trim($_POST['tipo_doc'] ?? '');
$numDoc    = trim($_POST['numero_doc'] ?? '');

// Validaciones básicas
$bancosPermitidos = ['nequi', 'bancolombia', 'davivienda', 'bogota', 'bbva', 'breb'];
$docsPermitidos   = ['CC', 'CE', 'NIT', 'PP', 'TI'];

if (!$courseId || !in_array($banco, $bancosPermitidos) ||
    !in_array($tipoDoc, $docsPermitidos) || strlen($numDoc) < 5) {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos o incompletos.']);
    exit;
}

$db = getDB();

// Verificar que el usuario está matriculado en el curso
$enrStmt = $db->prepare('SELECT id FROM enrollments WHERE user_id = ? AND course_id = ? AND activo = 1');
$enrStmt->execute([$userId, $courseId]);
if (!$enrStmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'No estás matriculado en este curso.']);
    exit;
}

// Verificar si ya pagó para este curso
$paidStmt = $db->prepare(
    'SELECT id FROM certificate_payments WHERE user_id = ? AND course_id = ? AND estado = "aprobado"'
);
$paidStmt->execute([$userId, $courseId]);
if ($paidStmt->fetch()) {
    echo json_encode(['success' => true, 'ya_pagado' => true, 'error' => 'Ya realizaste el pago para este curso.']);
    exit;
}

// Generar referencia única PSE
$referencia = 'PSE-' . strtoupper(substr(md5($userId . $courseId . microtime()), 0, 6))
            . '-' . date('ymdHis');

// Insertar pago en estado pendiente
$insStmt = $db->prepare('
    INSERT INTO certificate_payments
        (user_id, course_id, banco, tipo_persona, tipo_doc, numero_doc, monto, estado, referencia_pse)
    VALUES (?, ?, ?, ?, ?, ?, 15000.00, "pendiente", ?)
');
$insStmt->execute([$userId, $courseId, $banco, $tipoPers, $tipoDoc, $numDoc, $referencia]);
$paymentId = $db->lastInsertId();

// Simulación: 90% de éxito
sleep(1); // Simula latencia de red
$aprobado = (rand(1, 100) <= 90);

$nuevoEstado = $aprobado ? 'aprobado' : 'rechazado';
$db->prepare('UPDATE certificate_payments SET estado = ? WHERE id = ?')
   ->execute([$nuevoEstado, $paymentId]);

echo json_encode([
    'success'    => $aprobado,
    'estado'     => $nuevoEstado,
    'referencia' => $referencia,
    'monto'      => '15.000',
    'banco'      => $banco,
    'payment_id' => $paymentId,
    'message'    => $aprobado
        ? 'Pago aprobado exitosamente.'
        : 'Transacción rechazada. Intente con otra opción de pago.',
]);
