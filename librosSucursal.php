<?php
include("conexion.php");

function mostrarImagenBlob($blob) {
    if (!$blob || !is_object($blob)) return '<span style="color:#a6744f;">Sin portada</span>';
    return '<img class="img-portada" src="data:image/jpeg;base64,' . base64_encode($blob->load()) . '" alt="Portada">';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Stock por Sucursal | Librería Hooli</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora&family=Raleway:wght@300&family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css">
    <style>
        .contenido-principal {
            padding: 40px;
        }
        .card {
            background: #fff;
            border: 1px solid #d7c4ae;
            border-radius: 10px;
            padding: 24px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.10);
            max-width: 900px;
            margin: 0 auto 30px auto;
        }
        .encabezado {
            text-align: center;
            margin-bottom: 24px;
        }
        .tabla-sucursal {
            margin-top: 30px;
        }
        .tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .tabla thead {
            background-color: #a6744f;
            color: white;
        }
        .tabla th, .tabla td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e2d6c3;
            vertical-align: middle;
        }
        .tabla tbody tr:hover {
            background-color: #f9f3ed;
        }
        .mensaje-vacio {
            color: #a6744f;
            background: #fff8e7;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            font-size: 1.1em;
            text-align: center;
        }
        .img-portada {
            max-width: 60px;
            max-height: 80px;
            border-radius: 7px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.10);
            object-fit: cover;
        }
        .boton {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 22px;
            background: #a6744f;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-family: 'Raleway', sans-serif;
            font-size: 1.1em;
            transition: background 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .boton:hover {
            background: #8b5e3c;
        }
        .formulario {
            display: flex;
            flex-direction: column;
            gap: 14px;
            max-width: 400px;
            margin: 0 auto;
        }
        .formulario label {
            font-weight: bold;
            color: #5a3d2b;
        }
        .formulario input[type="number"] {
            padding: 10px;
            border: 1px solid #d7c4ae;
            border-radius: 5px;
            font-size: 1em;
            background-color: #fefefe;
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body>
    <div class="contenido-principal">
        <header class="encabezado">
            <h1><i class="fa-solid fa-warehouse"></i> Stock por Sucursal</h1>
            <p>Consulta la existencia de un libro en todas las sucursales</p>
        </header>

        <section class="card">
            <form method="POST" class="formulario">
                <label for="id_libro">ID del Libro:</label>
                <input type="number" id="id_libro" name="id_libro" required>
                <button type="submit" class="boton">Consultar</button>
            </form>
            <a href="index.php" class="boton" style="margin: 10px auto 0 auto; display: block; max-width: 220px;">← Regresar a Principal</a>
        </section>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_libro"])) {
            $id_libro = $_POST["id_libro"];

            $sql = "
                SELECT 
                    L.id_libro,
                    L.Titulo,
                    S.id_sucursal,
                    S.Nombre AS nombre_sucursal,
                    I.Cantidad_Disponible AS stock,
                    S.Direccion,
                    L.Portada
                FROM Libro L
                JOIN Inventario I ON L.id_libro = I.id_libro
                JOIN Sucursal S ON I.id_sucursal = S.id_sucursal
                WHERE L.id_libro = :id_libro
                ORDER BY S.id_sucursal
            ";

            $stmt = oci_parse($conexion, $sql);
            oci_bind_by_name($stmt, ":id_libro", $id_libro);
            oci_execute($stmt);

            echo '<section class="card tabla-sucursal">';
            echo "<h2><i class='fa-solid fa-book'></i> Resultados del libro en sucursales:</h2>";
            echo '<table class="tabla">';
            echo "<thead>
                    <tr>
                        <th>ID Libro</th>
                        <th>Título</th>
                        <th>ID Sucursal</th>
                        <th>Nombre Sucursal</th>
                        <th>Stock</th>
                        <th>Dirección</th>
                        <th>Portada</th>
                    </tr>
                  </thead>
                  <tbody>";

            $hay_resultados = false;
            while ($row = oci_fetch_assoc($stmt)) {
                $hay_resultados = true;
                echo "<tr>";
                echo "<td>".htmlspecialchars($row['ID_LIBRO'])."</td>";
                echo "<td>".htmlspecialchars($row['TITULO'])."</td>";
                echo "<td>".htmlspecialchars($row['ID_SUCURSAL'])."</td>";
                echo "<td>".htmlspecialchars($row['NOMBRE_SUCURSAL'])."</td>";
                echo "<td>".htmlspecialchars($row['STOCK'])."</td>";
                echo "<td>".htmlspecialchars($row['DIRECCION'])."</td>";
                echo "<td>" . mostrarImagenBlob($row['PORTADA']) . "</td>";
                echo "</tr>";
            }

            if (!$hay_resultados) {
                echo '<tr><td colspan="7" class="mensaje-vacio">❌ No hay registros para este libro.</td></tr>';
            }

            echo "</tbody></table>";
            echo '</section>';

            oci_free_statement($stmt);
            oci_close($conexion);
        }
        ?>
    </div>
</body>
</html>

