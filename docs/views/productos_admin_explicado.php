<?php
// Vista: productos_admin.php (gestión CRUD de productos para administradores)
// Permite agregar, editar y eliminar productos usando formularios POST clásicos con protección CSRF.

session_start(); // Control de sesión.

// Verifica autenticación.
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Verifica rol admin.
if (empty($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo 'Acceso denegado';
    exit;
}

require_once '../config/config.php'; // Conexión BD.

// Genera token CSRF si no existe.
if (empty($_SESSION['csrf_token'])) { // Si no hay token.
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Crea token aleatorio 64 hex.
}
$csrfToken = $_SESSION['csrf_token']; // Asigna a variable para usar en inputs.

// Recupera listado de productos para la tabla.
$stmt = $pdo->query("SELECT id, nombre, descripcion, id_categoria FROM productos ORDER BY id DESC");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC); // Array productos.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"> <!-- UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive -->
    <title>Administrar Productos</title> <!-- Título -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap -->
    <link rel="stylesheet" href="../assets/css/app.css"> <!-- Tema global -->
    <link rel="stylesheet" href="../assets/css/dashboard.css"> <!-- Reutiliza estilo dashboard -->
</head>
<body class="app-body">
<div class="overlay"></div> <!-- Fondo -->
<div class="dashboard-layout"> <!-- Layout -->
    <aside class="sidebar"> <!-- Sidebar -->
        <div class="sidebar-header"> <!-- Header -->
            <h2 class="h5 mb-0">Admin Panel</h2> <!-- Título -->
        </div>
        <nav class="sidebar-nav"> <!-- Navegación -->
            <a href="admin_dashboard.php">Dashboard</a> <!-- Link dashboard -->
            <a href="productos_admin.php" class="active">Productos</a> <!-- Actual -->
            <a href="catalogo.php" target="_blank">Catálogo Público</a> <!-- Público -->
            <a href="logout.php" class="text-danger">Salir</a> <!-- Logout -->
        </nav>
        <div class="sidebar-footer small"> <!-- Pie -->
            Sesión: <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '') ?> <!-- Usuario -->
        </div>
    </aside>
    <main class="dashboard-main"> <!-- Contenido principal -->
        <header class="dashboard-header"> <!-- Encabezado -->
            <h1 class="h3 mb-0">Productos</h1> <!-- Título -->
        </header>
        <section class="data-sections"> <!-- Sección -->
            <div class="data-card" style="grid-column: span 2;"> <!-- Form agregar -->
                <h2 class="h6 mb-3">Agregar Producto</h2> <!-- Título -->
                <form action="../controllers/productos_crud.php" method="POST" class="row g-3"> <!-- Form POST -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"> <!-- Token CSRF -->
                    <input type="hidden" name="accion" value="agregar_producto"> <!-- Acción -->
                    <div class="col-md-4"> <!-- Campo nombre -->
                        <label class="form-label">Nombre</label> <!-- Etiqueta -->
                        <input type="text" name="nombre" class="form-control" required> <!-- Input -->
                    </div>
                    <div class="col-md-4"> <!-- Campo descripción -->
                        <label class="form-label">Descripción</label> <!-- Etiqueta -->
                        <input type="text" name="descripcion" class="form-control" required> <!-- Input -->
                    </div>
                    <div class="col-md-3"> <!-- Campo categoría -->
                        <label class="form-label">ID Categoría</label> <!-- Etiqueta -->
                        <input type="number" name="categoria" class="form-control" required> <!-- Input -->
                    </div>
                    <div class="col-md-1 d-flex align-items-end"> <!-- Botón -->
                        <button class="btn btn-primary w-100">Guardar</button> <!-- Guardar -->
                    </div>
                </form>
            </div>
            <div class="data-card" style="grid-column: span 4;"> <!-- Lista productos -->
                <h2 class="h6 mb-3">Listado</h2> <!-- Título -->
                <div class="table-responsive"> <!-- Tabla -->
                    <table class="table table-sm table-dark table-hover align-middle mb-0"> <!-- Tabla -->
                        <thead>
                            <tr>
                                <th>ID</th> <!-- Encabezado -->
                                <th>Nombre</th> <!-- Encabezado -->
                                <th>Descripción</th> <!-- Encabezado -->
                                <th>Categoría</th> <!-- Encabezado -->
                                <th style="width:140px;">Acciones</th> <!-- Acciones -->
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($productos as $p): ?> <!-- Loop productos -->
                            <tr>
                                <td><?= htmlspecialchars($p['id']) ?></td> <!-- ID -->
                                <td><?= htmlspecialchars($p['nombre']) ?></td> <!-- Nombre -->
                                <td class="text-truncate" style="max-width:160px;"> <!-- Desc -->
                                    <?= htmlspecialchars($p['descripcion']) ?> <!-- Descripción -->
                                </td>
                                <td><?= htmlspecialchars($p['id_categoria']) ?></td> <!-- Categoría -->
                                <td> <!-- Acciones -->
                                    <div class="d-flex gap-1"> <!-- Flex botones -->
                                        <form action="../controllers/productos_crud.php" method="POST" class="flex-grow-1"> <!-- Form editar -->
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"> <!-- CSRF -->
                                            <input type="hidden" name="accion" value="editar_producto"> <!-- Acción -->
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($p['id']) ?>"> <!-- ID -->
                                            <input type="hidden" name="nombre" value="<?= htmlspecialchars($p['nombre']) ?>"> <!-- Nombre -->
                                            <input type="hidden" name="descripcion" value="<?= htmlspecialchars($p['descripcion']) ?>"> <!-- Descripción -->
                                            <input type="hidden" name="categoria" value="<?= htmlspecialchars($p['id_categoria']) ?>"> <!-- Categoría -->
                                            <button class="btn btn-sm btn-outline-light w-100">Editar</button> <!-- Botón -->
                                        </form>
                                        <form action="../controllers/productos_crud.php" method="POST" onsubmit="return confirm('¿Eliminar producto?');" class="flex-grow-1"> <!-- Form eliminar -->
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"> <!-- CSRF -->
                                            <input type="hidden" name="accion" value="eliminar_producto"> <!-- Acción -->
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($p['id']) ?>"> <!-- ID -->
                                            <button class="btn btn-sm btn-danger w-100">Eliminar</button> <!-- Botón -->
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($productos)): ?> <!-- Sin productos -->
                            <tr><td colspan="5" class="text-center text-muted">No hay productos.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>
</body>
</html>
