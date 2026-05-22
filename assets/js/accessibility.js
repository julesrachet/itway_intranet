/**
 * Accessibility Toolbar
 * Gère : taille de police, mode dyslexie, contraste élevé
 * Persistance via localStorage
 */
(function () {
    'use strict';

    const STORAGE_KEY = 'a11y_prefs';

    // Tailles disponibles (null = défaut)
    const FONT_SIZES = [null, 'a11y-font-md', 'a11y-font-lg', 'a11y-font-xl'];
    const FONT_LABELS = ['A', 'A+', 'A++', 'A+++'];

    // Charge les préférences sauvegardées
    function loadPrefs() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY)) || {};
        } catch (e) {
            return {};
        }
    }

    // Sauvegarde les préférences
    function savePrefs(prefs) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(prefs));
        } catch (e) {}
    }

    // Applique toutes les préférences sur le body
    function applyPrefs(prefs) {
        const body = document.body;

        // --- Taille de police ---
        FONT_SIZES.forEach(cls => { if (cls) body.classList.remove(cls); });
        const fontClass = FONT_SIZES[prefs.fontSize || 0];
        if (fontClass) body.classList.add(fontClass);

        // --- Dyslexie ---
        body.classList.toggle('a11y-dyslexia', !!prefs.dyslexia);

        // --- Contraste ---
        body.classList.toggle('a11y-contrast', !!prefs.contrast);

        // Met à jour l'état visuel des boutons
        updateButtons(prefs);
    }

    function updateButtons(prefs) {
        // Boutons taille
        document.querySelectorAll('[data-a11y-font]').forEach(btn => {
            const idx = parseInt(btn.dataset.a11yFont, 10);
            btn.classList.toggle('active', idx === (prefs.fontSize || 0));
        });

        // Bouton dyslexie
        const dyslexiaBtn = document.getElementById('a11y-dyslexia-btn');
        if (dyslexiaBtn) dyslexiaBtn.classList.toggle('active', !!prefs.dyslexia);

        // Bouton contraste
        const contrastBtn = document.getElementById('a11y-contrast-btn');
        if (contrastBtn) contrastBtn.classList.toggle('active', !!prefs.contrast);
    }

    // Construit la barre d'accessibilité et l'insère avant la navbar
    function buildToolbar(prefs) {
        const toolbar = document.createElement('div');
        toolbar.id = 'a11y-toolbar';
        toolbar.setAttribute('role', 'toolbar');
        toolbar.setAttribute('aria-label', 'Options d\'accessibilité');

        const container = document.createElement('div');
        container.className = 'container';

        // Label
        const label = document.createElement('span');
        label.className = 'a11y-label';
        label.textContent = 'Accessibilité';
        container.appendChild(label);

        // Séparateur
        container.appendChild(makeSep());

        // --- Taille de police ---
        const fontLabel = document.createElement('span');
        fontLabel.className = 'a11y-label';
        fontLabel.textContent = 'Texte :';
        container.appendChild(fontLabel);

        FONT_LABELS.forEach((lbl, idx) => {
            const btn = document.createElement('button');
            btn.className = 'a11y-btn';
            btn.textContent = lbl;
            btn.dataset.a11yFont = idx;
            btn.setAttribute('aria-label', idx === 0 ? 'Taille de texte normale' : `Taille de texte ${lbl}`);
            btn.title = idx === 0 ? 'Taille normale' : `Taille ${lbl}`;
            btn.style.fontSize = (0.78 + idx * 0.08) + 'rem';
            btn.addEventListener('click', function () {
                prefs.fontSize = idx;
                savePrefs(prefs);
                applyPrefs(prefs);
            });
            container.appendChild(btn);
        });

        // Séparateur
        container.appendChild(makeSep());

        // --- Dyslexie ---
        const dyslexiaBtn = document.createElement('button');
        dyslexiaBtn.className = 'a11y-btn';
        dyslexiaBtn.id = 'a11y-dyslexia-btn';
        dyslexiaBtn.innerHTML = '&#128214; Dyslexie';
        dyslexiaBtn.setAttribute('aria-pressed', !!prefs.dyslexia);
        dyslexiaBtn.title = 'Activer la police OpenDyslexic';
        dyslexiaBtn.addEventListener('click', function () {
            prefs.dyslexia = !prefs.dyslexia;
            this.setAttribute('aria-pressed', prefs.dyslexia);
            savePrefs(prefs);
            applyPrefs(prefs);
        });
        container.appendChild(dyslexiaBtn);

        // Séparateur
        container.appendChild(makeSep());

        // --- Contraste ---
        const contrastBtn = document.createElement('button');
        contrastBtn.className = 'a11y-btn';
        contrastBtn.id = 'a11y-contrast-btn';
        contrastBtn.innerHTML = '&#9681; Contraste';
        contrastBtn.setAttribute('aria-pressed', !!prefs.contrast);
        contrastBtn.title = 'Activer le mode contraste élevé';
        contrastBtn.addEventListener('click', function () {
            prefs.contrast = !prefs.contrast;
            this.setAttribute('aria-pressed', prefs.contrast);
            savePrefs(prefs);
            applyPrefs(prefs);
        });
        container.appendChild(contrastBtn);

        // Séparateur
        container.appendChild(makeSep());

        // --- Réinitialiser ---
        const resetBtn = document.createElement('button');
        resetBtn.className = 'a11y-btn';
        resetBtn.innerHTML = '&#x21BA; Réinitialiser';
        resetBtn.title = 'Remettre les paramètres par défaut';
        resetBtn.addEventListener('click', function () {
            prefs.fontSize = 0;
            prefs.dyslexia = false;
            prefs.contrast = false;
            savePrefs(prefs);
            applyPrefs(prefs);
        });
        container.appendChild(resetBtn);

        toolbar.appendChild(container);

        // Insertion avant le <body> premier enfant (avant la navbar)
        document.body.insertBefore(toolbar, document.body.firstChild);

        // Applique l'état initial des boutons
        updateButtons(prefs);
    }

    function makeSep() {
        const sep = document.createElement('div');
        sep.className = 'a11y-separator';
        sep.setAttribute('aria-hidden', 'true');
        return sep;
    }

    // Point d'entrée
    document.addEventListener('DOMContentLoaded', function () {
        const prefs = loadPrefs();
        applyPrefs(prefs);
        buildToolbar(prefs);
    });

    // Applique les classes AVANT le DOMContentLoaded pour éviter le flash
    (function earlyApply() {
        const prefs = loadPrefs();
        const body = document.body || document.documentElement;
        if (!body) return;
        FONT_SIZES.forEach(cls => { if (cls) body.classList.remove(cls); });
        const fontClass = FONT_SIZES[prefs.fontSize || 0];
        if (fontClass) body.classList.add(fontClass);
        if (prefs.dyslexia) body.classList.add('a11y-dyslexia');
        if (prefs.contrast) body.classList.add('a11y-contrast');
    })();
})();
