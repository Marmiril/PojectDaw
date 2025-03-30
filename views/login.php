<?php
session_start();//Iniciar la sesión para acceder a los mensajes.
//Capturar el error en caso de que exista.
$mensaje_error = '';
if (isset($_SESSION['mensaje-error'])) {
    $mensaje_error = $_SESSION['mensaje-error'];
    unset($_SESSION['mensaje-error']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Proyecto CuentaCuentos</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    
    <header><h1>Iniciar Sesión</h1>
        <nav>
            <ul>
                <li><a href="../index.php">Inicio</a></li>
                <li><a href="registro.html">Registro</a></li>
            </ul>
        </nav>
        </header>
    <main>
        <section id="login">
            <h2>Accede a tu cuenta</h2>
            <form action="../php/login_usuario.php" method="POST" id="form-login">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>


                <button type="submit">Iniciar Sesión</button>
            </form>
                <!--Mostrar mensaje de error si existe-->
                <?php if(!empty($mensaje_error)): ?>
                    <div class="mensaje-error">
                        <?php echo htmlspecialchars($mensaje_error); ?>
                    </div>
                <?php endif; ?>
                
            <p><a href="#">¿Olvidaste la contraseña?</a></p>
            
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Proyecto Cuentacuentos. Todos los derechos reservados.</p>
    </footer>

    <script src="../js/validacion_login.js"></script>   
</body>
</html>