<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST["correo"]);
    $contraseña = trim($_POST["contraseña"]);
    $keep_signed_in = isset($_POST["keep_signed_in"]) ? (int)$_POST["keep_signed_in"] : 0;

    // Conectar a la base de datos
    $conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

    if (!$conn) {
        echo json_encode(["success" => false, "message" => "Error de conexión"]);
        exit;
    }

    // Buscar usuario en la base de datos
    $stmt = $conn->prepare("SELECT id, nombre_usuario, contraseña FROM final_usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $nombre_usuario_db, $hashed_password);
        $stmt->fetch();

        // Verificar contraseña
        if (password_verify($contraseña, $hashed_password)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["nombre_usuario"] = $nombre_usuario_db;
            $_SESSION["LAST_ACTIVITY"] = time(); // Guardar el tiempo de inicio de sesión

            // Si "Keep me signed in" está marcado, guardar cookies por 30 minutos
            if ($keep_signed_in) {
                setcookie("user_id", $id, time() + 1800, "/"); // 30 minutos
                setcookie("nombre_usuario", $nombre_usuario_db, time() + 1800, "/");
            }

            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Contraseña incorrecta"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Usuario no encontrado"]);
    }

    $stmt->close();
    $conn->close();
}
?>
