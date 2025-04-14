<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// Datos de conexión a la base de datos (ajusta tus credenciales)
$servername = "dbserver";
$username = "grupo38";
$password = "lu0xaiM8Si";
$database = "db_grupo38";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]));
}

$response = ["success" => false];

// Cantidad total de productos
$sql_productos = "SELECT COUNT(*) AS total FROM final_productos";
$result_productos = $conn->query($sql_productos);
if ($result_productos && $result_productos->num_rows > 0) {
    $row = $result_productos->fetch_assoc();
    $response["total_productos"] = intval($row["total"]);
} else {
    $response["total_productos"] = 0;
}

// Número de usuarios registrados
$sql_usuarios = "SELECT COUNT(*) AS total FROM final_usuarios";
$result_usuarios = $conn->query($sql_usuarios);
if ($result_usuarios && $result_usuarios->num_rows > 0) {
    $row = $result_usuarios->fetch_assoc();
    $response["total_usuarios"] = intval($row["total"]);
} else {
    $response["total_usuarios"] = 0;
}

// Cantidad de ventas realizadas
$sql_ventas = "SELECT COUNT(*) AS total FROM final_pedidos WHERE estado = 'Pagado'";
$result_ventas = $conn->query($sql_ventas);
if ($result_ventas && $result_ventas->num_rows > 0) {
    $row = $result_ventas->fetch_assoc();
    $response["total_ventas"] = intval($row["total"]);
} else {
    $response["total_ventas"] = 0;
}

// Actividad reciente: Últimos productos añadidos
$sql_ultimos_productos = "SELECT titulo, fecha_creacion FROM final_productos ORDER BY fecha_creacion DESC LIMIT 5";
$result_ultimos_productos = $conn->query($sql_ultimos_productos);
$ultimos_productos = [];
if ($result_ultimos_productos && $result_ultimos_productos->num_rows > 0) {
    while ($row = $result_ultimos_productos->fetch_assoc()) {
        $ultimos_productos[] = $row;
    }
    $response["ultimos_productos"] = $ultimos_productos;
} else {
    $response["ultimos_productos"] = [];
}

// Actividad reciente: Últimos usuarios registrados
$sql_ultimos_usuarios = "SELECT nombre_usuario AS nombre, fecha_registro FROM final_usuarios ORDER BY fecha_registro DESC LIMIT 5";
$result_ultimos_usuarios = $conn->query($sql_ultimos_usuarios);
$ultimos_usuarios = [];
if ($result_ultimos_usuarios && $result_ultimos_usuarios->num_rows > 0) {
    while ($row = $result_ultimos_usuarios->fetch_assoc()) {
        $ultimos_usuarios[] = $row;
    }
    $response["ultimos_usuarios"] = $ultimos_usuarios;
} else {
    $response["ultimos_usuarios"] = [];
}

// Últimos cuatro ventas realizadas (user_id, total, titulo, fecha_pedido)
$sql_ultimas_ventas = "SELECT
    fp.user_id,
    fp.total,
    fp.titulo,
    fp.fecha_pedido
FROM
    final_pedidos fp
ORDER BY
    fp.fecha_pedido DESC
LIMIT 4";

$result_ultimas_ventas = $conn->query($sql_ultimas_ventas);
$ultimas_ventas = [];
if ($result_ultimas_ventas && $result_ultimas_ventas->num_rows > 0) {
    while ($row = $result_ultimas_ventas->fetch_assoc()) {
        $ultimas_ventas[] = $row;
    }
    $response["ultimas_ventas"] = $ultimas_ventas;
} else {
    $response["ultimas_ventas"] = [];
}

$response["success"] = true;
echo json_encode($response);

$conn->close();
?>