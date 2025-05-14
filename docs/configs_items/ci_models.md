# Configuration Item: Data Models

Item de Configuração: Eloquent Data Models
ID: IC-016
Tipo: Código-fonte
Versão: 1.0.0
Repositório: https://github.com/seu-usuario/fynn
Branch: main
Commit ID: [Latest Commit ID]
Mudanças: 
- Implementação dos modelos Eloquent
- Definição de relacionamentos
- Configuração de atributos
- Implementação de scopes

Data Release: 01/05/2024

## Modelos Principais

### User
- Atributos: name, email, password, settings
- Relacionamentos: transactions, categories, budgets
- Traits: HasFactory, Notifiable
- Scopes: active, verified

### Transaction
- Atributos: amount, description, date, type
- Relacionamentos: user, category
- Scopes: income, expense, monthly, yearly
- Casts: amount, date

### Category
- Atributos: name, type, color
- Relacionamentos: transactions, budgets
- Scopes: expense, income
- Validações: unique name per user

### Budget
- Atributos: amount, period, category_id
- Relacionamentos: user, category
- Scopes: active, exceeded
- Casts: amount, period

## Traits e Scopes

### Traits Compartilhados
- HasUser
- HasTimestamps
- SoftDeletes
- HasFactory

### Scopes Globais
- ActiveScope
- UserScope
- OrderByLatest
- WithinPeriod

## Validações e Regras

### Regras Comuns
- Valores monetários
- Datas válidas
- Campos obrigatórios
- Unicidade

### Eventos
- Created
- Updated
- Deleted
- Restored

## Histórico de Alterações

### Versão 1.0.0 (01/05/2024)
- Criação dos modelos principais
- Implementação dos relacionamentos
- Configuração de scopes
- Definição de validações 