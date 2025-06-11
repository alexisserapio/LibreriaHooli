<?php
include("conexion.php");

// Función para mostrar portada
function mostrarImagenBlob($blob) {
    if (!$blob || !is_object($blob)) return '<span style="color:#a6744f;">Sin portada</span>';
    return '<img class="img-portada" src="data:image/jpeg;base64,' . base64_encode($blob->load()) . '" alt="Portada">';
}

// Consulta todos los libros
$sql = "SELECT id_libro, Titulo, Autor, Genero, Precio, Stock, Anio_Publicacion, Portada FROM Libro ORDER BY id_libro ASC";
$stmt = oci_parse($conexion, $sql);
oci_execute($stmt);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librería Hooli | Catálogo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora&family=Raleway:wght@300&family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css">
    <style>
        .app { display: flex; min-height: 100vh; }
        .menu-aside { width: 250px; background: #8b5e3c; padding: 20px; box-shadow: 3px 0 8px rgba(0,0,0,0.2); color: white; }
        .logo { font-size: 1.5em; font-weight: bold; text-align: center; margin-bottom: 30px; }
        .menu a { color: #fff8e7; text-decoration: none; display: block; padding: 12px 10px; border-radius: 5px; margin: 8px 0; transition: background 0.3s ease; }
        .menu a:hover { background: rgba(255,255,255,0.1);}
        .contenido-principal { flex-grow: 1; padding: 40px; background-color: #f8f5f0;}
        .encabezado h1 { font-family: 'Raleway', sans-serif; font-size: 2.5em; margin-bottom: 5px; color: #5a3d2b;}
        .encabezado p { font-size: 1.2em; color: #7b5e44;}
        .tabla { width: 100%; border-collapse: collapse; margin-top: 24px; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);}
        .tabla thead { background-color: #a6744f; color: white;}
        .tabla th, .tabla td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e2d6c3; vertical-align: middle;}
        .tabla tbody tr:hover { background-color: #f9f3ed;}
        .img-portada { max-width: 60px; max-height: 80px; border-radius: 7px; box-shadow: 0 1px 6px rgba(0,0,0,0.10); object-fit: cover; }
        .acciones-link { display: inline-block; margin: 10px 10px 0 0; padding: 8px 16px; background: #a6744f; color: #fff; border-radius: 4px; text-decoration: none; font-size: 1em; transition: background 0.3s; }
        .acciones-link:hover { background: #8b5e3c;}
        .menu-titulo { font-size: 1.1em; font-weight: 500; margin-bottom: 18px; margin-top: -10px; text-align:center;}
        @media (max-width: 900px) {
            .tabla th, .tabla td { padding: 6px 8px; font-size: 0.92em;}
            .contenido-principal {padding: 8px;}
            .menu-aside {width: 90px;}
        }
    </style>
</head>
<body>
    <div class="app">
        <!-- Menú lateral -->
        <aside class="menu-aside">
            <div class="logo">
                <span class="i-logo"><i class="fa-solid fa-book"></i><br> Librería Hooli</span>
            </div>
            <div class="menu-titulo">Acciones</div>
            <ul class="menu">
                <li><a href="insertarLibro.php"><i class="fa-solid fa-square-plus"></i> Insertar libro</a></li>
                <li><a href="actualizarStock.php"><i class="fa-solid fa-boxes-stacked"></i> Actualizar stock</a></li>
                <li><a href="realizarPedido.php"><i class="fa-solid fa-cart-plus"></i> Realizar pedido</a></li>
                <li><a href="pedidosCliente.php"><i class="fa-solid fa-clipboard-list"></i> Ver pedidos</a></li>
		<li><a href="librosSucursal.php"><i class="fa-solid fa-warehouse"></i> Stock por sucursal</a></li>
		<li><a href="elminarLibro.php"><i class="fa-solid fa-crosshairs"></i> Eliminar libro </a></li>
            </ul>
        </aside>

        <!-- Contenido principal -->
        <div class="contenido-principal">
            <header class="encabezado">
                <h1><i class="fa-solid fa-book"></i> Nuestros Libros </h1>
                <p>Revisa todos los libros que tenemos disponibles</p>
            </header>
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Portada</th>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Género</th>
                        <th>Año</th>
                        <th>Precio</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $hay_resultados = false;
                while ($row = oci_fetch_assoc($stmt)) {
                    $hay_resultados = true;
                    echo '<tr>';
                    echo '<td>' . mostrarImagenBlob($row['PORTADA']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['ID_LIBRO']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['TITULO']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['AUTOR']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['GENERO']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['ANIO_PUBLICACION']) . '</td>';
                    echo '<td>$' . htmlspecialchars($row['PRECIO']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['STOCK']) . '</td>';
                    echo '</tr>';
                }
                if (!$hay_resultados) {
                    echo '<tr><td colspan="8" style="color:#a6744f; text-align:center;">No hay libros registrados.</td></tr>';
                }
                oci_free_statement($stmt);
                oci_close($conexion);
                ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

