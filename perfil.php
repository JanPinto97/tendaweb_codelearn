<?php
// ========================================================
// perfil.php
// Pàgina de l'usuari — canviar dades i veure els diners gastats
// ========================================================

require_once 'includes/db.php';
require_once 'includes/auth.php';

// Cal haver iniciat sessió per veure el compte
protegirClient('login.php');

// Els admins no tenen pàgina de compte
if (esAdmin()) {
    header('Location: admin/index.php');
    exit;
}

$id    = $_SESSION['usuari_id'];
$error = '';
$ok    = '';

// --------------------------------------------------------
// Processem el formulari quan s'envia per POST
// --------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom      = trim($_POST['nom'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($nom) || empty($email)) {
        $error = 'El nom i el correu no poden estar buits.';

    // La contrasenya és opcional, però si se'n posa una ha de tenir 6 caràcters
    } elseif ($password !== '' && strlen($password) < 6) {
        $error = 'La contrasenya ha de tenir mínim 6 caràcters.';

    } else {
        // Comprovem que el correu no el faci servir un altre usuari
        $stmt = $pdo->prepare("SELECT id FROM usuaris WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);

        if ($stmt->fetch()) {
            $error = 'Aquest correu ja està registrat.';
        } else {
            // Si s'ha escrit una contrasenya nova, la canviem també
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE usuaris SET nom = ?, email = ?, password = ? WHERE id = ?");
                $stmt->execute([$nom, $email, $hash, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE usuaris SET nom = ?, email = ? WHERE id = ?");
                $stmt->execute([$nom, $email, $id]);
            }

            // Actualitzem el nom que es mostra a la capçalera
            $_SESSION['nom'] = $nom;
            $ok = 'Dades actualitzades correctament.';
        }
    }
}

// --------------------------------------------------------
// Carreguem les dades actuals de l'usuari
// --------------------------------------------------------
$stmt = $pdo->prepare("SELECT * FROM usuaris WHERE id = ?");
$stmt->execute([$id]);
$usuari = $stmt->fetch();

// --------------------------------------------------------
// Carreguem les compres passades de l'usuari (les més noves primer)
// --------------------------------------------------------
$stmt = $pdo->prepare("SELECT * FROM comandes WHERE usuari_id = ? ORDER BY data DESC");
$stmt->execute([$id]);
$comandes = $stmt->fetchAll();

$base = './';
require_once 'includes/header.php';
?>

<h1 class="perfil-titol">El meu compte</h1>

<div class="perfil">

    <!-- Columna esquerra: dades del compte -->
    <div class="formulari-container perfil-dades">
        <h2>Les meves dades</h2>

        <!-- Missatges d'error o d'èxit -->
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if ($ok): ?>
            <p class="avis-compra"><?= htmlspecialchars($ok) ?></p>
        <?php endif; ?>

        <!-- Total de diners gastats -->
        <p class="gastat">
            Total gastat: <strong><?= number_format($usuari['gastat'], 2) ?> €</strong>
        </p>

        <form method="POST" action="perfil.php">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" required
                   value="<?= htmlspecialchars($usuari['nom']) ?>">

            <label for="email">Correu</label>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($usuari['email']) ?>">

            <label for="password">Nova contrasenya</label>
            <input type="password" id="password" name="password"
                   placeholder="Deixa-ho buit per no canviar-la">

            <button type="submit" class="boto">Desar canvis</button>
        </form>
    </div>

    <!-- Columna dreta: compres passades -->
    <section class="perfil-compres">
        <h2>Compres passades</h2>

        <?php if (empty($comandes)): ?>
            <p class="missatge-buit">Encara no has fet cap compra.</p>

        <?php else: ?>
            <?php foreach ($comandes as $comanda): ?>
                <?php
                    // Productes d'aquesta comanda
                    $stmt = $pdo->prepare("SELECT * FROM comanda_productes WHERE comanda_id = ?");
                    $stmt->execute([$comanda['id']]);
                    $productes_comanda = $stmt->fetchAll();
                ?>
                <article class="compra">
                    <p class="compra-data">
                        <?= date('d/m/Y H:i', strtotime($comanda['data'])) ?>
                    </p>

                    <ul class="compra-productes">
                        <?php foreach ($productes_comanda as $linia): ?>
                            <li>
                                <?= htmlspecialchars($linia['nom']) ?>
                                × <?= $linia['quantitat'] ?>
                                — <?= number_format($linia['preu'] * $linia['quantitat'], 2) ?> €
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <p class="compra-total">
                        Total: <?= number_format($comanda['total'], 2) ?> €
                    </p>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

</div>

<?php
require_once 'includes/footer.php';
?>
