<?php
require_once __DIR__ . '/../config/config.php';

$router = new Router();

// Auth
$router->get('/login',    'AuthController', 'login')
       ->post('/login',   'AuthController', 'login')
       ->get('/registro', 'AuthController', 'registro')
       ->post('/registro','AuthController', 'registro')
       ->get('/privacidade','AuthController', 'privacidade')
       ->post('/logout',  'AuthController', 'logout');

// Usuário comum
$router->get('/',                           'ChamadoController', 'meusChamados')
       ->get('/meus-chamados',              'ChamadoController', 'meusChamados')
       ->get('/chamados/criar',             'ChamadoController', 'criar')
       ->post('/chamados/criar',            'ChamadoController', 'criar')
       ->get('/meus-chamados/{id}',         'ChamadoController', 'detalhar')
       ->post('/meus-chamados/{id}/comentar','ChamadoController','comentar');

// Admin / Suporte
$router->get('/admin',                      'AdminController', 'index')
       ->get('/admin/chamado/{id}',         'AdminController', 'detalhar')
       ->post('/admin/chamado/{id}/comentar','AdminController','comentar')
       ->post('/admin/chamado/{id}/status', 'AdminController', 'atualizarStatus')
       ->post('/admin/chamado/{id}/atribuir','AdminController', 'atribuir');

// IA (AJAX)
$router->post('/ia/analisar',               'IAController', 'analisar')
       ->post('/ia/reanalisar/{id}',        'IAController', 'reanalisar');

$router->proteger(['/meus-chamados','/chamados/criar','/admin']);

$router->despachar();
