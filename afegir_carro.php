<?php
// ========================================================
// afegir_carro.php
// Afegeix un producte al carro (guardat a la sessió)
// ========================================================

require_once 'includes/db.php';
require_once 'includes/auth.php';

// --------------------------------------------------------
// Només els clients registrats poden comprar
// Si no ha iniciat sessió, el portem al login
// Si és admin, no pot comprar i el tornem a la botiga
// --------------------------------------------------------
protegirClient('login.php');
if (esAdmin()) {
    header('Location: index.php');
    exit;
}

// --------------------------------------------------------
// Comprovem que s'ha passat un ID vàlid
// --------------------------------------------------------
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

// --------------------------------------------------------
// Comprovem que el producte existeix i té estoc
// --------------------------------------------------------
$stmt = $pdo->prepare("SELECT id, estoc FROM productes WHERE id = ?");
$stmt->execute([$id]);
$producte = $stmt->fetch();

if ($producte && $producte['estoc'] > 0) {
    // Inicialitzem el carro si encara no existeix
    if (!isset($_SESSION['carro'])) {
        $_SESSION['carro'] = [];
    }

    // Sumem una unitat sense superar l'estoc disponible
    $quantitat_actual = $_SESSION['carro'][$id] ?? 0;
    if ($quantitat_actual < $producte['estoc']) {
        $_SESSION['carro'][$id] = $quantitat_actual + 1;
    }
}

// --------------------------------------------------------
// Tornem a la pàgina d'on venia (o a l'inici per defecte)
// --------------------------------------------------------
$desti = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header("Location: $desti");
exit;
