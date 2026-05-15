<div class="mb-3">
    <a href="index.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Retour aux actualités
    </a>
</div>

<div class="article-wrap">
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>

    <div class="post-meta mt-2">
        <span class="badge-author">
            <i class="bi bi-person-fill me-1"></i><?php echo htmlspecialchars($post['author_name']); ?>
        </span>
        <span><i class="bi bi-calendar3 me-1"></i><?php echo date('d F Y à H:i', strtotime($post['created_at'])); ?></span>
    </div>

    <hr style="border-color: var(--border); margin: 1.5rem 0;">

    <div class="article-content">
        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
    </div>
</div>
