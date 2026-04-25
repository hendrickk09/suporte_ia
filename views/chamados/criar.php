<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="page-header">
  <h1>Novo Chamado</h1>
  <p>Descreva o problema com detalhes — a IA irá classificar automaticamente</p>
</div>

<div class="form-card">
  <form method="POST" action="<?= APP_URL ?>/chamados/criar">
    <div class="form-group">
      <label for="titulo">Título do problema <span style="color:var(--red)">*</span></label>
      <input type="text" id="titulo" name="titulo" required maxlength="200"
             placeholder="Ex: Computador não liga após atualização do Windows"
             value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label for="descricao">Descrição detalhada <span style="color:var(--red)">*</span></label>
      <textarea id="descricao" name="descricao" required minlength="20"
                placeholder="Descreva o problema com detalhes: quando começou, mensagens de erro, o que já foi tentado..."><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
    </div>

    <div id="ia-preview" class="ia-preview"></div>

    <div style="display:flex;gap:10px;margin-top:24px;align-items:center">
      <button type="submit" class="btn btn-primary">Abrir Chamado</button>
      <a href="<?= APP_URL ?>/chamados" class="btn btn-outline">Cancelar</a>
      <span style="margin-left:auto;font-size:.78rem;color:var(--text3);font-family:var(--font-mono)">⬡ IA analisa ao enviar</span>
    </div>
  </form>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
