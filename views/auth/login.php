<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="brand-icon" style="font-size:2.5rem;color:var(--accent)">⬡</div>
      <h1>SupporteIA</h1>
      <p>Sistema Inteligente de Chamados</p>
    </div>
    <?php if (isset($_SESSION['flash'])): ?>
      <div class="flash flash-<?= $_SESSION['flash']['tipo'] ?>"><?= htmlspecialchars($_SESSION['flash']['mensagem']) ?></div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <form method="POST" action="<?= APP_URL ?>/login">
      <div class="form-group">
        <label>E-mail</label>
        <input type="email" name="email" required placeholder="seu@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Senha</label>
        <input type="password" name="senha" required placeholder="••••••••">
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px">Entrar</button>
    </form>
    <p class="auth-footer">Não tem conta? <a href="<?= APP_URL ?>/registro">Criar conta</a></p>
  </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
