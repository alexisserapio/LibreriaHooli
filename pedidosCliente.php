<?php
include("conexion.php");

function mostrarImagenBlob($blob) {
    if (!$blob || !is_object($blob)) return '<span style="color:#a6744f;">Sin portada</span>';
    return '<img class="img-portada" src="data:image/jpeg;base64,' . base64_encode($blob->load()) . '" alt="Portada">';
}

// Nueva función para mostrar video desde un BLOB
function mostrarVideoBlob($blob) {
    if (!$blob || !is_object($blob)) return '<span style="color:#a6744f;">Sin video</span>';
    $videoData = base64_encode($blob->load());
    // Posterior a HTML5: source con tipo
    return '
    <video width="150" controls>
        <source src="data:video/mp4;base64,' . $videoData . '" type="video/mp4">
        Tu navegador no soporta video HTML5.
    </video>
    ';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedidos del cliente | Librería Hooli</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora&family=Raleway:wght@300&family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css">
    <style>
        .tabla-pedidos { margin-top: 30px; }
        .tabla { width: 100%; border-collapse: collapse; margin-top: 10px; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);}
        .tabla thead { background-color: #a6744f; color: white;}
        .tabla th, .tabla td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e2d6c3; vertical-align: middle;}
        .tabla tbody tr:hover { background-color: #f9f3ed;}
        .mensaje-vacio { color: #a6744f; background: #fff8e7; padding: 15px; border-radius: 8px; margin-top: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); font-size: 1.1em; text-align: center;}
        .img-portada { max-width: 60px; max-height: 80px; border-radius: 7px; box-shadow: 0 1px 6px rgba(0,0,0,0.10); object-fit: cover; }
        video { border-radius: 7px; box-shadow: 0 1px 6px rgba(0,0,0,0.10); margin: 0 auto;}
    </style>
</head>
<body>
    <div class="contenido-principal">
        <header class="encabezado">
            <h1><i class="fa-solid fa-clipboard-list"></i> Consulta de pedido</h1>
            <p>Busca y observa el detalle de tu pedido con el ID asignado</p>
        </header>

        <section class="card">
            <form action="pedidosCliente.php" method="POST" class="formulario">
                <label for="id_pedido">Ingrese el ID de su pedido:</label>
                <input type="number" id="id_pedido" name="id_pedido" required>
                <button type="submit" class="boton">Consultar pedido</button>
            </form>
        </section>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_pedido"])) {
            include("conexion.php"); // Asegúrate de tener la conexión lista
            $id_pedido = $_POST["id_pedido"];
            $sql = "
                SELECT 
                    P.id_pedido,
                    C.Nombre AS nombre_cliente,
                    P.Total,
                    TO_CHAR(P.Fecha_Pedido, 'DD-MM-YYYY') AS fecha_pedido,
                    L.Titulo AS nombre_libro,
                    L.Autor,
                    E.Nombre AS editorial,
                    L.PORTADA,
                    P.FACTURA_PDF
                FROM Pedido P
                JOIN Cliente C ON P.id_cliente = C.id_cliente
                JOIN Detalle_Pedido DP ON P.id_pedido = DP.id_pedido
                JOIN Libro L ON DP.id_libro = L.id_libro
                JOIN Editorial E ON L.id_editorial = E.id_editorial
                WHERE P.id_pedido = :id_pedido
            ";

            $stmt = oci_parse($conexion, $sql);
            oci_bind_by_name($stmt, ":id_pedido", $id_pedido);
            oci_execute($stmt);

            echo '<section class="card tabla-pedidos">';
            echo "<h2><i class='fa-solid fa-book'></i> Detalles del pedido #".htmlspecialchars($id_pedido)."</h2>";
            echo '<table class="tabla">';
            echo "<thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Fecha</th>
                        <th>Libro</th>
                        <th>Autor</th>
                        <th>Editorial</th>
                        <th>Portada</th>
                        <th>Video</th>
                    </tr>
                  </thead>
                  <tbody>";

            $hay_resultados = false;
            while ($row = oci_fetch_assoc($stmt)) {
                $hay_resultados = true;
                echo "<tr>";
                echo "<td>".htmlspecialchars($row['ID_PEDIDO'])."</td>";
                echo "<td>".htmlspecialchars($row['NOMBRE_CLIENTE'])."</td>";
                echo "<td>$".htmlspecialchars($row['TOTAL'])."</td>";
                echo "<td>".htmlspecialchars($row['FECHA_PEDIDO'])."</td>";
                echo "<td>".htmlspecialchars($row['NOMBRE_LIBRO'])."</td>";
                echo "<td>".htmlspecialchars($row['AUTOR'])."</td>";
                echo "<td>".htmlspecialchars($row['EDITORIAL'])."</td>";
                echo "<td>" . mostrarImagenBlob($row['PORTADA']) . "</td>";
                echo "<td>" . mostrarVideoBlob($row['FACTURA_PDF']) . "</td>";
                echo "</tr>";
            }
            if (!$hay_resultados) {
                echo '<tr><td colspan="9" class="mensaje-vacio">❌ No se encontró ningún pedido con ese ID.</td></tr>';
            }
            echo "</tbody></table>";
            echo '</section>';

            oci_free_statement($stmt);
            oci_close($conexion);
        }
        ?>

        <a href="index.php" class="boton" style="margin: 30px auto 0 auto; text-align: center; display: block; max-width: 250px;">← Volver al inicio</a>
    </div>
</body>
</html>

