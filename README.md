# SupporteIA

Sistema web para registro, classificação e acompanhamento de chamados de suporte técnico. A aplicação utiliza PHP orientado a objetos, arquitetura MVC, MySQL e a API Google Gemini para sugerir categoria, prioridade, diagnóstico e próximos passos para cada chamado.

O projeto possui interfaces separadas para usuários e equipe de suporte:

- usuários registram e acompanham os próprios chamados;
- administradores e atendentes consultam a fila geral, aplicam filtros, atribuem responsáveis, respondem solicitações e alteram o status;
- a integração com o Gemini classifica os chamados nas categorias Redes, Infraestrutura, Impressora, Software, Hardware, Acesso, E-mail, Segurança ou Outros;
- quando a API não está configurada ou está indisponível, o chamado continua sendo registrado com uma classificação de fallback.

## Tecnologias utilizadas

| Tecnologia | Finalidade |
| --- | --- |
| PHP 8.1 ou superior | Aplicação, regras de negócio e orientação a objetos |
| MySQL 8 ou MariaDB 10.4+ | Persistência dos dados |
| PDO MySQL | Consultas preparadas e acesso ao banco |
| HTML5 e CSS3 | Estrutura e interface |
| JavaScript | Preview da classificação e alternância de tema |
| Google Gemini API | Classificação e sugestão de resolução |
| Apache `mod_rewrite` | Roteamento em instalações com XAMPP/Apache |

O projeto não utiliza Composer, npm ou frameworks. Todas as dependências de execução são extensões nativas do PHP.

## Pré-requisitos

- PHP 8.1 ou superior;
- extensões PHP `curl`, `json`, `mbstring`, `pdo`, `pdo_mysql` e `session`;
- MySQL 8 ou MariaDB 10.4 ou superior;
- Apache com `mod_rewrite`, caso seja utilizado XAMPP;
- acesso à internet apenas para a integração com o Gemini e carregamento das fontes externas;
- chave da API Gemini opcional para testar a classificação automática.

No Windows, o ambiente recomendado é o XAMPP com Apache, PHP e MySQL.

## Instalação

### 1. Obter o código

Com Git:

```powershell
cd C:\xampp\htdocs
git clone https://github.com/hendrickk09/suporte_ia.git
cd suporte_ia
```

Também é possível baixar o ZIP do repositório e extrair o conteúdo para:

```text
C:\xampp\htdocs\suporte_ia
```

### 2. Verificar os requisitos

No terminal, execute:

```powershell
C:\xampp\php\php.exe scripts\check_requirements.php
```

O comando informa extensões ausentes, problemas no arquivo de configuração e avisos sobre a chave Gemini.

### 3. Criar o banco de dados

1. Inicie o MySQL pelo painel do XAMPP.
2. Acesse `http://localhost/phpmyadmin`.
3. Abra a opção **Importar**.
4. Selecione `database/schema.sql`.
5. Execute a importação.

O script cria o banco `suporte_ia` e as tabelas:

- `usuarios`;
- `chamados`;
- `comentarios`;
- `historico_status`.

Para atualizar uma instalação antiga que ainda não possui o histórico de status, importe somente `database/migration_v1_1.sql`.

## Configuração do ambiente

### Arquivo local

Copie o arquivo de exemplo:

```powershell
Copy-Item config\config.example.php config\config.local.php
```

Edite `config/config.local.php`:

```php
<?php

return [
    'DB_HOST' => 'localhost',
    'DB_PORT' => '3306',
    'DB_NAME' => 'suporte_ia',
    'DB_USER' => 'root',
    'DB_PASS' => '',
    'APP_URL' => 'http://localhost/suporte_ia/public',
    'GEMINI_API_KEY' => 'SUA_CHAVE_AQUI',
    'GEMINI_MODEL' => 'gemini-2.5-flash-lite',
];
```

`config/config.local.php` é ignorado pelo Git e não deve ser enviado ao repositório.

### Variáveis de ambiente

A aplicação também aceita as seguintes variáveis de ambiente:

| Variável | Obrigatória | Valor padrão |
| --- | --- | --- |
| `DB_HOST` | Não | `localhost` |
| `DB_PORT` | Não | `3306` |
| `DB_NAME` | Não | `suporte_ia` |
| `DB_USER` | Não | `root` |
| `DB_PASS` | Não | vazio |
| `APP_URL` | Sim, se a URL for diferente do padrão | `http://localhost/suporte_ia/public` |
| `GEMINI_API_KEY` | Somente para usar a IA | não configurada |
| `GEMINI_MODEL` | Não | `gemini-2.5-flash-lite` |

Não há carregador de arquivo `.env` neste projeto. Configure as variáveis diretamente no sistema operacional/servidor ou use `config/config.local.php`.

