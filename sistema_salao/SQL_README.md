# 📊 Scripts SQL - Sistema Salão Nil Sisters

## 📋 Arquivos Disponíveis

### 1. `schema_completo.sql`
**Arquivo completo** com:
- ✅ Estrutura completa do banco de dados
- ✅ Todas as tabelas com índices e chaves estrangeiras
- ✅ **Dados de exemplo** para teste imediato
- ✅ Views para relatórios
- ✅ Procedures armazenadas
- ✅ Triggers para integridade

### 2. `schema_basico.sql`
**Arquivo básico** com:
- ✅ Apenas a estrutura das tabelas
- ✅ Índices e chaves estrangeiras
- ✅ **Sem dados de exemplo**
- ✅ Pronto para produção

## 🚀 Como Usar

### Opção 1: Instalação Completa (Recomendado para Testes)

```bash
# No phpMyAdmin ou MySQL Workbench:
# 1. Criar banco de dados 'salao'
# 2. Executar o arquivo schema_completo.sql
```

### Opção 2: Instalação Básica (Recomendado para Produção)

```bash
# No phpMyAdmin ou MySQL Workbench:
# 1. Criar banco de dados 'salao'
# 2. Executar o arquivo schema_basico.sql
```

### Opção 3: Via Linha de Comando

```bash
# Windows (XAMPP)
mysql -u root -p < schema_completo.sql

# Linux/Mac
mysql -u root -p salao < schema_completo.sql
```

## 📊 Estrutura do Banco

### Tabelas Principais

| Tabela | Descrição | Campos Principais |
|--------|-----------|-------------------|
| `clientes` | Cadastro de clientes | nome, telefone, email |
| `funcionarios` | Equipe do salão | nome, telefone, email, comissao_padrao |
| `servicos` | Serviços oferecidos | nome, descricao, preco, comissao |
| `agendamentos` | Agendamentos realizados | cliente, servico, funcionario, data, hora |
| `financeiro` | Controle financeiro | descricao, valor, tipo, categoria |

### Índices e Performance

- ✅ Índices em campos de busca frequente
- ✅ Chaves estrangeiras para integridade referencial
- ✅ COLLATE utf8mb4_unicode_ci para suporte internacional

## 🎯 Dados de Exemplo (schema_completo.sql)

### Funcionários
- Maria Silva (10% comissão)
- João Santos (12% comissão)
- Ana Costa (8% comissão)

### Serviços
- Corte feminino: R$ 45,00 (15% comissão)
- Corte masculino: R$ 30,00 (12% comissão)
- Coloração: R$ 120,00 (20% comissão)
- Manicure: R$ 25,00 (10% comissão)
- Pedicure: R$ 35,00 (12% comissão)
- Massagem: R$ 60,00 (18% comissão)
- Hidratação: R$ 80,00 (15% comissão)
- Limpeza de pele: R$ 70,00 (16% comissão)

### Clientes
- 5 clientes de exemplo com dados completos

### Agendamentos
- Agendamentos futuros (status: agendado)
- Agendamentos passados (status: concluido)

### Financeiro
- Lançamentos de entrada e saída para teste

## 🔍 Views Disponíveis

### `vw_agendamentos_completos`
Relatório completo de agendamentos com dados de cliente, serviço e funcionário.

### `vw_financeiro_mensal`
Resumo financeiro mensal agrupado por tipo.

### `vw_relatorio_comissoes`
Relatório detalhado de comissões por funcionário e período.

## ⚙️ Procedures Disponíveis

### `sp_calcular_comissao_funcionario`
Calcula comissão total de um funcionário em período específico.

### `sp_relatorio_financeiro`
Gera relatório financeiro por período.

## 🔐 Segurança

- ✅ **Prepared Statements** em todo o código PHP
- ✅ **Validação de entrada** em todos os formulários
- ✅ **Índices otimizados** para performance
- ✅ **Chaves estrangeiras** para integridade

## 📝 Configuração

Após executar o SQL, verifique o arquivo `config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Sua senha
define('DB_NAME', 'salao');
define('DB_CHARSET', 'utf8mb4');
```

## 🎉 Próximos Passos

1. ✅ Executar um dos arquivos SQL
2. ✅ Verificar conexão no `conexao.php`
3. ✅ Acessar `index.php` no navegador
4. ✅ Começar a cadastrar dados reais

## 🆘 Suporte

Em caso de problemas:
1. Verifique se o MySQL/MariaDB está rodando
2. Confirme as credenciais no `config.php`
3. Execute os arquivos SQL em ordem
4. Verifique logs de erro do PHP

---
**Sistema de Gestão para Salão Nil Sisters v2.0.0**