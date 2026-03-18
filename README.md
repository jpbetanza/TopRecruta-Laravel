# Avaliação - Back-end Laravel
Olá! Muito obrigado por participar da avaliação técnica para integrar a equipe de desenvolvimento da Top Solutions.

## 📋 Sobre o Projeto

### 🏛️ Portal da Transparência

Este é um **desafio técnico** para avaliação de candidatos **Back-end Laravel**.

O projeto é uma API simplificada de um **Portal da Transparência**, onde órgãos públicos (secretarias de uma Prefeitura) divulgam como o dinheiro público está sendo gasto — incluindo qual órgão gastou, com qual fornecedor, e quanto.

> ⚠️ **Estado atual**: Toda a lógica da API está implementada diretamente no arquivo de rotas (`routes/api.php`), sem Controllers, Resources, Form Requests ou qualquer separação de responsabilidades. **Sua tarefa é refatorar.**

---

## 🚀 Setup do Projeto

```bash
# 1. Crie um Fork do repositório e clone para sua máquina

# 2. Instalar dependências
composer install

# 3. Configurar ambiente - copie o arquivo .env.example para .env e gere uma chave
cp .env.example .env
php artisan key:generate

# 4. Crie o banco de dados sqlite na pasta abaixo
database/database.sqlite

# 5. Rodar migrations e seeds
php artisan migrate --seed

# 6. Iniciar o servidor
php artisan serve
```

A API estará disponível em `http://localhost:8000/api`

---

## 📊 Estrutura do Banco de Dados

### 🏢 Órgãos (`orgaos`)
| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | bigint | Chave primária |
| name | string | Nome do órgão (único) |
| timestamps | - | created_at / updated_at |

### 🏭 Fornecedores (`fornecedores`)
| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | bigint | Chave primária |
| name | string | Nome do fornecedor |
| document | string | CNPJ/CPF (único) |
| timestamps | - | created_at / updated_at |

### 💰 Despesas (`despesas`)
| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | bigint | Chave primária |
| orgao_id | FK | Referência ao órgão |
| fornecedor_id | FK | Referência ao fornecedor |
| descricao | string | Descrição da despesa |
| valor | decimal(14,2) | Valor gasto |
| timestamps | - | created_at / updated_at |

**Relacionamentos:**
- Um órgão tem várias despesas
- Um fornecedor pode ter várias despesas
- Uma despesa pertence a um órgão **e** a um fornecedor

---

## 📡 Endpoints Disponíveis

### Órgãos

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/orgaos` | Listar todos os órgãos |
| POST | `/api/orgaos` | Criar novo órgão |
| GET | `/api/orgaos/{id}` | Buscar órgão por ID |
| PUT | `/api/orgaos/{id}` | Atualizar órgão |
| DELETE | `/api/orgaos/{id}` | Remover órgão |

### Fornecedores

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/fornecedores` | Listar todos os fornecedores |
| POST | `/api/fornecedores` | Criar novo fornecedor |
| GET | `/api/fornecedores/{id}` | Buscar fornecedor por ID |
| PUT | `/api/fornecedores/{id}` | Atualizar fornecedor |
| DELETE | `/api/fornecedores/{id}` | Remover fornecedor |

