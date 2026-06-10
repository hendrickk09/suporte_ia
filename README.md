# SupporteIA

Sistema MVC em PHP puro para gerenciamento de chamados, classificação automática, prioridades e atendimento administrativo.

## Requisitos

- PHP 8.1 ou superior com cURL e PDO MySQL
- MySQL 8 ou MariaDB compatível
- Apache com `mod_rewrite`

## Instalação local

1. Copie a pasta para `C:\xampp\htdocs\grafico`.
2. Importe `database/schema.sql` em uma instalação nova.
3. Em uma instalação existente, execute `database/migration_v1_1.sql`.
4. Ajuste `APP_URL` em `config/config.php`.
5. Configure `GEMINI_API_KEY` como variável de ambiente do servidor.
6. Inicie Apache e MySQL e acesse `http://localhost/grafico/public`.

Não coloque a chave diretamente em commits, prints, logs ou arquivos públicos.

## Melhorias da versão

- proteção CSRF em formulários e chamadas AJAX;
- validação de propriedade de chamados;
- cookies de sessão endurecidos;
- logout via POST;
- limite básico de tentativas de login;
- mascaramento de dados pessoais antes da classificação;
- remoção do diagnóstico público;
- pesquisa e filtros administrativos;
- paginação da fila;
- atribuição de atendente;
- histórico de mudanças de status;
- política de privacidade.

## Validação

```powershell
C:\xampp\php\php.exe tests\security_smoke.php
```

