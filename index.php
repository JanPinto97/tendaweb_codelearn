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

$sql = "SELECT p.*, c.nom AS categoria_nom
        FROM productes p
        LEFT JOIN categories c ON p.categoria_id = c.id";
$params = [];

if ($categoria_sel > 0) {
    $sql .= " WHERE p.categoria_id = ?";
    $params[] = $categoria_sel;
}

$sql .= " ORDER BY p.nom ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$productes = $stmt->fetchAll();

// Carreguem la capçalera comuna
$base = './';
require_once 'includes/header.php';
?>

<!-- Avís de compra realitzada (compra simulada) -->
<?php if (isset($_GET['compra']) && $_GET['compra'] === 'ok'): ?>
    <p class="avis-compra">Compra realitzada correctament. Gràcies!</p>
<?php endif; ?>

<!-- Filtre per categories -->
<section class="filtres">
    <details class="desplegable-categories">
        <summary>Categories ▾</summary>
        <div class="llista-categories">
            <a href="index.php" class="<?= $categoria_sel === 0 ? 'actiu' : '' ?>">
                Tots
            </a>
            <?php foreach ($categories as $categoria): ?>
                <a href="index.php?categoria=<?= $categoria['id'] ?>"
                   class="<?= $categoria_sel === $categoria['id'] ? 'actiu' : '' ?>">
                    <?= htmlspecialchars($categoria['nom']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </details>

    <!-- Carro: enllaç amb el nombre d'articles, a la punta dreta -->
    <!-- Els admins no compren, així que no veuen el carro -->
    <?php if (!esAdmin()): ?>
        <a href="carro.php" class="carro-enllac">
            Carro (<?= array_sum($_SESSION['carro'] ?? []) ?>)
        </a>
    <?php endif; ?>
</section>

<!-- Llistat de productes -->
<section class="productes-graella">
    <?php if (empty($productes)): ?>
        <p class="missatge-buit">No hi ha productes disponibles.</p>

    <?php else: ?>
        <?php foreach ($productes as $producte): ?>
            <article class="producte-targeta">

                <!-- Imatge del producte (clicable) -->
                <a href="producte.php?id=<?= $producte['id'] ?>" class="producte-imatge">
                    <?php if ($producte['imatge']): ?>
                        <img src="uploads/<?= htmlspecialchars($producte['imatge']) ?>"
                             alt="<?= htmlspecialchars($producte['nom']) ?>">
                    <?php else: ?>
                        <div class="imatge-buida">Sense imatge</div>
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

                    <a href="producte.php?id=<?= $producte['id'] ?>" class="boto boto-blanc">
                        Veure producte
                    </a>

                    <!-- Botó d'afegir al carro (només si hi ha estoc i no és admin) -->
                    <?php if ($producte['estoc'] > 0 && !esAdmin()): ?>
                        <a href="afegir_carro.php?id=<?= $producte['id'] ?>" class="boto boto-carro">
                            Afegir al carro
                        </a>
                    <?php endif; ?>
                </div>

            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php
require_once 'includes/footer.php';
?>
