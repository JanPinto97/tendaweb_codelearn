<?php
// ========================================================
// admin/categories.php
// Gestio de categories
// ========================================================

require_once '../includes/db.php';
require_once '../includes/auth.php';

// Protegim la pagina - nomes admins
protegirAdmin('../login.php');

$error = '';

// Eliminem una categoria
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];

    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: categories.php');
    exit;
}

// Afegim una categoria nova
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');

    if ($nom === '') {
        $error = 'El nom de la categoria no pot estar buit.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (nom) VALUES (?)");
        $stmt->execute([$nom]);

        header('Location: categories.php');
        exit;
    }
}

// Obtenim totes les categories
$stmt = $pdo->prepare("SELECT * FROM categories ORDER BY nom ASC");
$stmt->execute();
$categories = $stmt->fetchAll();

$base = '../';
require_once '../includes/header.php';
?>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<div class="admin-barra">
    <h2>Categories</h2>
    <a href="index.php" class="boto">Tornar</a>
</div>

<form method="POST" action="categories.php" class="formulari-admin">
    <label for="nom">Nova categoria</label>
    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">

    <button type="submit" class="boto boto-nou">Afegir categoria</button>
</form>

<section class="categories-llista">
    <?php if (empty($categories)): ?>
        <p class="missatge-buit">No hi ha categories.</p>
    <?php else: ?>
        <?php foreach ($categories as $categoria): ?>
            <div class="categoria-fila">
                <span><?= htmlspecialchars($categoria['nom']) ?></span>

                <a href="categories.php?eliminar=<?= $categoria['id'] ?>"
                   class="boto boto-eliminar"
                   onclick="return confirm('Segur que vols eliminar aquesta categoria?')">
                    Eliminar
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php require_once '../includes/footer.php'; ?>
