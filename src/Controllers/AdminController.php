<?php

class AdminController extends Controller
{
    private Chamado $chamado;
    private Usuario $usuario;

    public function __construct()
    {
        $this->chamado = new Chamado();
        $this->usuario = new Usuario();
    }

    public function index(): void
    {
        $this->exigirAdmin();

        $filtros = [
            'busca' => mb_substr(trim((string) ($_GET['busca'] ?? '')), 0, 100),
            'status' => $this->opcao($_GET['status'] ?? '', ['aberto','em_andamento','resolvido','fechado']),
            'prioridade' => $this->opcao($_GET['prioridade'] ?? '', ['baixa','media','alta','critica']),
            'categoria' => mb_substr(trim((string) ($_GET['categoria'] ?? '')), 0, 100),
        ];
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        $porPagina = 15;
        $totalFiltrado = $this->chamado->contarPainel($filtros);
        $totalPaginas = max(1, (int) ceil($totalFiltrado / $porPagina));
        $pagina = min($pagina, $totalPaginas);
        $chamados = $this->chamado->buscarPainel($filtros, $porPagina, ($pagina - 1) * $porPagina);
        $contagem = $this->chamado->contarPorStatus();
        $prioridades = $this->chamado->contarPorPrioridade();
        $total = array_sum($contagem);

        $this->renderizar('admin/dashboard', compact(
            'chamados',
            'contagem',
            'total',
            'prioridades',
            'filtros',
            'pagina',
            'totalPaginas',
            'totalFiltrado'
        ) + ['titulo' => 'Painel Admin']);
    }

    public function detalhar(string $id): void
    {
        $this->exigirAdmin();
        $chamadoId = $this->idValido($id);
        $chamado = $this->chamado->detalhe($chamadoId);

        if (!$chamado) {
            $this->flash('erro', 'Chamado nao encontrado.');
            $this->redirecionar('/admin');
        }

        $comentarios = $this->chamado->comentarios($chamadoId);
        $historico = $this->chamado->historicoStatus($chamadoId);
        $atendentes = $this->usuario->atendentes();
        $this->renderizar(
            'admin/detalhar',
            compact('chamado', 'comentarios', 'historico', 'atendentes') + ['titulo' => "Chamado #{$chamadoId}"]
        );
    }

    public function comentar(string $id): void
    {
        $this->exigirAdmin();
        $this->exigirCsrf();
        $chamadoId = $this->idValido($id);
        $chamado = $this->chamado->porId($chamadoId);
        $texto = $this->post('texto');

        if ($chamado && $chamado['status'] !== 'fechado'
            && mb_strlen($texto) >= 1 && mb_strlen($texto) <= 3000) {
            $this->chamado->addComentario($chamadoId, $this->uid(), $texto);
        }
        $this->redirecionar('/admin/chamado/' . $chamadoId);
    }

    public function atualizarStatus(string $id): void
    {
        $this->exigirAdmin();
        $this->exigirCsrf();
        $chamadoId = $this->idValido($id);
        $chamado = $this->chamado->porId($chamadoId);
        $status = $this->opcao($this->post('status'), ['aberto','em_andamento','resolvido','fechado']);

        if ($chamado && $status && $status !== $chamado['status']) {
            $this->chamado->atualizar($chamadoId, ['status' => $status]);
            $this->chamado->registrarHistorico($chamadoId, $this->uid(), $chamado['status'], $status);
            $this->flash('sucesso', 'Status atualizado.');
        }
        $this->redirecionar('/admin/chamado/' . $chamadoId);
    }

    public function atribuir(string $id): void
    {
        $this->exigirAdmin();
        $this->exigirCsrf();
        $chamadoId = $this->idValido($id);
        $atendenteId = filter_var($this->post('atendente_id'), FILTER_VALIDATE_INT);
        $permitidos = array_column($this->usuario->atendentes(), 'id');

        if ($atendenteId && in_array((int) $atendenteId, array_map('intval', $permitidos), true)) {
            $this->chamado->atualizar($chamadoId, ['atendente_id' => (int) $atendenteId]);
            $this->flash('sucesso', 'Atendente atribuido.');
        }
        $this->redirecionar('/admin/chamado/' . $chamadoId);
    }

    private function opcao(mixed $valor, array $permitidos): string
    {
        return is_string($valor) && in_array($valor, $permitidos, true) ? $valor : '';
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
