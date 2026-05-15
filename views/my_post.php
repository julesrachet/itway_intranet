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
        <div class="empty-icon">✍️</div>
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
                <button type="button"
                        class="btn btn-outline-danger btn-sm"
                        onclick="confirmDelete(<?php echo $post['id']; ?>, '<?php echo htmlspecialchars(addslashes($post['title'])); ?>')">
                    <i class="bi bi-trash me-1"></i>Supprimer
                </button>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Modal confirmation suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; border:none;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" style="color:var(--danger);">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirmer la suppression
                </h5>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer l'article <strong id="deleteTitle"></strong> ?
                Cette action est irréversible.
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                <a href="#" id="deleteConfirmBtn" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash me-1"></i>Supprimer définitivement
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, title) {
    document.getElementById('deleteTitle').textContent = '"' + title + '"';
    document.getElementById('deleteConfirmBtn').href = 'my_post.php?id=' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
