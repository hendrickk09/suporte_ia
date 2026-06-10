<?php $perfil_layout = 'usuario'; require_once __DIR__ . '/../layouts/base.php'; ?>

<div class="page-header">
  <h1>Novo Chamado</h1>
  <p>Descreva o problema com detalhes. A IA classifica categoria e prioridade ao enviar.</p>
</div>

<div class="form-card">
  <form method="POST" action="<?= APP_URL ?>/chamados/criar">
    <?= $csrfCampo ?>
    <div class="form-group">
      <label for="titulo">Titulo do problema <span style="color:var(--red)">*</span></label>
      <input type="text" id="titulo" name="titulo" required maxlength="200"
             placeholder="Ex: Impressora nao imprime apos troca de toner">
    </div>
    <div class="form-group">
      <label for="descricao">Descreva o problema <span style="color:var(--red)">*</span></label>
      <textarea id="descricao" name="descricao" required minlength="20" maxlength="5000"
                placeholder="Descreva com detalhes. Não inclua senhas, tokens ou dados pessoais."></textarea>
    </div>

    <div id="ia-preview" class="ia-preview"></div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Abrir Chamado</button>
      <a href="<?= APP_URL ?>/meus-chamados" class="btn btn-outline">Cancelar</a>
      <span class="ia-hint">IA analisa automaticamente ao enviar</span>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
