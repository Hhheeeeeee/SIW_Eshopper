<?php
header('Content-Type: application/json');


$conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");
if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión a la base de datos"]));
}

$sql = "SELECT id, name FROM final_categorias ORDER BY id, name";
$result = $conn->query($sql);

$categories = [];
while ($row = $result->fetch_assoc()) {
    $main_category = $row['id'];
    $sub_category = $row['name'];

    if (!isset($categories[$main_category])) {
        $categories[$main_category] = [];
    }
    $categories[$main_category][] = $sub_category;
}

$conn->close();
echo json_encode($categories);
?>
