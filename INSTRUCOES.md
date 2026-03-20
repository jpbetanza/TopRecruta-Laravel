# Desafio Técnico — Desenvolvedor Mobile (React + Capacitor)

Olá! Bem-vindo ao desafio técnico para a vaga de **Desenvolvedor Mobile**.

Neste desafio você vai construir um aplicativo mobile de **Portal da Transparência** que consome uma API REST já pronta. O foco é avaliar sua capacidade de criar uma experiência mobile real — rodando em emulador ou dispositivo físico — usando React e Capacitor.

---

## O Projeto

Você vai construir o app mobile de um portal de transparência pública, permitindo que cidadãos consultem gastos governamentais: quais órgãos gastaram, com quais fornecedores, e os comprovantes de cada despesa.

**Stack obrigatória:** React + Capacitor
**Recomendado:** Vite + TypeScript

---

## A API

A API já está implementada e rodando. Você receberá as informações de acesso junto com este enunciado.

### URL base

```
https://avaliacaoapi.ext.topsolutionsrn.com.br/api
```

### Autenticação

Todas as requisições precisam do header:

```
X-API-Key: <chave-fornecida-pelo-recrutador>
```

> A chave será fornecida junto com este enunciado. **Não a commite hardcoded** — use variável de ambiente (`.env`).

---

## Endpoints da API

### Órgãos

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `GET` | `/api/orgaos` | Lista todos os órgãos |
| `GET` | `/api/orgaos/{id}` | Detalhe de um órgão |
| `POST` | `/api/orgaos` | Cria um órgão |
| `PUT` | `/api/orgaos/{id}` | Atualiza um órgão |
| `DELETE` | `/api/orgaos/{id}` | Remove um órgão |

**Campos de Órgão:**
```json
{
  "id": 1,
  "name": "Secretaria de Educação",
  "created_at": "2026-03-20T00:00:00.000000Z",
  "updated_at": "2026-03-20T00:00:00.000000Z"
}
```

**POST/PUT body:**
```json
{ "name": "Secretaria de Saúde" }
```

---

### Fornecedores

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `GET` | `/api/fornecedores` | Lista todos os fornecedores |
| `GET` | `/api/fornecedores/{id}` | Detalhe de um fornecedor |
| `POST` | `/api/fornecedores` | Cria um fornecedor |
| `PUT` | `/api/fornecedores/{id}` | Atualiza um fornecedor |
| `DELETE` | `/api/fornecedores/{id}` | Remove um fornecedor |

**Campos de Fornecedor:**
```json
{
  "id": 1,
  "name": "Empresa XYZ Ltda",
  "document": "12.345.678/0001-90",
  "created_at": "2026-03-20T00:00:00.000000Z",
  "updated_at": "2026-03-20T00:00:00.000000Z"
}
```

**POST/PUT body:**
```json
{ "name": "Empresa XYZ Ltda", "document": "12.345.678/0001-90" }
```

---

### Despesas

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `GET` | `/api/despesas` | Lista despesas (com filtros opcionais) |
| `GET` | `/api/despesas/{id}` | Detalhe de uma despesa |
| `POST` | `/api/despesas` | Cria uma despesa |
| `PUT` | `/api/despesas/{id}` | Atualiza uma despesa |
| `DELETE` | `/api/despesas/{id}` | Remove uma despesa |
| `GET` | `/api/despesas/total/orgao` | Total de gastos agrupado por órgão |
| `GET` | `/api/despesas/total/fornecedor` | Total de gastos agrupado por fornecedor |

**Filtros disponíveis em `GET /api/despesas` (query params):**

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `orgao_id` | integer | Filtra por órgão |
| `fornecedor_id` | integer | Filtra por fornecedor |
| `valor_min` | number | Valor mínimo |
| `valor_max` | number | Valor máximo |

**Campos de Despesa:**
```json
{
  "id": 1,
  "orgao_id": 1,
  "fornecedor_id": 2,
  "descricao": "Compra de material escolar",
  "valor": "15000.00",
  "comprovante_path": "arquivo.pdf",
  "comprovante_mime": "application/pdf",
  "comprovante_url": "https://avaliacaoapi.ext.topsolutionsrn.com.br/api/despesas/1/comprovante",
  "orgao": { "id": 1, "name": "Secretaria de Educação" },
  "fornecedor": { "id": 2, "name": "Empresa XYZ Ltda", "document": "12.345.678/0001-90" },
  "created_at": "2026-03-20T00:00:00.000000Z",
  "updated_at": "2026-03-20T00:00:00.000000Z"
}
```

**POST/PUT body:**
```json
{
  "orgao_id": 1,
  "fornecedor_id": 2,
  "descricao": "Compra de material escolar",
  "valor": 15000.00
}
```

**Resposta de `GET /api/despesas/total/orgao`:**
```json
[
  { "orgao_id": 1, "orgao": "Secretaria de Educação", "total": "45000.00" },
  { "orgao_id": 2, "orgao": "Secretaria de Saúde", "total": "120000.00" }
]
```

