<?php
require_once __DIR__ . '/../../includes/auth.php';

// Protegim la pàgina — només admins
protegirAdmin();

?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Admin Page</title>
    <link rel="stylesheet" href="../css/estils.css">
</head>
<body>
    <header>
        <h1>Pàgina d'Admin</h1>
        <nav>
            <?php if (estaAutenticat()): ?>

                <!-- Menú per a usuaris autenticats -->
                <span>Hola, <?= htmlspecialchars($_SESSION['nom']) ?></span>
                <?php if (esAdmin()): ?>
                    <a href="index.php">Panel Admin</a>
                <?php endif; ?>
                <a href="../logout.php">Tancar sessió</a>
            <?php else: ?>

                <!-- Menú per a visitants -->
                <a href="../login.php">Iniciar sessió</a>
                <a href="../registre.php">Registrar-se</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
