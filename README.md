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

## 🔐 Autenticação

Todas as rotas exigem o header `X-API-Key` com um segredo válido configurado na variável de ambiente `API_KEYS`.

```
X-API-Key: seu-segredo-aqui
```

Sem o header ou com valor inválido, a API retorna:

```json
HTTP 401
{ "message": "Unauthorized." }
```

---

## 📡 Endpoints Disponíveis

### Índice rápido

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/orgaos` | Listar órgãos |
| POST | `/api/orgaos` | Criar órgão |
| GET | `/api/orgaos/{id}` | Buscar órgão por ID |
| PUT | `/api/orgaos/{id}` | Atualizar órgão |
| DELETE | `/api/orgaos/{id}` | Remover órgão |
| GET | `/api/fornecedores` | Listar fornecedores |
| POST | `/api/fornecedores` | Criar fornecedor |
| GET | `/api/fornecedores/{id}` | Buscar fornecedor por ID |
| PUT | `/api/fornecedores/{id}` | Atualizar fornecedor |
| DELETE | `/api/fornecedores/{id}` | Remover fornecedor |
| GET | `/api/despesas` | Listar despesas (com filtros) |
| POST | `/api/despesas` | Criar despesa |
| GET | `/api/despesas/{id}` | Buscar despesa por ID |
| PUT | `/api/despesas/{id}` | Atualizar despesa |
| DELETE | `/api/despesas/{id}` | Remover despesa |
| GET | `/api/despesas/total/orgao` | Total de gastos agrupado por órgão |
| GET | `/api/despesas/total/fornecedor` | Total de gastos agrupado por fornecedor |
| POST | `/api/despesas/{id}/comprovante` | Fazer upload de comprovante |
| GET | `/api/despesas/{id}/comprovante` | Baixar/visualizar comprovante |
| DELETE | `/api/despesas/{id}/comprovante` | Remover comprovante |

---

### Órgãos

#### `GET /api/orgaos`

Lista todos os órgãos do tenant.

**Resposta `200`:**
```json
[
  { "id": 1, "name": "Secretaria de Saúde", "created_at": "2026-03-20T00:00:00.000000Z", "updated_at": "2026-03-20T00:00:00.000000Z" },
  { "id": 2, "name": "Secretaria de Educação", "created_at": "2026-03-20T00:00:00.000000Z", "updated_at": "2026-03-20T00:00:00.000000Z" }
]
```

---

#### `POST /api/orgaos`

Cria um novo órgão.

**Body (JSON):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `name` | string | ✅ | máx. 255 caracteres, único por tenant |

```json
{ "name": "Secretaria de Saúde" }
```

**Resposta `201`:**
```json
{ "id": 1, "name": "Secretaria de Saúde", "created_at": "2026-03-20T00:00:00.000000Z", "updated_at": "2026-03-20T00:00:00.000000Z" }
```

**Erros:**

| Status | Situação |
|--------|----------|
| `422` | Validação falhou (`name` ausente, muito longo ou duplicado) |

---

#### `GET /api/orgaos/{id}`

Retorna um órgão pelo ID.

**Resposta `200`:**
```json
{ "id": 1, "name": "Secretaria de Saúde", "created_at": "2026-03-20T00:00:00.000000Z", "updated_at": "2026-03-20T00:00:00.000000Z" }
```

**Erros:**

| Status | Situação |
|--------|----------|
| `404` | `{ "message": "Órgão não encontrado" }` |

---

#### `PUT /api/orgaos/{id}`

Atualiza um órgão.

**Body (JSON):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `name` | string | ✅ | máx. 255 caracteres, único por tenant (exceto o próprio registro) |

```json
{ "name": "Secretaria de Infraestrutura" }
```

**Resposta `200`:**
```json
{ "id": 1, "name": "Secretaria de Infraestrutura", "created_at": "2026-03-20T00:00:00.000000Z", "updated_at": "2026-03-20T12:00:00.000000Z" }
```

**Erros:**

| Status | Situação |
|--------|----------|
| `404` | Órgão não encontrado |
| `422` | Validação falhou |

---

#### `DELETE /api/orgaos/{id}`

Remove um órgão.

**Resposta `200`:**
```json
{ "message": "Órgão removido com sucesso" }
```

**Erros:**

| Status | Situação |
|--------|----------|
| `404` | Órgão não encontrado |

---

### Fornecedores

#### `GET /api/fornecedores`

Lista todos os fornecedores do tenant.

**Resposta `200`:**
```json
[
  { "id": 1, "name": "TechSol Informática", "document": "12.345.678/0001-99", "created_at": "2026-03-20T00:00:00.000000Z", "updated_at": "2026-03-20T00:00:00.000000Z" }
]
```

---

#### `POST /api/fornecedores`

Cria um novo fornecedor.

**Body (JSON):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `name` | string | ✅ | máx. 255 caracteres |
| `document` | string | ✅ | máx. 20 caracteres, único por tenant |

```json
{ "name": "TechSol Informática", "document": "12.345.678/0001-99" }
```

**Resposta `201`:**
```json
{ "id": 1, "name": "TechSol Informática", "document": "12.345.678/0001-99", "created_at": "2026-03-20T00:00:00.000000Z", "updated_at": "2026-03-20T00:00:00.000000Z" }
```

**Erros:**

| Status | Situação |
|--------|----------|
| `422` | Validação falhou (campos ausentes, document duplicado) |

---

#### `GET /api/fornecedores/{id}`

Retorna um fornecedor pelo ID.

**Resposta `200`:**
```json
{ "id": 1, "name": "TechSol Informática", "document": "12.345.678/0001-99", "created_at": "2026-03-20T00:00:00.000000Z", "updated_at": "2026-03-20T00:00:00.000000Z" }
```

**Erros:**

| Status | Situação |
|--------|----------|
| `404` | `{ "message": "Fornecedor não encontrado" }` |

---

#### `PUT /api/fornecedores/{id}`

Atualiza um fornecedor.

**Body (JSON):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `name` | string | ✅ | máx. 255 caracteres |
| `document` | string | ✅ | máx. 20 caracteres, único por tenant (exceto o próprio) |

**Resposta `200`:**
```json
{ "id": 1, "name": "TechSol Informática", "document": "12.345.678/0001-99", "created_at": "2026-03-20T00:00:00.000000Z", "updated_at": "2026-03-20T12:00:00.000000Z" }
```

**Erros:**

| Status | Situação |
|--------|----------|
| `404` | Fornecedor não encontrado |
| `422` | Validação falhou |

---

#### `DELETE /api/fornecedores/{id}`

Remove um fornecedor.

**Resposta `200`:**
```json
{ "message": "Fornecedor removido com sucesso" }
```

**Erros:**

| Status | Situação |
|--------|----------|
| `404` | Fornecedor não encontrado |

---

### Despesas

#### `GET /api/despesas`

Lista as despesas do tenant. Suporta filtros via query string.

**Query params (todos opcionais):**

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `orgao_id` | integer | Filtra por órgão |
| `fornecedor_id` | integer | Filtra por fornecedor |
| `valor_min` | numeric | Valor mínimo (≥) |
| `valor_max` | numeric | Valor máximo (≤) |

**Exemplos:**
```
GET /api/despesas
GET /api/despesas?orgao_id=1
GET /api/despesas?fornecedor_id=2
GET /api/despesas?valor_min=5000&valor_max=30000
GET /api/despesas?orgao_id=1&valor_min=10000
```

**Resposta `200`:**
```json
[
  {
    "id": 1,
    "orgao_id": 1,
    "fornecedor_id": 2,
    "descricao": "Compra de equipamentos de informática",
    "valor": "15000.00",
    "comprovante_path": null,
    "comprovante_mime": null,
    "created_at": "2026-03-20T00:00:00.000000Z",
    "updated_at": "2026-03-20T00:00:00.000000Z",
    "orgao": { "id": 1, "name": "Secretaria de Saúde" },
    "fornecedor": { "id": 2, "name": "TechSol Informática", "document": "12.345.678/0001-99" }
  }
]
```

---

#### `POST /api/despesas`

Cria uma nova despesa.

**Body (JSON):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `orgao_id` | integer | ✅ | deve existir na tabela `orgaos` |
| `fornecedor_id` | integer | ✅ | deve existir na tabela `fornecedores` |
| `descricao` | string | ✅ | máx. 500 caracteres |
| `valor` | numeric | ✅ | mín. 0.01 |

```json
{
  "orgao_id": 1,
  "fornecedor_id": 2,
  "descricao": "Compra de equipamentos de informática",
  "valor": 15000.00
}
```

**Resposta `201`:**
```json
{
  "id": 1,
  "orgao_id": 1,
  "fornecedor_id": 2,
  "descricao": "Compra de equipamentos de informática",
  "valor": "15000.00",
  "created_at": "2026-03-20T00:00:00.000000Z",
  "updated_at": "2026-03-20T00:00:00.000000Z",
  "orgao": { "id": 1, "name": "Secretaria de Saúde" },
  "fornecedor": { "id": 2, "name": "TechSol Informática", "document": "12.345.678/0001-99" }
}
```

**Erros:**

| Status | Situação |
|--------|----------|
| `422` | Validação falhou |

---

#### `GET /api/despesas/{id}`

Retorna uma despesa pelo ID, incluindo a URL do comprovante quando houver.

**Resposta `200`:**
```json
{
  "id": 1,
  "orgao_id": 1,
  "fornecedor_id": 2,
  "descricao": "Compra de equipamentos de informática",
  "valor": "15000.00",
  "comprovante_path": "uuid-do-arquivo.pdf",
  "comprovante_mime": "application/pdf",
  "created_at": "2026-03-20T00:00:00.000000Z",
  "updated_at": "2026-03-20T00:00:00.000000Z",
  "comprovante_url": "http://localhost:8000/api/despesas/1/comprovante",
  "orgao": { "id": 1, "name": "Secretaria de Saúde" },
  "fornecedor": { "id": 2, "name": "TechSol Informática", "document": "12.345.678/0001-99" }
}
```

> `comprovante_url` é `null` quando nenhum comprovante foi enviado.

**Erros:**

| Status | Situação |
|--------|----------|
| `404` | `{ "message": "Despesa não encontrada" }` |

---

#### `PUT /api/despesas/{id}`

Atualiza uma despesa.

**Body (JSON):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `orgao_id` | integer | ✅ | deve existir na tabela `orgaos` |
| `fornecedor_id` | integer | ✅ | deve existir na tabela `fornecedores` |
| `descricao` | string | ✅ | máx. 500 caracteres |
| `valor` | numeric | ✅ | mín. 0.01 |

**Resposta `200`:** mesmo formato do `GET /api/despesas/{id}`.

**Erros:**

| Status | Situação |
|--------|----------|
| `404` | Despesa não encontrada |
| `422` | Validação falhou |

---

#### `DELETE /api/despesas/{id}`

Remove uma despesa.

**Resposta `200`:**
```json
{ "message": "Despesa removida com sucesso" }
```

**Erros:**

| Status | Situação |
|--------|----------|
| `404` | Despesa não encontrada |

---

#### `GET /api/despesas/total/orgao`

⚠️ **Pendente de implementação** — Total de gastos agrupado por órgão.

**Resposta `200` esperada:**
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

---

#### `GET /api/despesas/total/fornecedor`

⚠️ **Pendente de implementação** — Total de gastos agrupado por fornecedor.

**Resposta `200` esperada:**
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

---

### Comprovantes de Despesa

#### `POST /api/despesas/{id}/comprovante`

Faz upload de um comprovante para uma despesa. Se já existir um comprovante, ele é substituído.

**Body (`multipart/form-data`):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `file` | file | ✅ | formatos aceitos: `jpeg`, `jpg`, `png`, `pdf` — tamanho máx: 5 MB |

**Exemplo com curl:**
```bash
curl -X POST http://localhost:8000/api/despesas/1/comprovante \
  -H "X-API-Key: seu-segredo" \
  -F "file=@/caminho/para/arquivo.pdf"
