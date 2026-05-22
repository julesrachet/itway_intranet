document.addEventListener('DOMContentLoaded', function () {

    /* ── Injection CSS dynamique — tout est géré ici, pas dans style.css ── */
    var styleTag = document.createElement('style');
    styleTag.id  = 'a11y-dynamic';
    document.head.appendChild(styleTag);

    function injectCSS() {
        var css = '';

        /* Taille de police : on change html pour que rem cascade partout */
        css += 'html { font-size: ' + fontSize + '% !important; }\n';

        /* Mode dyslexie */
        if (dysActive) {
            css += [
                'body, body *, body p, body h1, body h2, body h3,',
                'body li, body label, body input, body textarea,',
                'body .btn, body .nav-link, body .card, body .form-control {',
                '    font-family: "OpenDyslexic", Arial, sans-serif !important;',
                '    letter-spacing: .1em !important;',
                '    word-spacing: .25em !important;',
                '    line-height: 2 !important;',
                '}',
                'body { background-color: #fdf6e3 !important; }',
                '.post-card, .article-wrap, .form-wrap { background-color: #fffdf5 !important; }',
            ].join('\n');
        }

        /* Mode contraste élevé */
        if (conActive) {
            css += [
                'body { background: #000 !important; color: #fff !important; }',
                '.post-card, .article-wrap, .form-wrap, .modal-content {',
                '    background: #111 !important;',
                '    border-color: #fff !important;',
                '    color: #fff !important;',
                '}',
                '.post-title a, h1, h2, h3 { color: #ffe066 !important; }',
                '.post-meta, .post-excerpt, .article-content,',
                '.text-muted, small { color: #ccc !important; }',
                '.navbar { background: #0a0a0a !important; border-bottom: 2px solid #444 !important; }',
                '.a11y-bar { background: #111 !important; border-color: #555 !important; }',
                '.a11y-btn { background: #222 !important; border-color: #666 !important; color: #fff !important; }',
                '.a11y-btn.active { background: #ffe066 !important; color: #000 !important; border-color: #ffe066 !important; }',
                '.btn-accent { background: #ff9900 !important; color: #000 !important; }',
                '.btn-outline-primary { border-color: #fff !important; color: #fff !important; }',
                '.btn-secondary, .btn-outline-secondary { border-color: #aaa !important; color: #aaa !important; background: transparent !important; }',
                '.form-control { background: #1a1a1a !important; color: #fff !important; border-color: #888 !important; }',
                'footer { background: #111 !important; border-color: #444 !important; color: #aaa !important; }',
                '.badge-author { background: #333 !important; color: #ffe066 !important; }',
                '.page-header { border-color: #444 !important; }',
                '.empty-state { color: #aaa !important; }',
                'a { color: #7dd3fc !important; }',
            ].join('\n');
        }

        styleTag.textContent = css;
    }

    /* ── État initial ── */
    var fontSize  = parseInt(localStorage.getItem('a11y_fontsize'))  || 100;
    var dysActive = localStorage.getItem('a11y_dyslexia') === 'true';
    var conActive = localStorage.getItem('a11y_contrast') === 'true';

    /* ── Sync boutons au chargement ── */
    function syncButtons() {
        var btnDys = document.getElementById('btn-dyslexia');
        var btnCon = document.getElementById('btn-contrast');
        if (btnDys) btnDys.classList.toggle('active', dysActive);
        if (btnCon) btnCon.classList.toggle('active', conActive);
    }

    injectCSS();
    syncButtons();

    /* ── Taille ── */
    var btnUp   = document.getElementById('btn-font-up');
    var btnDown = document.getElementById('btn-font-down');
    if (btnUp) btnUp.onclick = function () {
        fontSize = Math.min(150, fontSize + 15);
        localStorage.setItem('a11y_fontsize', fontSize);
        injectCSS();
    };
    if (btnDown) btnDown.onclick = function () {
        fontSize = Math.max(70, fontSize - 15);
        localStorage.setItem('a11y_fontsize', fontSize);
        injectCSS();
    };

    /* ── Dyslexie ── */
    var btnDys = document.getElementById('btn-dyslexia');
    if (btnDys) btnDys.onclick = function () {
        dysActive = !dysActive;
        this.classList.toggle('active', dysActive);
        localStorage.setItem('a11y_dyslexia', dysActive);
        injectCSS();
    };

    /* ── Contraste ── */
    var btnCon = document.getElementById('btn-contrast');
    if (btnCon) btnCon.onclick = function () {
        conActive = !conActive;
        this.classList.toggle('active', conActive);
        localStorage.setItem('a11y_contrast', conActive);
        injectCSS();
    };

    /* ── Reset ── */
    var btnReset = document.getElementById('btn-reset');
    if (btnReset) btnReset.onclick = function () {
        fontSize  = 100;
        dysActive = false;
        conActive = false;
        localStorage.removeItem('a11y_fontsize');
        localStorage.removeItem('a11y_dyslexia');
        localStorage.removeItem('a11y_contrast');
        injectCSS();
        syncButtons();
        document.querySelectorAll('.a11y-btn').forEach(function (b) {
            b.classList.remove('active');
        });
    };
});
