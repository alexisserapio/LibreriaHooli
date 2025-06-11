<?php
include("conexion.php"); // Archivo con la conexión $conexion

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_libro = $_POST["id_libro"];
    $id_sucursal = $_POST["id_sucursal"];
    $cantidad = $_POST["cantidad"];

    // 1. Verificar si ya existe registro para ese libro y sucursal en Inventario
    $sql_check = "SELECT COUNT(*) AS count_inv FROM Inventario WHERE id_libro = :id_libro AND id_sucursal = :id_sucursal";
    $stmt_check = oci_parse($conexion, $sql_check);
    oci_bind_by_name($stmt_check, ":id_libro", $id_libro);
    oci_bind_by_name($stmt_check, ":id_sucursal", $id_sucursal);
    oci_execute($stmt_check);
    $row = oci_fetch_assoc($stmt_check);
    $count = $row['COUNT_INV'];

    if ($count > 0) {
        // 2a. Actualizar registro existente
        $sql_update = "UPDATE Inventario SET Cantidad_Disponible = :cantidad WHERE id_libro = :id_libro AND id_sucursal = :id_sucursal";
        $stmt_update = oci_parse($conexion, $sql_update);
        oci_bind_by_name($stmt_update, ":cantidad", $cantidad);
        oci_bind_by_name($stmt_update, ":id_libro", $id_libro);
        oci_bind_by_name($stmt_update, ":id_sucursal", $id_sucursal);
        $success = oci_execute($stmt_update, OCI_COMMIT_ON_SUCCESS);
    } else {
        // 2b. Insertar nuevo registro
        // Aquí asumo que usas una secuencia SEQ_INVENTARIO para id_inventario
        $sql_insert = "INSERT INTO Inventario (id_inventario, id_sucursal, id_libro, Cantidad_Disponible)
                       VALUES (SEQ_INVENTARIO.NEXTVAL, :id_sucursal, :id_libro, :cantidad)";
        $stmt_insert = oci_parse($conexion, $sql_insert);
        oci_bind_by_name($stmt_insert, ":id_sucursal", $id_sucursal);
        oci_bind_by_name($stmt_insert, ":id_libro", $id_libro);
        oci_bind_by_name($stmt_insert, ":cantidad", $cantidad);
        $success = oci_execute($stmt_insert, OCI_COMMIT_ON_SUCCESS);
    }

    if (!$success) {
        oci_rollback($conexion);
        echo "❌ Error al actualizar el inventario.";
        exit;
    }

    // 3. Calcular la suma total en Inventario para ese libro
    $sql_sum = "SELECT SUM(Cantidad_Disponible) AS total_stock FROM Inventario WHERE id_libro = :id_libro";
    $stmt_sum = oci_parse($conexion, $sql_sum);
    oci_bind_by_name($stmt_sum, ":id_libro", $id_libro);
    oci_execute($stmt_sum);
    $row_sum = oci_fetch_assoc($stmt_sum);
    $total_stock = $row_sum['TOTAL_STOCK'] ?? 0;

    // 4. Actualizar Stock en la tabla Libro
    $sql_update_libro = "UPDATE Libro SET Stock = :total_stock WHERE id_libro = :id_libro";
    $stmt_libro = oci_parse($conexion, $sql_update_libro);
    oci_bind_by_name($stmt_libro, ":total_stock", $total_stock);
    oci_bind_by_name($stmt_libro, ":id_libro", $id_libro);
    $success_libro = oci_execute($stmt_libro, OCI_COMMIT_ON_SUCCESS);

    if ($success_libro) {
        echo "✅ Stock actualizado correctamente. Stock total para libro $id_libro: $total_stock";
    } else {
        oci_rollback($conexion);
        echo "❌ Error al actualizar el stock total en Libro.";
    }

    // Liberar recursos
    oci_free_statement($stmt_check);
    if (isset($stmt_update)) oci_free_statement($stmt_update);
    if (isset($stmt_insert)) oci_free_statement($stmt_insert);
    oci_free_statement($stmt_sum);
    oci_free_statement($stmt_libro);
    oci_close($conexion);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar stock | Librería Hooli</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora&family=Raleway:wght@300&family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
    <div class="contenido-principal">
        <header class="encabezado">
            <h1><i class="fa-solid fa-boxes-stacked"></i> Actualizar stock</h1>
            <p>Agrega nuevas unidades al inventario de libros existentes</p>
        </header>

        <section class="card">
            <form action="actualizarStock.php" method="POST" class="formulario">
                <label for="id_libro">ID del libro:</label>
                <input type="number" id="id_libro" name="id_libro" required>
		
		<label for="id_sucursal">Num de Sucursal:</label>
                <input type="number" id="id_sucursal" name="id_sucursal" required>

                <label for="cantidad">Cantidad a agregar:</label>
                <input type="number" id="cantidad" name="cantidad" required>

                <button type="submit" class="boton">Actualizar stock</button>
            </form>

            <a href="index.php" class="boton" style="margin-top: 20px; text-align: center; display: inline-block;">← Volver al inicio</a>
        </section>
    </div>
</body>
</html>

