<?php
// ========================================================
// finalitzar_compra.php
// Compra simulada — resta l'estoc dels productes del carro
// i buida el carro
// ========================================================

require_once 'includes/db.php';
require_once 'includes/auth.php';

// Només els clients registrats poden comprar
protegirClient('login.php');
if (esAdmin()) {
    header('Location: index.php');
    exit;
}

// Si el carro és buit, no hi ha res a comprar
if (empty($_SESSION['carro'])) {
    header('Location: carro.php');
    exit;
}

// --------------------------------------------------------
// Recorrem el carro: restem l'estoc i sumem el total gastat
// --------------------------------------------------------
$total = 0;

foreach ($_SESSION['carro'] as $id => $quantitat) {
    // Obtenim el preu del producte
    $stmt = $pdo->prepare("SELECT preu FROM productes WHERE id = ?");
    $stmt->execute([$id]);
    $producte = $stmt->fetch();

    if ($producte) {
        $total += $producte['preu'] * $quantitat;

        // Restem l'estoc comprat
        $stmt = $pdo->prepare("UPDATE productes SET estoc = estoc - ? WHERE id = ?");
        $stmt->execute([$quantitat, $id]);
    }
}

// Sumem el total d'aquesta compra als diners gastats per l'usuari
$stmt = $pdo->prepare("UPDATE usuaris SET gastat = gastat + ? WHERE id = ?");
$stmt->execute([$total, $_SESSION['usuari_id']]);

// Buidem el carro
$_SESSION['carro'] = [];

// Tornem a la botiga amb un avís de compra realitzada
header('Location: index.php?compra=ok');
exit;
