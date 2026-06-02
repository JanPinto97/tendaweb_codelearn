<?php
// ========================================================
// admin/nou_producte.php
// Formulari per afegir un nou producte
// ========================================================

require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/funcions.php';

// Protegim la pàgina — només admins
protegirAdmin('../login.php');

$error = '';

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
    $imatge       = null;

    // Comprovem els camps obligatoris
    if ($nom === '' || $preu === '' || $estoc === '') {
        $error = 'Els camps nom, preu i estoc són obligatoris.';

    } elseif (!is_numeric($preu) || $preu < 0) {
        $error = 'El preu ha de ser un número positiu.';

    } elseif (!is_numeric($estoc) || $estoc < 0) {
        $error = 'L\'estoc ha de ser un número positiu.';

    } else {

        // Gestionem la pujada de la imatge si s'ha seleccionat
        $resultat = pujarImatge($_FILES['imatge'], '../uploads');
        if ($resultat['error']) {
            $error = $resultat['error'];
        } else {
            $imatge = $resultat['nom'];
        }

        // Inserim el producte si no hi ha errors
        if (empty($error)) {
            $stmt = $pdo->prepare("
                INSERT INTO productes (nom, descripcio, preu, estoc, imatge, categoria_id)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $nom,
                $descripcio,
                $preu,
                $estoc,
                $imatge,
                $categoria_id ?: null
            ]);

            // Tornem al llistat amb missatge d'èxit
            header('Location: index.php?ok=afegit');
            exit;
        }
    }
}

$base = '../';
require_once '../includes/header.php';
?>

<!-- Missatge d'error si n'hi ha -->
<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<div class="admin-toolbar">
    <h2>Afegir producte</h2>
    <a href="index.php" class="btn">← Tornar</a>
</div>

<!-- Formulari de nou producte -->
<form method="POST" action="nou_producte.php" enctype="multipart/form-data" class="formulari-admin">

    <label for="nom">Nom *</label>
    <input type="text" id="nom" name="nom" required
           value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">

    <label for="descripcio">Descripció</label>
    <textarea id="descripcio" name="descripcio" rows="4"><?= htmlspecialchars($_POST['descripcio'] ?? '') ?></textarea>

    <label for="preu">Preu (€) *</label>
    <input type="number" id="preu" name="preu" step="0.01" min="0" required
           value="<?= htmlspecialchars($_POST['preu'] ?? '') ?>">

    <label for="estoc">Estoc *</label>
    <input type="number" id="estoc" name="estoc" min="0" required
           value="<?= htmlspecialchars($_POST['estoc'] ?? '') ?>">

    <label for="categoria_id">Categoria</label>
    <select id="categoria_id" name="categoria_id">
        <option value="0">Sense categoria</option>
        <?php foreach ($categories as $categoria): ?>
            <option value="<?= $categoria['id'] ?>"
                <?= (($_POST['categoria_id'] ?? 0) == $categoria['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($categoria['nom']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="imatge">Imatge (JPG, PNG, WEBP — màx. 2MB)</label>
    <input type="file" id="imatge" name="imatge" accept="image/jpeg,image/png,image/webp">

    <button type="submit" class="btn btn-nou">Afegir producte</button>

</form>

<?php require_once '../includes/footer.php'; ?>
