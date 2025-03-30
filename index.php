<?php
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/session_start.php';

$usuario_nombre = null;

//Verificar si el usuario está conectado.
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $conexion = connectDB();

    // Obtener el nombre del usuario.
    $query ="SELECT nombre FROM usuarios WHERE id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('i', $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        $usuario_nombre = htmlspecialchars($usuario['nombre']);
    }

    $stmt->close();
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyecto Cuenta Cuentos.</title>
    <link rel="stylesheet" href="css/styles.css"> 
</head>
<body>
    <header>
        <h1>Bienvenido al Proyecto Cuenta Cuentos.</h1>
        <nav>
            <ul>
                <?php if(!isset($usuario_nombre)):?>
                <li><a href="views/registro.html">Regístrate</a></li>
                <li><a href="views/login.php">Iniciar sesión</a></li>
                <?php endif ?>
                <?php if($usuario_nombre):?>
                <li><a href="php/logout.php">Cerrar sesión</a></li>
                <li><a href="views/perfil.php">Perfil</a></li>
                <?php endif ?>


            </ul>
        </nav>
        <?php if($usuario_nombre):?>
            <div id="ususario-conectado">
                <p>Conectado como: <strong><?php echo $usuario_nombre; ?></strong></p>
            </div>
        <?php endif ?>
    </header>
    
    <main>
        <section id="welcome-section">
            <h2>¡Crea cuentos junto con más cuentacuentos!</h2>
            <p>Únete a nosotros, llenemos el mundo de cuentos ¡donde cada uno aporta su fragmento único!</p>
            <button id="create-story-button">Crear un nuevo cuento</button>
        </section>

        <section id="stories-list">
            <h2>Cuentos recientes</h2>
            <ul>
                <?php
                require_once __DIR__ . '/includes/db_connect.php';
                $conexion = connectDB();

                //Consulta de los títulos de los cuentos.
                $query = "SELECT id, titulo FROM cuentos ORDER BY fecha_creacion DESC LIMIT 10";
                $result = $conexion->query($query);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<li><a href="views/consultar_cuento.php?id=' . $row['id'] . '">' . htmlspecialchars($row['titulo']) . '</a></li>';
                    }
                } else {
                    echo '<li>Se el primero en crear un cuento</li>';
                }

                $conexion->close();                                           
                ?>
            </ul>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Proyecto Cuentacuentos</p>
    </footer>
</body>
</html>