<?php
// ========================================================
// index.php
// Pàgina principal de la botiga — llistat de productes
// ========================================================

require_once 'includes/db.php';
require_once 'includes/auth.php';

// --------------------------------------------------------
// Obtenim totes les categories per al filtre
// --------------------------------------------------------
$stmt = $pdo->prepare("SELECT * FROM categories ORDER BY nom ASC");
$stmt->execute();
$categories = $stmt->fetchAll();

// --------------------------------------------------------
// Filtrem per categoria si s'ha seleccionat una
// --------------------------------------------------------
$categoria_sel = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;

if ($categoria_sel > 0) {
    // Mostrem només els productes de la categoria seleccionada
    $stmt = $pdo->prepare("
        SELECT p.*, c.nom AS categoria_nom
        FROM productes p
        LEFT JOIN categories c ON p.categoria_id = c.id
        WHERE p.categoria_id = ?
        ORDER BY p.nom ASC
    ");
    $stmt->execute([$categoria_sel]);
} else {
    // Mostrem tots els productes
    $stmt = $pdo->prepare("
        SELECT p.*, c.nom AS categoria_nom
        FROM productes p
        LEFT JOIN categories c ON p.categoria_id = c.id
        ORDER BY p.nom ASC
    ");
    $stmt->execute();
}

$productes = $stmt->fetchAll();

// Carreguem la capçalera comuna
$base  = './';
$titol = 'Tenda Codelearn';
require_once 'includes/header.php';
?>

<!-- Filtre per categories -->
<section class="filtres">
    <a href="index.php" class="<?= $categoria_sel === 0 ? 'actiu' : '' ?>">
        Tots
    </a>
    <?php foreach ($categories as $categoria): ?>
        <a href="index.php?categoria=<?= $categoria['id'] ?>"
           class="<?= $categoria_sel === $categoria['id'] ? 'actiu' : '' ?>">
            <?= htmlspecialchars($categoria['nom']) ?>
        </a>
    <?php endforeach; ?>
</section>

<!-- Llistat de productes -->
<section class="productes-grid">
    <?php if (empty($productes)): ?>
        <p class="missatge-buit">No hi ha productes disponibles.</p>

    <?php else: ?>
        <?php foreach ($productes as $producte): ?>
            <article class="producte-card">

                <!-- Imatge del producte (clicable) -->
                <a href="producte.php?id=<?= $producte['id'] ?>" class="producte-imatge">
                    <?php if ($producte['imatge']): ?>
                        <img src="uploads/<?= htmlspecialchars($producte['imatge']) ?>"
                             alt="<?= htmlspecialchars($producte['nom']) ?>">
                    <?php else: ?>
                        <div class="imatge-placeholder">Sense imatge</div>
                    <?php endif; ?>
                </a>

                <!-- Informació del producte -->
                <div class="producte-info">
                    <span class="categoria"><?= htmlspecialchars($producte['categoria_nom'] ?? 'Sense categoria') ?></span>
                    <h2><?= htmlspecialchars($producte['nom']) ?></h2>
                    <p class="preu"><?= number_format($producte['preu'], 2) ?> €</p>

                    <!-- Estat de l'estoc -->
                    <?php if ($producte['estoc'] > 0): ?>
                        <span class="estoc disponible">Disponible (<?= $producte['estoc'] ?>)</span>
                    <?php else: ?>
                        <span class="estoc esgotat">Esgotat</span>
                    <?php endif; ?>

                    <a href="producte.php?id=<?= $producte['id'] ?>" class="btn">
                        Veure producte
                    </a>
                </div>

            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php
require_once 'includes/footer.php';
?>