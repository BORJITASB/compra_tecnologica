
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h2>Registro de Usuario</h2>
    <form id="formRegistroUsuario" class="mt-4">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
            <label for="correo" class="form-label">Correo</label>
            <input type="email" class="form-control" id="correo" name="correo" required>
        </div>
        <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="contrasena" name="contrasena" required>
        </div>
        <div class="mb-3">
            <label for="confirmar" class="form-label">Confirmar Contraseña</label>
            <input type="password" class="form-control" id="confirmar" name="confirmar" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrarse</button>
        <div id="mensaje" class="mt-3"></div>
    </form>
    <p class="mt-3">¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    <script src="../assets/js/registro_user.js"></script>
</body>
</html>