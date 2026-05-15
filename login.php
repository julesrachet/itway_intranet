<?php
require_once 'includes/auth.php';

$auth = new KeycloakAuth();

if ($auth->isAuthenticated()) {
    header('Location: index.php');
    exit();
}

$loginUrl = $auth->getLoginUrl();
header('Location: ' . $loginUrl);
exit();
?>
