<?php
include("conexion.php"); // Conexión a la BD

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario
    $id_editorial = $_POST["id_editorial"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];
    $genero = $_POST["genero"];
    $anio = $_POST["anio"];
    $autor = $_POST["autor"];
    $titulo = $_POST["titulo"];
    $portada = $_FILES["portada"]["tmp_name"];
    $video_mp4 = $_FILES["video_libro"]["tmp_name"];

    // Preparar inserción
    $sql = "INSERT INTO Libro (
                id_libro, id_editorial, Portada, Precio, Stock, Genero, Anio_Publicacion, Autor, Titulo, video_libro
            ) VALUES (
                SEQ_LIBRO.NEXTVAL, :id_editorial, EMPTY_BLOB(), :precio, :stock, :genero, :anio, :autor, :titulo, EMPTY_BLOB()
            ) RETURNING Portada, video_libro INTO :portada_blob, :video_blob";

    $stmt = oci_parse($conexion, $sql);

    // Vincular variables
    oci_bind_by_name($stmt, ":id_editorial", $id_editorial);
    oci_bind_by_name($stmt, ":precio", $precio);
    oci_bind_by_name($stmt, ":stock", $stock);
    oci_bind_by_name($stmt, ":genero", $genero);
    oci_bind_by_name($stmt, ":anio", $anio);
    oci_bind_by_name($stmt, ":autor", $autor);
    oci_bind_by_name($stmt, ":titulo", $titulo);

    // BLOB
    $lob_portada = oci_new_descriptor($conexion, OCI_D_LOB);
    $lob_video = oci_new_descriptor($conexion, OCI_D_LOB);
    oci_bind_by_name($stmt, ":portada_blob", $lob_portada, -1, OCI_B_BLOB);
    oci_bind_by_name($stmt, ":video_blob", $lob_video, -1, OCI_B_BLOB);

    // Ejecutar
    $success = oci_execute($stmt, OCI_NO_AUTO_COMMIT);
    $ok_portada = $lob_portada->save(file_get_contents($portada));
    $ok_video = $lob_video->save(file_get_contents($video_mp4));

    if ($success && $ok_portada && $ok_video) {
        oci_commit($conexion);
        echo "✅ Libro insertado exitosamente.";
    } else {
        oci_rollback($conexion);
        echo "❌ Error al insertar el libro.";
    }

    // Liberar recursos
    $lob_portada->free();
    $lob_video->free();
    oci_free_statement($stmt);
    oci_close($conexion);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Insertar libro | Librería Hooli</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora&family=Raleway:wght@300&family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
    <div class="contenido-principal">
        <header class="encabezado">
            <h1><i class="fa-solid fa-square-plus"></i> Insertar nuevo libro</h1>
            <p>Rellena los campos para agregar un nuevo libro al inventario</p>
        </header>

        <section class="card">
            <form action="insertarLibro.php" method="POST" enctype="multipart/form-data" class="formulario">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" required>

                <label for="autor">Autor:</label>
                <input type="text" id="autor" name="autor" required>

                <label for="id_editorial">Editorial:</label>
                <input type="number" id="id_editorial" name="id_editorial" required>

                <label for="precio">Precio:</label>
                <input type="number" step="0.01" id="precio" name="precio" required>

                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" required>

                <label for="genero">Género:</label>
                <input type="text" id="genero" name="genero" required>

                <label for="anio">Año de publicación:</label>
                <input type="number" id="anio" name="anio" required>

                <label for="portada">Portada:</label>
                <input type="file" id="portada" name="portada" accept="image/*" required>
		
		<label for="video_libro">Video presentación:</label>
                <input type="file" id="video_libro" name="video_libro" accept="video/mp4" required>

                <button type="submit" class="boton">Insertar libro</button>
            </form>
	<a href="index.php" class="boton" style="margin-top: 20px; text-align: center; display: inline-block;">← Volver al inicio</a>
        </section>
    </div>
</body>
</html>

