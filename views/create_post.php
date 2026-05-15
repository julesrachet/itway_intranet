<div class="page-header">
    <h1><i class="bi bi-pencil-square me-2" style="color:var(--accent)"></i>Nouvel article</h1>
</div>

<div class="form-wrap" style="max-width: 720px;">

    <?php if (isset($error)): ?>
        <div class="alert alert-danger mb-3">
            <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" novalidate>

        <div class="mb-4">
            <label for="title" class="form-label">Titre de l'article</label>
            <input type="text"
                   class="form-control"
                   id="title"
                   name="title"
                   placeholder="Un titre clair et accrocheur…"
                   value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
                   required>
        </div>

        <div class="mb-4">
            <label for="content" class="form-label">Contenu</label>
            <textarea class="form-control"
                      id="content"
                      name="content"
                      rows="12"
                      placeholder="Rédigez votre actualité ici…"
                      required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-accent">
                <i class="bi bi-send me-1"></i>Publier
            </button>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-1"></i>Annuler
            </a>
        </div>

    </form>
</div>
