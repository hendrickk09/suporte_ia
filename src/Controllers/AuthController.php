<?php
class AuthController extends Controller
{
    private Usuario $model;
    public function __construct() { $this->model = new Usuario(); }

    public function login(): void
    {
        if (isset($_SESSION['usuario_id'])) { $this->redirecionar('/chamados'); return; }

        if ($this->isPost()) {
            $u = $this->model->autenticar($this->post('email'), $_POST['senha'] ?? '');
            if (!$u) { $this->flash('erro', 'E-mail ou senha incorretos.'); $this->redirecionar('/login'); return; }
            $this->model->iniciarSessao($u);
            $this->redirecionar('/chamados');
            return;
        }
        $this->renderizar('auth/login', ['titulo' => 'Entrar']);
    }

    public function registro(): void
    {
        if ($this->isPost()) {
            $senha = $_POST['senha'] ?? '';
            if (strlen($senha) < 6) { $this->flash('erro', 'Senha mínima: 6 caracteres.'); $this->redirecionar('/registro'); return; }
            $id = $this->model->registrar($this->post('nome'), $this->post('email'), $senha);
            if (!$id) { $this->flash('erro', 'E-mail já cadastrado.'); $this->redirecionar('/registro'); return; }
            $this->flash('sucesso', 'Conta criada! Faça login.');
            $this->redirecionar('/login');
            return;
        }
        $this->renderizar('auth/registro', ['titulo' => 'Criar Conta']);
    }

    public function logout(): void { $this->model->encerrarSessao(); $this->redirecionar('/login'); }
}
