<div class="page-header">
    <h1><i class="bi bi-journal-text me-2" style="color:var(--accent)"></i>Mes articles</h1>
    <a href="create_post.php" class="btn btn-accent">
        <i class="bi bi-plus-lg me-1"></i>Nouvel article
    </a>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger mb-3">
        <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<?php if (empty($posts)): ?>
    <div class="empty-state">
        <div class="empty-icon">✍</div>
        <p>Vous n'avez pas encore publié d'article.</p>
        <a href="create_post.php" class="btn btn-primary">Publier mon premier article</a>
    </div>
<?php else: ?>
    <?php foreach ($posts as $post): ?>
        <div class="post-card">
            <div class="post-title">
                <a href="post.php?id=<?php echo $post['id']; ?>">
                    <?php echo htmlspecialchars($post['title']); ?>
                </a>
            </div>
            <div class="post-meta">
                <span><i class="bi bi-calendar3 me-1"></i><?php echo date('d/m/Y à H:i', strtotime($post['created_at'])); ?></span>
            </div>
            <p class="post-excerpt">
                <?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 200))); ?>
                <?php if (strlen($post['content']) > 200): ?>…<?php endif; ?>
            </p>
            <div class="card-actions">
                <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-eye me-1"></i>Voir
                </a>
                <a href="my_post.php?id=<?php echo $post['id']; ?>" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash me-1"></i>Supprimer
                </a>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
