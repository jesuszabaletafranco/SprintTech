<?php
// ============================================================
// config/session.php — Helpers de sesión y autenticación
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica si el usuario está autenticado.
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Redirige a login si el usuario no está autenticado.
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

/**
 * Obtiene el dato de sesión del usuario actual.
 */
function currentUser(string $key = 'nombre'): mixed {
    return $_SESSION[$key] ?? null;
}

/**
 * Establece un mensaje flash para mostrar en la siguiente request.
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Obtiene y elimina el mensaje flash.
 */
function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Renderiza el HTML del mensaje flash si existe.
 */
function renderFlash(): void {
    $flash = getFlash();
    if ($flash) {
        $type    = htmlspecialchars($flash['type']);
        $message = htmlspecialchars($flash['message']);
        echo "<div class=\"flash flash--{$type}\">{$message}</div>";
    }
}

// Base URL dinámica
define('BASE_URL', rtrim(
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')
    . dirname($_SERVER['SCRIPT_NAME']),
    '/\\'
));

// Calcular BASE_URL correctamente para subdirectorios
function baseUrl(string $path = ''): string {
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    // Encuentra la raíz del proyecto (SprintTech)
    $root = '';
    if (preg_match('#^(.*/SprintTech)#i', $script, $m)) {
        $root = $m[1];
    }
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $protocol . '://' . $host . $root . '/' . ltrim($path, '/');
}
