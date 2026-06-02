<?php
// ========================================================
// registre.php
// Formulari de registre de nous clients
// ========================================================

require_once 'includes/db.php';
require_once 'includes/auth.php';


$error = '';
$exit  = '';

// Processem el formulari quan s'envia per POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recollim i netejem les dades del formulari
    $nom      = trim($_POST['nom'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Comprovem que els camps no estiguin buits
    if (empty($nom) || empty($email) || empty($password)) {
        $error = 'Omple tots els camps.';

    // Comprovem que la contrasenya tingui mínim 6 caràcters
    } elseif (strlen($password) < 6) {
        $error = 'La contrasenya ha de tenir mínim 6 caràcters.';

    } else {
        // Comprovem que l'email no estigui ja registrat
        $stmt = $pdo->prepare("SELECT id FROM usuaris WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = 'Aquest email ja està registrat.';
        } else {
            // Hashegem la contrasenya abans de guardar-la
            $hash = password_hash($password, PASSWORD_BCRYPT);

            // Inserim el nou usuari com a client
            $stmt = $pdo->prepare("
                INSERT INTO usuaris (nom, email, password, rol)
                VALUES (?, ?, ?, 'client')
            ");
            $stmt->execute([$nom, $email, $hash]);

            // Redirigim al login amb missatge d'èxit
            header('Location: login.php?registrat=1');
            exit;
        }
    }
}
$base  = './';
$titol = 'Crear compte';
require_once 'includes/header.php';
?>

<div class="formulari-container">
    <h1>Crear compte</h1>

    <!-- Mostrem l'error si n'hi ha -->
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <!-- Formulari de registre -->
    <form method="POST" action="registre.php">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" required
                value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

        <label for="password">Contrasenya</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Registrar-se</button>
    </form>

    <p>Ja tens compte? <a href="login.php">Inicia sessió</a></p>
</div>
<?php
require_once 'includes/footer.php';
?>