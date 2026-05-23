# 🏢 Itway Intranet

Intranet de l'entreprise Itway — plateforme de blog interne avec authentification SSO via Keycloak.

## 📋 Fonctionnalités

- Authentification SSO via **Keycloak** (OAuth2 / OpenID Connect)
- Consultation des articles publiés par les collaborateurs
- Création d'articles
- Suppression de ses propres articles
- Interface responsive avec **Bootstrap 5**

## 🛠️ Stack technique

| Couche | Technologie |
|---|---|
| Backend | PHP 8 |
| Base de données | MariaDB |
| Authentification | Keycloak (SSO) |
| Frontend | Bootstrap 5, Bootstrap Icons |
| Serveur web | Apache |

## 📁 Structure du projet

```
itway_intranet/
├── assets/
│   ├── css/
│   │   ├── bootstrap.min.css
│   │   └── style.css
│   └── js/
│       ├── bootstrap.bundle.min.js
│       └── main.js
├── config/
│   ├── database.php       # Connexion PDO MariaDB
│   └── keycloak.php       # Configuration Keycloak
├── includes/
│   ├── auth.php           # Classe KeycloakAuth
│   └── functions.php      # Classe BlogFunctions
├── views/
│   ├── header.php
│   ├── footer.php
│   ├── home.php
│   ├── post.php
│   ├── my_post.php
│   └── create_post.php
├── index.php              # Liste des articles
├── post.php               # Détail d'un article
├── my_post.php            # Mes articles
├── create_post.php        # Créer un article
├── login.php              # Redirection vers Keycloak
├── logout.php             # Déconnexion
└── callback.php           # Callback OAuth2
```

## ⚙️ Installation

### Prérequis

- PHP 8+
- MariaDB
- Apache
- Un serveur Keycloak configuré

### 1. Cloner le dépôt

```bash
git clone https://github.com/julesrachet/itway_intranet.git
cd itway_intranet
```

### 2. Base de données

Créer la base et la table :

```sql
CREATE DATABASE blog_db;
USE blog_db;

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author_id VARCHAR(36) NOT NULL,
    author_name VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL
);

CREATE USER 'blog_user'@'localhost' IDENTIFIED BY 'ChangeMe2024!';
GRANT ALL PRIVILEGES ON blog_db.* TO 'blog_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Configuration

Éditer `config/keycloak.php` avec les infos de votre realm :

```php
return [
    'realm'           => 'votre-realm',
    'auth-server-url' => 'https://votre-keycloak.fr',
    'resource'        => 'votre-client-id',
    'credentials'     => ['secret' => 'votre-secret'],
    'redirect_uri'    => 'https://votre-domaine.fr/callback.php',
    'logout_uri'      => 'https://votre-domaine.fr/login.php'
];
```

Éditer `config/database.php` avec vos identifiants MariaDB.

### 4. Bootstrap en local (requis si CSP stricte)

```bash
curl -o assets/css/bootstrap.min.css https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css
curl -o assets/js/bootstrap.bundle.min.js https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js
```

## 🔐 Authentification

Le flux d'authentification suit le standard **Authorization Code Flow** d'OpenID Connect :

1. L'utilisateur accède à `login.php`
2. Redirection vers Keycloak
3. Après connexion, Keycloak redirige vers `callback.php` avec un `code`
4. Le code est échangé contre un `access_token`
5. Les infos utilisateur (`sub`, `name`) sont stockées en session

## 🚀 Déploiement

L'application est accessible à l'adresse :
```
https://intranet.itway.local
```

## 👤 Auteur

**Jules Rachet** — [github.com/julesrachet](https://github.com/julesrachet)
