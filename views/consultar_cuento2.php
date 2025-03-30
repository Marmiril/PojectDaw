<?php
require_once '../includes/db_connect.php';
require_once '../includes/session_start.php';

// Verificar si el usuario está autenticado.
$usuario_id = $_SESSION['usuario_id'] ?? null;
$conexion = connectDB();

// Obtener el nombre del usuario
$usuario_nombre = null;
$query = "SELECT nombre FROM usuarios WHERE id = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    $usuario_nombre = $usuario['nombre'];
}
$stmt->close();

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

    $stmt->close();
}
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Cuento</title>
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
    <div id="contenido-cuento">
        <div id="marco-cuento">
            <h2 id="titulo-cuento">
            <?php echo $cuentoSeleccionado ? htmlspecialchars($cuentoSeleccionado['titulo']) : 'Selecciona un título'; ?>
            </h2>
            <p id="texto-cuento">
                <?php echo $cuentoSeleccionado ? htmlspecialchars($cuentoSeleccionado['texto_completo']) : 'Texto no disponible'; ?>
            </p>
        </div>
    </div>
        <button id="continuar-cuento" style="display: none;" onclick="mostrarFormulario()">Continuar el cuento</button>
        <div id="formulario-continuar" style="display: none;">
            <form action="../php/continuar_cuento.php" method="POST">
                <input type="hidden" name="cuento_id" id="cuento-id">
                <textarea name="fragmento" id="fragmento" placeholder="Escribe aquí tu fragmento"></textarea>
                <p id="contador-palabras">0 palabras</p>
                <button type="submit" id="guardar-fragmento" disabled>Guardar</button>
            </form>
        </div>
        <div id="formulario-login" style="display: none;">
            <h3>Inicia sesión para continuar el cuento</h3>
            <form action="../php/login.php" method="POST">
                <label for="email">Correo electrónico:</label>
                <input type="email" name="email" id="email" required>
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required>
                <button type="submit">Iniciar sesión</button>
            </form>
        </div>
    </div>
    <div id="mensaje-no-continuar" style="display: none; color: red; margin-top: 10 px;"></div>
    <script>
        function cargarCuento(cuentoId) {
            // Realizar solicitud AJAX para cargar el cuento
            fetch(`../php/consultar_cuento_logic2.php?id=${cuentoId}`)
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    // Actualizar el contenido del marco
                    document.getElementById('titulo-cuento').textContent = data.titulo;
                    document.getElementById('texto-cuento').textContent = data.texto_completo;
                    document.getElementById('cuento-id').value = cuentoId;

                    // Mostrar el botón para continuar si es posible
                    const continuarButton = document.getElementById('continuar-cuento');
                    const formularioLogin = document.getElementById('formulario-login');
                    const formularioContinuar = document.getElementById('formulario-continuar');
                    const mensajeNoContinuar = document.getElementById('mensaje-no-continuar');

                    if (data.puede_continuar) {
                        continuarButton.style.display = 'block';
                        formularioLogin.style.display = 'none';
                        formularioContinuar.style.display = 'none';
                        mensajeNoContinuar.style.display = 'none';
                    } else if (!data.usuario_logueado) {
                        continuarButton.style.display = 'none';
                        formularioLogin.style.display = 'block';
                        formularioContinuar.style.display = 'none';
                        mensajeNoContinuar.style.display = 'none';
                    } else {
                        continuarButton.style.display = 'none';
                        formularioLogin.style.display = 'none';
                        formularioContinuar.style.display = 'none';
                        mensajeNoContinuar.style.display = 'block';
                        mensajeNoContinuar.textContent = 'No puedes continuar con este cuento porque ya has colaborado o lo has iniciado.';
                    }
                })
                .catch(error => console.error('Error al cargar el cuento:', error));
        }

        function mostrarFormulario() {
            document.getElementById('formulario-continuar').style.display = 'block';
        }

        // Contador de palabras
        const textarea = document.getElementById('fragmento');
        const contador = document.getElementById('contador-palabras');
        const botonGuardar = document.getElementById('guardar-fragmento');

        if (textarea) {
            textarea.addEventListener('input', function () {
                const palabras = textarea.value.trim().split(/\s+/).length;
                contador.textContent = `${palabras} palabras`;

                botonGuardar.disabled = palabras < 200 || palabras > 600;
            });
        }
    </script>
</body>
</html>