<?php
$perfil_layout = $_SESSION['usuario_perfil'] ?? 'usuario';
require_once __DIR__ . '/layouts/base.php';
?>

<div class="page-header">
  <h1>Privacidade e uso de dados</h1>
  <p>Como as informações dos chamados são tratadas.</p>
</div>

<div class="card detalhe-simples">
  <div class="card-body">
    <div class="ia-block">
      <h4>Dados coletados</h4>
      <p>O sistema armazena dados da conta, conteúdo dos chamados, comentários, status e responsável pelo atendimento.</p>
    </div>
    <div class="ia-block">
      <h4>Classificação automática</h4>
      <p>Título e descrição podem ser enviados ao serviço de classificação. E-mails, documentos, telefones, IPs e credenciais reconhecíveis são mascarados antes do envio.</p>
    </div>
    <div class="ia-block">
      <h4>Cuidados do usuário</h4>
      <p>Não informe senhas, tokens, chaves de API, dados bancários ou documentos pessoais nos chamados.</p>
    </div>
    <div class="ia-block">
      <h4>Acesso</h4>
      <p>Usuários visualizam apenas seus próprios chamados. A equipe administrativa acessa a fila para prestar suporte.</p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
