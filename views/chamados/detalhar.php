<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<script>const CHAMADO_ID = <?= (int)$chamado['id'] ?>;</script>

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start">
  <div>
    <h1>Chamado #<?= $chamado['id'] ?></h1>
    <p><?= htmlspecialchars($chamado['titulo']) ?></p>
  </div>
  <a href="<?= APP_URL ?>/chamados" class="btn btn-outline" style="margin-top:4px">← Voltar</a>
</div>

<div class="detalhe-grid">
  <div class="detalhe-main">

    <div class="card">
      <div class="card-header">Descrição do problema</div>
      <div class="card-body"><p class="descricao-texto"><?= htmlspecialchars($chamado['descricao']) ?></p></div>
    </div>

    <?php if ($chamado['ia_analise']): ?>
    <div class="card">
      <div class="card-header ia-card-header">⬡ Análise da Inteligência Artificial — Google Gemini</div>
      <div class="card-body">
        <div class="ia-info-row"><span class="ia-info-label">Categoria</span><span class="badge badge-andamento"><?= htmlspecialchars($chamado['categoria']) ?></span></div>
        <div class="ia-info-row"><span class="ia-info-label">Prioridade</span><span class="badge badge-<?= $chamado['prioridade'] ?>"><?= strtoupper($chamado['prioridade']) ?></span></div>
        <div class="ia-analise-block"><h4>Diagnóstico</h4><p><?= htmlspecialchars($chamado['ia_analise']) ?></p></div>
        <div class="ia-analise-block"><h4>Sugestão de resolução</h4><p><?= htmlspecialchars($chamado['ia_sugestao']) ?></p></div>
      </div>
    </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-header">Histórico de atendimento <span style="color:var(--text3)"><?= count($comentarios) ?> mensagens</span></div>
      <div class="card-body">
        <?php if (empty($comentarios)): ?>
          <p style="color:var(--text3);font-size:.85rem;text-align:center;padding:20px 0">Nenhum comentário ainda.</p>
        <?php else: ?>
          <?php foreach ($comentarios as $c): ?>
          <div class="comentario">
            <div class="comentario-meta">
              <span class="comentario-autor"><?= htmlspecialchars($c['usuario_nome']) ?></span>
              <span class="comentario-data"><?= date('d/m/Y H:i', strtotime($c['criado_em'])) ?></span>
            </div>
            <p class="comentario-texto"><?= nl2br(htmlspecialchars($c['texto'])) ?></p>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($chamado['status'] !== 'fechado'): ?>
        <form method="POST" action="<?= APP_URL ?>/chamados/<?= $chamado['id'] ?>/comentar" style="margin-top:16px;border-top:1px solid var(--border);padding-top:16px">
          <div class="form-group" style="margin-bottom:12px">
            <textarea name="texto" rows="3" required placeholder="Adicionar comentário..."></textarea>
          </div>
          <button type="submit" class="btn btn-primary" style="font-size:.82rem;padding:7px 14px">Enviar</button>
        </form>
        <?php endif; ?>
      </div>
    </div>

  </div>

  <div class="detalhe-sidebar">
    <div class="card">
      <div class="card-header">Informações</div>
      <div class="card-body">
        <div class="info-row"><span class="info-label">Status</span><span class="badge badge-<?= str_replace('_','-',$chamado['status']) ?>"><?= strtoupper(str_replace('_',' ',$chamado['status'])) ?></span></div>
        <div class="info-row"><span class="info-label">Prioridade</span><span class="badge badge-<?= $chamado['prioridade'] ?? 'media' ?>"><?= strtoupper($chamado['prioridade'] ?? 'MEDIA') ?></span></div>
        <div class="info-row"><span class="info-label">Categoria</span><span class="info-value"><?= htmlspecialchars($chamado['categoria'] ?? '—') ?></span></div>
        <div class="info-row"><span class="info-label">Solicitante</span><span class="info-value"><?= htmlspecialchars($chamado['usuario_nome']) ?></span></div>
        <div class="info-row"><span class="info-label">Atendente</span><span class="info-value"><?= htmlspecialchars($chamado['atendente_nome'] ?? '—') ?></span></div>
        <div class="info-row"><span class="info-label">Aberto em</span><span class="info-value" style="font-family:var(--font-mono);font-size:.78rem"><?= date('d/m/Y H:i', strtotime($chamado['criado_em'])) ?></span></div>
      </div>
    </div>

    <?php if ($_SESSION['usuario_perfil'] !== 'usuario'): ?>
    <div class="card">
      <div class="card-header">Ações</div>
      <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/chamados/<?= $chamado['id'] ?>/status" class="status-form" style="margin-bottom:14px">
          <select name="status">
            <?php foreach (['aberto','em_andamento','resolvido','fechado'] as $s): ?>
              <option value="<?= $s ?>" <?= $chamado['status'] === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-outline" style="font-size:.82rem;padding:6px 12px">Atualizar</button>
        </form>
        <button id="btn-reanalisar" class="btn btn-ia" onclick="reanalisarChamado(CHAMADO_ID)" style="width:100%;justify-content:center">⟳ Reanalisar com IA</button>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
