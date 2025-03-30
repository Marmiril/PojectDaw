<?php
require_once '../includes/session_start.php';
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar que el usuario está autenticado
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: ../views/login.php");
        exit();
    }

    // Obtención de los datos del formulario
    $titulo = trim($_POST['titulo']);
    $tema = trim($_POST['tema']);
    $palabra_guia = trim($_POST['palabra_guia']);
    $pasos = intval($_POST['pasos']);
    $texto_completo = trim($_POST['texto_cuento']);
    $creador_id = $_SESSION['usuario_id'];

    // Guardar los datos en la sesión para preservarlos
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

    // Calcular los pasos restantes
    $pasos_restantes = $pasos - 1;

    // Conexión a la base de datos
    $conexion = connectDB();

    // Inserción del cuento en la tabla 'cuentos'
    $query = "INSERT INTO cuentos (titulo, tema, palabra_guia, pasos, pasos_restantes, texto_completo, estado, creador_id) 
              VALUES (?, ?, ?, ?, ?, ?, 'abierto', ?)";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('sssissi', $titulo, $tema, $palabra_guia, $pasos, $pasos_restantes, $texto_completo, $creador_id);

    if ($stmt->execute()) {
        // Obtener el ID del cuento recién creado
        $cuento_id = $conexion->insert_id;

        // Insertar la primera colaboración en la tabla 'colaboraciones'
        $query = "INSERT INTO colaboraciones (cuento_id, usuario_id, fragmento) VALUES (?, ?, ?)";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param('iis', $cuento_id, $creador_id, $texto_completo);

        if ($stmt->execute()) {
            // Limpiar los datos de la sesión después de guardar con éxito
            unset($_SESSION['form-data']);
            unset($_SESSION['error_message']);
            header("Location: ../views/perfil.php?mensaje=Cuento%20guardado%20con%20éxito");
            exit();
        } else {
            // Mostrar error si no se pudo guardar la colaboración
            $_SESSION['error_message'] = 'Error al guardar la colaboración. Inténtalo de nuevo.';
            header("Location: ../views/perfil.php");
            exit();
        }
    } else {
        // Mostrar error si no se pudo guardar el cuento
        $_SESSION['error_message'] = 'Error al guardar el cuento. Inténtalo de nuevo.';
        header("Location: ../views/perfil.php");
        exit();
    }

    $stmt->close();
    $conexion->close();
} else {
    // Si no es una solicitud POST, redirigir al perfil
    header("Location: ../views/perfil.php");
    exit();
}
?>
