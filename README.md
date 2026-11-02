# Sistema Salão Nil Sisters

Sistema PHP para gestão de clientes, funcionários, serviços, agendamentos, financeiro e relatórios. O código da aplicação fica em `sistema_salao/`.

## Requisitos

- PHP 8.0 ou superior, com PDO MySQL e mbstring.
- MySQL/MariaDB compatível com o script SQL escolhido.
- Servidor PHP; permissão de escrita em `logs/` e `backups/`.

PHP 8 é necessário por recursos como union types, `mixed` e `str_ends_with`. Não há etapa de instalação via Composer.

## Instalação local

```sh
git clone https://github.com/Kakykyyy/sistema-salao.git
cd sistema-salao
```

1. Abra `sistema_salao/config.php` e configure `DB_HOST`, `DB_USER`, `DB_PASS` e `DB_NAME` (padrão `salao`).
2. Em um banco local de teste, importe **um** dos scripts pelo cliente MySQL ou phpMyAdmin:
   - `schema_basico.sql`: estrutura básica.
   - `schema_completo.sql`: estrutura ampliada e dados de demonstração.
3. Confira o banco selecionado e as instruções de criação presentes no script antes de importar. Não use um banco com dados existentes como ambiente de teste.
4. Execute:

```sh
php -S localhost:8000 -t sistema_salao
```

Abra http://localhost:8000/index.php. Há também `install.php`, descrito no guia interno; consulte-o antes de optar pelo instalador.

## Fluxo principal

Cadastre clientes, funcionários e serviços antes de criar agendamentos. Depois utilize os módulos financeiro e relatórios para acompanhar lançamentos e resultados.

| Arquivo | Responsabilidade |
|---|---|
| `config.php` | Banco, nome, timezone e caminhos |
| `conexao.php` | Singleton PDO, consultas e funções de validação/formatação |
| `clientes.php` | Cadastro de clientes |
| `funcionarios.php` | Equipe e comissões |
| `servicos.php` | Catálogo de serviços |
| `agendamento.php` | Agenda |
| `financeiro.php` | Entradas e saídas |
| `relatorios.php` | Consultas gerenciais |
| `backup.php` | Rotinas de backup |

## Configuração e operação

O timezone padrão é `America/Sao_Paulo`. Os erros são registrados em `sistema_salao/logs/php_errors.log`; a exibição depende do hostname. A conexão usa UTF-8 (`utf8mb4`) e prepared statements na camada Database.

Constantes como `SESSION_TIMEOUT` e `BACKUP_RETENTION` existem na configuração, mas sua presença não comprova que todas as páginas apliquem essas políticas. O repositório contém um backup SQL e um log; eles não são os arquivos indicados para uma instalação limpa.

## Documentação complementar

- [Guia interno existente](sistema_salao/README.md)
- [Guia dos scripts SQL](sistema_salao/SQL_README.md)
- [Histórico de alterações](sistema_salao/CHANGELOG.md)

Os guias internos contêm afirmações de funcionalidades e segurança que devem ser confirmadas no ambiente de uso. Esta atualização documenta a estrutura e o fluxo de instalação, sem certificar prontidão de produção.

## Verificação e problemas comuns

Não há suíte automatizada na árvore analisada. Em base de teste, cadastre cliente, funcionário e serviço; crie um agendamento; registre um lançamento e confira os relatórios. Verifique separadamente backup e restauração.

Erro de conexão: confira serviço do banco, credenciais e extensão PDO MySQL. Erro em `mb_strtolower`: habilite mbstring. Falha ao gravar logs ou backups: confira permissões das pastas.

## Licença

O guia interno menciona MIT, mas não há arquivo LICENSE na árvore analisada. Confirme os termos antes de redistribuir.

---
Documentação baseada na leitura da branch principal em 2 de novembro de 2026. Os procedimentos não foram executados nesta revisão documental.
