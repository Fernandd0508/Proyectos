<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usu = trim($_POST['usuario']);
    $pass = hash('sha256', $_POST['clave']);

    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE usuario=? AND clave=?");
    $stmt->bind_param("ss", $usu, $pass);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $_SESSION['usuario'] = $usu;
        header("Location: index.php");
        exit;
    } else {
        $error = "Usuario o clave incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Futurista</title>
    <link rel="stylesheet" href="estilo.css?v=1">
</head>
<body class="futuristic-bg">
    <div class="login-container futuristic-box">
        <h2>🔒 Acceso Seguro</h2>

        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <label for="usuario">👤 Usuario:</label>
            <input type="text" id="usuario" name="usuario" required>

            <label for="clave">🔑 Clave:</label>
            <input type="password" id="clave" name="clave" required>

            <input type="submit" value="🚀 Ingresar">
        </form>
    </div>
</body>
</html>
