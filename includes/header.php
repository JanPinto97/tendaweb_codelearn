<?php
require_once __DIR__ . '/auth.php';

// El títol depèn de si l'usuari és admin o no
$titol = esAdmin() ? "Panell d'Admin" : 'CodeShop';

// Ruta base per defecte (cada pàgina la pot sobreescriure abans del require)
$base = $base ?? './';

// El títol porta a l'inici (el panell si és admin, la botiga si no)
$inici = esAdmin() ? $base . 'admin/index.php' : $base . 'index.php';
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
        <h1><a href="<?= $inici ?>"><?= htmlspecialchars($titol) ?></a></h1>
        <nav>
            <?php if (estaAutenticat()): ?>

                <!-- Menú per a usuaris autenticats -->
                <span>Hola, <?= htmlspecialchars($_SESSION['nom']) ?></span>
                <?php if (esAdmin()): ?>
                    <a href="<?= $base ?>admin/afegir_usuari.php">Afegir usuari</a>
                <?php else: ?>
                    <a href="<?= $base ?>perfil.php">El meu compte</a>
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
