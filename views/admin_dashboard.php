<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h2>Panel de Administrador</h2>
    <nav class="mb-4">
        <a href="admin_dashboard.php" class="btn btn-primary">Dashboard</a>
        <a href="productos_admin.php" class="btn btn-secondary">Gestionar Productos</a>
        <a href="solicitudes.php" class="btn btn-secondary">Solicitudes de Compra</a>
        <a href="../controllers/logout.php" class="btn btn-danger">Cerrar sesión</a>
    </nav>
    <div class="card">
        <div class="card-body">
            <h4>Bienvenido, Administrador</h4>
            <p>Desde este panel puedes gestionar productos, ver solicitudes y administrar la plataforma.</p>
            <!-- Aquí puedes agregar más widgets o estadísticas -->
        </div>
    </div>
</body>
</html>