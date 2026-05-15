<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITWay Intranet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Barre d'accessibilité -->
<div class="a11y-bar" role="toolbar" aria-label="Options d'accessibilité">
    <div class="a11y-inner">
        <span class="a11y-label"><i class="bi bi-universal-access me-1"></i>Accessibilité</span>

        <button id="btn-dyslexia" class="a11y-btn" aria-pressed="false"
                title="Activer la police adaptée à la dyslexie (OpenDyslexic)">
            <i class="bi bi-fonts"></i> Dyslexie
        </button>

        <button id="btn-font-up" class="a11y-btn"
                aria-label="Augmenter la taille du texte" title="Agrandir le texte">
            <i class="bi bi-zoom-in"></i> A+
        </button>

        <button id="btn-font-down" class="a11y-btn"
                aria-label="Réduire la taille du texte" title="Réduire le texte">
            <i class="bi bi-zoom-out"></i> A-
        </button>

        <button id="btn-contrast" class="a11y-btn" aria-pressed="false"
                title="Activer le mode contraste élevé">
            <i class="bi bi-circle-half"></i> Contraste
        </button>

        <button id="btn-reset" class="a11y-btn a11y-reset"
                title="Réinitialiser les préférences d'accessibilité">
            <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
        </button>
    </div>
</div>

<!-- Navbar principale -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="index.php">
            <span class="brand-icon">📰</span>
            ITWay Intranet
        </a>
        <div class="d-flex align-items-center gap-1 ms-auto">
            <?php if (isset($user)): ?>
                <a class="nav-link" href="index.php"><i class="bi bi-house me-1"></i>Accueil</a>
                <a class="nav-link" href="create_post.php"><i class="bi bi-plus-circle me-1"></i>Publier</a>
                <a class="nav-link" href="my_post.php"><i class="bi bi-journal-text me-1"></i>Mes articles</a>
                <div class="nav-divider"></div>
                <span class="nav-link nav-user">
                    <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($user['name']); ?>
                </span>
                <a class="nav-link nav-logout" href="logout.php">
                    <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                </a>
            <?php else: ?>
                <a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="main-container">
