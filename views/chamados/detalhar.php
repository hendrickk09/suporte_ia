<?php $perfil_layout = 'usuario'; require_once __DIR__ . '/../layouts/base.php'; ?>

<div class="page-header space-between">
  <div>
    <h1>Chamado #<?= $chamado['id'] ?></h1>
    <p><?= htmlspecialchars($chamado['titulo']) ?></p>
  </div>
  <a href="<?= APP_URL ?>/meus-chamados" class="btn btn-outline">← Voltar</a>
</div>

<div class="detalhe-simples">

  <div class="status-bar-usuario">
    <div class="status-step <?= in_array($chamado['status'], ['aberto','em_andamento','resolvido','fechado']) ? 'ativo' : '' ?>">
      <div class="step-dot"></div><span>Aberto</span>
    </div>
    <div class="status-line <?= in_array($chamado['status'], ['em_andamento','resolvido','fechado']) ? 'completo' : '' ?>"></div>
    <div class="status-step <?= in_array($chamado['status'], ['em_andamento','resolvido','fechado']) ? 'ativo' : '' ?>">
      <div class="step-dot"></div><span>Em andamento</span>
    </div>
    <div class="status-line <?= in_array($chamado['status'], ['resolvido','fechado']) ? 'completo' : '' ?>"></div>
    <div class="status-step <?= in_array($chamado['status'], ['resolvido','fechado']) ? 'ativo' : '' ?>">
      <div class="step-dot"></div><span>Resolvido</span>
    </div>
    <div class="status-line <?= $chamado['status'] === 'fechado' ? 'completo' : '' ?>"></div>
    <div class="status-step <?= $chamado['status'] === 'fechado' ? 'ativo' : '' ?>">
      <div class="step-dot"></div><span>Fechado</span>
    </div>
  </div>

  <div class="card">
    <div class="card-header">Detalhes do problema</div>
    <div class="card-body">
      <p class="descricao-texto"><?= nl2br(htmlspecialchars($chamado['descricao'])) ?></p>
      <div class="chamado-meta-row">
        <span>Aberto em: <strong><?= date('d/m/Y H:i', strtotime($chamado['criado_em'])) ?></strong></span>
        <span>Atendente: <strong><?= htmlspecialchars($chamado['atendente_nome'] ?? 'Aguardando atribuição') ?></strong></span>
      </div>
    </div>
  </div>

  <?php if ($chamado['ia_analise']): ?>
  <div class="card">
    <div class="card-header ia-header">⬡ Análise Automática da IA</div>
    <div class="card-body">
      <div class="ia-pills">
        <span class="cat-tag"><?= htmlspecialchars($chamado['categoria']) ?></span>
        <span class="badge badge-<?= $chamado['prioridade'] ?>">Prioridade <?= strtoupper($chamado['prioridade']) ?></span>
      </div>
      <div class="ia-block"><h4>Diagnóstico</h4><p><?= htmlspecialchars(html_entity_decode($chamado['ia_analise'], ENT_QUOTES, 'UTF-8')) ?></p></div>
      <div class="ia-block"><h4>Sugestão</h4><p><?= htmlspecialchars(html_entity_decode($chamado['ia_sugestao'], ENT_QUOTES, 'UTF-8')) ?></p></div>
    </div>
  </div>
  <?php endif; ?>

  <div class="card">
    <div class="card-header">Histórico <span style="color:var(--text3)"><?= count($comentarios) ?> mensagens</span></div>
    <div class="card-body">
      <?php if (empty($comentarios)): ?>
        <p style="color:var(--text3);text-align:center;padding:20px 0">Seu chamado está na fila. Em breve nossa equipe entrará em contato.</p>
      <?php else: ?>
        <?php foreach ($comentarios as $c): ?>
        <div class="comentario comentario-<?= $c['usuario_perfil'] === 'usuario' ? 'usuario' : 'suporte' ?>">
          <div class="comentario-meta">
            <span class="comentario-autor"><?= htmlspecialchars($c['usuario_nome']) ?></span>
            <span class="comentario-perfil"><?= $c['usuario_perfil'] === 'usuario' ? 'Você' : '🛠️ Suporte' ?></span>
            <span class="comentario-data"><?= date('d/m/Y H:i', strtotime($c['criado_em'])) ?></span>
          </div>
          <p class="comentario-texto"><?= nl2br(htmlspecialchars($c['texto'])) ?></p>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php if ($chamado['status'] !== 'fechado'): ?>
      <form method="POST" action="<?= APP_URL ?>/meus-chamados/<?= $chamado['id'] ?>/comentar" style="margin-top:16px;border-top:1px solid var(--border);padding-top:16px">
        <?= $csrfCampo ?>
        <div class="form-group">
          <textarea name="texto" rows="3" maxlength="3000" required placeholder="Adicionar informação ou comentário..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Enviar</button>
      </form>
      <?php endif; ?>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
