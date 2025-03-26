<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = trim($_POST["nombre_usuario"]);
    $correo = trim($_POST["correo"]);
    $contraseña = password_hash(trim($_POST["contraseña"]), PASSWORD_BCRYPT); // Encriptar la contraseña

    // Validar que los campos no estén vacíos
    if (empty($nombre_usuario) || empty($correo) || empty($_POST["contraseña"])) {
        echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios"]);
        exit;
    }

    // Verificar si el usuario o el correo ya existen
    $conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

    // Verificar conexión
    if (!$conn) {
        die(json_encode(["error" => "Error de conexión a la base de datos: " . mysqli_connect_error()]));
    }


    $stmt = $conn->prepare("SELECT id FROM final_usuarios WHERE nombre_usuario = ? OR correo = ?");
    $stmt->bind_param("ss", $nombre_usuario, $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "El usuario o el correo ya existen"]);
        exit;
    }

    $stmt->close();

    // Insertar el nuevo usuario
    $stmt = $conn->prepare("INSERT INTO final_usuarios (nombre_usuario, contraseña, correo) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre_usuario, $contraseña, $correo);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al registrar el usuario"]);
    }

    $stmt->close();
    $conn->close();
}
?>