```

**Resposta `200`:**
```json
{ "path": "a1b2c3d4-uuid-gerado.pdf" }
```

**Erros:**

| Status | Situação |
|--------|----------|
| `404` | Despesa não encontrada |
| `422` | Arquivo ausente, formato inválido ou maior que 5 MB |

---

#### `GET /api/despesas/{id}/comprovante`

Baixa ou exibe o comprovante da despesa. Retorna o arquivo binário com o `Content-Type` original.

**Resposta:** arquivo binário (`application/pdf`, `image/jpeg`, etc.)

**Erros:**

| Status | Situação |
|--------|----------|
| `404` | `{ "message": "Comprovante não encontrado." }` — despesa não existe ou sem comprovante |

---

#### `DELETE /api/despesas/{id}/comprovante`

Remove o comprovante da despesa.

**Resposta `204`:** sem corpo.

**Erros:**

| Status | Situação |
|--------|----------|
| `404` | `{ "message": "Nenhum comprovante vinculado." }` |

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

5. **Implementar as rotas de totais** — As rotas `/api/despesas/total/orgao` e `/api/despesas/total/fornecedor` estão criadas mas retornam vazio. Você deve implementar a lógica para retornar os dados corretos (veja o formato esperado na seção de endpoints acima).

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

- **PHP** ^8.4
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
