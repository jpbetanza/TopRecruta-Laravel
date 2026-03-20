---
name: test-toprecruta-api
description: Use when the user wants to test the TopRecruta API endpoints, verify a deploy, check for regressions, or validate that all routes are working correctly.
---

# Test TopRecruta API

## Overview

Runs a full smoke test against the TopRecruta API using curl. Tests all endpoints across auth, órgãos, fornecedores, despesas, and comprovantes. Reports a pass/fail table at the end.

## Setup

Ask the user for:
- **API key** (if not already provided in the conversation)
- **Base URL** — default: `https://avaliacaoapi.ext.topsolutionsrn.com.br/api`

Set shell variables before running:
```bash
BASE="https://avaliacaoapi.ext.topsolutionsrn.com.br/api"
KEY="<chave-fornecida>"
```

## Test Sequence

Run each group below in order. **Always clean up** records created during the test (delete them at the end of each group).

---

### 1. Auth

```bash
# Sem chave → 401
curl -s -o /dev/null -w "%{http_code}" "$BASE/orgaos"

# Chave inválida → 401
curl -s -o /dev/null -w "%{http_code}" -H "X-API-Key: invalida" "$BASE/orgaos"
```

Expected: both `401`.

---

### 2. Órgãos (CRUD + paginado)

```bash
# Listar → 200
curl -s -o /dev/null -w "%{http_code}" -H "X-API-Key: $KEY" "$BASE/orgaos"

# Paginado → 200 com envelope
curl -s -H "X-API-Key: $KEY" "$BASE/orgaos/paginado?per_page=2"

# Criar → 201
ID=$(curl -s -H "X-API-Key: $KEY" -H "Content-Type: application/json" \
  -X POST "$BASE/orgaos" -d '{"name":"Orgao Teste Skill"}' | python3 -c "import sys,json; print(json.load(sys.stdin)['id'])")

# Detalhe → 200
curl -s -o /dev/null -w "%{http_code}" -H "X-API-Key: $KEY" "$BASE/orgaos/$ID"

# Atualizar → 200
curl -s -o /dev/null -w "%{http_code}" -H "X-API-Key: $KEY" -H "Content-Type: application/json" \
  -X PUT "$BASE/orgaos/$ID" -d '{"name":"Orgao Teste Skill Editado"}'

# Deletar → 200
curl -s -o /dev/null -w "%{http_code}" -H "X-API-Key: $KEY" -X DELETE "$BASE/orgaos/$ID"

# Não encontrado → 404
curl -s -o /dev/null -w "%{http_code}" -H "X-API-Key: $KEY" "$BASE/orgaos/999999"
```

---

### 3. Fornecedores (CRUD + paginado)

Mesma estrutura. Use `document` único para evitar conflito com dados existentes (ex: `"99.999.999/0001-99"`).

```bash
ID=$(curl -s -H "X-API-Key: $KEY" -H "Content-Type: application/json" \
  -X POST "$BASE/fornecedores" \
  -d '{"name":"Fornecedor Teste Skill","document":"99.999.999/0001-99"}' \
  | python3 -c "import sys,json; print(json.load(sys.stdin)['id'])")

# Detalhe, PUT, DELETE, 404 — igual ao grupo de órgãos
```

---

### 4. Despesas (CRUD + filtros + paginado + totais)

```bash
# Criar despesa de teste (use orgao_id=1 e fornecedor_id=1, que são do seed)
DID=$(curl -s -H "X-API-Key: $KEY" -H "Content-Type: application/json" \
  -X POST "$BASE/despesas" \
  -d '{"orgao_id":1,"fornecedor_id":1,"descricao":"Despesa Teste Skill","valor":1.00}' \
  | python3 -c "import sys,json; print(json.load(sys.stdin)['id'])")

# Listar → 200
curl -s -o /dev/null -w "%{http_code}" -H "X-API-Key: $KEY" "$BASE/despesas"

# Filtros → 200
curl -s -o /dev/null -w "%{http_code}" -H "X-API-Key: $KEY" "$BASE/despesas?orgao_id=1&valor_min=1&valor_max=50000"

# Paginado → 200 com envelope
curl -s -H "X-API-Key: $KEY" "$BASE/despesas/paginado?per_page=3&orgao_id=1"

# Total por órgão → 200, array não-vazio
curl -s -H "X-API-Key: $KEY" "$BASE/despesas/total/orgao"

# Total por fornecedor → 200, array não-vazio
curl -s -H "X-API-Key: $KEY" "$BASE/despesas/total/fornecedor"

# Detalhe → 200, inclui comprovante_url
curl -s -H "X-API-Key: $KEY" "$BASE/despesas/$DID"

# Atualizar → 200
curl -s -o /dev/null -w "%{http_code}" -H "X-API-Key: $KEY" -H "Content-Type: application/json" \
  -X PUT "$BASE/despesas/$DID" \
  -d '{"orgao_id":1,"fornecedor_id":1,"descricao":"Editada","valor":2.00}'

# Deletar → 200
curl -s -o /dev/null -w "%{http_code}" -H "X-API-Key: $KEY" -X DELETE "$BASE/despesas/$DID"
```

