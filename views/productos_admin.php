<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require_once("../config/config.php");



// Obtener productos y categorías
$stmt = $pdo->query("SELECT p.id_producto, p.nombre, p.descripcion, c.nombre AS categoria FROM productos p JOIN categorias c ON p.id_categoria = c.id_categoria ORDER BY p.id_producto DESC");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtCat = $pdo->query("SELECT id_categoria, nombre FROM categorias ORDER BY nombre ASC");
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
        <?php if (isset($_GET['mensaje'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_GET['tipo'] ?? 'success') ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['mensaje']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>
    <h2>Gestionar Productos</h2>
    <a href="admin_dashboard.php" class="btn btn-secondary mb-3">Volver al Dashboard</a>
    <!-- Formulario para agregar producto -->
    <div class="card mb-4">
        <div class="card-body">
            <h5>Agregar Producto</h5>
            <form method="POST" action="../controllers/productos_crud.php">
                <input type="hidden" name="accion" value="agregar">
                <div class="mb-3">
                    <label>Nombre</label>
                    <input type="text" class="form-control" name="nombre" required>
                </div>
                <div class="mb-3">
                    <label>Descripción</label>
                    <textarea class="form-control" name="descripcion" required></textarea>
                </div>
                <div class="mb-3">
                    <label>Categoría</label>
                    <select class="form-select" name="id_categoria" required>
                        <option value="">Seleccione</option>
                        <?php foreach($categorias as $cat): ?>
                            <option value="<?= $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Agregar</button>
            </form>
        </div>
    </div>
    <!-- Tabla de productos -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Categoría</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($productos as $prod): ?>
            <tr>
                <td><?= htmlspecialchars($prod['nombre']) ?></td>
                <td><?= htmlspecialchars($prod['descripcion']) ?></td>
                <td><?= htmlspecialchars($prod['categoria']) ?></td>
                <td>
                    <a href="productos_admin.php?editar=<?= $prod['id_producto'] ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="../controllers/productos_crud.php?eliminar=<?= $prod['id_producto'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este producto?')">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- Formulario de edición (opcional, solo si se selecciona editar) -->
    <?php
    if (isset($_GET['editar'])):
        $id = intval($_GET['editar']);
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id_producto = ?");
        $stmt->execute([$id]);
        $prodEdit = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($prodEdit):
    ?>
    <div class="card mt-4">
        <div class="card-body">
            <h5>Editar Producto</h5>
            <form method="POST" action="../controllers/productos_crud.php">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id_producto" value="<?= $prodEdit['id_producto'] ?>">
                <div class="mb-3">
                    <label>Nombre</label>
                    <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($prodEdit['nombre']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Descripción</label>
                    <textarea class="form-control" name="descripcion" required><?= htmlspecialchars($prodEdit['descripcion']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label>Categoría</label>
                    <select class="form-select" name="id_categoria" required>
                        <?php foreach($categorias as $cat): ?>
                            <option value="<?= $cat['id_categoria'] ?>" <?= $cat['id_categoria'] == $prodEdit['id_categoria'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a href="productos_admin.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
    <?php endif; endif; ?>
</body>
</html>