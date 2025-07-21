<?php
session_start();
include 'db.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Obtener ID del registro a editar
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ver_registros.php");
    exit;
}

$id = intval($_GET['id']);
$error = '';
$exito = '';

// Procesar el formulario al enviarse (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir datos del formulario
    $nombre_cliente = $_POST['nombre_cliente'] ?? '';
    $telefono_cliente = $_POST['telefono_cliente'] ?? '';
    $correo_cliente = $_POST['correo_cliente'] ?? '';
    $tipo_equipo = $_POST['tipo_equipo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $costo_estimado = $_POST['costo_estimado'] ?? '';
    $tiempo_estimado = $_POST['tiempo_estimado'] ?? '';
    $observaciones_finales = $_POST['observaciones_finales'] ?? '';

    // Validar mínimo básico (puedes ampliar)
    if (empty($nombre_cliente) || empty($telefono_cliente) || empty($correo_cliente) || empty($tipo_equipo) || empty($descripcion) || empty($costo_estimado) || empty($tiempo_estimado)) {
        $error = "Por favor completa todos los campos obligatorios.";
    } else {
        // Manejo de imágenes (opcional)
        // Foto de entrada
        $foto_entrada_path = null;
        if (isset($_FILES['foto_entrada']) && $_FILES['foto_entrada']['error'] === UPLOAD_ERR_OK) {
            $foto_entrada_tmp = $_FILES['foto_entrada']['tmp_name'];
            $foto_entrada_name = uniqid('entrada_') . '_' . basename($_FILES['foto_entrada']['name']);
            $destino_entrada = 'uploads/' . $foto_entrada_name;

            if (move_uploaded_file($foto_entrada_tmp, $destino_entrada)) {
                $foto_entrada_path = $destino_entrada;
            } else {
                $error = "Error al subir la foto de entrada.";
            }
        }

        // Foto de salida (opcional)
        $foto_salida_path = null;
        if (isset($_FILES['foto_salida']) && $_FILES['foto_salida']['error'] === UPLOAD_ERR_OK) {
            $foto_salida_tmp = $_FILES['foto_salida']['tmp_name'];
            $foto_salida_name = uniqid('salida_') . '_' . basename($_FILES['foto_salida']['name']);
            $destino_salida = 'uploads/' . $foto_salida_name;

            if (move_uploaded_file($foto_salida_tmp, $destino_salida)) {
                $foto_salida_path = $destino_salida;
            } else {
                $error = "Error al subir la foto de salida.";
            }
        }

        if (!$error) {
            // Construir la consulta UPDATE con o sin fotos
            $query = "UPDATE registros SET nombre_cliente=?, telefono_cliente=?, correo_cliente=?, tipo_equipo=?, descripcion=?, costo_estimado=?, tiempo_estimado=?, observaciones_finales=?";

            $params = [$nombre_cliente, $telefono_cliente, $correo_cliente, $tipo_equipo, $descripcion, $costo_estimado, $tiempo_estimado, $observaciones_finales];

            // Fotos (si se subieron)
            if ($foto_entrada_path) {
                $query .= ", foto_entrada=?";
                $params[] = $foto_entrada_path;
            }
            if ($foto_salida_path) {
                $query .= ", foto_salida=?";
                $params[] = $foto_salida_path;
            }

            $query .= " WHERE id=?";
            $params[] = $id;

            // Preparar y ejecutar
            $tipos = str_repeat("s", count($params) - 1) . "i"; // todos string menos id entero
            $stmt = $conexion->prepare($query);
            $stmt->bind_param($tipos, ...$params);

            if ($stmt->execute()) {
                $exito = "Registro actualizado correctamente.";
            } else {
                $error = "Error al actualizar el registro: " . $conexion->error;
            }
        }
    }
}

// Consultar los datos actuales para mostrar en el formulario
$stmt = $conexion->prepare("SELECT * FROM registros WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows !== 1) {
    // Si no existe registro, redirigir
    header("Location: ver_registros.php");
    exit;
}
$registro = $res->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Reparación</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
<header>
    <h1>Editar Reparación</h1>
</header>
<nav>
    <a href="index.php">Registrar reparación</a>
    <a href="ver_registros.php">Ver registros</a>
    <a href="logout.php">Cerrar sesión</a>
</nav>
<div class="container">
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($exito): ?>
        <div class="exito"><?= htmlspecialchars($exito) ?></div>
    <?php endif; ?>

    <form action="editar.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
        <label for="nombre_cliente">Nombre del cliente:</label>
        <input type="text" id="nombre_cliente" name="nombre_cliente" required value="<?= htmlspecialchars($registro['nombre_cliente']) ?>">

        <label for="telefono_cliente">Teléfono:</label>
        <input type="text" id="telefono_cliente" name="telefono_cliente" required value="<?= htmlspecialchars($registro['telefono_cliente']) ?>">

        <label for="correo_cliente">Correo:</label>
        <input type="email" id="correo_cliente" name="correo_cliente" required value="<?= htmlspecialchars($registro['correo_cliente']) ?>">

        <label for="tipo_equipo">Tipo de equipo:</label>
        <input type="text" id="tipo_equipo" name="tipo_equipo" required value="<?= htmlspecialchars($registro['tipo_equipo']) ?>">

        <label for="descripcion">Descripción del problema:</label>
        <textarea id="descripcion" name="descripcion" required><?= htmlspecialchars($registro['descripcion']) ?></textarea>

        <label for="costo_estimado">Costo estimado (S/):</label>
        <input type="number" step="0.01" id="costo_estimado" name="costo_estimado" required value="<?= htmlspecialchars($registro['costo_estimado']) ?>">

        <label for="tiempo_estimado">Tiempo estimado:</label>
        <input type="text" id="tiempo_estimado" name="tiempo_estimado" required value="<?= htmlspecialchars($registro['tiempo_estimado']) ?>">

        <label>Foto de entrada actual:</label><br>
        <?php if ($registro['foto_entrada']): ?>
            <img src="<?= htmlspecialchars($registro['foto_entrada']) ?>" width="120" alt="Foto Entrada"><br>
        <?php else: ?>
            <small>No hay foto de entrada.</small><br>
        <?php endif; ?>
        <label for="foto_entrada">Cambiar foto de entrada (opcional):</label>
        <input type="file" id="foto_entrada" name="foto_entrada" accept="image/*">

        <label>Foto de salida actual:</label><br>
        <?php if ($registro['foto_salida']): ?>
            <img src="<?= htmlspecialchars($registro['foto_salida']) ?>" width="120" alt="Foto Salida"><br>
        <?php else: ?>
            <small>No hay foto de salida.</small><br>
        <?php endif; ?>
        <label for="foto_salida">Cambiar foto de salida (opcional):</label>
        <input type="file" id="foto_salida" name="foto_salida" accept="image/*">

        <label for="observaciones_finales">Observaciones (opcional):</label>
        <textarea id="observaciones_finales" name="observaciones_finales"><?= htmlspecialchars($registro['observaciones_finales']) ?></textarea>

        <input type="submit" value="Actualizar Reparación">
    </form>
</div>
</body>
</html>
