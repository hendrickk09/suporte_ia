<?php
$configLocal = [];
$arquivoConfigLocal = __DIR__ . '/config.local.php';
if (is_file($arquivoConfigLocal)) {
    $configLocal = require $arquivoConfigLocal;
    if (!is_array($configLocal)) {
        throw new RuntimeException('config.local.php deve retornar um array.');
    }
}

$config = static function (string $chave, string $padrao = '') use ($configLocal): string {
    $ambiente = getenv($chave);
    if ($ambiente !== false && $ambiente !== '') {
        return $ambiente;
    }
    return (string) ($configLocal[$chave] ?? $padrao);
};

define('DB_HOST', $config('DB_HOST', 'localhost'));
define('DB_PORT', $config('DB_PORT', '3306'));
define('DB_NAME', $config('DB_NAME', 'suporte_ia'));
define('DB_USER', $config('DB_USER', 'root'));
define('DB_PASS', $config('DB_PASS'));
define('DB_CHARSET', 'utf8mb4');

define('APP_NAME', 'SupporteIA');
define('APP_URL', rtrim($config('APP_URL', 'http://localhost/suporte_ia/public'), '/'));
define('APP_VERSION', '1.1.2');

define('GEMINI_API_KEY', $config('GEMINI_API_KEY', 'SUA_CHAVE_AQUI'));
define('GEMINI_MODEL', $config('GEMINI_MODEL', 'gemini-2.5-flash-lite'));
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/' . GEMINI_MODEL . ':generateContent');
define('SESSION_DRIVER', $config('SESSION_DRIVER', 'files'));

spl_autoload_register(function (string $classe) {
    $mapa = [
        'Database'               => __DIR__ . '/../src/Core/Database.php',
        'DatabaseSessionHandler' => __DIR__ . '/../src/Core/DatabaseSessionHandler.php',
        'Router'                 => __DIR__ . '/../src/Core/Router.php',
        'Controller'             => __DIR__ . '/../src/Core/Controller.php',
        'Model'                  => __DIR__ . '/../src/Core/Model.php',
        'AuthController'         => __DIR__ . '/../src/Controllers/AuthController.php',
        'ChamadoController'      => __DIR__ . '/../src/Controllers/ChamadoController.php',
        'AdminController'        => __DIR__ . '/../src/Controllers/AdminController.php',
        'IAController'           => __DIR__ . '/../src/Controllers/IAController.php',
        'Chamado'                => __DIR__ . '/../src/Models/Chamado.php',
        'Usuario'                => __DIR__ . '/../src/Models/Usuario.php',
        'GeminiService'          => __DIR__ . '/../src/Services/GeminiService.php',
    ];
    if (isset($mapa[$classe])) require_once $mapa[$classe];
});

if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('suporte_ia_sess');

    if (SESSION_DRIVER === 'database') {
        session_set_save_handler(new DatabaseSessionHandler(), true);
    } else {
        $savePath = (string) ini_get('session.save_path');
        if ($savePath === '' || !is_dir($savePath) || !is_writable($savePath)) {
            session_save_path(sys_get_temp_dir());
        }
    }

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}
