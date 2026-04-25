<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="page-header">
  <h1>Painel de Chamados</h1>
  <p>Gerencie e acompanhe todas as solicitações de suporte</p>
</div>

<div class="stats-bar">
  <div class="stat-card aberto"><div class="stat-label">Abertos</div><div class="stat-value"><?= $contagem['aberto'] ?></div></div>
  <div class="stat-card andamento"><div class="stat-label">Em andamento</div><div class="stat-value"><?= $contagem['em_andamento'] ?></div></div>
  <div class="stat-card resolvido"><div class="stat-label">Resolvidos</div><div class="stat-value"><?= $contagem['resolvido'] ?></div></div>
  <div class="stat-card fechado"><div class="stat-label">Fechados</div><div class="stat-value"><?= $contagem['fechado'] ?></div></div>
</div>

<div class="tabela-container">
  <div class="tabela-header">
    <h2>Todos os chamados</h2>
    <a href="<?= APP_URL ?>/chamados/criar" class="btn btn-primary" style="font-size:.82rem;padding:7px 14px">+ Novo Chamado</a>
  </div>
  <?php if (empty($chamados)): ?>
    <div class="empty-state"><h3>Nenhum chamado encontrado</h3><p>Clique em "Novo Chamado" para registrar sua primeira solicitação.</p></div>
  <?php else: ?>
  <table>
    <thead><tr><th>#</th><th>Título</th><th>Categoria (IA)</th><th>Prioridade (IA)</th><th>Status</th><th>Solicitante</th><th>Data</th></tr></thead>
    <tbody>
    <?php foreach ($chamados as $c): ?>
      <tr>
        <td class="col-id"><?= $c['id'] ?></td>
        <td class="col-titulo"><a href="<?= APP_URL ?>/chamados/<?= $c['id'] ?>"><?= htmlspecialchars($c['titulo']) ?></a></td>
        <td><span style="font-size:.82rem;color:var(--text2)"><?= htmlspecialchars($c['categoria'] ?? '—') ?></span></td>
        <td><span class="badge badge-<?= $c['prioridade'] ?? 'media' ?>"><?= strtoupper($c['prioridade'] ?? 'MEDIA') ?></span></td>
        <td><span class="badge badge-<?= str_replace('_','-',$c['status']) ?>"><?= strtoupper(str_replace('_',' ',$c['status'])) ?></span></td>
        <td class="col-usuario"><?= htmlspecialchars($c['usuario_nome']) ?></td>
        <td class="col-data"><?= date('d/m/Y H:i', strtotime($c['criado_em'])) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
