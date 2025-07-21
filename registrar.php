<?php
session_start();
include 'db.php';
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

function subir($campo) {
    if (isset($_FILES[$campo]) && $_FILES[$campo]['name']) {
        $nombre = uniqid() . "_" . basename($_FILES[$campo]['name']);
        $destino = "uploads/" . $nombre;
        move_uploaded_file($_FILES[$campo]['tmp_name'], $destino);
        return $destino;
    }
    return "";
}

$datos = [
    $_POST['nombre_cliente'],
    $_POST['telefono_cliente'],
    $_POST['correo_cliente'],
    $_POST['tipo_equipo'],
    $_POST['descripcion'],
    $_POST['costo_estimado'],
    $_POST['tiempo_estimado'],
    subir('foto_entrada'),
    subir('foto_salida'),
    $_POST['observaciones_finales'],
    date('Y-m-d H:i:s')
];

$stmt = $conexion->prepare("
INSERT INTO registros
(nombre_cliente, telefono_cliente, correo_cliente, tipo_equipo, descripcion, costo_estimado, tiempo_estimado, foto_entrada, foto_salida, observaciones_finales, fecha_ingreso)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("sssssdsssss", ...$datos);
if ($stmt->execute()) {
    header("Location: ver_registros.php");
    exit;
} else {
    echo "Error: " . $stmt->error;
}
