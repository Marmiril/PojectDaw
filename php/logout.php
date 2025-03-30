<?php
session_start(); // Inicio de sesión.
session_unset(); // Destruir todas la variables de la sesión.
session_destroy(); // Destruir la sesión.

header('Location: ../index.php'); // Redireccionar al index.
exit();
?>