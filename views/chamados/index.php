<?php $perfil_layout = 'usuario'; require_once __DIR__ . '/../layouts/base.php'; ?>

<div class="page-header space-between">
  <div>
    <h1>Meus Chamados</h1>
    <p>Acompanhe suas solicitacoes de suporte</p>
  </div>
  <a href="<?= APP_URL ?>/chamados/criar" class="btn btn-primary">+ Novo Chamado</a>
</div>

<div class="stats-bar stats-usuario">
  <div class="stat-mini"><span class="stat-num"><?= $contagem['aberto'] ?></span><span>Abertos</span></div>
  <div class="stat-mini"><span class="stat-num andamento"><?= $contagem['em_andamento'] ?></span><span>Em andamento</span></div>
  <div class="stat-mini"><span class="stat-num resolvido"><?= $contagem['resolvido'] ?></span><span>Resolvidos</span></div>
  <div class="stat-mini"><span class="stat-num fechado"><?= $contagem['fechado'] ?></span><span>Fechados</span></div>
</div>

<?php if (empty($chamados)): ?>
<div class="empty-state">
  <div style="font-size:3rem;margin-bottom:12px">+</div>
  <h3>Nenhum chamado ainda</h3>
  <p>Clique em "Novo Chamado" para registrar seu primeiro problema.</p>
  <a href="<?= APP_URL ?>/chamados/criar" class="btn btn-primary" style="margin-top:16px">+ Abrir Chamado</a>
</div>
<?php else: ?>
<div class="chamados-lista">
  <?php foreach ($chamados as $c): ?>
  <a href="<?= APP_URL ?>/meus-chamados/<?= $c['id'] ?>" class="chamado-card">
    <div class="chamado-card-header">
      <span class="chamado-id">#<?= $c['id'] ?></span>
      <span class="badge badge-<?= str_replace('_','-',$c['status']) ?>"><?= strtoupper(str_replace('_',' ',$c['status'])) ?></span>
    </div>
    <h3 class="chamado-titulo"><?= htmlspecialchars($c['titulo']) ?></h3>
    <p class="chamado-descricao"><?= htmlspecialchars(mb_substr($c['descricao'], 0, 100)) ?>...</p>
    <div class="chamado-card-footer">
      <span class="cat-tag"><?= htmlspecialchars($c['categoria'] ?? 'Aguardando IA') ?></span>
      <span class="badge badge-<?= $c['prioridade'] ?? 'media' ?>"><?= strtoupper($c['prioridade'] ?? 'MEDIA') ?></span>
      <span class="chamado-data"><?= date('d/m/Y', strtotime($c['criado_em'])) ?></span>
    </div>
  </a>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
