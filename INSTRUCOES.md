# Avaliação Mobile — React + Capacitor

Olá! bem-vindo(a) à avaliação técnica para integrar a equipe de desenvolvimento da Top Solutions.

## Objetivo

O desafio consiste em desenvolver um aplicativo mobile administrativo de **Portal da Transparência**, consumindo uma API REST já disponibilizada pela empresa. O app deve permitir que o usuário consulte gastos públicos — quais órgãos gastaram, com quais fornecedores e os comprovantes de cada despesa.

A API está disponível em: `https://avaliacaoapi.ext.topsolutionsrn.com.br/api`

A autenticação é feita via header `X-API-Key`. A chave será fornecida junto com este enunciado.

## Requisitos Funcionais

### 1. Dashboard

Tela inicial exibindo um resumo financeiro:

- Total de gastos agrupados por órgão (`GET /api/despesas/total/orgao`)
- Total de gastos agrupados por fornecedor (`GET /api/despesas/total/fornecedor`)

### 2. Despesas

Tela que exibe todas as despesas cadastradas com as seguintes funcionalidades:

- Listagem com descrição, valor, órgão e fornecedor de cada item
- Filtros funcionais: `orgao_id`, `fornecedor_id`, `valor_min`, `valor_max`
- Cadastro de nova despesa (órgão, fornecedor, descrição, valor)
- Edição de despesa existente
- Exclusão de despesa (com confirmação)

### 3. Detalhe de Despesa

Tela que exibe todos os campos de uma despesa. Quando houver comprovante vinculado, deve oferecer opção para visualizá-lo ou abri-lo.

### 4. Órgãos

- Listagem de todos os órgãos cadastrados
- Cadastro de novo órgão
- Edição de órgão existente
- Exclusão de órgão (com confirmação)

### 5. Fornecedores

- Listagem de todos os fornecedores cadastrados
- Cadastro de novo fornecedor (nome e CNPJ/CPF)
- Edição de fornecedor existente
- Exclusão de fornecedor (com confirmação)

## Requisitos Técnicos

- O projeto deve ser feito em **React + Capacitor**.
- Utilize **Vite** e **TypeScript**.
- O app deve rodar em **emulador ou dispositivo físico** — não apenas no navegador.
- As respostas da API devem ser **tipadas com TypeScript** (evite `any`).
- Organize o projeto com separação clara entre componentes e camada de serviços/API.
- Armazene a `X-API-Key` em variável de ambiente (`.env`) — não a commite hardcoded.

### Sobre a API

Todas as requisições devem incluir o header:

```
X-API-Key: <chave-fornecida-pelo-recrutador>
```

Sem o header ou com valor inválido, a API retorna `HTTP 401`.

Endpoints disponíveis:

**Órgãos**

- `GET /api/orgaos` — lista todos os órgãos
- `GET /api/orgaos/paginado` — lista órgãos paginada (query params: `page`, `per_page`)
- `GET /api/orgaos/{id}` — detalhe de um órgão
- `POST /api/orgaos` — cria um órgão (body: `name`)
- `PUT /api/orgaos/{id}` — atualiza um órgão (body: `name`)
- `DELETE /api/orgaos/{id}` — remove um órgão

**Fornecedores**

- `GET /api/fornecedores` — lista todos os fornecedores
- `GET /api/fornecedores/paginado` — lista fornecedores paginada (query params: `page`, `per_page`)
- `GET /api/fornecedores/{id}` — detalhe de um fornecedor
- `POST /api/fornecedores` — cria um fornecedor (body: `name`, `document`)
- `PUT /api/fornecedores/{id}` — atualiza um fornecedor (body: `name`, `document`)
- `DELETE /api/fornecedores/{id}` — remove um fornecedor

**Despesas**

- `GET /api/despesas` — lista despesas (query params opcionais: `orgao_id`, `fornecedor_id`, `valor_min`, `valor_max`)
- `GET /api/despesas/paginado` — lista despesas paginada (query params: `page`, `per_page`, `orgao_id`, `fornecedor_id`, `valor_min`, `valor_max`)
- `GET /api/despesas/{id}` — detalhe de uma despesa (inclui `comprovante_url` quando houver)
- `POST /api/despesas` — cria uma despesa (body: `orgao_id`, `fornecedor_id`, `descricao`, `valor`)
- `PUT /api/despesas/{id}` — atualiza uma despesa (body: `orgao_id`, `fornecedor_id`, `descricao`, `valor`)
- `DELETE /api/despesas/{id}` — remove uma despesa
- `GET /api/despesas/total/orgao` — total de gastos agrupado por órgão
- `GET /api/despesas/total/fornecedor` — total de gastos agrupado por fornecedor

> **Rotas paginadas** retornam um envelope com os campos: `data` (itens da página), `current_page`, `last_page`, `per_page`, `total`, `from`, `to` e `links` (`first`, `last`, `prev`, `next`). O parâmetro `per_page` aceita valores entre 1 e 100 (padrão: 15).

**Comprovantes**

- `POST /api/despesas/{id}/comprovante` — upload de comprovante (multipart/form-data, campo `file`, formatos: jpeg, png, pdf, máx. 5 MB)
- `GET /api/despesas/{id}/comprovante` — baixar/visualizar comprovante
- `DELETE /api/despesas/{id}/comprovante` — remove o comprovante

### Setup do Projeto

```bash
npm create vite@latest portal-transparencia -- --template react-ts
cd portal-transparencia
npm install
npm install @capacitor/core @capacitor/cli
npx cap init
npm install @capacitor/android   # ou @capacitor/ios
npx cap add android              # ou ios
npm run build && npx cap sync
npx cap run android              # ou ios
```

Crie um arquivo `.env` com:

```
VITE_API_URL=https://avaliacaoapi.ext.topsolutionsrn.com.br/api
VITE_API_KEY=sua-chave-aqui
```

## Funcionalidades Gerais

- O app deve rodar corretamente em emulador ou dispositivo físico, não apenas no navegador.
- As telas de listagem devem exibir estado de carregamento enquanto os dados são buscados.
- Erros de requisição devem ser comunicados ao usuário de forma clara.
- O layout deve ser responsivo e adequado para uso mobile.

## Bônus

Essa etapa é opcional e serve como diferencial. Não é obrigatório realizar os itens abaixo:

- Upload de comprovante utilizando Capacitor Camera ou FilePicker.
- Pull-to-refresh nas listagens.
- Skeleton loading nos estados de carregamento.
- Tema escuro.
- Paginação ou scroll infinito nas listagens.
- Tratamento de erros com toasts ou snackbars.
- Ordenação das listagens por diferentes critérios (ex: despesas por valor ou data, fornecedores por nome).
- Filtros combinados na tela de despesas: por órgão, fornecedor, intervalo de valor — com opção de limpar filtros ativos.

## Instruções Finais

- O projeto deve ser feito em **React + Capacitor**, conforme indicado no enunciado.
- Utilize o repositório disponibilizado pela empresa no GitHub.
- Inclua no README do seu projeto: como rodar, em qual emulador/device foi testado e a API Key usada nos testes.
- Prazo: O prazo para entrega do desafio é de **7 dias** após o recebimento deste enunciado.

Boa sorte e bom desenvolvimento!
