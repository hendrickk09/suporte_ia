<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login — SupporteIA</title>
<script>
try {
  if (localStorage.getItem('supporteia-tema') === 'claro') {
    document.documentElement.classList.add('modo-claro');
  }
} catch (e) {}
</script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= APP_URL ?>/css/app.css?v=tema-2">
</head>
<body>
<button type="button" class="theme-toggle auth-theme-toggle" id="theme-toggle" aria-label="Alternar modo claro ou escuro" title="Alternar tema">
  <span class="theme-toggle-icon" aria-hidden="true"></span>
</button>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo">
      <div style="font-size:2.5rem;color:var(--accent)">⬡</div>
      <h1>SupporteIA</h1>
      <p>Sistema Inteligente de Chamados</p>
    </div>
    <?php if (isset($_SESSION['flash'])): ?>
      <div class="flash flash-<?= $_SESSION['flash']['tipo'] ?>"><?= htmlspecialchars($_SESSION['flash']['mensagem']) ?></div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <form method="POST" action="<?= APP_URL ?>/login">
      <?= $csrfCampo ?>
      <div class="form-group">
        <label>E-mail</label>
        <input type="email" name="email" maxlength="150" autocomplete="username" required placeholder="seu@email.com">
      </div>
      <div class="form-group">
        <label>Senha</label>
        <input type="password" name="senha" maxlength="72" autocomplete="current-password" required placeholder="••••••••">
      </div>
      <button type="submit" class="btn btn-primary btn-block">Entrar</button>
    </form>
    <p class="auth-footer">Não tem conta? <a href="<?= APP_URL ?>/registro">Criar conta</a><br><a href="<?= APP_URL ?>/privacidade">Privacidade</a></p>
  </div>
</div>
<script src="<?= APP_URL ?>/js/app.js?v=tema-2"></script>
</body>
</html>
