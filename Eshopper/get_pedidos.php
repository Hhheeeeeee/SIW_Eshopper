<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Configuración de la base de datos
$servername = "dbserver"; // Cambia según tu configuración
$username = "grupo38";
$password = "lu0xaiM8Si";
$database = "db_grupo38";

// Conectar a la base de datos
$conn = mysqli_connect($servername, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Conexión fallida: " . $conn->connect_error]));
}

// Consulta para obtener los pedidos con la información necesaria
$sql = "SELECT
            id,
            titulo,
            estado,
            precio_unidad,
            total
        FROM final_pedidos
        ORDER BY fecha_pedido DESC"; // Puedes ajustar el orden si lo necesitas

$result = $conn->query($sql);

$pedidos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Verificar si todos los campos requeridos están rellenos
        if (isset($row["id"]) && $row["titulo"] !== '' && isset($row["estado"]) && isset($row["precio_unidad"]) && isset($row["total"])) {
            $pedidos[] = [
                "id" => $row["id"],
                "titulo" => $row["titulo"],
                "estado" => $row["estado"],
                "precio_unidad" => $row["precio_unidad"],
                "total" => $row["total"],
            ];
        } else {
            // Opcional: Puedes loggear o manejar de otra manera los pedidos incompletos
            error_log("Pedido incompleto encontrado (ID: " . $row["id"] . "): " . json_encode($row));
        }
    }
}

// Cerrar conexión
$conn->close();

// Devolver los datos en formato JSON
echo json_encode(["success" => true, "pedidos" => $pedidos]);
?>