<?php
require_once __DIR__ . '/../config/config.php';

$router = new Router();

$router->get('/login',    'AuthController', 'login')
       ->post('/login',   'AuthController', 'login')
       ->get('/registro', 'AuthController', 'registro')
       ->post('/registro','AuthController', 'registro')
       ->get('/logout',   'AuthController', 'logout');

$router->get('/',                        'ChamadoController', 'index')
       ->get('/chamados',                'ChamadoController', 'index')
       ->get('/chamados/criar',          'ChamadoController', 'criar')
       ->post('/chamados/criar',         'ChamadoController', 'criar')
       ->get('/chamados/{id}',           'ChamadoController', 'detalhar')
       ->post('/chamados/{id}/comentar', 'ChamadoController', 'comentar')
       ->post('/chamados/{id}/status',   'ChamadoController', 'atualizarStatus');

$router->post('/ia/analisar',            'IAController', 'analisar')
       ->post('/ia/reanalisar/{id}',     'IAController', 'reanalisar');

$router->proteger(['/', '/chamados', '/chamados/criar']);

$router->despachar();
