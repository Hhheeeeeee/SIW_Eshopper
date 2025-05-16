<?php
header('Content-Type: application/json');
session_start();

if (isset($_SESSION["user_id"])) {
    echo json_encode(["success" => true, "message" => "Usuario logueado"]);
} else {
    echo json_encode(["success" => false, "message" => "Usuario no logueado"]);
}
?>
