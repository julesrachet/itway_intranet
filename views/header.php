<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog with Keycloak</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">My Blog</a>
            
            <div class="navbar-nav ms-auto">
                <?php if (isset($user)): ?>
                    <a class="nav-link" href="create_post.php">Créer un post</a>
                    <a class="nav-link" href="my_post.php">Mes posts</a>
                    <span class="nav-link">Bienvenue, <?php echo htmlspecialchars($user['name']); ?> !</span>
                    <a class="nav-link" href="logout.php">Déconnexion</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">Connexion</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
