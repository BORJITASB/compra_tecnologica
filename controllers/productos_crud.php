<?php

session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../views/login.php');
    exit;
}
require_once("../config/config.php");

// Detectar si existe columna imagen en la tabla productos (modo tolerante)
$hasImagen = false;
try { $pdo->query("SELECT imagen FROM productos LIMIT 1"); $hasImagen = true; } catch(Exception $e) { $hasImagen = false; }

// Preparar carpeta de subida si se maneja imagen
if ($hasImagen) {
    $uploadDir = realpath(__DIR__ . '/../assets/img');
    if ($uploadDir && is_dir($uploadDir)) {
        $uploadSubDir = $uploadDir . DIRECTORY_SEPARATOR . 'productos';
        if (!is_dir($uploadSubDir)) @mkdir($uploadSubDir, 0775, true);
    } else {
        $hasImagen = false; // Fallback si ruta base no existe
    }
}

function manejarSubidaImagen($campo, $hasImagen) {
    if (!$hasImagen || empty($_FILES[$campo]) || ($_FILES[$campo]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null; // No se subió imagen
    }
    $file = $_FILES[$campo];
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    // Validar tamaño (<= 2MB)
    if ($file['size'] > 2 * 1024 * 1024) return null;
    // Validar extensión
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg','jpeg','png','webp','gif'];
    if (!in_array($ext, $permitidas)) return null;
    // Nombre seguro
    $base = preg_replace('/[^a-zA-Z0-9_-]/','', pathinfo($file['name'], PATHINFO_FILENAME));
    if ($base === '') $base = 'img';
    $nombreFinal = $base . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
    $dest = realpath(__DIR__ . '/../assets/img') . DIRECTORY_SEPARATOR . 'productos' . DIRECTORY_SEPARATOR . $nombreFinal;
    if (@move_uploaded_file($file['tmp_name'], $dest)) {
        return 'productos/' . $nombreFinal; // Ruta relativa desde assets/img
    }
    return null;
}

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
    $imagen = manejarSubidaImagen('imagen', $hasImagen);
    if ($hasImagen) {
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, id_categoria, imagen) VALUES (?,?,?,?)");
        $stmt->execute([$nombre, $descripcion, $id_categoria, $imagen]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, id_categoria) VALUES (?, ?, ?)");
        $stmt->execute([$nombre, $descripcion, $id_categoria]);
    }
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
    $imagenActual = $_POST['imagen_actual'] ?? null;
    $nuevaImagen = manejarSubidaImagen('imagen', $hasImagen);
    if ($hasImagen) {
        if ($nuevaImagen) {
            $stmt = $pdo->prepare("UPDATE productos SET nombre=?, descripcion=?, id_categoria=?, imagen=? WHERE id_producto=?");
            $stmt->execute([$nombre, $descripcion, $id_categoria, $nuevaImagen, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE productos SET nombre=?, descripcion=?, id_categoria=? WHERE id_producto=?");
            $stmt->execute([$nombre, $descripcion, $id_categoria, $id]);
        }
    } else {
        $stmt = $pdo->prepare("UPDATE productos SET nombre=?, descripcion=?, id_categoria=? WHERE id_producto=?");
        $stmt->execute([$nombre, $descripcion, $id_categoria, $id]);
    }
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