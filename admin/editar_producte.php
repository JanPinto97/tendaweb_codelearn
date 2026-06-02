<?php
// ========================================================
// admin/editar_producte.php
// Formulari per editar un producte existent
// ========================================================

require_once '../includes/db.php';
require_once '../includes/auth.php';


$titol = 'Editar producte';
$error = '';

// --------------------------------------------------------
// Comprovem que s'ha passat un ID vàlid per la URL
// --------------------------------------------------------
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

// --------------------------------------------------------
// Obtenim el producte actual de la BD
// --------------------------------------------------------
$stmt = $pdo->prepare("SELECT * FROM productes WHERE id = ?");
$stmt->execute([$id]);
$producte = $stmt->fetch();

// Si no existeix el producte, tornem al llistat
if (!$producte) {
    header('Location: index.php');
    exit;
}

// --------------------------------------------------------
// Obtenim les categories per al selector
// --------------------------------------------------------
$stmt = $pdo->prepare("SELECT * FROM categories ORDER BY nom ASC");
$stmt->execute();
$categories = $stmt->fetchAll();

// --------------------------------------------------------
// Processem el formulari quan s'envia per POST
// --------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recollim i netejem les dades del formulari
    $nom          = trim($_POST['nom'] ?? '');
    $descripcio   = trim($_POST['descripcio'] ?? '');
    $preu         = trim($_POST['preu'] ?? '');
    $estoc        = trim($_POST['estoc'] ?? '');
    $categoria_id = (int)($_POST['categoria_id'] ?? 0);

    // Mantenim la imatge actual per defecte
    $imatge = $producte['imatge'];

    // Comprovem els camps obligatoris
    if (empty($nom) || empty($preu) || empty($estoc)) {
        $error = 'Els camps nom, preu i estoc són obligatoris.';

    } elseif (!is_numeric($preu) || $preu < 0) {
        $error = 'El preu ha de ser un número positiu.';

    } elseif (!is_numeric($estoc) || $estoc < 0) {
        $error = 'L\'estoc ha de ser un número positiu.';

    } else {

        // --------------------------------------------------------
        // Gestionem la pujada d'una nova imatge si s'ha seleccionat
        // --------------------------------------------------------
        if (!empty($_FILES['imatge']['name'])) {

            $tipus_permesos = ['image/jpeg', 'image/png', 'image/webp'];
            $tipus_fitxer   = $_FILES['imatge']['type'];

            if (!in_array($tipus_fitxer, $tipus_permesos)) {
                $error = 'La imatge ha de ser JPG, PNG o WEBP.';
            } elseif ($_FILES['imatge']['size'] > 2 * 1024 * 1024) {
                $error = 'La imatge no pot superar els 2MB.';
            } else {
                // Esborrem la imatge antiga si existia
                if ($producte['imatge'] && file_exists('../uploads/' . $producte['imatge'])) {
                    unlink('../uploads/' . $producte['imatge']);
                }

                // Generem un nom únic per a la nova imatge
                $extensio = pathinfo($_FILES['imatge']['name'], PATHINFO_EXTENSION);
                $imatge   = uniqid('prod_') . '.' . $extensio;

                // Movem la nova imatge a la carpeta uploads
                move_uploaded_file(
                    $_FILES['imatge']['tmp_name'],
                    '../uploads/' . $imatge
                );
            }
        }

        // Actualitzem el producte si no hi ha errors
        if (empty($error)) {
            $stmt = $pdo->prepare("
                UPDATE productes
                SET nom = ?, descripcio = ?, preu = ?, estoc = ?, imatge = ?, categoria_id = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $nom,
                $descripcio,
                $preu,
                $estoc,
                $imatge,
                $categoria_id ?: null,
                $id
            ]);

            // Tornem al llistat amb missatge d'èxit
            header('Location: index.php?ok=editat');
            exit;
        }
    }
}

require_once 'includes/header.php';
?>

<!-- Missatge d'error si n'hi ha -->
<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<div class="admin-toolbar">
    <h2>Editar producte</h2>
    <a href="index.php" class="btn">← Tornar</a>
</div>

<!-- Formulari d'edició — preomplert amb les dades actuals -->
<form method="POST" action="editar_producte.php?id=<?= $id ?>" enctype="multipart/form-data" class="formulari-admin">

    <label for="nom">Nom *</label>
    <input type="text" id="nom" name="nom" required
           value="<?= htmlspecialchars($_POST['nom'] ?? $producte['nom']) ?>">

    <label for="descripcio">Descripció</label>
    <textarea id="descripcio" name="descripcio" rows="4"><?= htmlspecialchars($_POST['descripcio'] ?? $producte['descripcio']) ?></textarea>

    <label for="preu">Preu (€) *</label>
    <input type="number" id="preu" name="preu" step="0.01" min="0" required
           value="<?= htmlspecialchars($_POST['preu'] ?? $producte['preu']) ?>">

    <label for="estoc">Estoc *</label>
    <input type="number" id="estoc" name="estoc" min="0" required
           value="<?= htmlspecialchars($_POST['estoc'] ?? $producte['estoc']) ?>">

    <label for="categoria_id">Categoria</label>
    <select id="categoria_id" name="categoria_id">
        <option value="0">Sense categoria</option>
        <?php foreach ($categories as $categoria): ?>
            <option value="<?= $categoria['id'] ?>"
                <?= (($_POST['categoria_id'] ?? $producte['categoria_id']) == $categoria['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($categoria['nom']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- Imatge actual -->
    <label>Imatge actual</label>
    <?php if ($producte['imatge']): ?>
        <img src="../uploads/<?= htmlspecialchars($producte['imatge']) ?>"
             alt="Imatge actual" class="previsualitzacio-imatge">
    <?php else: ?>
        <p class="text-gris">Sense imatge</p>
    <?php endif; ?>

    <!-- Pujada d'una nova imatge -->
    <label for="imatge">Canviar imatge (JPG, PNG, WEBP — màx. 2MB)</label>
    <input type="file" id="imatge" name="imatge" accept="image/jpeg,image/png,image/webp">

    <button type="submit" class="btn btn-nou">Guardar canvis</button>

</form>

<?php require_once 'includes/footer.php'; ?>