<?php

class IAController extends Controller
{
    private GeminiService $ia;
    private Chamado $chamado;

    public function __construct()
    {
        $this->ia = new GeminiService();
        $this->chamado = new Chamado();
    }

    public function analisar(): void
    {
        $this->exigirLogin();
        $this->exigirCsrf($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $titulo = trim((string) ($input['titulo'] ?? ''));
        $descricao = trim((string) ($input['descricao'] ?? ''));

        if (mb_strlen($titulo) < 5 || mb_strlen($titulo) > 200
            || mb_strlen($descricao) < 20 || mb_strlen($descricao) > 5000) {
            $this->json(['erro' => 'Titulo ou descricao invalidos.'], 400);
        }

        $analise = $this->ia->analisarChamado($titulo, $descricao);
        $this->json([
            'sucesso' => $this->ia->ultimoErro() === null,
            'analise' => $analise,
            'erro' => $this->ia->ultimoErro() ? 'Analise automatica temporariamente indisponivel.' : null,
        ]);
    }

    public function reanalisar(string $id): void
    {
        $this->exigirAdmin();
        $this->exigirCsrf($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');

        $chamadoId = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $chamado = $chamadoId ? $this->chamado->porId($chamadoId) : false;
        if (!$chamado) {
            $this->json(['erro' => 'Chamado nao encontrado.'], 404);
        }

        $analise = $this->ia->analisarChamado($chamado['titulo'], $chamado['descricao']);
        $this->chamado->salvarIA(
            $chamadoId,
            $analise['categoria'],
            $analise['prioridade'],
            $analise['analise'],
            $analise['sugestao']
        );
        $this->json([
            'sucesso' => $this->ia->ultimoErro() === null,
            'analise' => $analise,
            'erro' => $this->ia->ultimoErro() ? 'Nao foi possivel atualizar a analise agora.' : null,
        ]);
    }
}
