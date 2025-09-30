<?php
require_once 'config.php';

// Scanne les cours de manière sécurisée
$courses = scanCourses();
$totalCourses = 0;
foreach ($courses as $category => $coursesInCategory) {
    $totalCourses += count($coursesInCategory);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A-XPLAINS - Index des Cours</title>
    <link rel="stylesheet" href="assets/a-xplains.min.css">
    <style>
        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .course-card {
            background: #3e3e53;
            border-left: 4px solid #658C79;
            padding: 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            color: inherit;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(101, 140, 121, 0.3);
            border-left-width: 8px;
        }

        .course-card h3 {
            margin-top: 0;
            color: #658C79;
            font-size: 1.3rem;
        }

        .course-card p {
            color: #b8b8c8;
            font-size: 0.95rem;
            margin: 0.5rem 0;
        }

        .course-tags {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .category-section {
            margin-bottom: 3rem;
        }

        .category-title {
            color: #658C79;
            font-size: 1.8rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #658C79;
        }

        .search-box {
            margin: 2rem 0;
            width: 100%;
        }

        .search-box input {
            width: 100%;
            padding: 1rem;
            background: #3e3e53;
            border: 2px solid #658C79;
            border-radius: 8px;
            color: #ffffff;
            font-size: 1rem;
        }

        .search-box input:focus {
            outline: none;
            border-color: #7ea88f;
        }

        .stats {
            display: flex;
            gap: 2rem;
            margin: 2rem 0;
            flex-wrap: wrap;
        }

        .stat-box {
            background: #3e3e53;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #658C79;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #658C79;
        }

        .stat-label {
            color: #b8b8c8;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>A-XPLAINS</h1>
            <p class="subtitle">Plateforme d'apprentissage technique approfondi</p>
        </header>

        <div class="stats">
            <div class="stat-box">
                <div class="stat-number"><?php echo $totalCourses; ?></div>
                <div class="stat-label">Cours disponibles</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?php echo count($courses); ?></div>
                <div class="stat-label">Catégories</div>
            </div>
        </div>

        <div class="search-box">
            <input type="text" id="search-input" placeholder="🔍 Rechercher un cours..." oninput="filterCourses()">
        </div>

        <main id="courses-container">
            <?php foreach ($courses as $category => $coursesInCategory): ?>
                <div class="category-section" data-category="<?php echo htmlspecialchars($category); ?>">
                    <h2 class="category-title">
                        <?php echo htmlspecialchars($category); ?>
                    </h2>
                    
                    <div class="course-grid">
                        <?php foreach ($coursesInCategory as $course): ?>
                            <a href="view.php?course=<?php echo urlencode($course['file']); ?>" class="course-card" 
                               data-title="<?php echo htmlspecialchars(strtolower($course['title'])); ?>"
                               data-tags="<?php echo htmlspecialchars(strtolower(implode(' ', $course['tags']))); ?>">
                                <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                                <p><?php echo htmlspecialchars($course['description']); ?></p>
                                
                                <div class="course-tags">
                                    <?php 
                                    $badgeClass = 'badge-' . $course['difficulty'];
                                    $badgeText = DIFFICULTY_BADGES[$course['difficulty']] ?? 'COURS';
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>">
                                        <span><?php echo $badgeText; ?></span>
                                    </span>
                                    
                                    <?php foreach (array_slice($course['tags'], 0, 3) as $tag): ?>
                                        <span class="badge badge-warning">
                                            <span><?php echo htmlspecialchars($tag); ?></span>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($courses)): ?>
                <div class="content-box">
                    <p>Aucun cours disponible pour le moment, désolé... je ne sais pas pourquoi le site est accessible si j'ai rien mis dessus ? <code>courses/</code>.</p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script src="assets/a-xplains.min.js"></script>
    <script>
        function filterCourses() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase();
            const cards = document.querySelectorAll('.course-card');
            const sections = document.querySelectorAll('.category-section');
            
            cards.forEach(card => {
                const title = card.getAttribute('data-title');
                const tags = card.getAttribute('data-tags');
                const searchableText = title + ' ' + tags;
                
                if (searchableText.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Cache les catégories vides
            sections.forEach(section => {
                const visibleCards = section.querySelectorAll('.course-card[style="display: block;"], .course-card:not([style])');
                if (visibleCards.length === 0 && searchTerm !== '') {
                    section.style.display = 'none';
                } else {
                    section.style.display = 'block';
                }
            });
        }
    </script>
</body>
</html>
