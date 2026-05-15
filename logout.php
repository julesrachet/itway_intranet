<?php
require_once 'includes/auth.php';

$auth = new KeycloakAuth();

session_destroy();

$logoutUrl = $auth->getLogoutUrl();
header('Location: ' . $logoutUrl);
exit();
?>
