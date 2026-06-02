<?php
// ========================================================
// includes/funcions.php
// Funcions auxiliars compartides
// ========================================================

// --------------------------------------------------------
// Puja una imatge a la carpeta indicada
// Retorna ['nom' => 'fitxer.jpg', 'error' => null]
// o      ['nom' => null, 'error' => 'missatge d\'error']
// Si no s'ha enviat cap fitxer, retorna nom = null sense error
// --------------------------------------------------------
function pujarImatge(array $fitxer, string $carpeta): array {

    // Si no s'ha pujat res, no és error — simplement no hi ha imatge
    if (empty($fitxer['name'])) {
        return ['nom' => null, 'error' => null];
    }

    // Comprovem que sigui una imatge vàlida
    $tipus_permesos = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($fitxer['type'], $tipus_permesos)) {
        return ['nom' => null, 'error' => 'La imatge ha de ser JPG, PNG o WEBP.'];
    }

    // Màxim 2MB
    if ($fitxer['size'] > 2 * 1024 * 1024) {
        return ['nom' => null, 'error' => 'La imatge no pot superar els 2MB.'];
    }

    // Generem un nom únic per evitar col·lisions
    $extensio = pathinfo($fitxer['name'], PATHINFO_EXTENSION);
    $nom_nou  = uniqid('prod_') . '.' . $extensio;

    // Movem la imatge a la carpeta de destí
    move_uploaded_file($fitxer['tmp_name'], $carpeta . '/' . $nom_nou);

    return ['nom' => $nom_nou, 'error' => null];
}
