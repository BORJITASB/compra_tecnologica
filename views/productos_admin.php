<?php
session_start();
// Protección de acceso
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}
// Token CSRF (se reutiliza mientras dure la sesión)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require_once("../config/config.php");

// Productos + categoría (nombre)
$stmt = $pdo->query("SELECT p.id_producto, p.nombre, p.descripcion, c.nombre AS categoria, p.id_categoria FROM productos p JOIN categorias c ON p.id_categoria = c.id_categoria ORDER BY p.id_producto DESC");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Categorías para selects
$stmtCat = $pdo->query("SELECT id_categoria, nombre FROM categorias ORDER BY nombre ASC");
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

// Si edición solicitada cargar producto específico
$prodEdit = null;
if (isset($_GET['editar'])) {
    $idEdit = intval($_GET['editar']);
    $stmtE = $pdo->prepare("SELECT * FROM productos WHERE id_producto=?");
    $stmtE->execute([$idEdit]);
    $prodEdit = $stmtE->fetch(PDO::FETCH_ASSOC) ?: null;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Productos | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/app.css" rel="stylesheet">
    <style>
        /* Mejora visual del desplegable de categorías (compatibilidad limitada según navegador) */
        .form-select option, .form-select optgroup { background:#1d2228; color:#fff; }
        .form-select option:checked { background:#344256 linear-gradient(#344256,#344256); color:#fff; }
        /* Forzar mayor contraste en la tabla de productos */
        .products-table thead th { color:#ffffff !important; }
        .products-table tbody td { color:#f8fafc !important; }
        .products-table tbody tr:hover td { color:#ffffff !important; }
    </style>
</head>
<body class="app-body">
<div class="app-wrapper container">
    <!-- Barra de navegación superior -->
    <div class="nav-top mb-4">
        <div class="brand-mini">⚙️ Admin</div>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="productos_admin.php" class="active">Productos</a>
        <a href="catalogo.php" target="_blank">Catálogo Público</a>
        <a href="../controllers/logout.php" class="ms-auto text-danger" style="font-weight:600;">Salir</a>
    </div>

    <?php if (isset($_GET['mensaje'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_GET['tipo'] ?? 'success') ?> alert-dismissible fade show glass-box p-3" role="alert" style="background:rgba(0,0,0,0.35);">
            <?= htmlspecialchars($_GET['mensaje']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Formulario Agregar -->
        <div class="col-lg-5">
            <div class="glass-box h-100 d-flex flex-column">
                <h2 class="glass-title mb-2 fs-5">Agregar Producto</h2>
                <form method="POST" action="../controllers/productos_crud.php" class="mt-1">
                    <input type="hidden" name="accion" value="agregar">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="id_categoria" class="form-label">Categoría</label>
                        <select id="id_categoria" name="id_categoria" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach($categorias as $cat): ?>
                                <option value="<?= $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-soft">➕ Agregar</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Tabla Productos -->
        <div class="col-lg-7">
            <div class="glass-box">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="glass-title fs-5 mb-0">Listado de Productos</h2>
                    <span class="badge badge-soft"><?= count($productos) ?> total</span>
                </div>
                <div class="table-responsive" style="max-height:520px;">
                    <table class="table table-sm table-glass products-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:17%">Nombre</th>
                                <th style="width:43%">Descripción</th>
                                <th style="width:20%">Categoría</th>
                                <th style="width:20%" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if($productos): foreach($productos as $prod): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($prod['nombre']) ?></td>
                                <td class="text-truncate" style="max-width:260px;"><?= htmlspecialchars($prod['descripcion']) ?></td>
                                <td><?= htmlspecialchars($prod['categoria']) ?></td>
                                <td class="text-center">
                                    <a href="?editar=<?= $prod['id_producto'] ?>" class="btn btn-outline-light btn-sm">Editar</a>
                                    <a href="../controllers/productos_crud.php?eliminar=<?= $prod['id_producto'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Eliminar este producto?')">✖</a>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="4" class="text-center text-muted">Sin productos registrados.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php if($prodEdit): ?>
    <!-- Panel Edición -->
    <div class="glass-box mt-5">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 class="glass-title fs-5 mb-0">Editar: <?= htmlspecialchars($prodEdit['nombre']) ?></h2>
            <a href="productos_admin.php" class="btn btn-sm btn-outline-light">Cancelar</a>
        </div>
        <form method="POST" action="../controllers/productos_crud.php" class="row g-3">
            <input type="hidden" name="accion" value="editar">
            <input type="hidden" name="id_producto" value="<?= $prodEdit['id_producto'] ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <div class="col-md-4">
                <label class="form-label" for="edit_nombre">Nombre</label>
                <input type="text" id="edit_nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($prodEdit['nombre']) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="edit_categoria">Categoría</label>
                <select id="edit_categoria" name="id_categoria" class="form-select" required>
                    <?php foreach($categorias as $cat): ?>
                        <option value="<?= $cat['id_categoria'] ?>" <?= $cat['id_categoria']==$prodEdit['id_categoria']?'selected':'' ?>><?= htmlspecialchars($cat['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label" for="edit_descripcion">Descripción</label>
                <textarea id="edit_descripcion" name="descripcion" class="form-control" rows="3" required><?= htmlspecialchars($prodEdit['descripcion']) ?></textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-soft">💾 Guardar Cambios</button>
                <a href="productos_admin.php" class="btn btn-outline-light">Cancelar</a>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <div class="footer-simple">&copy; <?= date('Y') ?> Compra Tecnológica · Gestión de Productos</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
