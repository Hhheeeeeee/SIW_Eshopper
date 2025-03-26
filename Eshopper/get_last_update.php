<?php
// Incluir la conexión a la base de datos
//include('db_connection.php');


// Crear conexión
$connection = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

// Verificar conexión
if ($connection->connect_error) {
    die("Conexión fallida: " . $connection->connect_error);
}


// Consulta para obtener la última fecha de actualización
$query = "SELECT MAX(ultima_modificacion) AS ultima_actualizacion
          FROM (
              SELECT ultima_modificacion FROM final_productos
              UNION
              SELECT ultima_modificacion FROM final_categorias
              UNION
              SELECT ultima_modificacion FROM final_ofertas
              UNION
              SELECT ultima_modificacion FROM final_opiniones
          ) AS fechas";

$result = mysqli_query($connection, $query);
$row = mysqli_fetch_assoc($result);

// Obtener la última fecha de actualización
$last_update = $row['ultima_actualizacion'];

// Verificar si la fecha existe y es válida
if ($last_update && strtotime($last_update)) {
    $formatted_last_update = date('d-m-Y H:i:s', strtotime($last_update));
} else {
    $formatted_last_update = 'No disponible'; // Si la fecha no es válida
}

// Devolver la fecha en formato JSON para AJAX
echo json_encode(['last_update' => $formatted_last_update]);
?>
