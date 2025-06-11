<?php
// Parámetros de conexión
$usuario = 'adminbda';
$contrasena = 'admin123';
$cadena_conexion = '192.168.100.139/orcl';

$conexion = oci_connect($usuario, $contrasena, $cadena_conexion);

if (!$conexion) {
    $e = oci_error();
    die("❌ Error al conectar a la base de datos: " . htmlentities($e['message']));
}

// Solo muestra el mensaje si se accede directamente
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    echo "✅ ¡Conexión a Oracle 19c exitosa!";
    oci_close($conexion);
}
?>

