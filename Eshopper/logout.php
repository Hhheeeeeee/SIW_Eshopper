<?php
//Sirve para cerrar sesion
session_start();
session_destroy();

// Borrar cookies
setcookie("user_id", "", time() - 3600, "/");
setcookie("nombre_usuario", "", time() - 3600, "/");

header("Location: login.html"); // Redirigir al login
exit();
?>
