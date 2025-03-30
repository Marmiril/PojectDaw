<?php
require_once '../includes/db_connect.php';
require_once '../includes/session_start.php';

if(!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$conexion = connectDB();

// Método para obtener el nombre del usuario de la base de datos.
$query = "SELECT nombre FROM usuarios WHERE id = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        $usuario_nombre = $usuario['nombre'];
    } else {
        $usuario_nombre = 'Usuario';
    }

//Consultar cuentos iniciados por el usuario.
$comenzadosQuery = "SELECT id, titulo, pasos_restantes, estado FROM cuentos WHERE creador_id = ?";
$comenzadosStmt = $conexion->prepare($comenzadosQuery);
$comenzadosStmt->bind_param('i', $usuario_id);
$comenzadosStmt->execute();
$comenzados = $comenzadosStmt->get_result();

//Consultar cuentos colaborados.
$colaboradosQuery = "SELECT c.id, c.titulo FROM cuentos c INNER JOIN colaboraciones co ON c.id = co.cuento_id
                    WHERE co.usuario_id = ? AND c.creador_id != ?";
$colaboradosStmt = $conexion->prepare($colaboradosQuery);
$colaboradosStmt->bind_param('ii', $usuario_id, $usuario_id);
$colaboradosStmt->execute();
$colaborados = $colaboradosStmt->get_result();

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colección de cuentos</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <h1>Mi colección de cuentos</h1>
        <nav>
            <ul>
                <li><a href="../index.php">Inicio</a></li>
                <li><a href="../views/estadisticas.php">Estadísticas</a></li>
                <li><a href="../views/perfil.php">Perfil</a></li>
                <li><a href="../php/logout.php">Cerrar sesión</a></li>
            </ul>
        </nav>
        <?php if($usuario_nombre):?>
            <div id="usuario-conectado">
                <p>Conectado como: <strong><?php echo $usuario_nombre; ?></strong>
            </div>
        <?php endif; ?>
    </header>
    <main>
        <section>
            <h2>Cuentos iniciados</h2>

            <h3>Pendientes</h3>
            <ul>
                <?php if ($comenzados->num_rows > 0): ?>
                    <?php while ($cuento = $comenzados->fetch_assoc()): ?>
                        <?php if ($cuento['estado'] === 'abierto'): ?>
                            <li><?php echo htmlspecialchars($cuento['titulo']); ?> - Pasos restantes: <?php echo $cuento['pasos_restantes']; ?></li>
                        <?php endif; ?>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No has comenzado ningún cuento.</li>
                    <button type="button" onclick="toggleCuentoForm()">Empezar un cuento</button>            <div id="empezar-cuento" style="display: none;">
                <h3>Empieza el cuento</h3>
                <form id="form-cuento" action="../php/guardar_cuento.php" method="POST">
                    <label for="titulo">Título:</label>
                    <input type="text" id="titulo" name="titulo" required>

                    <label for="tema">Tema:</label>
                    <select id="tema" name="tema" required>
                    <option value="romance">Romance</option>
                    <option value="aventura">Aventura</option>
                    <option value="ciencia-ficcion">Ciencia-ficción</option>
                    <option value="terror">Terror</option>
                    <option value="suspense">Suspense</option>
                    <option value="comedia">Comedia</option>
                    <option value="tema-libre">Tema libre</option>
                </select>

                <label for="palabra_guia">Palabra clave:</label>
                <input type="text" id="palabra_guia" name="palabra_guia" maxlength="50" required></input>

                <label for="pasos">Aportaciones (10-15):</label>
                <input type="number" id="pasos" name="pasos" min="10" max="15" required></input>

                <label for="texto_cuento">Érase una vez...</label>
                <textarea id="texto_cuento" name="texto_cuento" required></textarea> 
                <p id="contador-palabras">0</p>

                <button type="submit">Guardar</button>
                </form>
            </div>
                <?php endif; ?>
            </ul>

            <h3>Concluidos</h3>
            <ul>
                <?php mysqli_data_seek($comenzados, 0); // Reiniciar el puntero del resultado ?>
                <?php if ($comenzados->num_rows > 0): ?>
                    <?php while ($cuento = $comenzados->fetch_assoc()): ?>
                        <?php if ($cuento['estado'] === 'cerrado'): ?>
                            <li><?php echo htmlspecialchars($cuento['titulo']); ?></li>
                        <?php endif; ?>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No has concluido ningún cuento.</li>
                <?php endif; ?>
            </ul>
        </section>
        <section>
            <h2>Cuentos en los que he colaborado</h2>
            <h3>Pendientes</h3>
            <ul>
                <?php if ($colaborados->num_rows > 0): ?>
                    <?php while ($cuento = $colaborados->fetch_assoc()): ?>
                        <?php if ($cuento['estado'] === 'abierto'): ?>
                            <li><?php echo htmlspecialchars($cuento['titulo']); ?></li>
                        <?php endif; ?>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No has colaborado en ningún cuento.</li>
                <?php endif; ?>
            </ul>
            <h3>Concluidos</h3>
            <ul>
                <?php mysqli_data_seek($colaborados, 0); // Reiniciar el puntero del resultado ?>
                <?php if ($colaborados->num_rows > 0): ?>
                    <?php while ($cuento = $colaborados->fetch_assoc()): ?>
                        <?php if ($cuento['estado'] === 'cerrado'): ?>
                            <li><?php echo htmlspecialchars($cuento['titulo']); ?></li>
                        <?php endif; ?>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No has colaborado en ningún cuento concluido.</li>
                <?php endif; ?>
            </ul>
        </section>
    </main>
    <script src="../js/comenzar_cuento.js"></script>
    <footer>
    <p>&copy; 2025 Proyecto Cuentacuentos. Todos los derechos reservados.</p>
</body>
</html>