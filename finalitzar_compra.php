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
// Creem la comanda (de moment amb total 0, el calcularem després)
// --------------------------------------------------------
$stmt = $pdo->prepare("INSERT INTO comandes (usuari_id, total) VALUES (?, 0)");
$stmt->execute([$_SESSION['usuari_id']]);
$comanda_id = $pdo->lastInsertId();

// --------------------------------------------------------
// Recorrem el carro: guardem cada producte a la comanda,
// restem l'estoc i anem sumant el total
// --------------------------------------------------------
$total = 0;

foreach ($_SESSION['carro'] as $id => $quantitat) {
    // Obtenim el nom i el preu del producte
    $stmt = $pdo->prepare("SELECT nom, preu FROM productes WHERE id = ?");
    $stmt->execute([$id]);
    $producte = $stmt->fetch();

    if ($producte) {
        $total += $producte['preu'] * $quantitat;

        // Guardem el producte dins la comanda
        $stmt = $pdo->prepare("INSERT INTO comanda_productes (comanda_id, nom, preu, quantitat) VALUES (?, ?, ?, ?)");
        $stmt->execute([$comanda_id, $producte['nom'], $producte['preu'], $quantitat]);

        // Restem l'estoc comprat
        $stmt = $pdo->prepare("UPDATE productes SET estoc = estoc - ? WHERE id = ?");
        $stmt->execute([$quantitat, $id]);
    }
}

// Guardem el total final a la comanda
$stmt = $pdo->prepare("UPDATE comandes SET total = ? WHERE id = ?");
$stmt->execute([$total, $comanda_id]);

// Sumem el total d'aquesta compra als diners gastats per l'usuari
$stmt = $pdo->prepare("UPDATE usuaris SET gastat = gastat + ? WHERE id = ?");
$stmt->execute([$total, $_SESSION['usuari_id']]);

// Buidem el carro
$_SESSION['carro'] = [];

// Tornem a la botiga amb un avís de compra realitzada
header('Location: index.php?compra=ok');
exit;
