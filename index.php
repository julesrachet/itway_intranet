<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Utilisation de la classe SimpleAuth au lieu de KeycloakAuth
$auth = new KeycloakAuth();
$blog = new BlogFunctions();

// Vérifie si l'utilisateur est connecté avant d'afficher le reste
$auth->requireAuth();

// Récupère les articles et l'utilisateur courant
$posts = $blog->getAllPosts();
$user = $auth->getCurrentUser();

// Inclusion des vues
include 'views/header.php';
include 'views/home.php';
include 'views/footer.php';
?>
