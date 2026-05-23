<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$auth = new KeycloakAuth();
$auth->requireAuth();

$blog = new BlogFunctions();
$user = $auth->getCurrentUser();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $blog->deletePostsByID($_GET['id']);
    header('Location: my_post.php');
    exit();
}

$posts = $blog->getPostsByAuthor($user['sub']);

include 'views/header.php';
include 'views/my_post.php';
include 'views/footer.php';
?>
