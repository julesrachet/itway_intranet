<div class="page-header">
    <h1><i class="bi me-2" style="color:var(--accent)"></i>Actualités</h1>
    <?php if (isset($user)): ?>
        <a href="create_post.php" class="btn btn-accent">
            <i class="bi bi-plus-lg me-1"></i>Publier un article
        </a>
    <?php endif; ?>
</div>

<?php if (empty($posts)): ?>
    <div class="empty-state">
        <div class="empty-icon">📭</div>
        <p>Aucun article publié pour le moment.</p>
        <?php if (isset($user)): ?>
            <a href="create_post.php" class="btn btn-primary">Publier le premier article</a>
        <?php endif; ?>
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
                <span class="badge-author">
                    <i class="bi  me-1"></i><?php echo htmlspecialchars($post['author_name']); ?>
                </span>
                <span><i class="bi me-1"></i><?php echo date('d/m/Y à H:i', strtotime($post['created_at'])); ?></span>
            </div>
            <p class="post-excerpt">
                <?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 220))); ?>
                <?php if (strlen($post['content']) > 220): ?>…<?php endif; ?>
            </p>
            <div class="card-actions">
                <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-outline-primary btn-sm">
                    <i class="bi  me-1"></i>Lire l'article
                </a>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
