<?php
require_once 'config.php';

// Récupère et sécurise le paramètre
$courseFile = $_GET['course'] ?? '';
$coursePath = securePath(COURSES_DIR . '/' . $courseFile);

// Vérifie la sécurité
if (!$coursePath || !isSafeFile($coursePath) || !file_exists($coursePath)) {
    http_response_code(404);
    die('Cours introuvable ou accès non autorisé.');
}

// Lit le contenu de manière sécurisée
$content = file_get_contents($coursePath);

// Remplace les chemins relatifs par les chemins absolus (flemme de retoucher les anciens cours merde...)
$content = str_replace(
    'cours.asha-services.org/a-xplains.min.css',
    'assets/a-xplains.min.css',
    $content
);

$content = str_replace(
    'cours.asha-services.org/a-xplains.min.js',
    'assets/a-xplains.min.js',
    $content
);

// Retour arrière lol
$backButton = '<div style="margin: 1rem 0;"><a href="index.php" class="btn-primary"><span>← Retour à l\'index</span></a></div>';
$content = str_replace('<div class="container">', '<div class="container">' . $backButton, $content);

// Affiche le contenu (déjà du HTML statique, aucune exécution, juste du HTML, pourquoi faire compliqué quand on peu faire simple (merci bibmath))
echo $content;
?>

