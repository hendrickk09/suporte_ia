<?php
class Router
{
    private array $rotas = [];
    private array $protegidas = [];

    public function get(string $uri, string $ctrl, string $metodo): self
    {
        $this->rotas['GET'][$uri] = ['ctrl' => $ctrl, 'metodo' => $metodo];
        return $this;
    }

    public function post(string $uri, string $ctrl, string $metodo): self
    {
        $this->rotas['POST'][$uri] = ['ctrl' => $ctrl, 'metodo' => $metodo];
        return $this;
    }

    public function proteger(array $uris): self
    {
        $this->protegidas = array_merge($this->protegidas, $uris);
        return $this;
    }

    public function despachar(): void
    {
        $metodo = $_SERVER['REQUEST_METHOD'];
        $uri    = $this->uri();

        if (in_array($uri, $this->protegidas) && !isset($_SESSION['usuario_id'])) {
            header('Location: '.APP_URL.'/login'); exit;
        }

        $rota = $this->encontrar($metodo, $uri);
        if (!$rota) { http_response_code(404); require_once __DIR__ . '/../../views/404.php'; return; }

        $ctrl = new $rota['ctrl']();
        $m    = $rota['metodo'];
        $ctrl->$m(...($rota['params'] ?? []));
    }

    private function uri(): string
    {
        $uri  = $_SERVER['REQUEST_URI'] ?? '/';
        $base = parse_url(APP_URL, PHP_URL_PATH);
        $uri  = str_replace($base, '', $uri);
        $uri  = parse_url($uri, PHP_URL_PATH);
        $uri  = '/' . trim($uri ?? '', '/');
        return $uri === '/' ? '/' : rtrim($uri, '/');
    }

    private function encontrar(string $metodo, string $uri): ?array
    {
        if (isset($this->rotas[$metodo][$uri])) return $this->rotas[$metodo][$uri];

        foreach ($this->rotas[$metodo] ?? [] as $padrao => $rota) {
            $regex = '#^' . preg_replace('/\{[a-z_]+\}/', '([^/]+)', $padrao) . '$#';
            if (preg_match($regex, $uri, $m)) {
                array_shift($m);
                $rota['params'] = $m;
                return $rota;
            }
        }
        return null;
    }
}
