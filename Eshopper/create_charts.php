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

// --- Gráfico de columnas: Cantidad de productos por categoría ---
$sql_productos_por_categoria = "SELECT fp.id_categoria, COUNT(fp.id) AS cantidad_productos
                                FROM final_productos fp
                                GROUP BY fp.id_categoria
                                ORDER BY fp.id_categoria";
$result_productos_por_categoria = $conn->query($sql_productos_por_categoria);
$productos_por_categoria = [];
if ($result_productos_por_categoria && $result_productos_por_categoria->num_rows > 0) {
    while ($row = $result_productos_por_categoria->fetch_assoc()) {
        $productos_por_categoria[] = $row;
    }
    $response["productos_por_categoria"] = $productos_por_categoria;
} else {
    $response["productos_por_categoria"] = [];
}

// --- Gráfico de tarta: Distribución del rating de los productos ---
$sql_rating_productos = "SELECT star_rating AS rating, COUNT(id) AS total_productos
                        FROM final_productos
                        GROUP BY star_rating
                        ORDER BY star_rating";
$result_rating_productos = $conn->query($sql_rating_productos);
$rating_productos = [];
if ($result_rating_productos && $result_rating_productos->num_rows > 0) {
    while ($row = $result_rating_productos->fetch_assoc()) {
        $rating_productos[] = $row;
    }
    $response["rating_productos"] = $rating_productos;
} else {
    $response["rating_productos"] = [];
}

// --- Gráfico de líneas: Nuevos pedidos realizados por mes ---
$sql_pedidos_por_mes = "SELECT
                            DATE_FORMAT(fecha_pedido, '%Y-%m') AS mes,
                            COUNT(id) AS total_pedidos
                        FROM
                            final_pedidos
                        WHERE estado = 'Pagado'
                        GROUP BY
                            mes
                        ORDER BY
                            mes";
$result_pedidos_por_mes = $conn->query($sql_pedidos_por_mes);
$pedidos_por_mes = [];
if ($result_pedidos_por_mes && $result_pedidos_por_mes->num_rows > 0) {
    while ($row = $result_pedidos_por_mes->fetch_assoc()) {
        $pedidos_por_mes[] = $row;
    }
    $response["pedidos_por_mes"] = $pedidos_por_mes;
} else {
    $response["pedidos_por_mes"] = [];
}

// --- Gráfico de barras horizontales: Precio promedio de productos por categoría ---
$sql_precio_promedio_categoria = "SELECT fp.id_categoria AS categoria_id, ROUND(AVG(fp.precio), 2) AS precio_promedio
                                  FROM final_productos fp
                                  GROUP BY fp.id_categoria
                                  ORDER BY precio_promedio DESC
                                  LIMIT 7";
$result_precio_promedio_categoria = $conn->query($sql_precio_promedio_categoria);
$precio_promedio_por_categoria = [];
if ($result_precio_promedio_categoria && $result_precio_promedio_categoria->num_rows > 0) {
    while ($row = $result_precio_promedio_categoria->fetch_assoc()) {
        $precio_promedio_por_categoria[] = $row;
    }
    $response["precio_promedio_por_categoria"] = $precio_promedio_por_categoria;
} else {
    $response["precio_promedio_por_categoria"] = [];
}

// --- Gráfico de dona: Distribución de productos con y sin oferta ---
$sql_distribucion_ofertas = "SELECT
                            CASE
                                WHEN EXISTS (
                                    SELECT 1
                                    FROM final_ofertas fo
                                    WHERE fo.product_id = fp.id
                                ) THEN 'Con Oferta'
                                ELSE 'Sin Oferta'
                            END AS tiene_oferta,
                            COUNT(fp.id) AS total_productos
                        FROM final_productos fp
                        GROUP BY tiene_oferta";
$result_distribucion_ofertas = $conn->query($sql_distribucion_ofertas);
$distribucion_ofertas = [];
if ($result_distribucion_ofertas && $result_distribucion_ofertas->num_rows > 0) {
    while ($row = $result_distribucion_ofertas->fetch_assoc()) {
        $distribucion_ofertas[] = $row;
    }
    $response["distribucion_ofertas"] = $distribucion_ofertas;
} else {
    $response["distribucion_ofertas"] = [];
}

// --- Gráfico de área: Usuarios acumulados por mes ---
$sql_usuarios_acumulados_mes = "SELECT
                                    DATE_FORMAT(fecha_registro, '%Y-%m') AS mes,
                                    COUNT(*) AS nuevos_usuarios
                                FROM
                                    final_usuarios
                                GROUP BY
                                    mes
                                ORDER BY
                                    mes";