**Resposta de `GET /api/despesas/total/fornecedor`:**
```json
[
  { "fornecedor_id": 1, "fornecedor": "Empresa XYZ Ltda", "total": "30000.00" },
  { "fornecedor_id": 2, "fornecedor": "Outra Empresa S/A", "total": "95000.00" }
]
```

---

### Comprovantes de Despesa

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `POST` | `/api/despesas/{id}/comprovante` | Faz upload de comprovante (multipart/form-data, campo `file`) |
| `GET` | `/api/despesas/{id}/comprovante` | Baixa/visualiza o comprovante |
| `DELETE` | `/api/despesas/{id}/comprovante` | Remove o comprovante |

**Formatos aceitos no upload:** JPEG, PNG, PDF (máx. 5 MB)

---

## Setup do Projeto

### 1. Criar o projeto

```bash
npm create vite@latest portal-transparencia -- --template react-ts
cd portal-transparencia
npm install
```

### 2. Instalar e inicializar o Capacitor

```bash
npm install @capacitor/core @capacitor/cli
npx cap init
```

Preencha o nome do app e o app ID (ex: `com.seunome.portaltransparencia`).

### 3. Adicionar a plataforma desejada

```bash
# Para Android:
npm install @capacitor/android
npx cap add android

# Para iOS (requer Mac + Xcode):
npm install @capacitor/ios
npx cap add ios
```

### 4. Configurar o IP da API

Crie um arquivo `.env` com a URL base da API e a chave fornecida pelo recrutador:

```env
VITE_API_URL=https://avaliacaoapi.ext.topsolutionsrn.com.br/api
VITE_API_KEY=sua-chave-aqui
```

### 5. Rodar no emulador/device

```bash
npm run build
npx cap sync

# Android:
npx cap run android

# iOS:
npx cap run ios
```

Para desenvolvimento com hot reload, você pode usar `npx cap run android --livereload --external` com Vite rodando em paralelo.

---

## Telas Obrigatórias

Seu app deve implementar ao menos as seguintes telas:

### Dashboard
- Exibe o total de gastos agrupado por **órgão** (`GET /api/despesas/total/orgao`)
- Exibe o total de gastos agrupado por **fornecedor** (`GET /api/despesas/total/fornecedor`)
- Ponto de entrada visual do app — destaque os números

### Lista de Despesas
- Listagem completa de despesas (`GET /api/despesas`)
- Filtros funcionais: por órgão, por fornecedor, por valor mínimo e valor máximo
- Deve mostrar descrição, valor, órgão e fornecedor de cada item

### Detalhe de Despesa
- Todos os campos da despesa
- Se houver comprovante (`comprovante_url != null`), ofereça botão para visualizá-lo ou abri-lo

### Lista de Órgãos
- Listagem simples de todos os órgãos (`GET /api/orgaos`)

### Lista de Fornecedores
- Listagem simples de todos os fornecedores (`GET /api/fornecedores`)

---

## Diferenciais (Opcional)

Estes itens não são obrigatórios, mas contam pontos:

- **CRUD completo** de órgãos, fornecedores e despesas (criar, editar, excluir)
- **Upload de comprovante** usando Capacitor Camera ou FilePicker (`POST /api/despesas/{id}/comprovante`)
- **Pull-to-refresh** nas listagens
- **Tratamento de erros** com feedback visual (toasts, snackbars, estados de erro inline)
- **Skeleton loading** / estados de carregamento
- **Tema escuro**
- **Paginação** ou scroll infinito nas listagens

---

## Critérios de Avaliação

| Critério | O que observamos |
|----------|-----------------|
| **Funcionalidade** | As telas obrigatórias funcionam corretamente? |
| **Capacitor** | O app roda no emulador ou device (não apenas no browser)? |
| **Organização** | Componentes bem divididos, camada de serviços/api separada |
| **TypeScript** | Respostas da API tipadas, sem `any` desnecessário |
| **React** | Uso correto de hooks, gerenciamento de estado, separação de responsabilidades |
| **UX/UI** | A experiência é fluida e visualmente agradável? |

---

## Entrega

1. Faça um **fork** do repositório fornecido (ou crie um repositório público no GitHub)
2. Desenvolva o projeto no seu fork
3. Envie o link do repositório ao recrutador até o prazo informado

**Prazo:** 7 dias a partir do recebimento deste enunciado

**No README do seu projeto, inclua:**
- Como rodar o projeto (passo a passo)
- Em qual emulador/device foi testado (ex: "Android API 34 no Android Studio")
- A API Key usada nos testes (para que possamos rodar e testar)

---

## Observações Finais

- **CORS**: a API já está configurada para aceitar requisições do Capacitor. Se houver problemas de CORS, avise o recrutador.
- **HTTPS**: a API usa HTTPS, então não há necessidade de configuração especial de cleartext no Android.
- **API Key no `.env`**: use `VITE_API_KEY` no seu `.env` e acesse via `import.meta.env.VITE_API_KEY`. Adicione `.env` ao `.gitignore`.

Boa sorte! Qualquer dúvida, entre em contato com o recrutador.
