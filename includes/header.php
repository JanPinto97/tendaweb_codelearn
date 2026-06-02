<?php
require_once __DIR__ . '/auth.php';

// El títol depèn de si l'usuari és admin o no
$titol = esAdmin() ? "Panell d'Admin" : 'CodeShop';

// Ruta base per defecte (cada pàgina la pot sobreescriure abans del require)
$base = $base ?? './';
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titol) ?></title>
    <link rel="stylesheet" href="<?= $base ?>css/estils.css">
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($titol) ?></h1>
        <nav>
            <?php if (estaAutenticat()): ?>

                <!-- Menú per a usuaris autenticats -->
                <span>Hola, <?= htmlspecialchars($_SESSION['nom']) ?></span>
                <a href="<?= $base ?>logout.php">Tancar sessió</a>
            <?php else: ?>

                <!-- Menú per a visitants -->
                <a href="<?= $base ?>login.php">Iniciar sessió</a>
                <a href="<?= $base ?>registre.php">Registrar-se</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