$result_usuarios_acumulados_mes = $conn->query($sql_usuarios_acumulados_mes);
$usuarios_acumulados_por_mes = [];
$total_acumulado_usuarios = 0;
if ($result_usuarios_acumulados_mes && $result_usuarios_acumulados_mes->num_rows > 0) {
    while ($row = $result_usuarios_acumulados_mes->fetch_assoc()) {
        $total_acumulado_usuarios += intval($row["nuevos_usuarios"]);
        $usuarios_acumulados_por_mes[] = ["mes" => $row["mes"], "total_acumulado" => $total_acumulado_usuarios];
    }
    $response["usuarios_acumulados_por_mes"] = $usuarios_acumulados_por_mes;
} else {
    $response["usuarios_acumulados_por_mes"] = [];
}

// --- Gráfico de dispersión: Precio del producto vs. Rating promedio (requiere unión) ---
$sql_precio_rating = "SELECT
                            fp.precio AS precio_producto,
                            ROUND(AVG(fo.rating),2) AS promedio_rating
                        FROM final_productos fp
                        LEFT JOIN final_opiniones fo ON fp.id = fo.producto_id
                        GROUP BY fp.id, fp.precio
                        HAVING COUNT(fo.id) > 0
                        ORDER BY fp.precio";
$result_precio_rating = $conn->query($sql_precio_rating);
$precio_rating_data = [];
if ($result_precio_rating && $result_precio_rating->num_rows > 0) {
    while ($row = $result_precio_rating->fetch_assoc()) {
        $precio_rating_data[] = ["precio" => floatval($row["precio_producto"]), "rating" => floatval($row["promedio_rating"])];
    }
    $response["precio_rating_data"] = $precio_rating_data;
} else {
    $response["precio_rating_data"] = [];
}

// --- Gráfico de columnas: Top vendedores por cantidad de productos vendidos ---
$sql_top_vendedores = "SELECT fv.seller_name AS vendedor, COUNT(fv.product_id) AS total_vendidos
                        FROM final_vendedores fv
                        GROUP BY fv.seller_name
                        ORDER BY total_vendidos DESC
                        LIMIT 10";
$result_top_vendedores = $conn->query($sql_top_vendedores);
$top_vendedores = [];
if ($result_top_vendedores && $result_top_vendedores->num_rows > 0) {
    while ($row = $result_top_vendedores->fetch_assoc()) {
        $top_vendedores[] = $row;
    }
    $response["top_vendedores"] = $top_vendedores;
} else {
    $response["top_vendedores"] = [];
}

// --- Gráfico de tarta: Distribución de pedidos por estado ---
$sql_distribucion_pedidos_estado = "SELECT
                                        estado,
                                        COUNT(id) AS total_pedidos
                                    FROM
                                        final_pedidos
                                    GROUP BY
                                        estado";
$result_distribucion_pedidos_estado = $conn->query($sql_distribucion_pedidos_estado);
$distribucion_pedidos_estado = [];
if ($result_distribucion_pedidos_estado && $result_distribucion_pedidos_estado->num_rows > 0) {
    while ($row = $result_distribucion_pedidos_estado->fetch_assoc()) {
        $distribucion_pedidos_estado[] = $row;
    }
    $response["distribucion_pedidos_estado"] = $distribucion_pedidos_estado;
} else {
    $response["distribucion_pedidos_estado"] = [];
}

// --- Datos de diferencia de precio individual por oferta ---
$sql_diferencia_precio_oferta = "SELECT
                                    precio_original,
                                    precio_oferta,
                                    precio_original - precio_oferta AS diferencia_precio
                                FROM
                                    final_ofertas
                                WHERE
                                    precio_oferta IS NOT NULL AND precio_original IS NOT NULL AND precio_original > 0";
$result_diferencia_precio_oferta = $conn->query($sql_diferencia_precio_oferta);
$diferencia_precio_oferta = [];
if ($result_diferencia_precio_oferta && $result_diferencia_precio_oferta->num_rows > 0) {
    while ($row = $result_diferencia_precio_oferta->fetch_assoc()) {
        $diferencia_precio_oferta[] = $row;
    }
    $response["diferencia_precio_oferta"] = $diferencia_precio_oferta;
} else {
    $response["diferencia_precio_oferta"] = [];
}

$response["success"] = true;
echo json_encode($response);

$conn->close();
?>