<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_cliente = $_POST["id_cliente"];
    $id_libro = $_POST["id_libro"];

    // 1. Obtener precio del libro
    $sql_precio = "SELECT Precio FROM Libro WHERE id_libro = :id_libro";
    $stmt_precio = oci_parse($conexion, $sql_precio);
    oci_bind_by_name($stmt_precio, ":id_libro", $id_libro);
    oci_execute($stmt_precio);
    $row_precio = oci_fetch_assoc($stmt_precio);

    if (!$row_precio) {
        echo "❌ Libro no encontrado.";
        exit;
    }
    $precio = $row_precio['PRECIO'];

    // 2. Insertar pedido con Factura_pdf vacío y fecha actual
    $sql_insert_pedido = "INSERT INTO Pedido (id_pedido, id_cliente, Factura_pdf, Total, Fecha_Pedido)
                          VALUES (SEQ_PEDIDO.NEXTVAL, :id_cliente, EMPTY_BLOB(), :total, SYSDATE)
                          RETURNING id_pedido INTO :id_pedido_out";

    $stmt_pedido = oci_parse($conexion, $sql_insert_pedido);
    oci_bind_by_name($stmt_pedido, ":id_cliente", $id_cliente);
    oci_bind_by_name($stmt_pedido, ":total", $precio);
    oci_bind_by_name($stmt_pedido, ":id_pedido_out", $id_pedido_out, 32, SQLT_INT);

    $success = oci_execute($stmt_pedido, OCI_NO_AUTO_COMMIT);

    if (!$success) {
        oci_rollback($conexion);
        echo "❌ Error al insertar el pedido.";
        exit;
    }

    // 3. Insertar en Detalle_Pedido
    $sql_detalle = "INSERT INTO Detalle_Pedido (id_detalle, id_pedido, id_libro, Cantidad, precio_unitario)
                    VALUES (SEQ_DETALLE.NEXTVAL, :id_pedido, :id_libro, 1, :precio)";

    $stmt_detalle = oci_parse($conexion, $sql_detalle);
    oci_bind_by_name($stmt_detalle, ":id_pedido", $id_pedido_out);
    oci_bind_by_name($stmt_detalle, ":id_libro", $id_libro);
    oci_bind_by_name($stmt_detalle, ":precio", $precio);
    $success_detalle = oci_execute($stmt_detalle, OCI_NO_AUTO_COMMIT);

    if (!$success_detalle) {
        oci_rollback($conexion);
        echo "❌ Error al insertar en Detalle_Pedido.";
        exit;
    }

    // 4. Decrementar inventario
    $sql_inv = "SELECT id_inventario, Cantidad_Disponible FROM Inventario WHERE id_libro = :id_libro AND Cantidad_Disponible > 0 ORDER BY Cantidad_Disponible DESC FETCH FIRST 1 ROWS ONLY";
    $stmt_inv = oci_parse($conexion, $sql_inv);
    oci_bind_by_name($stmt_inv, ":id_libro", $id_libro);
    oci_execute($stmt_inv);
    $row_inv = oci_fetch_assoc($stmt_inv);

    if (!$row_inv) {
        oci_rollback($conexion);
        echo "❌ No hay inventario disponible para este libro.";
        exit;
    }

    $id_inventario = $row_inv['ID_INVENTARIO'];

    $sql_update_inv = "UPDATE Inventario SET Cantidad_Disponible = Cantidad_Disponible - 1 WHERE id_inventario = :id_inventario";
    $stmt_update_inv = oci_parse($conexion, $sql_update_inv);
    oci_bind_by_name($stmt_update_inv, ":id_inventario", $id_inventario);
    $success_inv = oci_execute($stmt_update_inv, OCI_NO_AUTO_COMMIT);

    if (!$success_inv) {
        oci_rollback($conexion);
        echo "❌ Error al actualizar inventario.";
        exit;
    }

    // 5. Actualizar stock total en Libro
    $sql_sum = "SELECT NVL(SUM(Cantidad_Disponible),0) AS total_stock FROM Inventario WHERE id_libro = :id_libro";
    $stmt_sum = oci_parse($conexion, $sql_sum);
    oci_bind_by_name($stmt_sum, ":id_libro", $id_libro);
    oci_execute($stmt_sum);
    $row_sum = oci_fetch_assoc($stmt_sum);
    $total_stock = $row_sum['TOTAL_STOCK'];

    $sql_update_libro = "UPDATE Libro SET Stock = :total_stock WHERE id_libro = :id_libro";
    $stmt_update_libro = oci_parse($conexion, $sql_update_libro);
    oci_bind_by_name($stmt_update_libro, ":total_stock", $total_stock);
    oci_bind_by_name($stmt_update_libro, ":id_libro", $id_libro);
    $success_libro = oci_execute($stmt_update_libro, OCI_NO_AUTO_COMMIT);

    if (!$success_libro) {
        oci_rollback($conexion);
        echo "❌ Error al actualizar stock en Libro.";
        exit;
    }

    // 6. Confirmar transacción
    oci_commit($conexion);
    echo "✅ Pedido y detalle insertados exitosamente. ID Pedido: $id_pedido_out";

    // Liberar
    oci_free_statement($stmt_precio);
    oci_free_statement($stmt_pedido);
    oci_free_statement($stmt_detalle);
    oci_free_statement($stmt_inv);
    oci_free_statement($stmt_update_inv);
    oci_free_statement($stmt_sum);
    oci_free_statement($stmt_update_libro);
    oci_close($conexion);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Realizar pedido | Librería Hooli</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora&family=Raleway:wght@300&family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
    <div class="contenido-principal">
        <header class="encabezado">
            <h1><i class="fa-solid fa-cart-plus"></i> Realizar pedido</h1>
            <p>Completa los datos para registrar un pedido del cliente</p>
        </header>

        <section class="card">
            <form action="realizarPedido.php" method="POST" class="formulario">
                <label for="id_cliente">ID del cliente:</label>
                <input type="number" id="id_cliente" name="id_cliente" required>

                <label for="id_libro">ID del libro:</label>
                <input type="number" id="id_libro" name="id_libro" required>

                <label for="cantidad">Cantidad solicitada:</label>
                <input type="number" id="cantidad" name="cantidad" required>

                <label for="fecha">Fecha del pedido:</label>
                <input type="date" id="fecha" name="fecha" required>

                <button type="submit" class="boton">Registrar pedido</button>
            </form>

            <a href="index.php" class="boton" style="margin-top: 20px; text-align: center; display: inline-block;">← Volver al inicio</a>
        </section>
    </div>
</body>
</html>

