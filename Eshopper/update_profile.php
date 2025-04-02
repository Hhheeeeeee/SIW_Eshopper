<?php
session_start();
header('Content-Type: application/json');

$directorio = __DIR__ . "/uploads/";
if (!is_dir($directorio)) {
    mkdir($directorio, 0755, true); // Crea la carpeta con permisos adecuados
}

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "No has iniciado sesión"]);
    exit;
}

$conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

if (!$conn) {
    echo json_encode(["success" => false, "message" => "Error de conexión a la base de datos"]);
    exit;
}

$user_id = $_SESSION["user_id"];
$foto_perfil = isset($_FILES["foto_perfil"]) ? $_FILES["foto_perfil"] : null;
$nueva_contraseña = isset($_POST["nueva_contraseña"]) ? trim($_POST["nueva_contraseña"]) : null;
$contraseña_actual = isset($_POST["contraseña_actual"]) ? trim($_POST["contraseña_actual"]) : null;

// Manejo de actualización de imagen de perfil
if ($foto_perfil) {
    $ruta_destino = "uploads/" . basename($foto_perfil["name"]);
    move_uploaded_file($foto_perfil["tmp_name"], $ruta_destino);

    $stmt = $conn->prepare("UPDATE final_usuarios SET foto_perfil = ? WHERE id = ?");
    $stmt->bind_param("si", $ruta_destino, $user_id);
    $stmt->execute();
    $stmt->close();
}

// Manejo de actualización de contraseña
if ($nueva_contraseña || $contraseña_actual) {
    if (!$nueva_contraseña || !$contraseña_actual) {
        echo json_encode(["success" => false, "message" => "Debes proporcionar la contraseña actual y la nueva contraseña"]);
        exit;
    }
    
    $stmt = $conn->prepare("SELECT contraseña FROM final_usuarios WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($contraseña_hash);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($contraseña_actual, $contraseña_hash)) {
        echo json_encode(["success" => false, "message" => "La contraseña actual es incorrecta"]);
        exit;
    }

    $nueva_contraseña_hash = password_hash($nueva_contraseña, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE final_usuarios SET contraseña = ? WHERE id = ?");
    $stmt->bind_param("si", $nueva_contraseña_hash, $user_id);
    $stmt->execute();
    $stmt->close();
}

echo json_encode(["success" => true, "message" => "Perfil actualizado correctamente"]);
$conn->close();
?>
