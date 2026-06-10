<?php

class AuthController extends Controller
{
    private Usuario $model;

    public function __construct()
    {
        $this->model = new Usuario();
    }

    public function login(): void
    {
        if (isset($_SESSION['usuario_id'])) {
            $this->redirecionar(Usuario::isAdmin() ? '/admin' : '/meus-chamados');
        }

        if ($this->isPost()) {
            $this->exigirCsrf();
            $this->validarLimiteLogin();

            $email = filter_var($this->post('email'), FILTER_VALIDATE_EMAIL);
            $senha = is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '';
            $usuario = $email ? $this->model->autenticar($email, $senha) : false;

            if (!$usuario) {
                $this->registrarFalhaLogin();
                $this->flash('erro', 'E-mail ou senha incorretos.');
                $this->redirecionar('/login');
            }

            unset($_SESSION['login_tentativas']);
            $this->model->iniciarSessao($usuario);
            $this->redirecionar(
                in_array($usuario['perfil'], ['admin', 'suporte'], true)
                    ? '/admin'
                    : '/meus-chamados'
            );
        }

        $this->renderizar('auth/login', ['titulo' => 'Entrar']);
    }

    public function registro(): void
    {
        if ($this->isPost()) {
            $this->exigirCsrf();

            $nome = $this->post('nome');
            $email = filter_var($this->post('email'), FILTER_VALIDATE_EMAIL);
            $senha = is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '';

            if (mb_strlen($nome) < 2 || mb_strlen($nome) > 100 || !$email) {
                $this->flash('erro', 'Informe nome e e-mail validos.');
                $this->redirecionar('/registro');
            }

            if (strlen($senha) < 10 || strlen($senha) > 72) {
                $this->flash('erro', 'A senha deve ter entre 10 e 72 caracteres.');
                $this->redirecionar('/registro');
            }

            if (!$this->model->registrar($nome, $email, $senha)) {
                $this->flash('erro', 'E-mail ja cadastrado.');
                $this->redirecionar('/registro');
            }

            $this->flash('sucesso', 'Conta criada com sucesso. Faca login.');
            $this->redirecionar('/login');
        }

        $this->renderizar('auth/registro', ['titulo' => 'Criar Conta']);
    }

    public function logout(): void
    {
        if (!$this->isPost()) {
            http_response_code(405);
            return;
        }

        $this->exigirCsrf();
        $this->model->encerrarSessao();
        $this->redirecionar('/login');
    }

    public function privacidade(): void
    {
        $this->renderizar('privacidade', ['titulo' => 'Privacidade']);
    }

    private function validarLimiteLogin(): void
    {
        $dados = $_SESSION['login_tentativas'] ?? ['quantidade' => 0, 'inicio' => time()];
        if (time() - $dados['inicio'] > 900) {
            $_SESSION['login_tentativas'] = ['quantidade' => 0, 'inicio' => time()];
            return;
        }
        if ($dados['quantidade'] >= 5) {
            http_response_code(429);
            exit('Muitas tentativas. Aguarde alguns minutos.');
        }
    }

    private function registrarFalhaLogin(): void
    {
        $dados = $_SESSION['login_tentativas'] ?? ['quantidade' => 0, 'inicio' => time()];
        $dados['quantidade']++;
        $_SESSION['login_tentativas'] = $dados;
    }
}
