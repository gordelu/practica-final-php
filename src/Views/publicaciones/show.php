<?php $userId = (int) ($_SESSION['user_id'] ?? 0); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($publicacion['titulo']) ?></title>
</head>
<body>
    <p><a href="/publicaciones">← Volver</a></p>

    <h1><?= htmlspecialchars($publicacion['titulo']) ?></h1>
    <p><?= htmlspecialchars($publicacion['categoria']) ?> · <?= htmlspecialchars($publicacion['autor']) ?> · <?= $publicacion['created_at'] ?></p>
    <p><?= nl2br(htmlspecialchars($publicacion['cuerpo'])) ?></p>

    <?php if ($userId === (int) $publicacion['usuario_id']): ?>
        <a href="/publicaciones/<?= $publicacion['id'] ?>/editar">Editar</a>
        <form action="/publicaciones/<?= $publicacion['id'] ?>/borrar" method="POST" style="display: inline;">
            <button type="submit" onclick="return confirm('¿Borrar la publicación?')">Borrar</button>
        </form>
    <?php endif; ?>

    <h2>Comentarios (<?= count($comentarios) ?>)</h2>

    <?php foreach ($comentarios as $c): ?>
        <div>
            <strong><?= htmlspecialchars($c['autor']) ?></strong> · <?= $c['created_at'] ?>
            <p><?= nl2br(htmlspecialchars($c['contenido'])) ?></p>

            <?php if ($userId === (int) $c['usuario_id']): ?>
                <form action="/comentarios/<?= $c['id'] ?>/borrar" method="POST">
                    <button type="submit">Borrar comentario</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <?php if ($userId): ?>
        <form action="/publicaciones/<?= $publicacion['id'] ?>/comentarios" method="POST">
            <textarea name="contenido" rows="3" required></textarea><br>
            <button type="submit">Comentar</button>
        </form>
    <?php else: ?>
        <p><a href="/login">Inicia sesión</a> para comentar.</p>
    <?php endif; ?>
</body>
</html>
