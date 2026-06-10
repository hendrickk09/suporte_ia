<?php
$perfil_layout = $perfil_layout ?? ($_SESSION['usuario_perfil'] ?? 'usuario');
$isAdmin = in_array($perfil_layout, ['admin','suporte'], true);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($titulo ?? 'SupporteIA') ?> - <?= APP_NAME ?></title>
<script>
try {
  if (localStorage.getItem('supporteia-tema') === 'claro') {
    document.documentElement.classList.add('modo-claro');
  }
} catch (e) {}
</script>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= APP_URL ?>/css/app.css?v=tema-2">
<?php if ($isAdmin): ?>
<link rel="stylesheet" href="<?= APP_URL ?>/css/admin.css?v=tema-2">
<?php else: ?>
<link rel="stylesheet" href="<?= APP_URL ?>/css/usuario.css?v=tema-2">
<?php endif; ?>
<script>
const APP_URL = <?= json_encode(APP_URL, JSON_UNESCAPED_SLASHES) ?>;
const CSRF_TOKEN = <?= json_encode($csrfToken, JSON_UNESCAPED_SLASHES) ?>;
</script>
</head>
<body class="<?= $isAdmin ? 'tema-admin' : 'tema-usuario' ?>">

<?php if ($isAdmin): ?>
<nav class="navbar navbar-admin">
  <a href="<?= APP_URL ?>/admin" class="nav-brand">
    <span class="brand-icon" aria-hidden="true"></span> SupporteIA
  </a>
  <div class="nav-links">
    <a href="<?= APP_URL ?>/admin" class="nav-link">Dashboard</a>
  </div>
  <div class="nav-user">
    <button type="button" class="theme-toggle" id="theme-toggle" aria-label="Alternar modo claro ou escuro" title="Alternar tema">
      <span class="theme-toggle-icon" aria-hidden="true"></span>
    </button>
    <span class="user-badge badge-admin"><?= htmlspecialchars($_SESSION['usuario_perfil'] ?? '') ?></span>
    <span class="user-name"><?= htmlspecialchars($_SESSION['usuario_nome'] ?? '') ?></span>
    <form method="POST" action="<?= APP_URL ?>/logout" class="nav-logout-form">
      <?= $csrfCampo ?>
      <button type="submit" class="nav-logout">Sair</button>
    </form>
  </div>
</nav>
<?php else: ?>
<nav class="navbar navbar-usuario">
  <a href="<?= APP_URL ?>/meus-chamados" class="nav-brand">
    <span class="brand-icon" aria-hidden="true"></span> SupporteIA
  </a>
  <div class="nav-links">
    <a href="<?= APP_URL ?>/meus-chamados" class="nav-link">Meus Chamados</a>
    <a href="<?= APP_URL ?>/chamados/criar" class="nav-link btn-novo">+ Novo Chamado</a>
  </div>
  <div class="nav-user">
    <button type="button" class="theme-toggle" id="theme-toggle" aria-label="Alternar modo claro ou escuro" title="Alternar tema">
      <span class="theme-toggle-icon" aria-hidden="true"></span>
    </button>
    <span class="user-badge badge-usuario">usuario</span>
    <span class="user-name"><?= htmlspecialchars($_SESSION['usuario_nome'] ?? '') ?></span>
    <form method="POST" action="<?= APP_URL ?>/logout" class="nav-logout-form">
      <?= $csrfCampo ?>
      <button type="submit" class="nav-logout">Sair</button>
    </form>
  </div>
</nav>
<?php endif; ?>

<main class="main-content">
<?php if (isset($_SESSION['flash'])): ?>
  <div class="flash flash-<?= htmlspecialchars($_SESSION['flash']['tipo']) ?>"><?= htmlspecialchars($_SESSION['flash']['mensagem']) ?></div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
