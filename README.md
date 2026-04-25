# SupporteIA — Sistema Inteligente de Análise de Chamados de Suporte

![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-005C87?style=flat-square&logo=mysql)
![Google Gemini](https://img.shields.io/badge/Google%20Gemini-API-4285F4?style=flat-square&logo=google)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

## 📋 Descrição

**SupporteIA** é uma aplicação web de gerenciamento de chamados de suporte técnico que utiliza **Inteligência Artificial (Google Gemini API)** para analisar automaticamente cada solicitação registrada.

Ao abrir um chamado, a IA:
- 🏷️ Classifica o problema por **categoria** (Hardware, Software, Rede, E-mail, Impressora, etc.)
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

### Gerenciamento de Chamados
- ✅ Criação de chamados com análise automática de IA
- ✅ Painel dashboard com 4 cards de estatísticas (Abertos, Em Andamento, Resolvidos, Fechados)
- ✅ Tabela de chamados com filtros por status, categoria e prioridade
- ✅ Detalhe completo do chamado com histórico

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
- ✅ Dark mode moderno e profissional
- ✅ Responsivo (desktop, tablet, mobile)
- ✅ CSS puro, sem frameworks
- ✅ Animações suaves e indicadores visuais

---

## 🏗️ Arquitetura

O projeto segue o padrão **MVC (Model-View-Controller)** implementado em **PHP puro**, sem frameworks.

```
suporte_ia/
├── config/
│   └── config.php              # Configurações globais + autoloader
├── src/
│   ├── Core/
│   │   ├── Database.php        # Singleton PDO
│   │   ├── Router.php          # Roteador GET/POST
│   │   ├── Controller.php      # Superclasse abstrata
│   │   └── Model.php           # CRUD genérico abstrato
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── ChamadoController.php
│   │   └── IAController.php
│   ├── Models/
│   │   ├── Chamado.php
│   │   └── Usuario.php
│   └── Services/
│       └── GeminiService.php   # Integração Google Gemini
├── views/
│   ├── layouts/                # header.php + footer.php
│   ├── auth/                   # login.php + registro.php
│   └── chamados/               # index.php + criar.php + detalhar.php
├── public/
│   ├── index.php               # Front Controller
│   ├── .htaccess
│   ├── css/app.css
│   └── js/app.js
└── database/
    └── schema.sql              # DDL completo
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

---

## 🚀 Como Rodar Localmente

### Pré-requisitos
- PHP 8.1+
- MySQL 8.0+
- Apache com mod_rewrite habilitado
- (Recomendado: XAMPP)

### Instalação

**1. Extrair o projeto**
```bash
# No Windows
C:\xampp\htdocs\suporte_ia\
```

**2. Criar o banco de dados**
- Abra phpMyAdmin: `http://localhost/phpmyadmin`
- Crie um banco chamado `suporte_ia`
- Importe o arquivo `database/schema.sql`

**3. Configurar as credenciais**
Edite `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'suporte_ia');
define('DB_USER', 'root');
define('DB_PASS', '');
define('GEMINI_API_KEY', 'SUA_CHAVE_AQUI');
```

**4. Obter a chave da API Gemini**
- Acesse: `https://aistudio.google.com/app/apikey`
- Clique em "Create API Key"
- Copie a chave e cole em `config.php`

**5. Habilitar mod_rewrite**
- Abra `C:\xampp\apache\conf\httpd.conf`
- Procure por `#LoadModule rewrite_module`
- Remova o `#` no início
- Reinicie o Apache

**6. Acessar o sistema**
```
http://localhost/suporte_ia/public/login
```

### Usuário de teste
- **E-mail:** admin@suporte.com
- **Senha:** 123456

---

## 🌐 Deploy em Produção

### InfinityFree (Gratuito)

1. Acesse `https://infinityfree.com` e crie uma conta
2. Crie um subdomínio (ex: `suporteia.infinityfreeapp.com`)
3. Faça upload do projeto via FTP ou File Manager
4. Crie o banco de dados MySQL
5. Importe o `schema.sql`
6. Configure `config.php` com as credenciais fornecidas

### Railway (Recomendado)

1. Acesse `https://railway.app`
2. Conecte com sua conta GitHub
3. Railway detecta PHP automaticamente
4. Defina as variáveis de ambiente
5. Deploy automático a cada push no GitHub

---

## 📡 API REST — Endpoints de IA

### Analisar chamado (AJAX)
```http
POST /ia/analisar
Content-Type: application/json

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
- ✅ Validação e sanitização de entrada (htmlspecialchars)
- ✅ Session regeneration ao login
- ✅ Proteção de rotas com autenticação
- ✅ .gitignore para não expor `config.php` (chave da API)

---

## 📚 Tecnologias Utilizadas

| Tecnologia | Versão | Uso |
|-----------|--------|-----|
| PHP | 8.1+ | Linguagem principal (POO puro) |
| MySQL | 8.0 | Banco de dados |
| PDO | Nativo | Acesso a dados |
| Google Gemini | 2.0 Flash | Análise de chamados com IA |
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
| GET | `/logout` | AuthController | Logout |
| GET | `/chamados` | ChamadoController | Dashboard |
| GET | `/chamados/criar` | ChamadoController | Formulário novo |
| POST | `/chamados/criar` | ChamadoController | Salvar novo |
| GET | `/chamados/{id}` | ChamadoController | Detalhe |
| POST | `/chamados/{id}/comentar` | ChamadoController | Adicionar comentário |
| POST | `/chamados/{id}/status` | ChamadoController | Atualizar status |
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

- [ ] Paginação da listagem de chamados
- [ ] Filtros avançados (status, categoria, prioridade, data)
- [ ] Dashboard com gráficos de tendências
- [ ] Testes unitários (PHPUnit)

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


