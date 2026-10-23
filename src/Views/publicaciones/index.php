<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Publicaciones</title>
</head>
<body>
    <h1>Publicaciones</h1>

    <?php if (!empty($_SESSION['user_id'])): ?>
        <p><a href="/publicaciones/crear">Nueva publicación</a> | <a href="/logout">Cerrar sesión</a></p>
    <?php else: ?>
        <p><a href="/login">Iniciar sesión</a> | <a href="/register">Registrarse</a></p>
    <?php endif; ?>

    <form action="/publicaciones" method="GET">
        <input type="text" name="busqueda" placeholder="Buscar por título o categoría" value="<?= htmlspecialchars($busqueda) ?>">
        <button type="submit">Buscar</button>
    </form>

    <?php if (empty($publicaciones)): ?>
        <p>No hay publicaciones.</p>
    <?php endif; ?>

    <?php foreach ($publicaciones as $p): ?>
        <article>
            <h2><a href="/publicaciones/<?= $p['id'] ?>"><?= htmlspecialchars($p['titulo']) ?></a></h2>
            <p>
                <a href="/publicaciones?busqueda=<?= urlencode($p['categoria']) ?>"><?= htmlspecialchars($p['categoria']) ?></a>
                · <?= htmlspecialchars($p['autor']) ?> · <?= $p['created_at'] ?>
            </p>
        </article>
    <?php endforeach; ?>
</body>
</html>
