<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $titulo ?? 'SupporteIA' ?> — <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= APP_URL ?>/css/app.css">
<script>const APP_URL = '<?= APP_URL ?>';</script>
</head>
<body>
<?php if (isset($_SESSION['usuario_id'])): ?>
<nav class="navbar">
  <a href="<?= APP_URL ?>/chamados" class="nav-brand"><span class="brand-icon">⬡</span> SupporteIA</a>
  <div class="nav-links">
    <a href="<?= APP_URL ?>/chamados" class="nav-link">Chamados</a>
    <a href="<?= APP_URL ?>/chamados/criar" class="nav-link btn-nav-primary">+ Novo Chamado</a>
  </div>
  <div class="nav-user">
    <span class="user-badge"><?= htmlspecialchars($_SESSION['usuario_perfil']) ?></span>
    <span class="user-name"><?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
    <a href="<?= APP_URL ?>/logout" class="nav-logout">Sair</a>
  </div>
</nav>
<?php endif; ?>
<main class="main-content">
<?php if (isset($_SESSION['flash'])): ?>
  <div class="flash flash-<?= $_SESSION['flash']['tipo'] ?>"><?= htmlspecialchars($_SESSION['flash']['mensagem']) ?></div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
