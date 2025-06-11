<?php
include("conexion.php");

$mensaje = "";
$tipo_mensaje = ""; // 'exito' o 'error'

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_libro"])) {
    $id_libro = $_POST["id_libro"];

    // Primero verifica si el libro existe
    $sql_verifica = "SELECT COUNT(*) AS TOTAL FROM Libro WHERE id_libro = :id_libro";
    $stmt_verifica = oci_parse($conexion, $sql_verifica);
    oci_bind_by_name($stmt_verifica, ":id_libro", $id_libro);
    oci_execute($stmt_verifica);
    $row = oci_fetch_assoc($stmt_verifica);

    if ($row["TOTAL"] == 0) {
        $mensaje = "❌ El libro con ID <b>$id_libro</b> no existe.";
        $tipo_mensaje = "error";
    } else {
        // Intenta eliminar el libro
        $sql = "DELETE FROM Libro WHERE id_libro = :id_libro";
        $stmt = oci_parse($conexion, $sql);
        oci_bind_by_name($stmt, ":id_libro", $id_libro);
        $exito = @oci_execute($stmt);

        if ($exito) {
            $mensaje = "✅ Libro con ID <b>$id_libro</b> eliminado exitosamente.";
            $tipo_mensaje = "exito";
        } else {
            $e = oci_error($stmt);
            if (strpos($e['message'], 'ORA-02292') !== false) {
                $mensaje = "❌ No se puede eliminar el libro porque existen registros relacionados (por ejemplo, en pedidos o inventario).";
            } else {
                $mensaje = "❌ Error al intentar eliminar: " . htmlentities($e['message']);
            }
            $tipo_mensaje = "error";
        }
        oci_free_statement($stmt);
    }
    oci_free_statement($stmt_verifica);
    oci_close($conexion);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar libro | Librería Hooli</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora&family=Raleway:wght@300&family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css">
    <style>
        .contenido-principal { padding: 40px; }
        .card { background: #fff; border: 1px solid #d7c4ae; border-radius: 10px; padding: 24px; box-shadow: 0 4px 10px rgba(0,0,0,0.10); max-width: 500px; margin: 0 auto 30px auto;}
        .encabezado { text-align: center; margin-bottom: 24px; }
        .formulario { display: flex; flex-direction: column; gap: 16px; max-width: 320px; margin: 0 auto; }
        .formulario label { font-weight: bold; color: #5a3d2b; }
        .formulario input[type="number"] { padding: 10px; border: 1px solid #d7c4ae; border-radius: 5px; font-size: 1em; background-color: #fefefe; font-family: 'Roboto', sans-serif;}
        .boton { display: inline-block; margin-top: 10px; padding: 10px 22px; background: #a6744f; color: white; border-radius: 5px; text-decoration: none; font-family: 'Raleway', sans-serif; font-size: 1.1em; transition: background 0.3s ease; border: none; cursor: pointer;}
        .boton:hover { background: #8b5e3c;}
        .mensaje-exito { color: #227c41; background: #e6faec; padding: 13px 20px; border-radius: 8px; margin-bottom: 18px; border: 1.5px solid #5dbb7e; font-size: 1.08em; text-align:center;}
        .mensaje-error { color: #b53a33; background: #ffe8e8; padding: 13px 20px; border-radius: 8px; margin-bottom: 18px; border: 1.5px solid #df8c8c; font-size: 1.08em; text-align:center;}
    </style>
</head>
<body>
    <div class="contenido-principal">
        <header class="encabezado">
            <h1><i class="fa-solid fa-trash"></i> Eliminar libro</h1>
            <p>Elimina un libro existente</p>
        </header>
        <section class="card">
            <?php if ($mensaje): ?>
                <div class="mensaje-<?= $tipo_mensaje ?>">
                    <?= $mensaje ?>
                </div>
            <?php endif; ?>
            <form method="POST" class="formulario">
                <label for="id_libro">ID del libro a eliminar:</label>
                <input type="number" id="id_libro" name="id_libro" min="1" required>
                <button type="submit" class="boton"><i class="fa-solid fa-trash"></i> Eliminar libro</button>
            </form>
            <a href="index.php" class="boton" style="margin: 18px auto 0 auto; display: block; max-width: 220px;">← Regresar a principal</a>
        </section>
    </div>
</body>
</html>

