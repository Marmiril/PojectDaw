<?php
session_start();
if(!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preferncias del Usuario</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header><h1>Indica tus preferencias</h1></header>

    <main>
        <section id="preferencias">
            <h2>¡Cuenta algo sobre ti!</h2>
            <form action="../php/preferencias_usuario.php" method="POST" id="form-preferencias">
                <input type="hidden" name="usuario_id" value="<?php echo $_SESSION['usuario_id']; ?>">

                <label for="color_favorito">Color favorito</label>
                <input type="text" id="color_favorito" name="color_favorito" required>

                <label for="edad">Edad</label>
                <input type="number" id="edad" name="edad" required min="1">

                <label for="altura">Altura</label>
                <input type="number" id="altura" name="altura" required step="0.1" min="1">

                <label for="peso">Peso</label>
                <input type="number" id="peso" name="peso" required step="0.1" min="1">

                <label for="genero">Género</label>
                <select id="genero" name="genero" required>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                </select>
                
                <div id="mensaje" style="display: none;"></div>

                <button type="submit">Guardar preferencias</button>

            </form>
        </section>
    </main>
    <script src="../js/validacion_preferencias.js"></script>
</body>
</html>