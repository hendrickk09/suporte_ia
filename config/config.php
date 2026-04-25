<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'suporte_ia');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('APP_NAME', 'SupporteIA');
define('APP_URL', 'http://localhost/suporte_ia/public');
define('APP_VERSION', '1.0.0');

define('GEMINI_API_KEY', 'SUA_CHAVE_AQUI');
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent');

define('SESSION_NAME', 'suporte_ia_session');

if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

spl_autoload_register(function (string $classe) {
    $mapa = [
        'Database'          => __DIR__ . '/../src/Core/Database.php',
        'Router'            => __DIR__ . '/../src/Core/Router.php',
        'Controller'        => __DIR__ . '/../src/Core/Controller.php',
        'Model'             => __DIR__ . '/../src/Core/Model.php',
        'ChamadoController' => __DIR__ . '/../src/Controllers/ChamadoController.php',
        'AuthController'    => __DIR__ . '/../src/Controllers/AuthController.php',
        'IAController'      => __DIR__ . '/../src/Controllers/IAController.php',
        'Chamado'           => __DIR__ . '/../src/Models/Chamado.php',
        'Usuario'           => __DIR__ . '/../src/Models/Usuario.php',
        'GeminiService'     => __DIR__ . '/../src/Services/GeminiService.php',
    ];
    if (isset($mapa[$classe])) require_once $mapa[$classe];
});
