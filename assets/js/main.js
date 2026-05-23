document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = this.getAttribute('data-id');
            var title = this.getAttribute('data-title');
            if (confirm('Supprimer "' + title + '" ? Cette action est irréversible.')) {
                window.location.href = 'my_post.php?id=' + id;
            }
        });
    });
});
