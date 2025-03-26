<?php

//Sirve para restringir el acceso a doc .html si un usuario no esta logeado

session_start();

// Si no hay sesión ni cookies, redirigir al login
if (!isset($_SESSION["user_id"]) && !isset($_COOKIE["user_id"])) {
    header("Location: login.html");
    exit();
}

// Si la sesión viene de una cookie, restaurarla
if (!isset($_SESSION["user_id"]) && isset($_COOKIE["user_id"])) {
    $_SESSION["user_id"] = $_COOKIE["user_id"];
    $_SESSION["nombre_usuario"] = $_COOKIE["nombre_usuario"];
}

/*  EN HTML SE LLAMA A DASHBORAD.PHP ANTES O INMEDIATAMENTE AL RECARGAR PAGINA
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Bienvenido, <?php echo $_SESSION["nombre_usuario"] ?? $_COOKIE["nombre_usuario"]; ?>!</h1>
    <a href="logout.php">Cerrar sesión</a>
</body>
</html>
*/
?>