### Despesas

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/despesas` | Listar despesas (filtro por `orgao_id` disponível) |
| POST | `/api/despesas` | Criar nova despesa |
| GET | `/api/despesas/{id}` | Buscar despesa por ID |
| PUT | `/api/despesas/{id}` | Atualizar despesa |
| DELETE | `/api/despesas/{id}` | Remover despesa |
| GET | `/api/despesas/total/orgao` | ⚠️ **Implementar** — Total de gastos por órgão |
| GET | `/api/despesas/total/fornecedor` | ⚠️ **Implementar** — Total de gastos por fornecedor |

#### Filtro disponível em Despesas

```
GET /api/despesas?orgao_id=1
```

---

## 🎯 Sua Tarefa

O código atual funciona, mas está **todo concentrado no arquivo `routes/api.php`**. Seu desafio é **refatorar o projeto** aplicando boas práticas do Laravel.

### ✅ O que você deve fazer (obrigatório):

1. **Criar Controllers** — Extrair a lógica das rotas para Controllers dedicados
2. **Ajustar as Rotas** — Apontar as rotas para os métodos dos Controllers
3. **Criar API Resources** — Padronizar as respostas JSON com Resources/Collections
4. **Implementar os filtros de despesas** — Além do filtro por `orgao_id` (já existente), você deve implementar os seguintes filtros:
   - `fornecedor_id` — Filtrar despesas por fornecedor
   - `valor_min` — Filtrar despesas com valor **maior ou igual** ao informado
   - `valor_max` — Filtrar despesas com valor **menor ou igual** ao informado

   **Exemplos de uso:**
   ```
   GET /api/despesas?fornecedor_id=2
   GET /api/despesas?valor_min=10000
   GET /api/despesas?valor_max=20000
   GET /api/despesas?valor_min=5000&valor_max=30000
   GET /api/despesas?orgao_id=1&valor_min=10000
   ```

5. **Implementar as rotas de totais** — As rotas `/api/despesas/total/orgao` e `/api/despesas/total/fornecedor` estão criadas mas retornam vazio. Você deve implementar a lógica para retornar os dados corretos.

#### Saída esperada — `/api/despesas/total/orgao`

```json
{
  "total": 171000.00,
  "por_orgao": [
    { "orgao": "Secretaria de Saúde", "valor_total": 35500.00 },
    { "orgao": "Secretaria de Educação", "valor_total": 73000.00 },
    { "orgao": "Secretaria de Trânsito", "valor_total": 62500.00 }
  ]
}
```

#### Saída esperada — `/api/despesas/total/fornecedor`

```json
{
  "total": 171000.00,
  "por_fornecedor": [
    { "fornecedor": "Limpa Natal Ltda", "valor_total": 24000.00 },
    { "fornecedor": "TechSol Informática", "valor_total": 92000.00 },
    { "fornecedor": "TransNatal Transportes", "valor_total": 40000.00 },
    { "fornecedor": "MedSupply Hospitalar", "valor_total": 15000.00 }
  ]
}
```

### ⭐ Diferenciais (opcional):

- **Criar Form Requests** — Separar as validações em classes dedicadas
- Implementar **paginação** nas listagens
- Implementar **tratamento de erros**
- Melhorar o **tratamento de erros** com respostas padronizadas
- Qualquer outra melhoria que você julgue pertinente

### Critérios de avaliação:

- ✅ Organização e separação de responsabilidades
- ✅ Uso correto dos recursos do Laravel (Controllers, Resources, Form Requests, etc...)
- ✅ Padronização das respostas JSON
- ✅ Qualidade do código e boas práticas
- ✅ Conhecimento do ecossistema Laravel

### 📦 Dados já disponíveis:

Ao rodar `php artisan migrate --seed`, o banco será populado automaticamente com dados de exemplo. **Você não precisa cadastrar novos órgãos, fornecedores ou despesas** — basta trabalhar com os dados que já existem no seed.

- **3 Órgãos**: Secretaria de Saúde, Secretaria de Educação, Secretaria de Trânsito
- **4 Fornecedores**: Limpa Natal Ltda, TechSol Informática, TransNatal Transportes, MedSupply Hospitalar
- **9 Despesas**: Diversos gastos distribuídos entre os órgãos e fornecedores

> 💡 **Dica**: Foque na refatoração e organização do código, não na criação de dados. Os seeds já fornecem tudo que você precisa para testar os endpoints.

---

## 🛠️ Tecnologias

- **PHP** ^8.3
- **Laravel** ^13.0
- **SQLite** (banco de dados local)

---

## 📬 Instruções de Entrega

1. **Faça um Fork** — Crie um fork deste repositório para a sua conta pessoal no GitHub.
2. **Desenvolva** — Implemente a solução realizando os commits diretamente no seu fork.
3. **Envie** — Disponibilize o link do seu repositório finalizado para a nossa equipe avaliar.

> ⏰ **Prazo**: Você tem **7 dias** a partir do recebimento deste enunciado para concluir e entregar o desafio.

---

Boa sorte e bom desenvolvimento! 🚀
