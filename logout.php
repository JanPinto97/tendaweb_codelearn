<?php
// ========================================================
// logout.php
// Tanca la sessió i redirigeix al login
// ========================================================

require_once 'includes/auth.php';

// Destruïm totes les dades de la sessió
$_SESSION = [];

// Destruïm la sessió del servidor
session_destroy();

// Redirigim a la pàgina de login
header('Location: login.php');
exit;