<?php

session_start();
header('Content-Type: application/json');

if (isset($_SESSION["user_id"])) {
    $conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

    if (!$conn) {
        echo json_encode(["logged_in" => false, "error" => "Error de conexión"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT nombre_usuario, foto_perfil FROM final_usuarios WHERE id = ?");
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $stmt->bind_result($nombre_usuario, $foto_perfil);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    echo json_encode([
        "logged_in" => true,
        "nombre_usuario" => $nombre_usuario,
        "foto_perfil" => $foto_perfil ? $foto_perfil : "https://astrobriga.es/wp-content/plugins/give/assets/dist/images/anonymous-user.svg"
    ]);
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


