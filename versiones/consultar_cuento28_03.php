<?php
require_once '../includes/db_connect.php';
require_once '../includes/session_start.php';

$conexion = connectDB();


// Consultar los cuentos disponibles
$query = "SELECT id, titulo FROM cuentos";
$stmt = $conexion->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$cuentos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cuentos[] = $row;
    }
}
$stmt->close();


///////////////////////////////////////////////////////////////
// Verificar si se ha proporcionado un ID en la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Error: ID no válido');
}

$cuento_id = intval($_GET['id']);
$conexion = connectDB();

// Consultar los detalles del cuento
$query = "SELECT c.titulo, c.tema, c.palabra_guia, c.pasos_restantes, c.texto_completo, c.fecha_creacion, c.creador_id, u.nombre AS creador
          FROM cuentos c
          JOIN usuarios u ON c.creador_id = u.id
          WHERE c.id = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param('i', $cuento_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $cuento = $result->fetch_assoc();
    $palabra_guia = $cuento['palabra_guia'] ?? 'No disponible';
    $tema= $cuento['tema'] ?? 'No disponible';
    $fecha_creacion = $cuento['fecha_creacion'] ?? 'No disponible';
    $creador = $cuento['creador'] ?? 'No disponible';
    $pasos_restantes = $cuento['pasos_restantes'] ?? 'No disponible';
    $texto_completo = $cuento['texto_completo'] ?? 'No disponible';
} else {
    die("Cuento no encontrado. Verifica que el ID proporcionado sea correcto.");
}


//Verificar si el usuario está en sesión activa.
$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
$usuario_nombre = null;
///////////////////////////////////////////////////////////////////

// Verificar si el usuario ya ha comenzado o colaborado en el cuento.
$puede_continuar = false;
if($usuario_id) {
    $query = "SELECT * FROM colaboraciones WHERE cuento_id = ? AND usuario_id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('ii', $cuento_id, $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $puede_continuar = false;
//        echo '<p>Ya has colaborado en este cuento pero puedes empezar otro</p>';
    } else {
        $puede_continuar = true;
    }
    $stmt->close();

/*
    if($result->num_rows === 0 && $cuento['creador_id'] !== $usuario_id) {
        $puede_continuar = true;
    }    
    $stmt->close();
*/

// Obtener el nombre del usuario
$query = "SELECT nombre FROM usuarios WHERE id = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    $usuario_nombre = $usuario['nombre'];
} else {
    $usuario_nombre = "Usuario";
}
$stmt->close();
}


/////////////////////////////////////////////////////
// Verificar si se haproporcionado un ID en la URL.
$cuentoSeleccionado = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $cuento_id = intval($_GET['id']);

    // Consultar los detalles del cuento seleccionado.
    $query = "SELECT c.titulo, c.texto_completo 
              FROM cuentos c
              WHERE c.id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('i', $cuento_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $cuentoSeleccionado = $result->fetch_assoc();
    } else {
        echo '<p>Error: No se encontró el cuento con el ID proporcionado.</p>';
    }
}
    $stmt->close();
$conexion->close();



?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($cuento['titulo']); ?></title>
    <link rel="stylesheet" href="../css/styles.css">

