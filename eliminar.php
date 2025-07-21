<?php
session_start();
include 'db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Validar que se envió el ID a eliminar
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ver_registros.php?error=ID inválido para eliminar");
    exit;
}

$id = intval($_GET['id']);

// Preparar y ejecutar la eliminación
$stmt = $conexion->prepare("DELETE FROM registros WHERE id = ?");
if (!$stmt) {
    header("Location: ver_registros.php?error=Error en la preparación de la consulta");
    exit;
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: ver_registros.php?msg=Registro eliminado correctamente");
    exit;
} else {
    $stmt->close();
    header("Location: ver_registros.php?error=No se pudo eliminar el registro");
    exit;
}
?>
