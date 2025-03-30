<?php  
require_once '../includes/session_start.php';
require_once '../includes/db_connect.php';

if(!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}
$usuario_id = $_SESSION['usuario_id'];

//Creación de la varible que guardará el nombre del usuario.
$usuario_nombre = null;

//Recuperar los datos del formulario desde la sesión, si existen.
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [
    'titulo' => '',
    'tema' => '',
    'palabra_guia' => '',
    'pasos' => '',
    'texto_completo' => '',
];

//Recuperar el mensaje de error, si existe.
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null;

// Limpiar el mensaje de error después de mostrarlo.
unset($_SESSION['error_message']);
?>
<?php if ($error_message): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>
<?php
//Conexión con la BBDD para obtener el nombre del usuario.
$conexion = connectDB();
$query ="SELECT nombre FROM usuarios WHERE id = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    $usuario_nombre = $usuario['nombre'];
} else {
    $usuario_nombre = "Usuario";
}

$stmt->close();
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del usuario</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header><h1>Perfil del Usuario</h1>
        <nav>
            <!--ul>(li>a)*2-->
            <ul>
                <li><a href="#">Cuentos</a></li>
                <li><a href="../index.php">Inicio</a></li>
                <li><a href="/../views/estadisticas.php">Estadísiticas</a></li>
                <li><a href="../php/logout.php">Cerrar sesión</a></li>
            </ul>
        </nav>
        <?php if($usuario_nombre):?>
            <div id="usuario-conectado">
                <p>Conectado como: <strong><?php echo $usuario_nombre; ?></strong></p>
            </div>
        <?php endif; ?>
    </header>
    
    <main>
        <h2>Bienvenido <?php echo htmlspecialchars($usuario_nombre); ?></h2>
            <div class="acciones">
                <button onclick="location.href='../views/formulario_preferencias.php'">Modificar preferencias</button>
                <button type="button" onclick="toggleCuentoForm()">Empezar un cuento</button>
                <button onclick="location.href='../views/coleccion_cuentos.php'">Colección de cuentos</button>
            </div>

            <div id="empezar-cuento" style="display: none;">
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
    </main>
    <script src="../js/comenzar_cuento.js"></script>
<script>
        document.getElementById('form-cuento').addEventListener('submit', function (event) {
            event.preventDefault(); // Detener el envío temporalmente
            const formData = new FormData(this);
            for (const [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
            this.submit(); // Enviar el formulario después de depurar
        });
    </script>
    
    <footer>
        <p>&copy; 2025 Proyecto Cuentacuentos. Todos los derechos reservados.</p>
</body>
</html>