</head>
<body>
<header>
        <h1>Bienvenido al Proyecto Cuenta Cuentos.</h1>
        <nav>
            <ul>
                <li><a href="../index.php">Inicio</a></li>
                <?php if(!isset($usuario_nombre)):?>
                <li><a href="../views/registro.html">Regístrate</a></li>
                <li><a href="../views/login.php">Iniciar sesión</a></li>
                <?php endif ?>
                <?php if($usuario_nombre):?>
                <li><a href="../php/logout.php">Cerrar sesión</a></li>
                <li><a href="../views/perfil.php">Perfil</a></li>
                <?php endif ?>
            </ul>
        </nav>
        <?php if($usuario_nombre):?>
            <div id="ususario-conectado">
                <p>Conectado como: <strong><?php echo $usuario_nombre; ?></strong></p>
            </div>
        <?php endif ?>
    </header>

    <?php if (isset($_SESSION['success_message'])): ?>
        <p class="success-message"><?php echo htmlspecialchars($_SESSION['success_message']); ?></p>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <p class="error-message"><?php echo htmlspecialchars($_SESSION['error_message']); ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif ?>


        <header>
        <h1><?php echo htmlspecialchars($cuento['titulo']); ?></h1>
    </header>
    <div id="listado-cuentos">
        <h3>Listado de cuentos</h3>
        <ul>
            <?php foreach ($cuentos as $cuento): ?>
                <li onclick="cargarCuento(<?php echo $cuento['id']; ?>)">
                    <?php echo htmlspecialchars($cuento['titulo']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

        <p><strong>Palabra clave:</strong> <?php echo htmlspecialchars($palabra_guia ?? 'No disponible'); ?></p>
        <p><strong>Tema:</strong> <?php echo htmlspecialchars($tema ?? 'No disponible'); ?></p>
        <p><strong>Fecha de creación:</strong> <?php echo htmlspecialchars($fecha_creacion ?? 'No disponible'); ?></p>
        <p><strong>Creador:</strong> <?php echo htmlspecialchars($creador ?? 'No disponible'); ?></p>
        <p><strong>Paso actual:</strong> <?php echo htmlspecialchars($pasos_restantes?? 'No disponible'); ?></p>
        <hr>
        <div id="contenido-cuento">
        <div id="marco-cuento">
            <h2 id="titulo-cuento">
            <?php echo $cuentoSeleccionado ? htmlspecialchars($cuentoSeleccionado['titulo']) : 'Selecciona un título'; ?>
            </h2>
            <p id="texto-cuento">
                <p><?php echo nl2br(htmlspecialchars($texto_completo ?? 'No disponible')); ?></p>
            </p>
        </div>
    <main>
        <!-- Formulario de login/registro -->
        <div id="formulario-login" style="display: none";>
            <h3>Inicia sesión o regístrate para continuar con la historia</h3>
            <form action="../php/login_usuario.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?> " method="POST">
                <label for="email">Correo electrónico:</label>
                <input type="email" id="email" name="email" required>
                <label for="password">Contraseña: </label>
                <input type="password" id="password" name="password" required>
                <button type="submit">Iniciar sesión</button>
            </form>
            <p>¿No tienes cuenta?<a href="../views/registro.html">Regístrate aquí</a></p>
        </div>
<        

        <!--Botón para continuar la historia.-->
        <button id="continuar-cuento" onclick="verificarUsuario()">Continuar el cuento</button>




        <!--Formulario para continuar la historia.-->
        <div id="formulario-continuar" style="display: none;">
            <form action="../php/continuar_cuento.php" method="POST">
                <input type="hidden" name="cuento_id" value="<?php echo $cuento_id ?>">
                <textarea id="fragmento" name="fragmento" rows="10" placeholder="Escribe aquí tu fragmento"></textarea>
                <p id="contador-palabras">0 palabras</p>
                <button type="submit" id="guardar-fragmento" disabled>Guardar fragmento</button>
            </form>
        </div>
    </main>
    <footer>
        <p>&copy; 2025 Proyecto Cuentacuentos</p>
    </footer>
    <script>
        const usuarioLogueado = <?php echo json_encode($usuario_id !== null); ?>;
        const puedeContinuar = <?php echo json_encode($puede_continuar); ?>;
        function verificarUsuario() {          
            if (usuarioLogueado) {
                if (puedeContinuar) {
                    // Mostrar el formulario para continuar la historia
                    mostrarFormulario();
                } else {
                    // Mostrar mensaje de que ya colaboró
                    alert("Ya has colaborado en este cuento.");
                }
            } else {
                //Mostrar el formulario de login/registro
                document.getElementById('formulario-login').style.display  = 'block';
                //alert("Inicia sesión o regístrate para continuar el cuento.");
            }
        }
    
        // Mostrar formulario para continuar la historia.
        function mostrarFormulario() {
            document.getElementById('formulario-continuar').style.display = 'block';
        }

        // Contador de palabras y habilitación del botón.
        const textarea = document.getElementById('fragmento');
        const contador = document.getElementById('contador-palabras');
        const botonGuardar = document.getElementById('guardar-fragmento');

        if (textarea) {
            textarea.addEventListener('input', function() {
                // Divid el texto en palabras usando espacio como separadores
                const palabras = textarea.value.trim().split(/\s+/);

                //Actualiza el contador de palabras
                contador.textContent = `${palabras.length} palabras`;

                //Habilita o deshabilita el botónd de guardado según el número de palabras
                if (palabras.length >= 200 && palabras.length <= 600) {
                    botonGuardar.disabled = false;
                } else {
                    botonGuardar.disabled = true;
                }                
            });
        }

        function cargarCuento(cuentoId) {
            // Redirige a la misma página con el ID del cuento en la URL
            window.location.href = `consultar_cuento.php?id=${cuentoId}`;
        };


    </script>

</body>
</html>