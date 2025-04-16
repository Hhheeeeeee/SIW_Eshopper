<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

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

// Leer el archivo de productos insertados
$productos_insertados = leer_productos_insertados();
$total = count($productos_insertados);
$actualizados = 0;
$sin_actualizacion = 0;
$fallos_api = 0;

foreach ($productos_insertados as $asin) {
    // Obtener la información del producto desde la API
    $producto = obtener_producto($asin);
    if (!$producto) {
        $fallos_api++;
        continue;
    }

    // Actualizar el producto en la base de datos
    $actualizado = actualizarProducto($producto, $conn);
    if ($actualizado) {
        $actualizados++;
    } else {
        $sin_actualizacion++;
    }
}

// Cerrar conexión
$conn->close();

// Devolver el resumen de las actualizaciones
echo json_encode([
    "success" => true,
    "total" => $total,
    "actualizados" => $actualizados,
    "sin_actualizacion" => $sin_actualizacion,
    "fallos_api" => $fallos_api
]);

// ---------------- FUNCIONES ---------------- //

function obtener_producto($asin)
{
    // Aquí puedes agregar tu código para obtener la información desde la API
    // Simulando una respuesta de ejemplo
    return [
        'asin' => $asin,
        'titulo' => 'Ejemplo de Producto ' . $asin,
        'precio' => 19.99,
        'star_rating' => 4.5,
        'url_producto' => "https://www.amazon.es/dp/$asin",
        'url_marca' => "https://www.amazon.es/brand/$asin",
        'imagen' => json_encode(["https://example.com/product-image.jpg"]),
        'detalles' => json_encode(["Detalle 1", "Detalle 2"]),
        'id_categoria' => 'Categoria ejemplo'
    ];
}

function actualizarProducto($producto, $conn)
{
    // Escapar las variables para prevenir inyecciones SQL
    $asin = $conn->real_escape_string($producto['asin']);
    $titulo = $conn->real_escape_string($producto['titulo']);
    $precio = ($producto['precio'] !== null) ? $producto['precio'] : 'NULL';
    $star_rating = ($producto['star_rating'] !== null) ? $producto['star_rating'] : 'NULL';
    $url_producto = $conn->real_escape_string($producto['url_producto']);
    $url_marca = ($producto['url_marca']) ? "'" . $conn->real_escape_string($producto['url_marca']) . "'" : 'NULL';
    $imagen = $conn->real_escape_string(json_encode($producto['imagen']));
    $detalles = $conn->real_escape_string(json_encode($producto['detalles']));
    $id_categoria = $conn->real_escape_string($producto['id_categoria']);
    
    // Verificar si el producto existe
    $sql_select = "SELECT * FROM final_productos WHERE asin = '$asin'";
    $result = $conn->query($sql_select);

    if ($result && $result->num_rows > 0) {
        $producto_actual = $result->fetch_assoc();

        // Verificar si hay cambios en los datos del producto
        $cambios = array_diff_assoc([
            'titulo' => $titulo,
            'precio' => $precio,
            'star_rating' => $star_rating,
            'url_producto' => $url_producto,
            'url_marca' => $url_marca,
            'imagen' => $imagen,
            'detalles' => $detalles,
            'id_categoria' => $id_categoria
        ], [
            'titulo' => $producto_actual['titulo'],
            'precio' => $producto_actual['precio'],
            'star_rating' => $producto_actual['star_rating'],
            'url_producto' => $producto_actual['url_producto'],
            'url_marca' => $producto_actual['url_marca'],
            'imagen' => $producto_actual['imagen'],
            'detalles' => $producto_actual['informacion_relevante'],
            'id_categoria' => $producto_actual['id_categoria']
        ]);

        // Si hay cambios, actualizar el producto
        if (!empty($cambios)) {
            $sql_update = "UPDATE final_productos SET 
                titulo = '$titulo',
                precio = $precio,
                star_rating = $star_rating,
                url_producto = '$url_producto',
                url_marca = $url_marca,
                imagen = '$imagen',
                informacion_relevante = '$detalles',
                id_categoria = '$id_categoria',
                ultima_modificacion = CURRENT_TIMESTAMP
                WHERE asin = '$asin'";

            // Ejecutar la consulta y verificar el resultado
            if ($conn->query($sql_update) === TRUE) {
                return true;
            } else {
                error_log("Error al actualizar producto: " . $conn->error);
                return false;
            }
        }
        return true; // No había cambios, pero se consideró exitoso
    }

    return false; // No se encontró el producto
}

function leer_productos_insertados()
{
    $archivo = 'productos_insertados.txt';
    return file_exists($archivo) ? file($archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
}
?>
