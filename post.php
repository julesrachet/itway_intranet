<?php
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$blog = new BlogFunctions();
$auth = new KeycloakAuth();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$post = $blog->getPostById((int)$_GET['id']);
if (!$post) {
    header('Location: index.php');
    exit();
}

// $user disponible pour le header si connecté
$user = $auth->getCurrentUser();

include 'views/header.php';
include 'views/post.php';
include 'views/footer.php';
?>
