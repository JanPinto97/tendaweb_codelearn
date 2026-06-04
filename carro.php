<?php
// ========================================================
// carro.php
// Pàgina del carro — resum dels productes i finalitzar compra
// ========================================================

require_once 'includes/db.php';
require_once 'includes/auth.php';

// Només els clients registrats poden veure el carro i comprar
protegirClient('login.php');
if (esAdmin()) {
    header('Location: index.php');
    exit;
}

// --------------------------------------------------------
// Permetem eliminar un producte del carro (?eliminar=ID)
// --------------------------------------------------------
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    unset($_SESSION['carro'][(int)$_GET['eliminar']]);
    header('Location: carro.php');
    exit;
}

$carro = $_SESSION['carro'] ?? [];

// --------------------------------------------------------
// Obtenim les dades de cada producte que hi ha al carro
// --------------------------------------------------------
$productes_carro = [];
$total = 0;

foreach ($carro as $id => $quantitat) {
    $stmt = $pdo->prepare("SELECT * FROM productes WHERE id = ?");
    $stmt->execute([$id]);
    $producte = $stmt->fetch();

    if ($producte) {
        $total += $producte['preu'] * $quantitat;
        $productes_carro[] = $producte;
    }
}

$base = './';
require_once 'includes/header.php';
?>

<!-- Navegació enrere -->
<nav class="engruna">
    <a href="index.php">← Tornar a la botiga</a>
</nav>

<h2>El meu carro</h2>

<?php if (empty($productes_carro)): ?>
    <p class="missatge-buit">El carro és buit. <a href="index.php">Afegeix productes!</a></p>

<?php else: ?>
    <section class="carro-resum">
        <table class="carro-taula">
            <thead>
                <tr>
                    <th>Producte</th>
                    <th>Preu</th>
                    <th>Quantitat</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productes_carro as $producte): ?>
                    <?php
                        $quantitat = $carro[$producte['id']];
                        $subtotal  = $producte['preu'] * $quantitat;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($producte['nom']) ?></td>
                        <td><?= number_format($producte['preu'], 2) ?> €</td>
                        <td><?= $quantitat ?></td>
                        <td><?= number_format($subtotal, 2) ?> €</td>
                        <td>
                            <a href="carro.php?eliminar=<?= $producte['id'] ?>"
                               class="boto boto-eliminar">Treure</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="total-etiqueta">Total</td>
                    <td colspan="2" class="total-import"><?= number_format($total, 2) ?> €</td>
                </tr>
            </tfoot>
        </table>

        <!-- Botó de finalitzar compra (simulada) amb confirmació -->
        <a href="finalitzar_compra.php"
           class="boto"
           onclick="return confirm('Segur que vols finalitzar la compra?')">
            Finalitzar la compra
        </a>
    </section>
<?php endif; ?>

<?php
require_once 'includes/footer.php';
?>
