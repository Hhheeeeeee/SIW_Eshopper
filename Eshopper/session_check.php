<?php
session_start();
header('Content-Type: application/json');

$session_lifetime = 1800; // 30 minutos

//Expirar sesion si pasa mucho tiempo
if (isset($_SESSION["LAST_ACTIVITY"]) && (time() - $_SESSION["LAST_ACTIVITY"] > $session_lifetime)) {
    session_unset();
    session_destroy();
    setcookie("user_id", "", time() - 3600, "/");
    setcookie("nombre_usuario", "", time() - 3600, "/");
    echo json_encode(["logged_in" => false, "message" => "Sesion expirada"]);
    exit;
}

//Reactivar sesion desde cookies si no existe
if (!isset($_SESSION["user_id"]) && isset($_COOKIE["user_id"])) {
    // Validar el ID con la base de datos
    $conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

    if ($conn) {
        $user_id = intval($_COOKIE["user_id"]); // Proteger el dato
        $stmt = $conn->prepare("SELECT nombre_usuario, es_admin FROM final_usuarios WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($nombre_usuario, $es_admin);

        if ($stmt->fetch()) {
            // Usuario vÃ¡lido: reconstruir sesiÃ³n
            $_SESSION["user_id"] = $user_id;
            $_SESSION["nombre_usuario"] = $nombre_usuario;
            $_SESSION["es_admin"] = $es_admin;
            $_SESSION["LAST_ACTIVITY"] = time();
        }

        $stmt->close();
        $conn->close();
    }
}

//Actualizar tiempo si la sesion existe
if (isset($_SESSION["user_id"])) {
    $_SESSION["LAST_ACTIVITY"] = time();

    // Consultar datos del usuario para mostrar
    $conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

    if (!$conn) {
        echo json_encode(["logged_in" => false, "error" => "Error de conexiÃ³n"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT nombre_usuario, foto_perfil, es_admin FROM final_usuarios WHERE id = ?");
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $stmt->bind_result($nombre_usuario, $foto_perfil, $es_admin);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    $_SESSION["es_admin"] = $es_admin;

    echo json_encode([
        "logged_in" => true,
        "nombre_usuario" => $nombre_usuario,
        "foto_perfil" => $foto_perfil ?: "https://astrobriga.es/wp-content/plugins/give/assets/dist/images/anonymous-user.svg",
        "es_admin" => $es_admin
    ]);
} else {
    echo json_encode(["logged_in" => false]);
}
?>