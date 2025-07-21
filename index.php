<?php
session_start();
include 'db.php';
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar reparación</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
<header><h1>Sistema de Reparación</h1></header>
<nav>
    <a href="index.php">Registrar reparación</a>
    <a href="ver_registros.php">Ver registros</a>
    <a href="logout.php">Cerrar sesión</a>
</nav>
<div class="container">
    <h2>Registrar nueva reparación</h2>
    <form action="registrar.php" method="POST" enctype="multipart/form-data">
        <label for="nombre_cliente">Nombre del cliente:</label>
        <input type="text" id="nombre_cliente" name="nombre_cliente" required>

        <label for="telefono_cliente">Teléfono:</label>
        <input type="text" id="telefono_cliente" name="telefono_cliente" required>

        <label for="correo_cliente">Correo:</label>
        <input type="email" id="correo_cliente" name="correo_cliente" required>

        <label for="tipo_equipo">Tipo de equipo:</label>
        <input type="text" id="tipo_equipo" name="tipo_equipo" required>

        <label for="descripcion">Descripción del problema:</label>
        <textarea id="descripcion" name="descripcion" rows="4" required></textarea>

        <label for="costo_estimado">Costo estimado (S/.):</label>
        <input type="number" id="costo_estimado" name="costo_estimado" step="0.01" required>

        <label for="tiempo_estimado">Tiempo estimado:</label>
        <input type="text" id="tiempo_estimado" name="tiempo_estimado" required>

        <label for="foto_entrada">Foto de entrada:</label>
        <input type="file" id="foto_entrada" name="foto_entrada" accept="image/*" required>

        <label for="foto_salida">Foto de salida:</label>
        <input type="file" id="foto_salida" name="foto_salida" accept="image/*">

        <label for="observaciones_finales">Observaciones:</label>
        <textarea id="observaciones_finales" name="observaciones_finales" rows="3"></textarea>

        <input type="submit" value="Registrar reparación">
    </form>
</div>
</body>
</html>
