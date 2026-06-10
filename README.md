# SupporteIA — Sistema Inteligente de Análise de Chamados de Suporte

![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-005C87?style=flat-square&logo=mysql)
![Google Gemini](https://img.shields.io/badge/Google%20Gemini-API-4285F4?style=flat-square&logo=google)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

## 📋 Descrição

**SupporteIA** é uma aplicação web de gerenciamento de chamados de suporte técnico que utiliza **Inteligência Artificial (Google Gemini API)** para analisar automaticamente cada solicitação registrada.

Ao abrir um chamado, a IA:
- 🏷️ Classifica o problema por **categoria** (Redes, Infraestrutura, Impressora, Software, Hardware, Acesso, E-mail, Segurança ou Outros)
- ⚡ Define a **prioridade** (baixa, média, alta, crítica)
- 🔍 Gera um **diagnóstico técnico**
- 💡 Sugere **próximos passos para resolução**

---

## 🎯 Problema Resolvido

Nas organizações tradicionais, a triagem de chamados é:
- ⏱️ **Lenta** — um atendente lê cada chamado manualmente
- ❌ **Sujeita a erros** — classificações inconsistentes
- 👤 **Dependente** — precisa sempre de alguém disponível

O **SupporteIA** automatiza esse processo com IA, tornando a triagem **instantânea, precisa e escalável**.

---

## ✨ Funcionalidades

### Autenticação & Segurança
- ✅ Login com e-mail e senha (bcrypt)
- ✅ Registro de novo usuário com validação
- ✅ Controle de acesso por perfil (usuário, suporte, admin)
- ✅ Proteção de rotas com sessão PHP
- ✅ Proteção CSRF nas operações de escrita
- ✅ Validação de propriedade dos chamados
- ✅ Limite básico de tentativas de login
- ✅ Mascaramento de dados pessoais antes do envio à API

### Gerenciamento de Chamados
- ✅ Criação de chamados com análise automática de IA
- ✅ Painel dashboard com 4 cards de estatísticas (Abertos, Em Andamento, Resolvidos, Fechados)
- ✅ Tabela de chamados com pesquisa, filtros e paginação
- ✅ Detalhe completo do chamado com histórico
- ✅ Atribuição de atendente responsável

### Análise com IA
- ✅ **Preview em tempo real** enquanto o usuário digita (debounce de 1,8s)
- ✅ Classificação automática por categoria e prioridade
- ✅ Diagnóstico e sugestão de solução gerados pela IA
- ✅ Reanálise de chamados já existentes

### Histórico & Comentários
- ✅ Sistema de comentários com histórico completo
- ✅ Atualização de status (Aberto → Em Andamento → Resolvido → Fechado)
- ✅ Rastreamento de atendente responsável

### Design & UX
- ✅ Modos claro e escuro
- ✅ Interfaces distintas para usuário e administração
- ✅ Responsivo (desktop, tablet, mobile)
- ✅ CSS puro, sem frameworks
- ✅ Animações suaves e indicadores visuais

---

## 🏗️ Arquitetura

O projeto segue o padrão **MVC (Model-View-Controller)** implementado em **PHP puro**, sem frameworks.

```
suporte_ia/
├── config/
│   ├── config.php              # Configurações globais + autoloader
│   └── config.example.php      # Exemplo de configuração local
├── src/
│   ├── Core/
│   │   ├── Database.php        # Singleton PDO
│   │   ├── Router.php          # Roteador GET/POST
│   │   ├── Controller.php      # Superclasse abstrata
│   │   └── Model.php           # CRUD genérico abstrato
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── ChamadoController.php
│   │   ├── AdminController.php
│   │   └── IAController.php
│   ├── Models/
│   │   ├── Chamado.php
│   │   └── Usuario.php
│   └── Services/
│       └── GeminiService.php   # Integração Google Gemini
├── views/
│   ├── layouts/                # base.php + footer.php
│   ├── auth/                   # login.php + registro.php
│   ├── admin/                  # dashboard.php + detalhar.php
│   └── chamados/              # index.php + criar.php + detalhar.php
├── public/
│   ├── index.php               # Front Controller
│   ├── .htaccess
│   ├── css/app.css
│   └── js/app.js
└── database/
    ├── schema.sql              # Instalação nova
    └── migration_v1_1.sql      # Atualização de banco existente
```

---

## 🏛️ Pilares da POO Aplicados

### 1️⃣ Encapsulamento
Todos os atributos são `private` ou `protected`. Acesso apenas por métodos públicos.
```php
class Database {
    private static ?Database $instancia = null;
    private PDO $conexao;
    private function __construct() { ... }
}
```

### 2️⃣ Herança
Superclasses base (`Controller`, `Model`) reutilizáveis por todas as subclasses.
```php
class ChamadoController extends Controller { ... }
class Chamado extends Model { ... }
```

### 3️⃣ Polimorfismo
Cada controller/model sobrescreve métodos com comportamento específico.
```php
class ChamadoController extends Controller {
    public function index() { /* listagem com JOIN */ }
}
```

### 4️⃣ Abstração
Classes abstratas definem contrato sem implementação específica.
```php
abstract class Model {
    abstract public function todos(): array;
}
```

---

## 🗄️ Banco de Dados

### Modelo Entidade-Relacionamento

```
usuarios (1) ──< (N) chamados
usuarios (1) ──< (N) comentarios
chamados (1) ──< (N) comentarios
usuarios (1) ──< (N) historico_status
chamados (1) ──< (N) historico_status
```

### Tabelas

**usuarios**
| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT PK | ID único |
| nome | VARCHAR(100) | Nome completo |
| email | VARCHAR(150) UNIQUE | E-mail de login |
| senha | VARCHAR(255) | Bcrypt hash |
| perfil | ENUM | usuario, suporte, admin |
| criado_em | TIMESTAMP | Data de criação |

**chamados**
| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT PK | ID único |
| titulo | VARCHAR(200) | Título do problema |
| descricao | TEXT | Descrição detalhada |
| categoria | VARCHAR(100) | Classificação IA |
| prioridade | ENUM | baixa, media, alta, critica |
| status | ENUM | aberto, em_andamento, resolvido, fechado |
| usuario_id | INT FK | Quem abriu |
| atendente_id | INT FK | Responsável |
| ia_analise | TEXT | Diagnóstico IA |
| ia_sugestao | TEXT | Sugestão IA |

**comentarios**
| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT PK | ID único |
| chamado_id | INT FK | Chamado associado |
| usuario_id | INT FK | Autor |
| texto | TEXT | Conteúdo |
| criado_em | TIMESTAMP | Data |

**historico_status**
| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT PK | ID único |
| chamado_id | INT FK | Chamado alterado |
| usuario_id | INT FK | Responsável pela alteração |
| status_anterior | VARCHAR(30) | Status anterior |
| status_novo | VARCHAR(30) | Novo status |
| criado_em | TIMESTAMP | Data da alteração |

---

## 🚀 Como Rodar Localmente

### Pré-requisitos
- PHP 8.1 ou superior com as extensões cURL e PDO MySQL
- MySQL 8.0 ou MariaDB compatível
- Apache com `mod_rewrite` habilitado
- Git (opcional se baixar o ZIP)
- XAMPP recomendado no Windows

### Instalação

**1. Clonar o projeto**
```powershell
cd C:\xampp\htdocs
git clone https://github.com/hendrickk09/suporte_ia.git
cd suporte_ia
```

Também é possível baixar o ZIP do GitHub e extrair o conteúdo em:

```text
C:\xampp\htdocs\suporte_ia
```

**2. Criar e configurar o banco de dados**
- Abra phpMyAdmin: `http://localhost/phpmyadmin`
- Importe o arquivo `database/schema.sql`

O próprio script cria o banco `suporte_ia` e todas as tabelas necessárias.

**3. Criar a configuração local**

No PowerShell:

```powershell
Copy-Item config\config.example.php config\config.local.php
```

Edite somente `config/config.local.php`:

```php
<?php

return [
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'suporte_ia',
    'DB_USER' => 'root',
    'DB_PASS' => '',
    'APP_URL' => 'http://localhost/suporte_ia/public',
    'GEMINI_API_KEY' => 'COLE_SUA_CHAVE_AQUI',
    'GEMINI_MODEL' => 'gemini-3.1-flash-lite',
];
```

O arquivo local é ignorado pelo Git para evitar a publicação de credenciais.

**4. Obter a chave da API Gemini**
- Acesse [Google AI Studio](https://aistudio.google.com/app/apikey)
- Crie uma chave da API
- Cole a chave em `config/config.local.php`

**5. Habilitar mod_rewrite**
- Abra `C:\xampp\apache\conf\httpd.conf`
- Procure por `#LoadModule rewrite_module`
- Remova o `#` no início
- Confirme que `AllowOverride All` está habilitado para `C:/xampp/htdocs`
- Reinicie o Apache

**6. Acessar o sistema**
```text
http://localhost/suporte_ia/public/login
```

**7. Criar o primeiro administrador**

Crie uma conta normalmente em `/registro`. Depois, no phpMyAdmin, execute:

```sql
UPDATE usuarios
SET perfil = 'admin'
WHERE email = 'seu-email@exemplo.com';
```

Saia e entre novamente para carregar o novo perfil.

### Atualização de uma instalação existente

Depois de atualizar os arquivos com `git pull`, importe:

```text
database/migration_v1_1.sql
```

Não execute essa migração em uma instalação nova, pois `schema.sql` já contém a estrutura atual.

### Verificação

```powershell
C:\xampp\php\php.exe tests\security_smoke.php
```

---

## 🌐 Deploy em Produção

Para produção, configure `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `APP_URL`,
`GEMINI_API_KEY` e `GEMINI_MODEL` como variáveis de ambiente do servidor.

Requisitos adicionais:

- publicar somente a pasta `public/` como raiz web;
- usar HTTPS;
- impedir acesso público a `config/`, `database/`, `src/`, `tests/` e `views/`;
- adicionar rate limiting no servidor web ou proxy;
- manter backups protegidos do banco;
- não versionar `config/config.local.php`.

---

## 📡 API REST — Endpoints de IA

### Analisar chamado (AJAX)
```http
POST /ia/analisar
Content-Type: application/json
X-CSRF-Token: token-da-sessao

{
  "titulo": "Impressora não imprime",
  "descricao": "A impressora HP do setor de RH..."
}
```

**Resposta:**
```json
{
  "sucesso": true,
  "analise": {
    "categoria": "Impressora",
    "prioridade": "media",
    "analise": "Possível incompatibilidade de toner...",
    "sugestao": "Verificar compatibilidade..."
  }
}
```

### Reanalisar chamado existente
```http
POST /ia/reanalisar/{id}
```

---

## 🔐 Segurança

- ✅ Prepared statements (PDO) contra SQL Injection
- ✅ Bcrypt para hash de senhas
- ✅ Validação de entrada e escape de saída
- ✅ Regeneração da sessão ao login
- ✅ Cookies `HttpOnly`, `SameSite=Lax` e `Secure` quando há HTTPS
- ✅ Proteção CSRF em formulários e chamadas AJAX
- ✅ Autorização por perfil e propriedade do chamado
- ✅ Mascaramento de e-mail, documentos, telefone e segredos antes da API
- ✅ Configuração local ignorada pelo Git

Consulte também [SECURITY.md](SECURITY.md).

---

## 📚 Tecnologias Utilizadas

| Tecnologia | Versão | Uso |
|-----------|--------|-----|
| PHP | 8.1+ | Linguagem principal (POO puro) |
| MySQL | 8.0 | Banco de dados |
| PDO | Nativo | Acesso a dados |
| Google Gemini | 3.1 Flash-Lite | Análise de chamados com IA |
| HTML5 | - | Markup |
| CSS3 | - | Design e layout |
| JavaScript | ES6+ | Interatividade (Fetch API) |
| cURL | Nativo | Requisições HTTP para API |

---

## 📖 Estrutura de Rotas

| Método | Rota | Controller | Descrição |
|--------|------|-----------|-----------|
| GET | `/login` | AuthController | Exibir login |
| POST | `/login` | AuthController | Processar login |
| GET | `/registro` | AuthController | Exibir registro |
| POST | `/registro` | AuthController | Processar registro |
| GET | `/privacidade` | AuthController | Política de privacidade |
| POST | `/logout` | AuthController | Logout |
| GET | `/` | ChamadoController | Redirecionar/listar chamados |
| GET | `/meus-chamados` | ChamadoController | Chamados do usuário |
| GET | `/chamados/criar` | ChamadoController | Formulário novo |
| POST | `/chamados/criar` | ChamadoController | Salvar novo |
| GET | `/meus-chamados/{id}` | ChamadoController | Detalhe do próprio chamado |
| POST | `/meus-chamados/{id}/comentar` | ChamadoController | Adicionar comentário |
| GET | `/admin` | AdminController | Dashboard administrativo |
| GET | `/admin/chamado/{id}` | AdminController | Detalhe administrativo |
| POST | `/admin/chamado/{id}/comentar` | AdminController | Responder chamado |
| POST | `/admin/chamado/{id}/status` | AdminController | Atualizar status |
| POST | `/admin/chamado/{id}/atribuir` | AdminController | Atribuir atendente |
| POST | `/ia/analisar` | IAController | Analisar com IA |
| POST | `/ia/reanalisar/{id}` | IAController | Reanalisar |

---

## 🎓 Projeto Acadêmico

Este é um projeto integrador de **Programação Orientada a Objetos com PHP** desenvolvido para demonstrar:

- ✅ Domínio de PHP POO puro (sem frameworks)
- ✅ Integração com APIs externas (Google Gemini)
- ✅ Padrão MVC e separação de responsabilidades
- ✅ Design de banco de dados relacional
- ✅ Desenvolvimento full-stack (front-end + back-end)
- ✅ Boas práticas de segurança
- ✅ Versionamento com Git

---

## 📸 Telas do Sistema

### Login
![Login](https://github.com/user-attachments/assets/a797177f-2785-4d3a-8e47-9cfecd4e15da)

### Dashboard
<img width="1228" height="771" alt="Image" src="https://github.com/user-attachments/assets/7aa204aa-ebae-4eb3-b7b1-4eef9d08c9e3" />

### Novo Chamado com Preview de IA
<img width="1208" height="1164" alt="Image" src="https://github.com/user-attachments/assets/833028c8-aabb-44d1-ab88-5f86d74e58a2" />

### Detalhe do Chamado
![Detalhe](./docs/screenshots/detalhe.png)

---

## 🐛 Desafios Enfrentados

| Desafio | Solução |
|---------|---------|
| mod_rewrite não funcionava | Editar `httpd.conf` e habilitar LoadModule rewrite_module |
| Latência da API Gemini | Implementar debounce de 1,8s no preview |
| JSON dentro de Markdown na resposta | Regex para limpar blocos ```json antes de parse |
| Erro de autoload de classes | Implementar mapa de classes no config.php |
| Bcrypt não funcionava no XAMPP | Criar arquivo PHP temporário para gerar hash |

---

## 🚧 Próximas Melhorias

- [x] Paginação da listagem administrativa
- [x] Filtros por status e prioridade
- [x] Atribuição de atendente
- [x] Histórico de status
- [ ] Filtro por período e exportação de relatórios
- [ ] Recuperação de senha por e-mail
- [ ] Testes automatizados com PHPUnit

---

## 📝 Licença

MIT License — veja o arquivo `LICENSE` para detalhes.

---

## 👨‍💻 Autor

**Hendrick Symon**

Projeto Integrador — Programação Orientada a Objetos com IA

UNIFAPCE
2026

---

## 🤝 Contribuições

Este é um projeto acadêmico, mas sugestões e melhorias são bem-vindas!

---

## 📞 Suporte

Dúvidas sobre o projeto? Abra uma **Issue** no GitHub.

---

## 🔗 Links Úteis

- [Documentação PHP](https://www.php.net/manual/pt_BR/)
- [Google Gemini API](https://ai.google.dev/)
- [Padrão MVC](https://pt.wikipedia.org/wiki/Modelo%E2%80%93view%E2%80%93controller)
- [Segurança em PHP](https://www.php.net/manual/pt_BR/security.php)
