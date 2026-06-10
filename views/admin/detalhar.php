<?php $perfil_layout = 'admin'; require_once __DIR__ . '/../layouts/base.php'; ?>
<script>const CHAMADO_ID = <?= (int)$chamado['id'] ?>;</script>

<div class="page-header space-between">
  <div>
    <h1>Chamado #<?= $chamado['id'] ?></h1>
    <p><?= htmlspecialchars($chamado['titulo']) ?></p>
  </div>
  <a href="<?= APP_URL ?>/admin" class="btn btn-outline">← Voltar</a>
</div>

<div class="detalhe-grid">
  <div class="detalhe-main">

    <div class="card">
      <div class="card-header">Descrição do problema</div>
      <div class="card-body">
        <p class="descricao-texto"><?= nl2br(htmlspecialchars($chamado['descricao'])) ?></p>
      </div>
    </div>

    <div class="card">
      <div class="card-header ia-header">⬡ Análise da Inteligência Artificial — Google Gemini</div>
      <div class="card-body">
        <?php if ($chamado['ia_analise']): ?>
          <div class="ia-info-row"><span class="ia-label">Categoria</span><span class="cat-tag"><?= htmlspecialchars($chamado['categoria']) ?></span></div>
          <div class="ia-info-row"><span class="ia-label">Prioridade</span><span class="badge badge-<?= $chamado['prioridade'] ?>"><?= strtoupper($chamado['prioridade']) ?></span></div>
          <div class="ia-block"><h4>Diagnóstico</h4><p><?= htmlspecialchars(html_entity_decode($chamado['ia_analise'], ENT_QUOTES, 'UTF-8')) ?></p></div>
          <div class="ia-block"><h4>Sugestão de resolução</h4><p><?= htmlspecialchars(html_entity_decode($chamado['ia_sugestao'], ENT_QUOTES, 'UTF-8')) ?></p></div>
        <?php else: ?>
          <p style="color:var(--text3)">Chamado ainda não foi analisado pela IA.</p>
        <?php endif; ?>
        <button id="btn-reanalisar" class="btn btn-ia" style="margin-top:14px;width:100%;justify-content:center" onclick="reanalisarChamado(CHAMADO_ID)">
          ⟳ Reanalisar com IA
        </button>
      </div>
    </div>

    <div class="card">
      <div class="card-header">Histórico <span style="color:var(--text3)"><?= count($comentarios) ?> mensagens</span></div>
      <div class="card-body">
        <?php if (empty($comentarios)): ?>
          <p style="color:var(--text3);text-align:center;padding:20px 0">Nenhum comentário ainda.</p>
        <?php else: ?>
          <?php foreach ($comentarios as $c): ?>
          <div class="comentario comentario-<?= $c['usuario_perfil'] === 'usuario' ? 'usuario' : 'suporte' ?>">
            <div class="comentario-meta">
              <span class="comentario-autor"><?= htmlspecialchars($c['usuario_nome']) ?></span>
              <span class="comentario-perfil"><?= $c['usuario_perfil'] === 'usuario' ? 'Solicitante' : 'Suporte' ?></span>
              <span class="comentario-data"><?= date('d/m/Y H:i', strtotime($c['criado_em'])) ?></span>
            </div>
            <p class="comentario-texto"><?= nl2br(htmlspecialchars($c['texto'])) ?></p>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($chamado['status'] !== 'fechado'): ?>
        <form method="POST" action="<?= APP_URL ?>/admin/chamado/<?= $chamado['id'] ?>/comentar" style="margin-top:16px;border-top:1px solid var(--border);padding-top:16px">
          <?= $csrfCampo ?>
          <div class="form-group">
            <textarea name="texto" rows="3" maxlength="3000" required placeholder="Responder ao usuário..."></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Enviar resposta</button>
        </form>
        <?php endif; ?>
      </div>
    </div>

    <div class="card">
      <div class="card-header">Mudanças de status</div>
      <div class="card-body">
        <?php if (empty($historico)): ?>
          <p style="color:var(--text3)">Nenhuma mudança registrada ainda.</p>
        <?php else: ?>
          <?php foreach ($historico as $evento): ?>
            <div class="info-row">
              <span class="info-label"><?= htmlspecialchars($evento['usuario_nome']) ?></span>
              <span class="info-value">
                <?= htmlspecialchars(str_replace('_', ' ', $evento['status_anterior'] ?? '-')) ?>
                → <?= htmlspecialchars(str_replace('_', ' ', $evento['status_novo'])) ?>
                · <?= date('d/m/Y H:i', strtotime($evento['criado_em'])) ?>
              </span>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

  </div>

  <div class="detalhe-sidebar">
    <div class="card">
      <div class="card-header">Informações</div>
      <div class="card-body">
        <div class="info-row"><span class="info-label">Status</span><span class="badge badge-<?= str_replace('_','-',$chamado['status']) ?>"><?= strtoupper(str_replace('_',' ',$chamado['status'])) ?></span></div>
        <div class="info-row"><span class="info-label">Prioridade</span><span class="badge badge-<?= $chamado['prioridade'] ?? 'media' ?>"><?= strtoupper($chamado['prioridade'] ?? '—') ?></span></div>
        <div class="info-row"><span class="info-label">Categoria</span><span class="info-value"><?= htmlspecialchars($chamado['categoria'] ?? '—') ?></span></div>
        <div class="info-row"><span class="info-label">Solicitante</span><span class="info-value"><?= htmlspecialchars($chamado['usuario_nome']) ?></span></div>
        <div class="info-row"><span class="info-label">Atendente</span><span class="info-value"><?= htmlspecialchars($chamado['atendente_nome'] ?? '—') ?></span></div>
        <div class="info-row"><span class="info-label">Aberto em</span><span class="info-value" style="font-size:.78rem;font-family:var(--font-mono)"><?= date('d/m/Y H:i', strtotime($chamado['criado_em'])) ?></span></div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">Atribuir atendente</div>
      <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/admin/chamado/<?= $chamado['id'] ?>/atribuir">
          <?= $csrfCampo ?>
          <div class="form-group">
            <select name="atendente_id" required>
              <option value="">Selecione</option>
              <?php foreach ($atendentes as $atendente): ?>
                <option value="<?= (int) $atendente['id'] ?>" <?= (int) ($chamado['atendente_id'] ?? 0) === (int) $atendente['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($atendente['nome']) ?> (<?= htmlspecialchars($atendente['perfil']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Atribuir</button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header">Atualizar Status</div>
      <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/admin/chamado/<?= $chamado['id'] ?>/status">
          <?= $csrfCampo ?>
          <div class="form-group">
            <select name="status">
              <?php foreach (['aberto','em_andamento','resolvido','fechado'] as $s): ?>
                <option value="<?= $s ?>" <?= $chamado['status'] === $s ? 'selected' : '' ?>>
                  <?= ucfirst(str_replace('_',' ',$s)) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Atualizar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
