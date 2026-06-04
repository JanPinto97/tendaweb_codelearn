<?php
// ========================================================
// producte.php
// Pàgina de detall d'un producte
// ========================================================

require_once 'includes/db.php';
require_once 'includes/auth.php';

// --------------------------------------------------------
// Comprovem que s'ha passat un ID vàlid per la URL
// --------------------------------------------------------
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

// --------------------------------------------------------
// Obtenim el producte amb el nom de la seva categoria
// --------------------------------------------------------
$stmt = $pdo->prepare("
    SELECT p.*, c.nom AS categoria_nom
    FROM productes p
    LEFT JOIN categories c ON p.categoria_id = c.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$producte = $stmt->fetch();

// Si no existeix el producte, tornem a l'inici
if (!$producte) {
    header('Location: index.php');
    exit;
}

$base = './';
require_once 'includes/header.php';
?>

<!-- Navegació enrere -->
<nav class="engruna">
    <a href="index.php">← Tornar a la botiga</a>
</nav>

<!-- Detall del producte -->
<section class="producte-detall">

    <!-- Imatge -->
    <div class="producte-imatge">
        <?php if ($producte['imatge']): ?>
            <img src="uploads/<?= htmlspecialchars($producte['imatge']) ?>"
                 alt="<?= htmlspecialchars($producte['nom']) ?>">
        <?php else: ?>
            <div class="imatge-buida">Sense imatge</div>
        <?php endif; ?>
    </div>

    <!-- Informació -->
    <div class="producte-info">
        <span class="categoria">
            <?= htmlspecialchars($producte['categoria_nom'] ?? 'Sense categoria') ?>
        </span>

        <h1><?= htmlspecialchars($producte['nom']) ?></h1>

        <p class="preu"><?= number_format($producte['preu'], 2) ?> €</p>

        <p class="descripcio">
            <?= htmlspecialchars($producte['descripcio'] ?? 'Sense descripció') ?>
        </p>

        <!-- Estat de l'estoc -->
        <?php if ($producte['estoc'] > 0): ?>
            <span class="estoc disponible">
                Disponible — <?= $producte['estoc'] ?> unitats
            </span>
        <?php else: ?>
            <span class="estoc esgotat">Esgotat</span>
        <?php endif; ?>

        <!-- Botó d'afegir al carro (només si hi ha estoc i no és admin) -->
        <?php if ($producte['estoc'] > 0 && !esAdmin()): ?>
            <a href="afegir_carro.php?id=<?= $producte['id'] ?>" class="boto boto-carro">
                Afegir al carro
            </a>
        <?php endif; ?>

        <a href="index.php" class="boto boto-blanc">← Tornar a la botiga</a>
    </div>

</section>

<?php
require_once 'includes/footer.php';
?>