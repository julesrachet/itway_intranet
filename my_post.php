<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$auth = new KeycloakAuth();
$auth->requireAuth();

$blog = new BlogFunctions();
$user = $auth->getCurrentUser();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // on passe l'authorId pour que seul le propriétaire puisse supprimer
    if ($blog->deletePostsByID((int)$_GET['id'], $user['sub'])) {
        header('Location: my_post.php');
        exit();
    } else {
        $error = "Suppression impossible : ce post ne vous appartient pas ou n'existe pas.";
    }
}

$posts = $blog->getPostsByAuthor($user['sub']);

include 'views/header.php';
include 'views/my_post.php';
include 'views/footer.php';
?>
