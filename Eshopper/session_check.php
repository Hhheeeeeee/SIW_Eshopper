<?php

//Sirve para comprobar dentro de una pagina si un usuario esta logeado o no. 
session_start();
header('Content-Type: application/json');

if (isset($_SESSION["user_id"])) {
    echo json_encode(["logged_in" => true, "nombre_usuario" => $_SESSION["nombre_usuario"]]);
} elseif (isset($_COOKIE["user_id"])) {
    // Restaurar sesión desde la cookie si el usuario eligió "Keep me signed in"
    $_SESSION["user_id"] = $_COOKIE["user_id"];
    $_SESSION["nombre_usuario"] = $_COOKIE["nombre_usuario"];
    echo json_encode(["logged_in" => true, "nombre_usuario" => $_SESSION["nombre_usuario"]]);
} else {
    echo json_encode(["logged_in" => false]);
}


/*   EJEEMPLO DE USO EN JS EN HTML

$(document).ready(function () {
    $.ajax({
        url: "session_check.php",
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.logged_in) {
                $("#userWelcome").text("Bienvenido, " + response.nombre_usuario);
            } else {
                window.location.href = "login.html"; // Redirigir si no está logueado
            }
        }
    });
});

*/
?>


