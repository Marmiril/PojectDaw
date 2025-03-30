<?php
require_once '../includes/session_start.php';
require_once '../includes/db_connect.php';

/**
 * Valida los datos del formulario.
 *
 * @param array $data Datos del formulario.
 * @return array Resultado de la validación (éxito o error).
 */
function validarDatosFormulario($data) {
    $errores = [];

    if (empty($data['titulo']) || empty($data['tema']) || empty($data['palabra_guia']) || empty($data['pasos']) || empty($data['texto_completo'])) {
        $errores[] = 'Todos los campos son obligatorios.';
    }

    $palabras = str_word_count($data['texto_completo'], 0);
    if ($palabras < 200 || $palabras > 600) {
        $errores[] = 'El texto debe comprender entre 200 y 600 palabras.';
    }

    return $errores;
}

/**
 * Inserta un cuento en la base de datos.
 *
 * @param mysqli $conexion Conexión a la base de datos.
 * @param array $data Datos del cuento.
 * @param int $creador_id ID del creador.
 * @return int|false ID del cuento recién creado o false en caso de error.
 */
function insertarCuento($conexion, $data, $creador_id) {
    $pasos_restantes = $data['pasos'] - 1;

    $query = "INSERT INTO cuentos (titulo, tema, palabra_guia, pasos, pasos_restantes, texto_completo, estado, creador_id) 
              VALUES (?, ?, ?, ?, ?, ?, 'abierto', ?)";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param(
        'sssissi',
        $data['titulo'],
        $data['tema'],
        $data['palabra_guia'],
        $data['pasos'],
        $pasos_restantes,
        $data['texto_completo'],
        $creador_id
    );

    if ($stmt->execute()) {
        return $conexion->insert_id; // Devuelve el ID del cuento recién creado
    }

    return false; // Error al insertar
}

/**
 * Inserta una colaboración en la base de datos.
 *
 * @param mysqli $conexion Conexión a la base de datos.
 * @param int $cuento_id ID del cuento.
 * @param int $usuario_id ID del usuario.
 * @param string $fragmento Texto del fragmento.
 * @return bool True si se insertó correctamente, false en caso contrario.
 */
function insertarColaboracion($conexion, $cuento_id, $usuario_id, $fragmento) {
    $query = "INSERT INTO colaboraciones (cuento_id, usuario_id, fragmento) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('iis', $cuento_id, $usuario_id, $fragmento);

    return $stmt->execute();
}

/**
 * Maneja el guardado del cuento y la colaboración.
 *
 * @param array $data Datos del formulario.
 * @param int $creador_id ID del creador.
 * @return array Resultado del proceso (éxito o error).
 */
function guardarCuento($data, $creador_id) {
    $conexion = connectDB();

    // Validar datos
    $errores = validarDatosFormulario($data);
    if (!empty($errores)) {
        return ['error' => implode(' ', $errores)];
    }

    // Insertar cuento
    $cuento_id = insertarCuento($conexion, $data, $creador_id);
    if (!$cuento_id) {
        return ['error' => 'Error al guardar el cuento. Inténtalo de nuevo.'];
    }

    // Insertar colaboración
    if (!insertarColaboracion($conexion, $cuento_id, $creador_id, $data['texto_completo'])) {
        return ['error' => 'Error al guardar la colaboración. Inténtalo de nuevo.'];
    }

    return ['success' => 'Cuento guardado con éxito.'];
}
?>
<?php
require_once '../includes/session_start.php';
require_once '../includes/db_connect.php';

/**
 * Verifica si el usuario está autenticado.
 * Redirige al login si no lo está.
 */
function verificarAutenticacion() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: ../views/login.php");
        exit();
    }
}

/**
 * Obtiene y valida los datos del formulario.
 *
 * @return array Datos del formulario o redirige con un mensaje de error.
 */
function obtenerDatosFormulario() {
    $titulo = trim($_POST['titulo']);
    $tema = trim($_POST['tema']);
    $palabra_guia = trim($_POST['palabra_guia']);
    $pasos = intval($_POST['pasos']);
    $texto_completo = trim($_POST['texto_cuento']);

    // Guardar los datos en la sesión para preservarlos en caso de error
    $_SESSION['form-data'] = [
        'titulo' => $titulo,
        'tema' => $tema,
        'palabra_guia' => $palabra_guia,
        'pasos' => $pasos,
        'texto_completo' => $texto_completo,
    ];

    // Validar que los campos no estén vacíos
    if (empty($titulo) || empty($tema) || empty($palabra_guia) || empty($pasos) || empty($texto_completo)) {
        $_SESSION['error_message'] = 'Todos los campos son obligatorios.';
        header("Location: ../views/perfil.php");
        exit();
    }

    // Validar el rango de palabras
    $palabras = str_word_count($texto_completo, 0);
    if ($palabras < 200 || $palabras > 600) {
        $_SESSION['error_message'] = 'El texto debe comprender entre 200 y 600 palabras.';
        header("Location: ../views/perfil.php");
        exit();
    }

    return [
        'titulo' => $titulo,
        'tema' => $tema,
        'palabra_guia' => $palabra_guia,
        'pasos' => $pasos,
        'texto_completo' => $texto_completo,
    ];
}

/**
 * Guarda un cuento en la base de datos.
 *
 * @param mysqli $conexion Conexión a la base de datos.
 * @param array $datos Datos del cuento.
 * @param int $creador_id ID del creador.
 * @return int|false ID del cuento recién creado o false en caso de error.
 */
function guardarCuento($conexion, $datos, $creador_id) {
    $pasos_restantes = $datos['pasos'] - 1;

    $query = "INSERT INTO cuentos (titulo, tema, palabra_guia, pasos, pasos_restantes, texto_completo, estado, creador_id) 
              VALUES (?, ?, ?, ?, ?, ?, 'abierto', ?)";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param(
        'sssissi',
        $datos['titulo'],
        $datos['tema'],
        $datos['palabra_guia'],
        $datos['pasos'],
        $pasos_restantes,
        $datos['texto_completo'],
        $creador_id
    );

    if ($stmt->execute()) {
        return $conexion->insert_id; // Devuelve el ID del cuento recién creado
    }

    return false; // Error al guardar el cuento
}

/**
 * Guarda la primera colaboración en la base de datos.
 *
 * @param mysqli $conexion Conexión a la base de datos.
 * @param int $cuento_id ID del cuento.
 * @param int $usuario_id ID del usuario.
 * @param string $fragmento Texto del fragmento.
 * @return bool True si se insertó correctamente, false en caso contrario.
 */
function guardarColaboracion($conexion, $cuento_id, $usuario_id, $fragmento) {
    $query = "INSERT INTO colaboraciones (cuento_id, usuario_id, fragmento) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('iis', $cuento_id, $usuario_id, $fragmento);

    return $stmt->execute();
}
?>