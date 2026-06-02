<?php
require_once __DIR__ . '/auth.php';

// Valors per defecte (per si la pàgina no els defineix)
$base  = $base  ?? './';
$titol = $titol ?? 'Tenda Codelearn';
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
                <?php if (esAdmin()): ?>
                    <a href="<?= $base ?>admin/index.php">Panel Admin</a>
                <?php endif; ?>
                <a href="<?= $base ?>logout.php">Tancar sessió</a>
            <?php else: ?>

                <!-- Menú per a visitants -->
                <a href="<?= $base ?>login.php">Iniciar sessió</a>
                <a href="<?= $base ?>registre.php">Registrar-se</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
