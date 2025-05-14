# Configuration Item: Database Migrations

Item de Configuração: Database Structure and Migrations
ID: IC-008
Tipo: Código-fonte
Versão: 1.0.0
Repositório: https://github.com/faresmts/fynn
Branch: main
Commit ID: [Latest Commit ID]
Mudanças: 
- Configuração inicial das migrações
- Estrutura de tabelas do sistema
- Relacionamentos e chaves estrangeiras
- Índices e otimizações

Data Release: 01/05/2024

## Estrutura Principal

### Tabelas Core
- users
- transactions
- categories
- budgets
- settings

### Relacionamentos
- transactions -> users
- transactions -> categories
- budgets -> categories
- settings -> users

### Índices
- transactions (date, user_id)
- categories (user_id)
- budgets (month, year)

## Campos Principais

### Users
- id (primary key)
- name
- email
- password
- settings_id
- timestamps

### Transactions
- id (primary key)
- user_id (foreign key)
- category_id (foreign key)
- amount
- description
- date
- type (income/expense)
- timestamps

### Categories
- id (primary key)
- user_id (foreign key)
- name
- type
- color
- timestamps

## Histórico de Alterações

### Versão 1.0.0 (01/05/2024)
- Criação das tabelas principais
- Configuração de relacionamentos
- Adição de índices
- Otimização de consultas 