---

### 5. Validação (sem Accept: application/json)

```bash
# Deve retornar 422 JSON, não 302
curl -s -w "\nHTTP %{http_code}" -H "X-API-Key: $KEY" -H "Content-Type: application/json" \
  -X POST "$BASE/despesas" -d '{"valor":-1}'
```

Expected: `HTTP 422` com `{"message":"...","errors":{...}}`.

---

### 6. Comprovantes

```bash
# Criar despesa temporária
DID=$(curl -s -H "X-API-Key: $KEY" -H "Content-Type: application/json" \
  -X POST "$BASE/despesas" \
  -d '{"orgao_id":1,"fornecedor_id":1,"descricao":"Comprovante Skill","valor":1.00}' \
  | python3 -c "import sys,json; print(json.load(sys.stdin)['id'])")

# Sem comprovante → 404
curl -s -o /dev/null -w "%{http_code}" -H "X-API-Key: $KEY" "$BASE/despesas/$DID/comprovante"

# Upload PNG mínimo
python3 -c "
import struct, zlib
def png():
    sig=b'\x89PNG\r\n\x1a\n'
    def c(t,d): return struct.pack('>I',len(d))+t+d+struct.pack('>I',zlib.crc32(t+d)&0xffffffff)
    return sig+c(b'IHDR',struct.pack('>IIBBBBB',1,1,8,2,0,0,0))+c(b'IDAT',zlib.compress(b'\x00\xff\x00\x00'))+c(b'IEND',b'')
open('/tmp/skill_test.png','wb').write(png())
"
curl -s -o /dev/null -w "%{http_code}" -H "X-API-Key: $KEY" \
  -X POST "$BASE/despesas/$DID/comprovante" \
  -F "file=@/tmp/skill_test.png;type=image/png"

# Deletar comprovante → 204
curl -s -o /dev/null -w "%{http_code}" -H "X-API-Key: $KEY" -X DELETE "$BASE/despesas/$DID/comprovante"

# Segundo delete → 404
curl -s -o /dev/null -w "%{http_code}" -H "X-API-Key: $KEY" -X DELETE "$BASE/despesas/$DID/comprovante"

# Limpar despesa
curl -s -o /dev/null -H "X-API-Key: $KEY" -X DELETE "$BASE/despesas/$DID"
```

---

## Relatório Final

Após rodar todos os grupos, apresente uma tabela:

| # | Endpoint | Esperado | Resultado | Status |
|---|----------|----------|-----------|--------|
| 1 | Auth sem chave | 401 | ... | ✅/❌ |
| 2 | Auth chave inválida | 401 | ... | ✅/❌ |
| 3 | GET /orgaos | 200 | ... | ✅/❌ |
| 4 | GET /orgaos/paginado | 200 + envelope | ... | ✅/❌ |
| 5 | POST /orgaos | 201 | ... | ✅/❌ |
| 6 | GET /orgaos/{id} | 200 | ... | ✅/❌ |
| 7 | PUT /orgaos/{id} | 200 | ... | ✅/❌ |
| 8 | DELETE /orgaos/{id} | 200 | ... | ✅/❌ |
| 9 | GET /orgaos/999999 | 404 | ... | ✅/❌ |
| 10 | GET /fornecedores | 200 | ... | ✅/❌ |
| 11 | GET /fornecedores/paginado | 200 + envelope | ... | ✅/❌ |
| 12 | POST /fornecedores | 201 | ... | ✅/❌ |
| 13 | GET /fornecedores/{id} | 200 | ... | ✅/❌ |
| 14 | PUT /fornecedores/{id} | 200 | ... | ✅/❌ |
| 15 | DELETE /fornecedores/{id} | 200 | ... | ✅/❌ |
| 16 | GET /despesas | 200 | ... | ✅/❌ |
| 17 | GET /despesas?filtros | 200 | ... | ✅/❌ |
| 18 | GET /despesas/paginado | 200 + envelope | ... | ✅/❌ |
| 19 | GET /despesas/total/orgao | 200 + dados | ... | ✅/❌ |
| 20 | GET /despesas/total/fornecedor | 200 + dados | ... | ✅/❌ |
| 21 | POST /despesas | 201 | ... | ✅/❌ |
| 22 | GET /despesas/{id} | 200 + comprovante_url | ... | ✅/❌ |
| 23 | PUT /despesas/{id} | 200 | ... | ✅/❌ |
| 24 | DELETE /despesas/{id} | 200 | ... | ✅/❌ |
| 25 | Validação sem Accept header | 422 JSON | ... | ✅/❌ |
| 26 | GET /comprovante (ausente) | 404 | ... | ✅/❌ |
| 27 | POST /comprovante (upload) | 200 | ... | ✅/❌ |
| 28 | DELETE /comprovante | 204 | ... | ✅/❌ |
| 29 | DELETE /comprovante (já removido) | 404 | ... | ✅/❌ |

Termine com: **X/29 testes passaram** e liste quaisquer falhas com detalhes.
