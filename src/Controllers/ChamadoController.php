<?php

class ChamadoController extends Controller
{
    private Chamado $chamado;
    private GeminiService $ia;

    public function __construct()
    {
        $this->chamado = new Chamado();
        $this->ia = new GeminiService();
    }

    public function meusChamados(): void
    {
        $this->exigirLogin();
        if ($this->isAdmin()) {
            $this->redirecionar('/admin');
        }

        $chamados = $this->chamado->todosComUsuario($this->uid());
        $contagem = $this->chamado->contarPorUsuario($this->uid());
        $this->renderizar('chamados/index', compact('chamados', 'contagem') + ['titulo' => 'Meus Chamados']);
    }

    public function criar(): void
    {
        $this->exigirLogin();
        if ($this->isAdmin()) {
            $this->redirecionar('/admin');
        }

        if ($this->isPost()) {
            $this->exigirCsrf();
            $titulo = $this->post('titulo');
            $descricao = $this->post('descricao');

            if (mb_strlen($titulo) < 5 || mb_strlen($titulo) > 200
                || mb_strlen($descricao) < 20 || mb_strlen($descricao) > 5000) {
                $this->flash('erro', 'Revise o titulo e a descricao do chamado.');
                $this->redirecionar('/chamados/criar');
            }

            $id = $this->chamado->inserir([
                'titulo' => $titulo,
                'descricao' => $descricao,
                'usuario_id' => $this->uid(),
                'status' => 'aberto',
            ]);

            $analise = $this->ia->analisarChamado($titulo, $descricao);
            $this->chamado->salvarIA(
                $id,
                $analise['categoria'],
                $analise['prioridade'],
                $analise['analise'],
                $analise['sugestao']
            );

            $mensagem = $this->ia->ultimoErro()
                ? "Chamado #{$id} aberto. A classificacao automatica ficou pendente."
                : "Chamado #{$id} aberto e classificado.";
            $this->flash('sucesso', $mensagem);
            $this->redirecionar('/meus-chamados/' . $id);
        }

        $this->renderizar('chamados/criar', ['titulo' => 'Novo Chamado']);
    }

    public function detalhar(string $id): void
    {
        $this->exigirLogin();
        $chamadoId = $this->idValido($id);

        if ($this->isAdmin()) {
            $this->redirecionar('/admin/chamado/' . $chamadoId);
        }

        $chamado = $this->chamado->detalhe($chamadoId);
        if (!$chamado || (int) $chamado['usuario_id'] !== $this->uid()) {
            $this->flash('erro', 'Chamado nao encontrado.');
            $this->redirecionar('/meus-chamados');
        }

        $comentarios = $this->chamado->comentarios($chamadoId);
        $this->renderizar(
            'chamados/detalhar',
            compact('chamado', 'comentarios') + ['titulo' => "Chamado #{$chamadoId}"]
        );
    }

    public function comentar(string $id): void
    {
        $this->exigirLogin();
        $this->exigirCsrf();

        $chamadoId = $this->idValido($id);
        $chamado = $this->chamado->porId($chamadoId);
        $texto = $this->post('texto');

        // A propriedade é validada novamente no POST para impedir IDOR por URL manipulada.
        if (!$chamado || (int) $chamado['usuario_id'] !== $this->uid()) {
            http_response_code(404);
            exit('Chamado nao encontrado.');
        }

        if ($chamado['status'] !== 'fechado' && mb_strlen($texto) >= 1 && mb_strlen($texto) <= 3000) {
            $this->chamado->addComentario($chamadoId, $this->uid(), $texto);
        }

        $this->redirecionar('/meus-chamados/' . $chamadoId);
    }

    private function idValido(string $id): int
    {
        $valor = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$valor) {
            http_response_code(404);
            exit('Recurso nao encontrado.');
        }
        return $valor;
    }
}
