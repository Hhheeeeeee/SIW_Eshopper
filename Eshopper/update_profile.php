<?php
session_start();
header('Content-Type: application/json');

// Define the directory to save uploaded files
$directorio = __DIR__ . "/uploads/";
if (!is_dir($directorio)) {
    mkdir($directorio, 0755, true); // Create the directory if it doesn't exist
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

// Flag para verificar si hubo algún cambio
$actualizado = false;

// Manejo de actualización de imagen de perfil
if ($foto_perfil && $foto_perfil["size"] > 0) { // Verifica que se haya subido un archivo válido
    $ruta_destino = __DIR__ . "/uploads/" . basename($foto_perfil["name"]);
    if (move_uploaded_file($foto_perfil["tmp_name"], $ruta_destino)) {
        $ruta_destino_db = "uploads/" . basename($foto_perfil["name"]); // Guardar ruta relativa en la BD
        $stmt = $conn->prepare("UPDATE final_usuarios SET foto_perfil = ? WHERE id = ?");
        $stmt->bind_param("si", $ruta_destino_db, $user_id);
        $stmt->execute();
        $stmt->close();
        $actualizado = true;
    } else {
        echo json_encode(["success" => false, "message" => "Error al guardar la imagen en el servidor"]);
        exit;
    }
}

// Solo procesar la actualización de la contraseña si se han enviado las contraseñas
if (!empty($nueva_contraseña) && !empty($contraseña_actual)) {
    // Verifica si ambas contraseñas están completas
    if (empty($nueva_contraseña) || empty($contraseña_actual)) {
        echo json_encode(["success" => false, "message" => "Debes proporcionar la contraseña actual y la nueva contraseña"]);
        exit;
    }

    // Verifica la contraseña actual
    $stmt = $conn->prepare("SELECT contraseña FROM final_usuarios WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($contraseña_hash);
    $stmt->fetch();
    $stmt->close();

    // Verifica si la contraseña actual es correcta
    if (!password_verify($contraseña_actual, $contraseña_hash)) {
        echo json_encode(["success" => false, "message" => "La contraseña actual es incorrecta"]);
        exit;
    }

    // Si la contraseña actual es correcta, actualiza la nueva contraseña
    $nueva_contraseña_hash = password_hash($nueva_contraseña, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE final_usuarios SET contraseña = ? WHERE id = ?");
    $stmt->bind_param("si", $nueva_contraseña_hash, $user_id);
    $stmt->execute();
    $stmt->close();
    $actualizado = true;
}

// Si se ha realizado alguna actualización, devolver mensaje de éxito
if ($actualizado) {
    echo json_encode(["success" => true, "message" => "Perfil actualizado correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "No se realizaron cambios"]);
}

$conn->close();
?>
