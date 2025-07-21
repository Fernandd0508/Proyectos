<?php
session_start();
include 'db.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$bus = $_GET['buscar'] ?? '';
$msg = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';

if ($bus) {
    $stmt = $conexion->prepare("SELECT * FROM registros WHERE nombre_cliente LIKE ? ORDER BY fecha_ingreso DESC");
    $p = "%$bus%";
    $stmt->bind_param("s", $p);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = $conexion->query("SELECT * FROM registros ORDER BY fecha_ingreso DESC");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver registros - Sistema de Reparación</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
<header>
    <h1>Sistema de Reparación</h1>
</header>

<nav>
    <a href="index.php">Registrar reparación</a>
    <a href="ver_registros.php" class="btn-clear">Ver todos</a>
    <a href="logout.php">Cerrar sesión</a>
</nav>

<div class="container">
    <h2>Registros de Reparación <a href="exportar_pdf.php" class="btn-export">📄 Exportar PDF</a></h2>
    
    <?php if ($msg): ?>
        <div class="msg"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="GET" class="search-form">
        <input type="text" name="buscar" placeholder="Buscar cliente" value="<?= htmlspecialchars($bus) ?>">
        <input type="submit" value="Buscar">
        <a href="ver_registros.php" class="btn-clear">Ver todos</a>
    </form>

    <?php if ($res && $res->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Equipo</th>
                <th>Problema</th>
                <th>Costo (S/)</th>
                <th>Tiempo</th>
                <th>Entrada</th>
                <th>Salida</th>
                <th>Obs.</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($r = $res->fetch_assoc()): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['nombre_cliente']) ?></td>
                <td><?= htmlspecialchars($r['telefono_cliente']) ?></td>
                <td><?= htmlspecialchars($r['correo_cliente']) ?></td>
                <td><?= htmlspecialchars($r['tipo_equipo']) ?></td>
                <td><?= htmlspecialchars($r['descripcion']) ?></td>
                <td><?= number_format($r['costo_estimado'], 2) ?></td>
                <td><?= htmlspecialchars($r['tiempo_estimado']) ?></td>
                <td><img src="<?= htmlspecialchars($r['foto_entrada']) ?>" width="80" alt="Foto entrada"></td>
                <td><?= $r['foto_salida'] ? "<img src='" . htmlspecialchars($r['foto_salida']) . "' width='80' alt='Foto salida'>" : 'Pendiente' ?></td>
                <td><?= htmlspecialchars($r['observaciones_finales']) ?></td>
                <td><?= $r['fecha_ingreso'] ?></td>
                <td>
                    <a href="editar.php?id=<?= $r['id'] ?>">Editar</a> |
                    <a href="eliminar.php?id=<?= $r['id'] ?>" onclick="return confirm('¿Eliminar este registro?');">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>No se encontraron registros.</p>
    <?php endif; ?>
</div>
</body>
</html>
