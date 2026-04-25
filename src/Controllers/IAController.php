<?php
class IAController extends Controller
{
    private GeminiService $ia;
    private Chamado $model;

    public function __construct() { $this->ia = new GeminiService(); $this->model = new Chamado(); }

    public function analisar(): void
    {
        $this->exigirLogin();
        if (!$this->isPost()) { $this->json(['erro'=>'Método não permitido.'], 405); return; }

        $input     = json_decode(file_get_contents('php://input'), true) ?? [];
        $titulo    = htmlspecialchars(trim($input['titulo']    ?? $this->post('titulo')),    ENT_QUOTES, 'UTF-8');
        $descricao = htmlspecialchars(trim($input['descricao'] ?? $this->post('descricao')), ENT_QUOTES, 'UTF-8');

        if (!$titulo || !$descricao) { $this->json(['erro'=>'Campos obrigatórios.'], 400); return; }

        $this->json(['sucesso'=>true, 'analise'=>$this->ia->analisarChamado($titulo, $descricao)]);
    }

    public function reanalisar(string $id): void
    {
        $this->exigirLogin();
        $chamado = $this->model->porId((int)$id);
        if (!$chamado) { $this->json(['erro'=>'Não encontrado.'], 404); return; }

        $a = $this->ia->analisarChamado($chamado['titulo'], $chamado['descricao']);
        $this->model->salvarIA((int)$id, $a['categoria'], $a['prioridade'], $a['analise'], $a['sugestao']);
        $this->json(['sucesso'=>true, 'analise'=>$a]);
    }
}
