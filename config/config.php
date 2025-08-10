<?php
$host = "localhost";
$user = "jean"; // usuario de MySQL
$pass = "root";     // contraseña (vacía si es XAMPP por defecto)
$db   = "compras_tecnologicas";
$port = 3307;   // número de puerto como entero

$conn = new mysqli($host, $user, $pass, $db, $port);


?>
<?php
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=compras_tecnologicas;port=3307;charset=utf8",
        "root",
        ""
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
