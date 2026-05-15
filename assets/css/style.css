</div><!-- /.main-container -->

<footer>
    <p class="mb-0">
        &copy; <?php echo date('Y'); ?> ITWay &mdash; Intranet interne &mdash; Tous droits réservés
    </p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/main.js"></script>
<script>
(function () {
    /* ── Helpers ── */
    const $ = id => document.getElementById(id);
    const save = (k, v) => localStorage.setItem('a11y_' + k, v);
    const load = k => localStorage.getItem('a11y_' + k);

    /* ── Taille de police ── */
    let fontSize = parseInt(load('fontsize')) || 100;
    function applyFontSize(val) {
        fontSize = Math.min(140, Math.max(80, val));
        document.documentElement.style.fontSize = fontSize + '%';
        save('fontsize', fontSize);
    }
    applyFontSize(fontSize);
    $('btn-font-up').addEventListener('click', () => applyFontSize(fontSize + 10));
    $('btn-font-down').addEventListener('click', () => applyFontSize(fontSize - 10));

    /* ── Mode dyslexie ── */
    function applyDyslexia(active) {
        document.body.classList.toggle('dyslexia-mode', active);
        $('btn-dyslexia').classList.toggle('active', active);
        $('btn-dyslexia').setAttribute('aria-pressed', active);
        save('dyslexia', active);
    }
    applyDyslexia(load('dyslexia') === 'true');
    $('btn-dyslexia').addEventListener('click', () =>
        applyDyslexia(!document.body.classList.contains('dyslexia-mode'))
    );

    /* ── Contraste élevé ── */
    function applyContrast(active) {
        document.body.classList.toggle('high-contrast', active);
        $('btn-contrast').classList.toggle('active', active);
        $('btn-contrast').setAttribute('aria-pressed', active);
        save('contrast', active);
    }
    applyContrast(load('contrast') === 'true');
    $('btn-contrast').addEventListener('click', () =>
        applyContrast(!document.body.classList.contains('high-contrast'))
    );

    /* ── Réinitialiser ── */
    $('btn-reset').addEventListener('click', () => {
        applyFontSize(100);
        applyDyslexia(false);
        applyContrast(false);
    });
})();
</script>
</body>
</html>