Crie uma chave em [Google AI Studio](https://aistudio.google.com/app/apikey)

## Execução do projeto

### Opção A: XAMPP e Apache

1. Inicie Apache e MySQL no painel do XAMPP.
2. Confirme que esta linha está habilitada em `C:\xampp\apache\conf\httpd.conf`:

```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

3. No bloco referente a `C:/xampp/htdocs`, confirme:

```apache
AllowOverride All
```

4. Reinicie o Apache.
5. Acesse:

```text
http://localhost/suporte_ia/public/login
```

### Opção B: servidor embutido do PHP

Esta opção não exige Apache, mas o MySQL ainda precisa estar em execução.

No `config/config.local.php`, altere:

```php
'APP_URL' => 'http://localhost:8000',
```

Depois execute:

```powershell
C:\xampp\php\php.exe -S localhost:8000 -t public public\router.php
```

Acesse `http://localhost:8000/login`.

## Primeiro acesso administrativo

As contas criadas pela tela de registro recebem o perfil `usuario`. Isso impede que qualquer pessoa se cadastre diretamente como administrador.

Para promover a primeira conta, abra a aba SQL do phpMyAdmin e execute:

```sql
USE suporte_ia;

UPDATE usuarios
SET perfil = 'admin'
WHERE email = 'seu-email@exemplo.com';
```

Saia da aplicação e entre novamente para atualizar os dados da sessão.

## Validação

Execute o verificador de requisitos:

```powershell
C:\xampp\php\php.exe scripts\check_requirements.php
```

Execute o teste de mascaramento de dados:

```powershell
C:\xampp\php\php.exe tests\security_smoke.php
```

Para validar a sintaxe dos arquivos PHP:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object {
    C:\xampp\php\php.exe -l $_.FullName
}
```

## Possíveis problemas e soluções

### Erro 404 nas rotas

- confirme que `mod_rewrite` está habilitado;
- confirme `AllowOverride All` no Apache;
- verifique se `public/.htaccess` existe;
- confira se `APP_URL` corresponde exatamente à pasta usada no navegador;
- reinicie o Apache após alterar sua configuração.

### Erro 403 ao enviar formulários

- confirme que os cookies estão habilitados no navegador;
- verifique se o diretório definido por `session.save_path` existe e possui permissão de escrita;
- execute `scripts/check_requirements.php` para identificar problemas no diretório de sessões;
- atualize a página antes de reenviar um formulário aberto há muito tempo.

### Falha na conexão com o banco

- confirme que o MySQL está iniciado;
- confira `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` e `DB_PASS`;
- importe `database/schema.sql`;
- verifique se o banco configurado possui as quatro tabelas esperadas.

### Classificação automática indisponível

- confirme que a extensão `curl` está habilitada;
- confira a chave em `GEMINI_API_KEY`;
- verifique a conexão com a internet;
- confirme se o projeto/modelo possui quota disponível no Google AI Studio;
- aguarde o restabelecimento da quota em respostas HTTP 429.

O sistema continua registrando chamados quando a API não responde, mas utiliza valores de fallback até uma nova análise.

### Página sem estilos ou links incorretos

O valor de `APP_URL` provavelmente não corresponde ao endereço usado no navegador. Não inclua barra no final da URL.

### Funções `mb_*` não encontradas

Habilite a extensão `mbstring` no `php.ini` e reinicie o Apache.

### Alterações de JavaScript não aparecem

Use `Ctrl + F5` para ignorar o cache do navegador.

## Estrutura do projeto

```text
suporte_ia/
|-- config/
|   |-- config.php
|   `-- config.example.php
|-- database/
|   |-- schema.sql
|   `-- migration_v1_1.sql
|-- public/
|   |-- css/
|   |-- js/
|   |-- .htaccess
|   |-- index.php
|   `-- router.php
|-- scripts/
|   `-- check_requirements.php
|-- src/
|   |-- Controllers/
|   |-- Core/
|   |-- Models/
|   `-- Services/
|-- tests/
|   `-- security_smoke.php
|-- views/
|   |-- admin/
|   |-- auth/
|   |-- chamados/
|   `-- layouts/
|-- LICENSE
|-- README.md
`-- SECURITY.md
```

## Arquitetura e orientação a objetos

O sistema utiliza uma implementação manual do padrão MVC:

- **Models:** acesso e persistência dos dados;
- **Views:** interface apresentada ao usuário;
- **Controllers:** validação das requisições e coordenação do fluxo;
- **Services:** integração isolada com a API Gemini;
- **Core:** roteamento, conexão PDO e recursos compartilhados.

As classes aplicam encapsulamento, herança e abstração para separar autenticação, chamados, banco de dados e integração externa.

## Segurança

O projeto inclui:

- hash de senhas com `password_hash`;
- consultas preparadas com PDO;
- proteção CSRF;
- controle de acesso por perfil e propriedade do chamado;
- regeneração de sessão após login;
- cookies `HttpOnly`, `SameSite=Lax` e `Secure` quando há HTTPS;
- mascaramento de dados pessoais antes do envio ao Gemini;
- configuração local e arquivos `.env` ignorados pelo Git.

Consulte [SECURITY.md](SECURITY.md) para informações adicionais.

## Licença

Distribuído sob a licença MIT. Consulte [LICENSE](LICENSE).

## Autor

Hendrick Symon

Projeto Integrador de Programação Orientada a Objetos com PHP

UNIFAPCE, 2026
