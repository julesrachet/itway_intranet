<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog with Keycloak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/accessibility.css" rel="stylesheet">
    <!-- Applique les prefs avant le rendu pour éviter le flash de style -->
    <script>
        (function() {
            try {
                var prefs = JSON.parse(localStorage.getItem('a11y_prefs')) || {};
                var classes = [];
                var sizes = [null, 'a11y-font-md', 'a11y-font-lg', 'a11y-font-xl'];
                if (prefs.fontSize && sizes[prefs.fontSize]) classes.push(sizes[prefs.fontSize]);
                if (prefs.dyslexia) classes.push('a11y-dyslexia');
                if (prefs.contrast) classes.push('a11y-contrast');
                if (classes.length) document.documentElement.className += ' ' + classes.join(' ');
            } catch(e) {}
        })();
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Intranet ITWAY</a>
            
            <div class="navbar-nav ms-auto">
                <?php if (isset($user)): ?>
                    <a class="nav-link" href="create_post.php">Publier</a>
                    <a class="nav-link" href="my_post.php">Mes Posts</a>
                    <span class="nav-link">Bienvenue, <?php echo htmlspecialchars($user['name']); ?>!</span>
                    <a class="nav-link" href="logout.php">Deconnexion</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">Se connecter</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/accessibility.js"></script>
