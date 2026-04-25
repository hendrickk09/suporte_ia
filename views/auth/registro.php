<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="brand-icon" style="font-size:2.5rem;color:var(--accent)">⬡</div>
      <h1>Criar Conta</h1>
      <p>SupporteIA — Acesso ao sistema</p>
    </div>
    <?php if (isset($_SESSION['flash'])): ?>
      <div class="flash flash-<?= $_SESSION['flash']['tipo'] ?>"><?= htmlspecialchars($_SESSION['flash']['mensagem']) ?></div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <form method="POST" action="<?= APP_URL ?>/registro">
      <div class="form-group">
        <label>Nome completo</label>
        <input type="text" name="nome" required placeholder="Seu nome" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>E-mail</label>
        <input type="email" name="email" required placeholder="seu@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Senha <span style="color:var(--text3)">(mín. 6 caracteres)</span></label>
        <input type="password" name="senha" required placeholder="••••••••" minlength="6">
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px">Criar conta</button>
    </form>
    <p class="auth-footer">Já tem conta? <a href="<?= APP_URL ?>/login">Entrar</a></p>
  </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
