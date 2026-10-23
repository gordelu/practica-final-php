<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
</head>
<body>
    <h1>Registro</h1>

    <?php if (!empty($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="/register" method="POST">
        <label>Nombre <input type="text" name="nombre" required></label><br>
        <label>Email <input type="email" name="email" required></label><br>
        <label>Contraseña <input type="password" name="password" minlength="6" required></label><br>
        <button type="submit">Registrarse</button>
    </form>

    <p>¿Ya tienes cuenta? <a href="/login">Inicia sesión</a></p>
</body>
</html>
