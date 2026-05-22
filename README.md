# ITWay Intranet

Intranet interne d'ITWay : un mini-portail de publication d'articles (type blog d'entreprise) écrit en **PHP** pur, adossé à une base **MySQL** et protégé par une authentification **SSO Keycloak (OpenID Connect)**.

L'utilisateur se connecte via le SSO `sso.itway.fr`, peut consulter les actualités publiées par les collaborateurs, en rédiger de nouvelles, consulter la liste de ses propres articles et les supprimer.

---

## Sommaire

- [Architecture générale](#architecture-générale)
- [Stack technique](#stack-technique)
- [Arborescence du projet](#arborescence-du-projet)
- [Fonctionnement détaillé de chaque fichier](#fonctionnement-détaillé-de-chaque-fichier)
  - [Fichiers PHP racine](#fichiers-php-racine)
  - [Dossier `config/`](#dossier-config)
  - [Dossier `includes/`](#dossier-includes)
  - [Dossier `views/`](#dossier-views)
  - [Dossier `assets/`](#dossier-assets)
- [Flux d'authentification Keycloak](#flux-dauthentification-keycloak)
- [Schéma de base de données](#schéma-de-base-de-données)
- [Installation et configuration](#installation-et-configuration)
- [Points d'attention et améliorations possibles](#points-dattention-et-améliorations-possibles)

---

## Architecture générale

L'application suit un découpage très classique de type MVC simplifié, sans framework :

- Les **contrôleurs** sont les fichiers PHP de la racine (`index.php`, `create_post.php`, `my_post.php`, etc.). Ils orchestrent l'auth, l'accès aux données, puis incluent une vue.
- Le **modèle** est centralisé dans `includes/functions.php` via la classe `BlogFunctions` qui parle à la base via PDO.
- L'**authentification** est isolée dans `includes/auth.php` via la classe `KeycloakAuth` qui implémente le flow OpenID Connect "Authorization Code".
- Les **vues** dans `views/` sont des templates HTML/PHP inclus par les contrôleurs ; `header.php` et `footer.php` encadrent toutes les pages.
- La **configuration** (BDD et Keycloak) est isolée dans `config/`.

Chaque page suit le même schéma :

```
require auth + functions  ->  instancier KeycloakAuth & BlogFunctions
        |
        v
   requireAuth()      ->   redirection vers /login.php si non connecté
        |
        v
  traitement métier (POST, GET, lecture, écriture BDD)
        |
        v
  include views/header.php + view spécifique + views/footer.php
```

---

## Stack technique

- **Langage** : PHP (89,8 % du code) + CSS (10,2 %)
- **Base de données** : MySQL / MariaDB (accès via PDO)
- **Auth** : Keycloak (realm `master`, client `INTRANET`) via OpenID Connect
- **Front** : Bootstrap 5.3 + Bootstrap Icons 1.11 (chargés via CDN)
- **JS** : Vanilla JS pour la barre d'accessibilité (taille de police, mode dyslexie, contraste élevé)
- **Sessions** : sessions PHP natives, cookie `HttpOnly`, `Secure`, `SameSite=Lax`

---

## Arborescence du projet

```
itway_intranet/
├── assets/
│   ├── css/
│   │   ├── main.css         # Styles de base (body, card, navbar)
│   │   └── style.css        # Fichier référencé dans header.php (voir note plus bas)
│   └── js/
│       └── main.js          # Fichier JS vide (réservé pour usage futur)
│
├── config/
│   ├── database.php         # Classe Database (PDO + credentials MySQL)
│   └── keycloak.php         # Tableau de configuration OIDC Keycloak
│
├── includes/
│   ├── auth.php             # Classe KeycloakAuth (flow OIDC complet)
│   └── functions.php        # Classe BlogFunctions (CRUD articles)
│
├── views/
│   ├── header.php           # En-tête HTML, barre d'a11y, navbar
│   ├── footer.php           # Pied de page + scripts d'accessibilité
│   ├── home.php             # Liste des articles (page d'accueil)
│   ├── create_post.php      # Formulaire de création d'article
│   ├── my_post.php          # Liste des articles de l'utilisateur + modal de suppression
│   ├── post.php             # Affichage d'un article complet
│   └── step.deb             # Paquet Debian de step-cli (voir avertissement)
│
├── index.php                # Page d'accueil : liste des articles
├── login.php                # Lance le flux de connexion Keycloak
├── logout.php               # Détruit la session et appelle le logout Keycloak
├── callback.php             # Endpoint de redirection OIDC (échange code -> token)
├── callback.php.bak         # Ancienne version sauvegardée de callback.php
├── create_post.php          # Création d'un nouvel article (formulaire + POST)
├── my_post.php              # Page « Mes articles » + suppression
└── post.php                 # Affichage détaillé d'un article par son id
```

---

## Fonctionnement détaillé de chaque fichier

### Fichiers PHP racine

#### `index.php`

Page d'accueil de l'intranet. Elle :

1. Charge `includes/auth.php` et `includes/functions.php`.
2. Instancie `KeycloakAuth` et `BlogFunctions`.
3. Appelle `requireAuth()` : si l'utilisateur n'est pas authentifié, redirection automatique vers `login.php`.
4. Récupère tous les articles avec `getAllPosts()` (10 derniers par défaut, triés par date décroissante) et l'utilisateur courant via `getCurrentUser()`.
5. Inclut successivement `views/header.php`, `views/home.php` et `views/footer.php` pour afficher la page.

C'est donc le point d'entrée par défaut et le seul à lister toutes les actualités.

#### `login.php`

Initie le flux de connexion :

1. Si l'utilisateur est déjà authentifié (`isAuthenticated()` retourne `true`), il est redirigé vers `index.php`.
2. Sinon, on appelle `getLoginUrl()` qui construit l'URL d'autorisation Keycloak (`/realms/master/protocol/openid-connect/auth`) avec les paramètres OIDC (`client_id`, `redirect_uri`, `response_type=code`, `scope=openid profile email`) et on redirige le navigateur dessus.

L'utilisateur arrive alors sur la mire de connexion Keycloak.

#### `logout.php`

Termine la session :

1. Détruit la session PHP locale (`session_destroy()`), ce qui supprime `$_SESSION['user']` et `$_SESSION['access_token']`.
2. Construit l'URL de logout Keycloak via `getLogoutUrl()` (`/realms/master/protocol/openid-connect/logout`) avec un `redirect_uri` pointant vers `login.php`.
3. Redirige l'utilisateur vers Keycloak pour invalider sa session côté SSO.

#### `callback.php`

Endpoint de redirection OAuth déclaré côté Keycloak (`https://intranet.itway.local/callback.php`). Après une connexion réussie, Keycloak renvoie l'utilisateur ici avec un paramètre `?code=...`. Le fichier :

1. Récupère le `code` d'autorisation.
2. L'échange contre un `access_token` via `exchangeCodeForToken($code)`, qui fait un POST sur `/protocol/openid-connect/token`.
3. Si un `access_token` a bien été obtenu, appelle `getUserInfo()` sur l'endpoint `/userinfo` pour récupérer le profil utilisateur (nom, email, `sub`, etc.).
4. Stocke `access_token` et `user` en session PHP.
5. Redirige vers `index.php`.

En cas d'échec, redirige vers `login.php`.

#### `callback.php.bak`

Copie antérieure de `callback.php`. Le contenu est quasi identique, à de simples différences de formatage près. C'est un fichier de sauvegarde laissé dans le dépôt qui pourrait être supprimé en production.

#### `create_post.php`

Page de création d'article :

1. Requiert l'authentification.
2. Si la requête est en POST, lit `title` et `content`, les trim et vérifie qu'ils ne sont pas vides.
3. Appelle `BlogFunctions::createPost()` avec le titre, le contenu, l'identifiant Keycloak de l'utilisateur (`$user['sub']`) et son nom (`$user['name']`).
4. En cas de succès, redirige vers `index.php`. Sinon stocke un message dans `$error` qui sera affiché par la vue.
5. Inclut `views/header.php`, `views/create_post.php` (le formulaire), puis `views/footer.php`.

#### `my_post.php`

Page « Mes articles » avec gestion de la suppression :

1. Requiert l'authentification.
2. Si un paramètre `?id=...` numérique est passé en GET, appelle `deletePostsByID($id, $user['sub'])`. Le second argument garantit côté SQL que **seul le propriétaire** peut supprimer son article (`WHERE id = :id AND author_id = :author_id`). En cas de succès, redirection vers `my_post.php` ; sinon `$error` est défini.
3. Récupère ensuite uniquement les articles de l'utilisateur courant via `getPostsByAuthor($user['sub'])`.
4. Inclut les vues header, my_post (liste + modal Bootstrap de confirmation) et footer.

#### `post.php`

Affichage d'un article individuel :

1. Vérifie que le paramètre `?id=...` est bien numérique, sinon redirige vers `index.php`.
2. Récupère l'article via `getPostById((int)$_GET['id'])`. Si rien n'est trouvé, redirige vers `index.php`.
3. Récupère l'utilisateur courant (sans `requireAuth()` ici, mais en pratique le header s'attend à un utilisateur connecté).
4. Inclut header, vue `post.php` (rendu de l'article complet) et footer.

---

### Dossier `config/`

#### `config/database.php`

Définit la classe `Database` :

- Propriétés privées : `host`, `db_name`, `username`, `password` (en clair dans le fichier).
- Méthode `getConnection()` qui crée un objet `PDO` MySQL en `utf8mb4`, configure `ATTR_ERRMODE` sur `EXCEPTION`, et renvoie la connexion.
- En cas d'erreur PDO, log l'erreur via `error_log()` et arrête le script avec un message générique pour ne pas exposer la stack.

Ce fichier est instancié par `BlogFunctions` au démarrage de chaque page nécessitant la BDD.

#### `config/keycloak.php`

Retourne un tableau associatif PHP contenant la configuration OIDC du client Keycloak `INTRANET` :

- `realm` : `master`
- `auth-server-url` : `https://sso.itway.fr`
- `resource` (client_id) : `INTRANET`
- `credentials.secret` : secret client (en clair dans le dépôt — à externaliser.
- `redirect_uri` : `https://intranet.itway.local/callback.php`
- `logout_uri` : `https://intranet.itway.local/login.php`

Il est chargé par `KeycloakAuth` dans son constructeur.

---

### Dossier `includes/`

#### `includes/auth.php`

Cœur de l'authentification. Avant toute déclaration, le fichier :

- Force des cookies de session sécurisés (`session.cookie_secure=1`, `httponly=1`, `samesite=Lax`).
- Démarre la session si elle n'est pas déjà ouverte.

Puis définit la classe `KeycloakAuth` :

- **`__construct()`** : charge `config/keycloak.php` et compose `baseUrl = auth-server-url + /realms/<realm>`.
- **`getLoginUrl()`** : construit l'URL d'autorisation OIDC avec `response_type=code` et `scope=openid profile email`.
- **`getLogoutUrl()`** : construit l'URL de déconnexion Keycloak avec le `redirect_uri` post-logout.
- **`httpPost($url, $data)`** : helper cURL pour les appels POST `application/x-www-form-urlencoded` retournant du JSON décodé. `SSL_VERIFYPEER` et `SSL_VERIFYHOST` sont désactivés (autorité de certification interne non reconnue).
- **`httpGet($url, $token)`** : helper cURL GET avec en-tête `Authorization: Bearer <token>`.
- **`exchangeCodeForToken($code)`** : POST sur `/protocol/openid-connect/token` avec `grant_type=authorization_code`, le `client_id`, le `client_secret`, le `code` reçu et l'URI de redirection ; renvoie le JSON contenant l'`access_token`.
- **`getUserInfo($accessToken)`** : GET sur `/protocol/openid-connect/userinfo` avec le bearer token ; renvoie le profil utilisateur (claims OIDC).
- **`isAuthenticated()`** : vérifie la présence simultanée de `$_SESSION['user']` et `$_SESSION['access_token']`.
- **`requireAuth()`** : si non authentifié, redirige vers `login.php` et termine le script.
- **`getCurrentUser()`** : retourne `$_SESSION['user']` ou `null`.

#### `includes/functions.php`

Définit la classe `BlogFunctions`. Le constructeur instancie `Database` et stocke la connexion PDO. Méthodes :

- **`createPost($title, $content, $authorId, $authorName)`** : `INSERT` paramétré dans la table `posts` avec `created_at = NOW()`. Retourne `true`/`false`.
- **`getAllPosts($limit = 10, $offset = 0)`** : `SELECT` paginé trié par `created_at DESC`. Retourne un tableau associatif.
- **`deletePostsByID($id, $authorId)`** : `DELETE` avec **double condition** `id = :id AND author_id = :author_id`. Renvoie `true` uniquement si `rowCount() > 0`, ce qui empêche un utilisateur de supprimer les articles d'un autre même s'il forge l'URL.
- **`getPostById($id)`** : `SELECT` d'un article par son id.
- **`getPostsByAuthor($authorId)`** : tous les articles d'un auteur donné, triés par date.

Toutes les requêtes utilisent des **prepared statements PDO**, ce qui protège contre l'injection SQL.

---

### Dossier `views/`

#### `views/header.php`

En-tête HTML inclus en haut de chaque page :

- `<!DOCTYPE html>` + balises `<head>` avec Bootstrap 5.3 et Bootstrap Icons depuis le CDN jsDelivr, plus `/assets/css/style.css`.
- **Barre d'accessibilité** (`<div class="a11y-bar">`) avec 5 boutons : dyslexie (police OpenDyslexic), agrandir/réduire le texte, contraste élevé, réinitialiser. Les boutons sont contrôlés par le script JS du footer.
- **Navbar** Bootstrap avec le logo « ITWay Intranet ». Si `$user` est défini, affiche les liens Accueil / Publier / Mes articles / nom de l'utilisateur / Déconnexion. Sinon, un seul lien Connexion.
- Ouvre `<div class="main-container">` qui sera fermée par le footer.

#### `views/footer.php`

Pied de page :

- Ferme la `main-container`.
- Affiche le copyright (`&copy; <année> ITWay`).
- Charge Bootstrap JS Bundle et `/assets/js/main.js`.
- Contient un script JS inline qui gère la **barre d'accessibilité** : taille de police entre 80 % et 140 % avec persistance dans `localStorage` (préfixe `a11y_`), classes `dyslexia-mode` et `high-contrast` ajoutées/retirées sur le `<body>`, et un bouton « Réinitialiser » qui remet les trois réglages par défaut.

#### `views/home.php`

Vue de la page d'accueil incluse par `index.php`. Affiche un titre « Actualités », un bouton « Publier un article » si l'utilisateur est connecté, puis boucle sur `$posts` pour générer des cartes (`.post-card`) avec titre lien, badge auteur, date formatée `d/m/Y à H:i`, extrait de 220 caractères du contenu (`htmlspecialchars` + `nl2br`) et un bouton « Lire l'article » menant vers `post.php?id=...`. Si aucun article, affiche un état vide avec invitation à publier.

#### `views/create_post.php`

Formulaire de publication inclus par `create_post.php`. Affiche éventuellement un message d'erreur (`$error`), puis un formulaire POST avec deux champs : `title` (input texte requis) et `content` (textarea 12 lignes requise). Les anciennes valeurs sont réinjectées si la soumission a échoué. Deux boutons : « Publier » et « Annuler » (retour à `index.php`).

#### `views/my_post.php`

Vue « Mes articles » incluse par `my_post.php`. Structure proche de `home.php` mais :

- N'affiche que les articles de l'utilisateur courant.
- Ajoute un bouton **Supprimer** sur chaque carte qui ouvre une **modale Bootstrap** de confirmation (`#deleteModal`).
- Le script JS `confirmDelete(id, title)` injecte le titre dans la modale et configure le lien de confirmation vers `my_post.php?id=<id>`, ce qui déclenchera la suppression dans le contrôleur.

#### `views/post.php`

Vue d'un article individuel incluse par `post.php`. Affiche un bouton « Retour aux actualités », puis le titre `<h1>`, les métadonnées (auteur + date au format `d F Y à H:i`) et le contenu complet de l'article (`htmlspecialchars` + `nl2br` pour préserver les sauts de ligne sans permettre d'injection HTML).

#### `views/step.deb`

**Fichier qui n'a rien à faire ici.** Il s'agit d'un paquet Debian de `step-cli` version 0.30.2 (14 Mo) — l'outil PKI de Smallstep utilisé pour générer des certificats. Il a probablement été déposé par erreur lors d'une manipulation et devrait être retiré du dépôt (cf. `.gitignore`).

---

### Dossier `assets/`

#### `assets/css/main.css`

Petite feuille de styles d'appoint (30 lignes) définissant un fond gris clair sur `body`, des ombres pour `.card`, une `.navbar-brand` en gras et quelques styles de transition. **Note : ce fichier n'est pas inclus** par `header.php` (qui référence `style.css`), il est donc actuellement inutilisé.

#### `assets/css/style.css`

**Attention : le contenu de ce fichier est manifestement incorrect.** Au lieu d'un fichier CSS, il contient en réalité une copie de `views/footer.php` (HTML + PHP + script JS d'accessibilité). C'est très probablement le résultat d'une mauvaise copie de fichier. Comme `header.php` référence `/assets/css/style.css`, le navigateur essaie de charger ce contenu en tant que CSS et l'ignore. Le rendu visuel actuel ne repose donc que sur Bootstrap et les styles inline référencés (`var(--accent)`, `var(--border)`, `var(--danger)`) qui ne sont **définis nulle part** — ces variables CSS devraient être déclarées ici.

#### `assets/js/main.js`

Fichier JavaScript **vide**. Référencé par `footer.php` (`<script src="/assets/js/main.js">`) mais ne contient aucun code. Réservé pour du JS spécifique au projet si besoin (toute la logique d'accessibilité est aujourd'hui inline dans `footer.php`).

---

## Flux d'authentification Keycloak

```
1. Utilisateur ouvre index.php
   -> requireAuth() détecte qu'il n'est pas authentifié
   -> redirection 302 vers login.php

2. login.php construit l'URL d'autorisation Keycloak
   -> redirection 302 vers https://sso.itway.fr/realms/master/protocol/openid-connect/auth?...

3. Keycloak affiche la mire, l'utilisateur saisit ses identifiants

4. Keycloak redirige le navigateur vers redirect_uri
   -> https://intranet.itway.local/callback.php?code=AUTH_CODE

5. callback.php POST sur /token avec code + client_id + client_secret
   -> reçoit { access_token, id_token, refresh_token, ... }

6. callback.php GET sur /userinfo avec Bearer access_token
   -> reçoit { sub, name, email, preferred_username, ... }

7. Stockage en session : $_SESSION['access_token'], $_SESSION['user']
   -> redirection 302 vers index.php (l'utilisateur est désormais connecté)
```

Côté logout, `logout.php` détruit la session PHP puis redirige vers `/protocol/openid-connect/logout` de Keycloak pour terminer aussi la session SSO.

---

## Schéma de base de données

La structure n'est pas fournie dans le dépôt mais peut être déduite des requêtes SQL de `BlogFunctions`. La base `blog_db` contient au minimum une table :

```sql
CREATE TABLE posts (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    content     TEXT NOT NULL,
    author_id   VARCHAR(255) NOT NULL,  -- le 'sub' Keycloak (UUID)
    author_name VARCHAR(255) NOT NULL,
    created_at  DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

`author_id` correspond au claim `sub` de l'OIDC (UUID Keycloak de l'utilisateur), ce qui rend l'identité indépendante d'un éventuel changement de nom.

---

## Installation et configuration

### Prérequis

- PHP 7.4+ avec extensions `pdo_mysql`, `curl`, `session`
- MySQL ou MariaDB
- Un serveur web (Apache ou Nginx) avec un VirtualHost pointant vers la racine du projet et accessible en HTTPS (le cookie de session est `Secure`)
- Une instance Keycloak avec un realm et un client OIDC configurés

### Étapes

1. Cloner le dépôt à la racine du DocumentRoot du VirtualHost.
2. Créer la base et l'utilisateur MySQL :
   ```sql
   CREATE DATABASE blog_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'blog_user'@'localhost' IDENTIFIED BY '<motdepasse>';
   GRANT ALL PRIVILEGES ON blog_db.* TO 'blog_user'@'localhost';
   FLUSH PRIVILEGES;
   ```
3. Créer la table `posts` (cf. [Schéma](#schéma-de-base-de-données)).
4. Adapter `config/database.php` (host, db_name, username, password).
5. Adapter `config/keycloak.php` (realm, URL du serveur SSO, client_id, secret, URI de redirection et de logout — ces deux dernières doivent être déclarées comme valides côté Keycloak).
6. Dans Keycloak, créer le client `INTRANET` en `confidential`, activer le `Standard Flow`, ajouter le `Valid Redirect URI` (par ex. `https://intranet.itway.local/callback.php`) et le `Valid Post Logout Redirect URI`.
7. Accéder à `https://intranet.itway.local/` : la redirection vers Keycloak doit se déclencher automatiquement.
