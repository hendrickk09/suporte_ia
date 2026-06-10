<?php $perfil_layout = 'admin'; require_once __DIR__ . '/../layouts/base.php'; ?>
<?php
$maiorStatus = max(1, max($contagem));
$maiorPrioridade = max(1, max($prioridades));
?>

<div class="page-header">
  <div>
    <h1>Painel Administrativo</h1>
    <p>Gerencie, filtre e priorize todos os chamados do sistema</p>
  </div>
</div>

<div class="stats-bar">
  <div class="stat-card stat-aberto">
    <div class="stat-icon">AB</div>
    <div class="stat-info">
      <div class="stat-value"><?= $contagem['aberto'] ?></div>
      <div class="stat-label">Abertos</div>
    </div>
  </div>
  <div class="stat-card stat-andamento">
    <div class="stat-icon">EA</div>
    <div class="stat-info">
      <div class="stat-value"><?= $contagem['em_andamento'] ?></div>
      <div class="stat-label">Em andamento</div>
    </div>
  </div>
  <div class="stat-card stat-resolvido">
    <div class="stat-icon">OK</div>
    <div class="stat-info">
      <div class="stat-value"><?= $contagem['resolvido'] ?></div>
      <div class="stat-label">Resolvidos</div>
    </div>
  </div>
  <div class="stat-card stat-fechado">
    <div class="stat-icon">FC</div>
    <div class="stat-info">
      <div class="stat-value"><?= $contagem['fechado'] ?></div>
      <div class="stat-label">Fechados</div>
    </div>
  </div>
</div>

<section class="dashboard-graficos">
  <div class="grafico-card grafico-status">
    <div class="grafico-header">
      <div>
        <h2>Chamados por status</h2>
        <p>Volume atual da fila operacional</p>
      </div>
      <span class="grafico-total"><?= $total ?> total</span>
    </div>

    <div class="bar-chart">
      <?php
      $statusLabels = [
          'aberto' => 'Abertos',
          'em_andamento' => 'Em andamento',
          'resolvido' => 'Resolvidos',
          'fechado' => 'Fechados',
      ];
      foreach ($statusLabels as $statusKey => $statusLabel):
          $valor = $contagem[$statusKey] ?? 0;
          $largura = ($valor / $maiorStatus) * 100;
      ?>
      <div class="bar-row">
        <div class="bar-label"><?= $statusLabel ?></div>
        <div class="bar-track">
          <div class="bar-fill bar-<?= str_replace('_', '-', $statusKey) ?>" style="width: <?= $largura ?>%"></div>
        </div>
        <div class="bar-value"><?= $valor ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="grafico-card grafico-prioridade">
    <div class="grafico-header">
      <div>
        <h2>Prioridade da IA</h2>
        <p>Classificacao automatica dos chamados</p>
      </div>
    </div>

    <div class="priority-chart">
      <?php foreach ($prioridades as $prioridadeKey => $valor):
          $altura = max(8, ($valor / $maiorPrioridade) * 100);
      ?>
      <div class="priority-col">
        <div class="priority-value"><?= $valor ?></div>
        <div class="priority-bar-wrap">
          <div class="priority-bar priority-<?= $prioridadeKey ?>" style="height: <?= $altura ?>%"></div>
        </div>
        <div class="priority-label"><?= strtoupper($prioridadeKey) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<div class="tabela-container">
  <div class="tabela-header">
    <h2>Fila geral <span class="total-badge"><?= $totalFiltrado ?></span></h2>
    <form class="tabela-filtros" method="GET" action="<?= APP_URL ?>/admin">
      <input type="search" name="busca" maxlength="100" value="<?= htmlspecialchars($filtros['busca']) ?>" placeholder="Buscar título ou usuário">
      <select name="status">
        <option value="">Todos os status</option>
        <?php foreach (['aberto','em_andamento','resolvido','fechado'] as $status): ?>
          <option value="<?= $status ?>" <?= $filtros['status'] === $status ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $status)) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="prioridade">
        <option value="">Todas as prioridades</option>
        <?php foreach (['critica','alta','media','baixa'] as $prioridade): ?>
          <option value="<?= $prioridade ?>" <?= $filtros['prioridade'] === $prioridade ? 'selected' : '' ?>><?= ucfirst($prioridade) ?></option>
        <?php endforeach; ?>
      </select>
      <input type="text" name="categoria" maxlength="100" value="<?= htmlspecialchars($filtros['categoria']) ?>" placeholder="Categoria">
      <button type="submit" class="btn btn-primary">Filtrar</button>
      <a href="<?= APP_URL ?>/admin" class="btn btn-outline">Limpar</a>
    </form>
  </div>

  <?php if (empty($chamados)): ?>
    <div class="empty-state"><p>Nenhum chamado registrado ainda.</p></div>
  <?php else: ?>
  <table id="tabela-chamados">
    <thead>
      <tr>
        <th>#</th>
        <th>Titulo</th>
        <th>Categoria IA</th>
        <th>Prioridade IA</th>
        <th>Status</th>
        <th>Solicitante</th>
        <th>Data</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($chamados as $c): ?>
      <tr data-status="<?= $c['status'] ?>" data-prioridade="<?= $c['prioridade'] ?? '' ?>">
        <td class="col-id"><?= $c['id'] ?></td>
        <td class="col-titulo">
          <a href="<?= APP_URL ?>/admin/chamado/<?= $c['id'] ?>">
            <?= htmlspecialchars($c['titulo']) ?>
          </a>
        </td>
        <td><span class="cat-tag"><?= htmlspecialchars($c['categoria'] ?? '-') ?></span></td>
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

<?php if ($totalPaginas > 1): ?>
<nav class="paginacao" aria-label="Paginação">
  <?php for ($numero = 1; $numero <= $totalPaginas; $numero++): ?>
    <?php $query = http_build_query(array_filter($filtros) + ['pagina' => $numero]); ?>
    <a class="<?= $numero === $pagina ? 'ativo' : '' ?>" href="<?= APP_URL ?>/admin?<?= htmlspecialchars($query) ?>"><?= $numero ?></a>
  <?php endfor; ?>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
