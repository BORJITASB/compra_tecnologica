<?php
require_once("../config/config.php");
$stmt = $pdo->query("SELECT p.nombre, p.descripcion, p.imagen, c.nombre AS categoria
FROM productos p
JOIN categorias c ON p.id_categoria = c.id_categoria
ORDER BY p.id_producto DESC");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h2>Catálogo de Productos Tecnológicos</h2>
    <div class="row">
        <?php foreach($productos as $prod): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <?php if (!empty($prod['imagen'])): ?>
                    <img src="../assets/img/<?= htmlspecialchars($prod['imagen']) ?>" class="card-img-top" alt="<?= htmlspecialchars($prod['nombre']) ?>">
                <?php else: ?>
                    <img src="../assets/img/default.png" class="card-img-top" alt="Sin imagen">
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($prod['nombre']) ?></h5>
                    <p class="card-text"><?= htmlspecialchars($prod['descripcion']) ?></p>
                    <span class="badge bg-secondary"><?= htmlspecialchars($prod['categoria']) ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</body>
</html>