<?php
class GeminiService
{
    public function analisarChamado(string $titulo, string $descricao): array
    {
        $prompt = "Você é um especialista em triagem de chamados de suporte de TI.
Analise o chamado abaixo e responda APENAS em JSON válido, sem texto extra.

Título: $titulo
Descrição: $descricao

Formato obrigatório:
{
  \"categoria\": \"Hardware|Software|Rede|Acesso e Permissões|E-mail|Impressora|Sistema Interno|Outros\",
  \"prioridade\": \"baixa|media|alta|critica\",
  \"analise\": \"resumo técnico em 2 frases\",
  \"sugestao\": \"próximos passos em 2 frases\"
}

Critérios: critica=sistema fora do ar; alta=funcionalidade principal; media=workaround possível; baixa=dúvida/melhoria.";

        $resp = $this->api($prompt);
        if (!$resp) return $this->padrao();
        return $this->parse($resp);
    }

    private function api(string $prompt): ?string
    {
        $url  = GEMINI_API_URL . '?key=' . GEMINI_API_KEY;
        $body = json_encode(['contents'=>[['parts'=>[['text'=>$prompt]]]],'generationConfig'=>['temperature'=>0.3,'maxOutputTokens'=>500]]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_POST=>true, CURLOPT_POSTFIELDS=>$body, CURLOPT_HTTPHEADER=>['Content-Type: application/json'], CURLOPT_TIMEOUT=>30]);
        $r    = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($code === 200) ? $r : null;
    }

    private function parse(string $raw): array
    {
        $data = json_decode($raw, true);
        $txt  = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $txt  = preg_replace('/```json\s*|\s*```/', '', trim($txt));
        $a    = json_decode($txt, true);

        if (!$a || !isset($a['categoria'])) return $this->padrao();

        $cats  = ['Hardware','Software','Rede','Acesso e Permissões','E-mail','Impressora','Sistema Interno','Outros'];
        $prios = ['baixa','media','alta','critica'];

        return [
            'categoria' => in_array($a['categoria'], $cats)  ? $a['categoria'] : 'Outros',
            'prioridade'=> in_array($a['prioridade'], $prios) ? $a['prioridade']: 'media',
            'analise'   => htmlspecialchars($a['analise']  ?? '', ENT_QUOTES, 'UTF-8'),
            'sugestao'  => htmlspecialchars($a['sugestao'] ?? '', ENT_QUOTES, 'UTF-8'),
        ];
    }

    private function padrao(): array
    {
        return ['categoria'=>'Outros','prioridade'=>'media','analise'=>'Análise manual necessária.','sugestao'=>'Revise o chamado manualmente.'];
    }
}
