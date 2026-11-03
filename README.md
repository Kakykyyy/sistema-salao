<div align="center">

# Salão Nil Sisters

**Gestão da rotina de um salão de beleza**

![PHP 8](https://img.shields.io/badge/PHP%208-20232a?style=flat-square) ![PDO](https://img.shields.io/badge/PDO-20232a?style=flat-square) ![MySQL](https://img.shields.io/badge/MySQL-20232a?style=flat-square) ![HTML](https://img.shields.io/badge/HTML-20232a?style=flat-square) ![CSS](https://img.shields.io/badge/CSS-20232a?style=flat-square)

[Sobre](#sobre) · [Como executar](#como-executar) · [Documentação](DOCUMENTACAO.md)

</div>

---

## Sobre

Sistema web que reúne cadastros, agenda e acompanhamento financeiro de um salão de beleza. A organização em módulos permite acompanhar a operação desde o serviço oferecido até os relatórios.

## Destaques

- Cadastro de clientes, profissionais e serviços.
- Organização de agendamentos.
- Registro de entradas e saídas financeiras.
- Relatórios e rotinas de backup.

## Como executar

Clone o repositório:

```sh
git clone https://github.com/Kakykyyy/sistema-salao.git
cd sistema-salao
```

Requer PHP 8+, PDO MySQL, mbstring e MySQL/MariaDB. Configure `sistema_salao/config.php` e importe um dos scripts SQL em uma base local antes de iniciar.

```sh
php -S localhost:8000 -t sistema_salao
```

Acesse [a aplicação local](http://localhost:8000/index.php).

## Organização

```text
sistema_salao/
├── clientes.php       # Clientes
├── funcionarios.php   # Equipe
├── servicos.php       # Serviços
├── agendamento.php    # Agenda
├── financeiro.php     # Lançamentos
├── relatorios.php     # Relatórios
└── config.php         # Configuração
```

## Prepare o banco

| Arquivo | Finalidade |
|---|---|
| `sistema_salao/schema_basico.sql` | Estrutura básica |
| `sistema_salao/schema_completo.sql` | Estrutura ampliada e dados de demonstração |

Escolha um script para uma base de teste e confira as instruções de criação antes de importar. O banco padrão da aplicação é `salao`.

## Fluxo de uso

Cadastre clientes, profissionais e serviços; organize os agendamentos; registre movimentações e acompanhe os relatórios.

## Documentação técnica

O [guia técnico](DOCUMENTACAO.md) reúne configuração, comportamento dos dados, limitações conhecidas e verificações manuais. As instruções foram elaboradas a partir da leitura do código; a execução dos projetos não foi validada nesta revisão.

## Autor

Desenvolvido por [Kayky Erick · @Kakykyyy](https://github.com/Kakykyyy).
