# 🎨 Sistema Salão Nil Sisters

**Sistema de Gestão Completo para Salões de Beleza**

[![Versão](https://img.shields.io/badge/Versão-2.0.0-blue.svg)](CHANGELOG.md)
[![PHP](https://img.shields.io/badge/PHP-8.0+-purple.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com)
[![Licença](https://img.shields.io/badge/Licença-MIT-green.svg)](LICENSE)

> Um sistema moderno e completo para gestão de salões de beleza, desenvolvido com PHP, MySQL e interface responsiva.

## 📋 Índice

- [🚀 Funcionalidades](#-funcionalidades)
- [🛠️ Tecnologias](#%EF%B8%8F-tecnologias)
- [📦 Instalação](#-instalação)
- [🔧 Configuração](#-configuração)
- [📊 Banco de Dados](#-banco-de-dados)
- [🎯 Uso](#-uso)
- [📁 Estrutura](#-estrutura)
- [🔒 Segurança](#-segurança)
- [📈 Performance](#-performance)
- [🐛 Troubleshooting](#-troubleshooting)
- [📝 Changelog](#-changelog)
- [🤝 Contribuição](#-contribuição)
- [📄 Licença](#-licença)

## 🚀 Funcionalidades

### 👥 Gestão de Clientes
- ✅ Cadastro completo de clientes
- ✅ Histórico de atendimentos
- ✅ Dados de contato
- ✅ Validação de telefone

### 👨‍💼 Gestão de Funcionários
- ✅ Cadastro de equipe
- ✅ Controle de comissões
- ✅ Especialidades e horários

### 💇‍♀️ Gestão de Serviços
- ✅ Catálogo de serviços
- ✅ Preços e durações
- ✅ Categorias organizadas

### 📅 Agendamentos
- ✅ Sistema de agendamento online
- ✅ Controle de horários
- ✅ Status de agendamentos
- ✅ Notificações automáticas

### 💰 Controle Financeiro
- ✅ Lançamentos de receita/despesa
- ✅ Relatórios financeiros
- ✅ Controle de comissões
- ✅ Categorias organizadas

### 📊 Relatórios
- ✅ Relatórios de agendamentos
- ✅ Relatórios financeiros mensais
- ✅ Relatórios de comissões
- ✅ Exportação de dados

## 🛠️ Tecnologias

### Backend
- **PHP 8.0+** - Linguagem principal
- **PDO** - Conexão segura com banco
- **MySQL 8.0+** - Banco de dados relacional

### Frontend
- **HTML5** - Estrutura semântica
- **CSS3** - Design moderno e responsivo
- **JavaScript** - Interações dinâmicas (mínimo)

### Infraestrutura
- **Apache/Nginx** - Servidor web
- **UTF-8** - Codificação universal
- **InnoDB** - Engine de banco otimizada

## 📦 Instalação

### Pré-requisitos
- PHP 8.0 ou superior
- MySQL 8.0 ou MariaDB 10.0+
- Apache/Nginx com mod_rewrite
- Extensão PDO MySQL habilitada

### Instalação Automática (Recomendado)

1. **Clone ou baixe** os arquivos para seu servidor
2. **Acesse o instalador**: `http://seusite.com/install.php`
3. **Siga as instruções** na tela
4. **Pronto!** Sistema instalado automaticamente

### Instalação Manual

1. **Configure o banco de dados**:
   ```bash
   mysql -u root -p < schema_completo.sql
   ```

2. **Configure as credenciais** em `config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', 'suasenha');
   define('DB_NAME', 'salao');
   ```

3. **Acesse o sistema**: `http://seusite.com/index.php`

## 🔧 Configuração

### Arquivo `config.php`
```php
// Configurações básicas
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'salao');

// Configurações avançadas
define('APP_TIMEZONE', 'America/Sao_Paulo');
define('ITEMS_PER_PAGE', 10);
define('SESSION_TIMEOUT', 3600);
```

### Arquivo `.htaccess`
- Configurações de segurança
- Regras de reescrita
- Compressão e cache
- Headers de proteção

## 📊 Banco de Dados

### Tabelas Principais
```
clientes       - Dados dos clientes
funcionarios   - Equipe do salão
servicos       - Serviços oferecidos
agendamentos   - Agendamentos realizados
financeiro     - Controle financeiro
```

### Arquivos SQL Disponíveis
- **`schema_completo.sql`** - Estrutura + dados de exemplo
- **`schema_basico.sql`** - Apenas estrutura (produção)

### Views Disponíveis
- `vw_agendamentos_completos` - Relatório detalhado
- `vw_financeiro_mensal` - Resumo mensal
- `vw_relatorio_comissoes` - Comissões por funcionário

## 🎯 Uso

### Navegação Principal
- **Dashboard** - Visão geral do sistema
- **Clientes** - Gerenciar cadastro de clientes
- **Agendamentos** - Sistema de reservas
- **Financeiro** - Controle de receitas/despesas

### Operações Comuns
1. **Cadastrar cliente** → Preencher dados pessoais
2. **Criar agendamento** → Selecionar serviço e horário
3. **Registrar financeiro** → Lançar receitas/despesas
4. **Gerar relatórios** → Visualizar dados consolidados

## 📁 Estrutura

```
sistema_salao/
├── 📄 index.php           # Dashboard principal
├── 📄 clientes.php        # Gestão de clientes
├── 📄 agendamento.php     # Sistema de agendamentos
├── 📄 financeiro.php      # Controle financeiro
├── 📄 config.php          # Configurações do sistema
├── 📄 conexao.php         # Conexão com banco
├── 📄 install.php         # Instalador automático
├── 📄 header.php          # Cabeçalho do sistema
├── 📄 footer.php          # Rodapé do sistema
├── 📁 assets/             # Arquivos estáticos
│   └── 📄 style.css       # Estilos CSS
├── 📄 schema_completo.sql # Banco com dados exemplo
├── 📄 schema_basico.sql   # Banco básico
├── 📄 SQL_README.md       # Documentação SQL
├── 📄 CHANGELOG.md        # Histórico de mudanças
├── 📄 .htaccess           # Configurações Apache
└── 📄 README.md           # Esta documentação
```

## 🔒 Segurança

### Medidas Implementadas
- ✅ **Prepared Statements** - Proteção contra SQL Injection
- ✅ **Sanitização de entrada** - Validação de dados
- ✅ **Headers de segurança** - Proteção XSS/CSRF
- ✅ **Logs estruturados** - Auditoria de ações
- ✅ **Validação de formulários** - Dados consistentes

### Recomendações Adicionais
- Use HTTPS em produção
- Mantenha backups regulares
- Atualize dependências
- Monitore logs de erro

## 📈 Performance

### Otimizações Implementadas
- ✅ **Índices otimizados** no banco de dados
- ✅ **Compressão GZIP** de assets
- ✅ **Cache de navegador** configurado
- ✅ **Queries eficientes** com JOINs apropriados
- ✅ **Lazy loading** em listagens

### Métricas Esperadas
- **Tempo de resposta**: < 500ms
- **Uptime**: > 99.5%
- **Concurrent users**: Até 100 simultâneos

## 🐛 Troubleshooting

### Problemas Comuns

#### Erro de Conexão com Banco
```
Solução: Verifique credenciais em config.php
         Confirme se MySQL está rodando
```

#### Página Não Carrega
```
Solução: Verifique permissões de arquivo (755)
         Confirme configuração do Apache
```

#### Erro 500
```
Solução: Verifique logs em logs/php_errors.log
         Confirme versão do PHP (8.0+)
```

### Logs e Debug
- **PHP Errors**: `logs/php_errors.log`
- **Application Logs**: `logs/error_YYYY-MM-DD.log`
- **Debug Mode**: Configure em `config.php`

## 📝 Changelog

Veja todas as mudanças em [CHANGELOG.md](CHANGELOG.md)

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch (`git checkout -b feature/nova-feature`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova feature'`)
4. Push para a branch (`git push origin feature/nova-feature`)
5. Abra um Pull Request

### Padrões de Código
- PSR-12 para PHP
- BEM para CSS
- Commits semânticos

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para detalhes.

---

## 📞 Suporte

**Sistema Salão Nil Sisters v2.0.0**
- Desenvolvido com ❤️ para salões de beleza
- Compatível com PHP 8+ e MySQL 8+
- Interface moderna e responsiva
- Código limpo e documentado

**Precisa de ajuda?** Verifique a documentação ou abra uma issue.

---

*Última atualização: Dezembro 2024*

### Infraestrutura
- **Apache/Nginx** - Servidor web
- **Composer** - Gerenciamento de dependências
- **Git** - Controle de versão

## 📁 Estrutura do Projeto

```
sistema_salao/
├── 📄 index.php              # Dashboard principal
├── 📄 clientes.php           # Gestão de clientes
├── 📄 servicos.php           # Catálogo de serviços
├── 📄 funcionarios.php       # Controle de funcionários
├── 📄 agendamento.php        # Sistema de agendamentos
├── 📄 financeiro.php         # Controle financeiro
├── 📄 relatorios.php         # Relatórios e exportação
├── 📄 backup.php             # Sistema de backup
├── 📄 config.php             # Configurações globais
├── 📄 conexao.php            # Conexão com banco de dados
├── 📄 header.php             # Template do cabeçalho
├── 📄 footer.php             # Template do rodapé
├── 📁 assets/                # Recursos estáticos
│   ├── 📄 style.css          # Estilos CSS
│   └── 📄 logo.png           # Logo da empresa
├── 📄 .htaccess              # Configurações Apache
├── 📄 sw.js                  # Service Worker
├── 📁 backups/               # Diretório de backups
├── 📁 logs/                  # Logs do sistema
└── 📄 README.md              # Esta documentação
```

## 🗄️ Estrutura do Banco de Dados

### Tabelas Principais

#### `clientes`
```sql
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nome (nome),
    INDEX idx_email (email)
);
```

#### `servicos`
```sql
CREATE TABLE servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    duracao VARCHAR(50) NOT NULL,
    preco DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativo TINYINT(1) DEFAULT 1,
    INDEX idx_nome (nome),
    INDEX idx_ativo (ativo)
);
```

#### `funcionarios`
```sql
CREATE TABLE funcionarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    email VARCHAR(255) UNIQUE,
    comissao DECIMAL(5,2) DEFAULT 0.00,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativo TINYINT(1) DEFAULT 1
);
```

#### `agendamentos`
```sql
CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_servico INT NOT NULL,
    id_funcionario INT,
    data_hora DATETIME NOT NULL,
    status ENUM('agendado','confirmado','concluido','cancelado') DEFAULT 'agendado',
    observacoes TEXT,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id),
    FOREIGN KEY (id_servico) REFERENCES servicos(id),
    FOREIGN KEY (id_funcionario) REFERENCES funcionarios(id),
    INDEX idx_data_hora (data_hora),
    INDEX idx_status (status)
);
```

#### `financeiro`
```sql
CREATE TABLE financeiro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('entrada','saida') NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    descricao TEXT,
    valor DECIMAL(10,2) NOT NULL,
    data_lancamento DATE NOT NULL,
    id_agendamento INT,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_agendamento) REFERENCES agendamentos(id),
    INDEX idx_tipo (tipo),
    INDEX idx_data (data_lancamento),
    INDEX idx_categoria (categoria)
);
```

## 🚀 Instalação e Configuração

### Pré-requisitos
- **PHP 8.0+** com extensões: mysqli, pdo, mbstring, gd
- **MySQL/MariaDB 5.7+**
- **Apache/Nginx** com mod_rewrite
- **Composer** (opcional)

### Passos de Instalação

1. **Clone/Download do projeto**
   ```bash
   cd /var/www/html/
   git clone https://github.com/seu-repo/sistema-salao.git
   cd sistema-salao
   ```

2. **Configure as permissões**
   ```bash
   chown -R www-data:www-data .
   chmod -R 755 .
   chmod -R 777 backups/ logs/
   ```

3. **Configure o banco de dados**
   ```sql
   CREATE DATABASE salao CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

4. **Execute o script de criação das tabelas**
   ```php
   // Acesse index.php para criar tabelas automaticamente
   ```

5. **Configure o arquivo config.php**
   ```php
   // Ajuste as constantes conforme seu ambiente
   define('DB_HOST', 'localhost');
   define('DB_USER', 'seu_usuario');
   define('DB_PASS', 'sua_senha');
   define('DB_NAME', 'salao');
   ```

6. **Configure o .htaccess** (se necessário)
   - Ajuste os caminhos absolutos
   - Configure redirects HTTPS se aplicável

## 🔧 Configuração Avançada

### Otimização de Performance

#### Cache de Navegador
- CSS e JS: 30 dias
- Imagens: 30 dias
- HTML dinâmico: No-cache

#### Compressão GZIP
- Ativada automaticamente via .htaccess
- Redução de ~70% no tamanho dos arquivos

#### Service Worker
- Cache offline para recursos estáticos
- Funciona mesmo sem conexão

### Segurança

#### Headers HTTP
```apache
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
```

#### Validações
- E-mail: RFC compliant
- Telefone: 10-11 dígitos
- Senhas: Mínimo 8 caracteres (futuro)
- Entrada: Sanitização automática

#### Logs de Auditoria
- Todas as ações são logadas
- Arquivos em `/logs/`
- Rotação automática por data

## 📊 Monitoramento e Manutenção

### Logs do Sistema
- **Localização:** `/logs/`
- **Rotação:** Diária automática
- **Tipos:** Erros, ações, auditoria

### Backup Automático
- **Frequência:** Manual/Diária (configurável)
- **Localização:** `/backups/`
- **Retenção:** 30 dias
- **Formato:** SQL completo

### Performance
- **Queries otimizadas** com índices apropriados
- **Paginação** para grandes datasets
- **Cache** de navegador e Service Worker
- **Compressão** automática

## 🔄 Atualizações e Migrações

### Versão 2.0.0 (Atual)
- ✅ Sistema completamente reescrito
- ✅ Segurança aprimorada com prepared statements
- ✅ Performance otimizada
- ✅ Interface moderna e responsiva
- ✅ Service Worker para cache offline
- ✅ Logs de auditoria completos

### Próximas Features
- 🔄 Autenticação de usuários
- 🔄 API REST para integração
- 🔄 Notificações push
- 🔄 Relatórios avançados
- 🔄 Integração com WhatsApp

## 🐛 Troubleshooting

### Problemas Comuns

#### Erro de Conexão com Banco
```php
// Verifique as configurações em config.php
// Certifique-se que o MySQL está rodando
// Verifique permissões do usuário
```

#### Erro 500 - Internal Server Error
```bash
# Verifique logs do Apache/PHP
tail -f /var/log/apache2/error.log
tail -f /var/log/php/error.log
```

#### Problemas de Permissões
```bash
# Ajuste permissões dos diretórios
chmod 755 /var/www/html/sistema-salao
chmod 777 /var/www/html/sistema-salao/backups
chmod 777 /var/www/html/sistema-salao/logs
```

#### Cache não Atualizando
```bash
# Limpe cache do navegador
# Force reload: Ctrl+F5
# Ou desabilite cache temporariamente
```

## 📞 Suporte

### Canais de Suporte
- **E-mail:** suporte@nilsisters.com
- **WhatsApp:** (11) 99999-9999
- **Site:** https://nilsisters.com

### Documentação Técnica
- **API Docs:** `/docs/api/`
- **Guia do Desenvolvedor:** `/docs/dev/`
- **Manual do Usuário:** `/docs/user/`

## 📈 Roadmap

### Q2 2026
- [ ] Sistema de autenticação
- [ ] Dashboard com gráficos
- [ ] Notificações por e-mail

### Q3 2026
- [ ] API REST completa
- [ ] App mobile companion
- [ ] Integração com redes sociais

### Q4 2026
- [ ] IA para recomendações
- [ ] Análise preditiva
- [ ] Marketplace de produtos

## 🤝 Contribuição

### Como Contribuir
1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

### Padrões de Código
- PSR-12 para PHP
- BEM para CSS
- ESLint para JavaScript
- Commits semânticos

## 📜 Licença

Este projeto é propriedade da Nil Sisters.
Todos os direitos reservados.

---

**Desenvolvido com ❤️ para profissionais da beleza**

*Nil Sisters - Sistema de Gestão para Salão v2.0.0*
- 4 clientes
- Alguns agendamentos e lançamentos financeiros

## Como Usar

1. **Dashboard**: Visão geral com estatísticas
2. **Clientes**: Cadastrar e gerenciar clientes
3. **Serviços**: Configurar serviços oferecidos
4. **Funcionários**: Cadastrar equipe e definir comissões
5. **Agendamentos**: Agendar serviços com desconto
6. **Financeiro**: Controlar entradas e despesas

## Funcionalidades Especiais

- **Sistema de Comissão**: Cálculo automático baseado no serviço realizado
- **Descontos**: Aplicação de desconto por agendamento
- **Relatórios**: Visão geral financeira e operacional
- **Interface Responsiva**: Compatível com dispositivos móveis

## Desenvolvimento

- **PHP**: Backend e lógica de negócio
- **MySQL**: Banco de dados relacional
- **CSS**: Interface moderna e elegante
- **HTML**: Estrutura semântica

## Suporte

Para dúvidas ou problemas, verifique:
1. Conexão com o banco de dados
2. Permissões de arquivo
3. Configurações do XAMPP

---

**Nil Sisters** - Gestão completa para salão e estética