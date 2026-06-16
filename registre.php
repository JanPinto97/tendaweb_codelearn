<?php
// ========================================================
// registre.php
// Formulari de registre de nous clients
// ========================================================

require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/funcions.php';

$error = '';

// Processem el formulari quan s'envia per POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recollim les dades del formulari
    $nom      = $_POST['nom'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Creem sempre un client des del registre públic
    $resultat = crearUsuari($pdo, $nom, $email, $password);

    if (!$resultat['ok']) {
        $error = $resultat['error'];
    } else {
        // Redirigim al login amb missatge d'èxit
        header('Location: login.php?registrat=1');
        exit;
    }
}

$base = './';
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

        <button type="submit" class="boto">Registrar-se</button>
    </form>

    <p>Ja tens compte? <a href="login.php">Inicia sessió</a></p>
</div>
<?php
require_once 'includes/footer.php';
?>
