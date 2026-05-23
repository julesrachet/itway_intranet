<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$auth = new KeycloakAuth();
$auth->requireAuth(); // ✅

$blog = new BlogFunctions();
$user = $auth->getCurrentUser(); // ✅

if ($_POST) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (!empty($title) && !empty($content)) {
        if ($blog->createPost($title, $content, $user['sub'], $user['name'])) {
            header('Location: index.php');
            exit();
        } else {
            $error = "Erreur lors de la création du post.";
        }
    } else {
        $error = "Le titre et le contenu sont obligatoires.";
    }
}

include 'views/header.php';
include 'views/create_post.php';
include 'views/footer.php';
?>
