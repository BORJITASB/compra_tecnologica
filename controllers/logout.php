<?php
session_start();
// Limpiar arreglo de sesión
$_SESSION = [];
// Eliminar cookie de sesión si existe
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
// Destruir la sesión
session_destroy();
header('Location: ../views/login.php');
exit;
