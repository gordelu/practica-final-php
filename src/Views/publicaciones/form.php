<?php
// Sirve para crear y para editar: si la publicación tiene id, es edición
$editando = !empty($publicacion['id']);
$action = $editando ? '/publicaciones/' . $publicacion['id'] . '/editar' : '/publicaciones/crear';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $editando ? 'Editar publicación' : 'Nueva publicación' ?></title>
</head>
<body>
    <p><a href="/publicaciones">← Volver</a></p>

    <h1><?= $editando ? 'Editar publicación' : 'Nueva publicación' ?></h1>

    <?php if (!empty($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="<?= $action ?>" method="POST">
        <label>Título <input type="text" name="titulo" maxlength="200" required
            value="<?= htmlspecialchars($publicacion['titulo'] ?? '') ?>"></label><br>
        <label>Categoría <input type="text" name="categoria" maxlength="50" required
            value="<?= htmlspecialchars($publicacion['categoria'] ?? '') ?>"></label><br>
        <label>Cuerpo<br><textarea name="cuerpo" rows="8" cols="60" required><?= htmlspecialchars($publicacion['cuerpo'] ?? '') ?></textarea></label><br>
        <button type="submit"><?= $editando ? 'Guardar cambios' : 'Publicar' ?></button>
    </form>
</body>
</html>
