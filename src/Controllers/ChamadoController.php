<?php
class ChamadoController extends Controller
{
    private Chamado $model;
    private GeminiService $ia;

    public function __construct()
    {
        $this->model = new Chamado();
        $this->ia    = new GeminiService();
    }

    public function index(): void
    {
        $this->exigirLogin();
        $uid      = Usuario::perfil() === 'usuario' ? Usuario::idLogado() : null;
        $chamados = $this->model->todosComUsuario($uid);
        $contagem = $this->model->contarPorStatus();
        $this->renderizar('chamados/index', compact('chamados', 'contagem') + ['titulo' => 'Painel de Chamados']);
    }

    public function criar(): void
    {
        $this->exigirLogin();
        if ($this->isPost()) {
            $titulo    = $this->post('titulo');
            $descricao = $this->post('descricao');
            if (!$titulo || !$descricao) { $this->flash('erro', 'Preencha todos os campos.'); $this->redirecionar('/chamados/criar'); return; }

            $id     = $this->model->inserir(['titulo'=>$titulo,'descricao'=>$descricao,'usuario_id'=>Usuario::idLogado(),'status'=>'aberto']);
            $analise= $this->ia->analisarChamado($titulo, $descricao);
            $this->model->salvarIA($id, $analise['categoria'], $analise['prioridade'], $analise['analise'], $analise['sugestao']);

            $this->flash('sucesso', "Chamado #$id criado e analisado pela IA!");
            $this->redirecionar('/chamados/'.$id);
            return;
        }
        $this->renderizar('chamados/criar', ['titulo' => 'Novo Chamado']);
    }

    public function detalhar(string $id): void
    {
        $this->exigirLogin();
        $chamado     = $this->model->detalhe((int)$id);
        if (!$chamado) { $this->flash('erro', 'Chamado não encontrado.'); $this->redirecionar('/chamados'); return; }
        $comentarios = $this->model->comentarios((int)$id);
        $this->renderizar('chamados/detalhar', compact('chamado','comentarios') + ['titulo' => "Chamado #$id"]);
    }

    public function comentar(string $id): void
    {
        $this->exigirLogin();
        $txt = $this->post('texto');
        if ($txt) $this->model->addComentario((int)$id, Usuario::idLogado(), $txt);
        $this->redirecionar('/chamados/'.$id);
    }

    public function atualizarStatus(string $id): void
    {
        $this->exigirLogin();
        $status = $this->post('status');
        if (in_array($status, ['aberto','em_andamento','resolvido','fechado'])) {
            $this->model->atualizar((int)$id, ['status'=>$status]);
            $this->flash('sucesso', 'Status atualizado.');
        }
        $this->redirecionar('/chamados/'.$id);
    }
}
