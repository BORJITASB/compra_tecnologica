
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h2>Iniciar Sesión</h2>
    <form id="formLogin" class="mt-4">
        <div class="mb-3">
            <label for="correo" class="form-label">Correo</label>
            <input type="email" class="form-control" id="correo" name="correo" required>
        </div>
        <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="contrasena" name="contrasena" required>
        </div>
        <button type="submit" class="btn btn-primary">Ingresar</button>
        <div id="mensaje" class="mt-3"></div>
    </form>
    <p class="mt-3">¿No tienes cuenta? <a href="registro_user.php">Regístrate aquí</a></p>
    <script src="../assets/js/login.js"></script>
</body>
</html>