<?php
session_start();

$userId = $_SESSION['user_id'] ?? null;



if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);

    if ($userId && $name && $email && $subject && $message) {
        $mysqli = new mysqli("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

        if ($mysqli->connect_error) {
            die("Error de conexión: " . $mysqli->connect_error);
        }

        $stmt = $mysqli->prepare("INSERT INTO final_mensajes(user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $userId, $name, $email, $subject, $message);

        if ($stmt->execute()) {
            echo "Mensaje enviado correctamente.";
        } else {
            echo "Error al enviar el mensaje.";
        }

        $stmt->close();
        $mysqli->close();
    } else {
        echo "Debe estar autenticado y completar todos los campos.";
    }
} else {
    echo "Acceso no permitido.";
}
?>
