<?php
// Configuration ultra-sécurisée - Aucune exécution de code externe

define('COURSES_DIR', __DIR__ . '/courses');
define('ALLOWED_EXTENSIONS', ['html']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB max

// Whitelist des caractères autorisés dans les noms de fichiers
define('SAFE_FILENAME_PATTERN', '/^[a-zA-Z0-9_\-]+\.html$/');

// Mapping des catégories (pour l'affichage uniquement)
define('CATEGORY_NAMES', [
    'security' => '🔐 Sécurité & Exploitation',
    'programming' => '💻 Programmation Système',
    'math' => '📐 Mathématiques',
    'network' => '🌐 Réseaux & Web',
    'algorithms' => '🧮 Algorithmes'
]);

// Mapping des badges de difficulté
define('DIFFICULTY_BADGES', [
    'danger' => 'AVANCÉ',
    'warning' => 'INTERMÉDIAIRE',
    'success' => 'DÉBUTANT'
]);

/**
 * Sécurise un chemin de fichier contre les attaques de traversée
 */
function securePath($path) {
    // Supprime les tentatives de traversée de répertoire
    $path = str_replace(['../', '..\\', './'], '', $path);
    
    // Nettoie le chemin
    $path = realpath($path);
    
    // Vérifie que le chemin est bien dans COURSES_DIR
    if ($path === false || strpos($path, COURSES_DIR) !== 0) {
        return false;
    }
    
    return $path;
}

/**
 * Vérifie si un fichier est sûr à lire
 */
function isSafeFile($filepath) {
    // Vérifie l'extension
    $extension = pathinfo($filepath, PATHINFO_EXTENSION);
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return false;
    }
    
    // Vérifie le nom de fichier
    $filename = basename($filepath);
    if (!preg_match(SAFE_FILENAME_PATTERN, $filename)) {
        return false;
    }
    
    // Vérifie la taille
    if (file_exists($filepath) && filesize($filepath) > MAX_FILE_SIZE) {
        return false;
    }
    
    return true;
}

/**
 * Extrait les métadonnées d'un fichier HTML de manière sécurisée
 */
function extractMetadata($filepath) {
    if (!isSafeFile($filepath)) {
        return null;
    }
    
    $content = file_get_contents($filepath);
    
    // Utilise DOMDocument pour parser de manière sûre
    $dom = new DOMDocument();
    @$dom->loadHTML($content, LIBXML_NOERROR | LIBXML_NOWARNING);
    
    $metadata = [
        'title' => '',
        'description' => '',
        'tags' => [],
        'difficulty' => 'warning'
    ];
    
    // Extrait le titre
    $h1 = $dom->getElementsByTagName('h1');
    if ($h1->length > 0) {
        $metadata['title'] = strip_tags($h1->item(0)->textContent);
    }
    
    // Extrait la description (premier <p> avec classe subtitle)
    $xpath = new DOMXPath($dom);
    $subtitles = $xpath->query("//p[@class='subtitle']");
    if ($subtitles->length > 0) {
        $metadata['description'] = strip_tags($subtitles->item(0)->textContent);
    }
    
    // Extrait les badges (tags)
    $badges = $xpath->query("//span[@class='badge']");
    foreach ($badges as $badge) {
        $classes = $badge->getAttribute('class');
        $text = strip_tags($badge->textContent);
        
        // Détermine la difficulté
        if (strpos($classes, 'badge-danger') !== false) {
            $metadata['difficulty'] = 'danger';
        } elseif (strpos($classes, 'badge-warning') !== false) {
            $metadata['difficulty'] = 'warning';
        } elseif (strpos($classes, 'badge-success') !== false) {
            $metadata['difficulty'] = 'success';
        }
        
        $metadata['tags'][] = $text;
    }
    
    return $metadata;
}

/**
 * Scanne le répertoire des cours et retourne la structure
 */
function scanCourses() {
    $courses = [];
    
    if (!is_dir(COURSES_DIR)) {
        return $courses;
    }
    
    // Scanne les catégories (sous-dossiers)
    $categories = array_diff(scandir(COURSES_DIR), ['.', '..']);
    
    foreach ($categories as $category) {
        $categoryPath = COURSES_DIR . '/' . $category;
        
        if (!is_dir($categoryPath)) {
            continue;
        }
        
        // Scanne les fichiers dans la catégorie
        $files = array_diff(scandir($categoryPath), ['.', '..']);
        
        foreach ($files as $file) {
            $filepath = $categoryPath . '/' . $file;
            
            // Vérifie la sécurité
            if (!isSafeFile($filepath)) {
                continue;
            }
            
            // Extrait les métadonnées
            $metadata = extractMetadata($filepath);
            
            if ($metadata && $metadata['title']) {
                if (!isset($courses[$category])) {
                    $courses[$category] = [];
                }
                
                $courses[$category][] = [
                    'title' => $metadata['title'],
                    'description' => $metadata['description'],
                    'file' => $category . '/' . $file,
                    'tags' => $metadata['tags'],
                    'difficulty' => $metadata['difficulty']
                ];
            }
        }
    }
    
    return $courses;
}
?>

