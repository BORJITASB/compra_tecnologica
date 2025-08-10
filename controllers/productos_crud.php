<?php

session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../views/login.php');
    exit;
}
require_once("../config/config.php");

// Agregar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'agregar') {
    // Verificación CSRF
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        header('Location: ../views/productos_admin.php?mensaje=Token+inválido&tipo=danger');
        exit;
    }
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $id_categoria = intval($_POST['id_categoria']);
    $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, id_categoria) VALUES (?, ?, ?)");
    $stmt->execute([$nombre, $descripcion, $id_categoria]);
    header('Location: ../views/productos_admin.php');
    exit;
}

// Editar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'editar') {
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        header('Location: ../views/productos_admin.php?mensaje=Token+inválido&tipo=danger');
        exit;
    }
    $id = intval($_POST['id_producto']);
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $id_categoria = intval($_POST['id_categoria']);
    $stmt = $pdo->prepare("UPDATE productos SET nombre=?, descripcion=?, id_categoria=? WHERE id_producto=?");
    $stmt->execute([$nombre, $descripcion, $id_categoria, $id]);
    header('Location: ../views/productos_admin.php');
    exit;
}

// Eliminar producto
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $pdo->prepare("DELETE FROM productos WHERE id_producto=?");
    $stmt->execute([$id]);
    header('Location: ../views/productos_admin.php');
    exit;
}
?>