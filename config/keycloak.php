<?php
return [
'realm' => 'master',
    'auth-server-url' => 'https://sso.itway.fr',
    'ssl-required' => 'external',
    'resource' => 'INTRANET',
    'credentials' => [
        'secret' => 'IijH3TGOwsOoDKhsblm1nrWaOdB0Yj3w'
    ],
    'confidential-port' => 0,
    'redirect_uri' => 'https://intranet.itway.local/callback.php',
    'logout_uri' => 'https://intranet.itway.local/login.php'
];
?>
