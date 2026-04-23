# Laravel API — Produtos & Estoque

API RESTful construída com **Laravel 12** + **Sanctum** + **MySQL** + **Docker**.

---

## Funcionalidades

- **Autenticação** via Laravel Sanctum (token Bearer)
- **Usuários** com CRUD completo
- **Grupos (Roles)** — agrupamento de permissões
- **Permissões individuais** — atribuição direta ao usuário
- **Produtos** — CRUD com soft delete
- **Estoque** — controle de entradas, saídas e ajustes com histórico imutável

---

## Pré-requisitos

- Docker e Docker Compose instalados

---

## Setup

```bash
# 1. Clone o projeto
git clone <repo> laravel-api && cd laravel-api

# 2. Copie o arquivo de ambiente
cp .env.example .env

# 3. Suba os containers (build automático)
docker compose up -d --build

# 4. Gere a APP_KEY
docker compose exec app php artisan key:generate

# 5. (Opcional) Rode seeders manualmente se o entrypoint já não rodou
docker compose exec app php artisan migrate --seed
```

A API estará disponível em: `http://localhost:8080/api`

---

## Autenticação

Todas as rotas (exceto `/auth/login`) exigem o header:

```
Authorization: Bearer {token}
```

---

## Endpoints

### Auth
| Método | Endpoint | Descrição |
|--------|----------|-----------|
| POST | `/api/auth/login` | Login — retorna token |
| POST | `/api/auth/logout` | Logout — invalida token |
| GET  | `/api/auth/me` | Usuário autenticado + permissões |

### Usuários
| Método | Endpoint | Permissão |
|--------|----------|-----------|
| GET    | `/api/users` | users.view |
| POST   | `/api/users` | users.create |
| GET    | `/api/users/{id}` | users.view |
| PUT    | `/api/users/{id}` | users.edit |
| DELETE | `/api/users/{id}` | users.delete |
| PUT    | `/api/users/{id}/roles` | users.manage-roles |
| PUT    | `/api/users/{id}/permissions` | users.manage-permissions |

### Grupos (Roles)
| Método | Endpoint | Permissão |
|--------|----------|-----------|
| GET    | `/api/roles` | roles.view |
| POST   | `/api/roles` | roles.create |
| GET    | `/api/roles/{id}` | roles.view |
| PUT    | `/api/roles/{id}` | roles.edit |
| DELETE | `/api/roles/{id}` | roles.delete |
| PUT    | `/api/roles/{id}/permissions` | roles.edit |

### Permissões
| Método | Endpoint | Permissão |
|--------|----------|-----------|
| GET    | `/api/permissions` | permissions.view |
| POST   | `/api/permissions` | permissions.create |
| PUT    | `/api/permissions/{id}` | permissions.edit |
| DELETE | `/api/permissions/{id}` | permissions.delete |

### Produtos
| Método | Endpoint | Permissão |
|--------|----------|-----------|
| GET    | `/api/products` | products.view |
| POST   | `/api/products` | products.create |
| GET    | `/api/products/{id}` | products.view |
| PUT    | `/api/products/{id}` | products.edit |
| DELETE | `/api/products/{id}` | products.delete |

### Estoque
| Método | Endpoint | Permissão |
|--------|----------|-----------|
| GET    | `/api/stock` | stock.view |
| GET    | `/api/stock/movements` | stock.view |
| GET    | `/api/stock/product/{id}` | stock.view |
| GET    | `/api/stock/product/{id}/movements` | stock.view |
| POST   | `/api/stock/movement` | stock.move |

---

## Exemplos de Requisições

### Login
```bash
curl -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

### Criar Produto
```bash
curl -X POST http://localhost:8080/api/products \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Produto Exemplo",
    "sku": "PROD-001",
    "price": 29.90,
    "unit": "un",
    "min_quantity": 5
  }'
```

### Entrada de Estoque
```bash
curl -X POST http://localhost:8080/api/stock/movement \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "type": "in",
    "quantity": 100,
    "unit_cost": 15.00,
    "reference": "NF-001",
    "reason": "Compra de fornecedor"
  }'
```

### Saída de Estoque
```bash
curl -X POST http://localhost:8080/api/stock/movement \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "type": "out",
    "quantity": 10,
    "reason": "Venda"
  }'
```

### Atribuir Grupos ao Usuário
```bash
curl -X PUT http://localhost:8080/api/users/2/roles \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"roles": [1, 2]}'
```

### Atribuir Permissões Individuais
```bash
curl -X PUT http://localhost:8080/api/users/2/permissions \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"permissions": [15, 19]}'
```

---

## Credenciais padrão (Seeder)

| Usuário | Email | Senha | Role |
|---------|-------|-------|------|
| Admin | admin@example.com | password | admin |
| Manager | manager@example.com | password | manager |

---

## Tipos de Movimentação de Estoque

| Tipo | Descrição |
|------|-----------|
| `in` | Entrada — soma ao estoque atual |
| `out` | Saída — subtrai do estoque atual |
| `adjustment` | Ajuste — define valor absoluto do estoque |

---

## Estrutura do Projeto

```
.
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── Controller.php
│   │   │   ├── PermissionController.php
│   │   │   ├── ProductController.php
│   │   │   ├── RoleController.php
│   │   │   ├── StockController.php
│   │   │   └── UserController.php
│   │   ├── Middleware/
│   │   │   └── CheckPermission.php
│   │   └── Requests/
│   │       ├── LoginRequest.php
│   │       ├── StockMovementRequest.php
│   │       ├── StoreProductRequest.php
│   │       └── UpdateProductRequest.php
│   ├── Models/
│   │   ├── Permission.php
│   │   ├── Product.php
│   │   ├── Role.php
│   │   ├── Stock.php
│   │   ├── StockMovement.php
│   │   └── User.php
│   └── Services/
│       └── StockService.php
├── bootstrap/
│   ├── app.php
│   ├── cache/
│   └── providers.php
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── filesystems.php
│   ├── hashing.php
│   ├── logging.php
│   ├── sanctum.php
│   └── session.php
├── database/
│   ├── migrations/
│   └── seeders/
│       └── DatabaseSeeder.php
├── docker/
│   └── entrypoint.sh
├── nginx/
│   └── default.conf
├── public/
│   └── index.php
├── routes/
│   ├── api.php
│   └── console.php
├── storage/
│   ├── app/
│   │   ├── private/
│   │   └── public/
│   ├── framework/
│   │   ├── cache/
│   │   ├── sessions/
│   │   └── views/
│   └── logs/
├── .env.example
├── artisan
├── composer.json
├── docker-compose.yml
├── Dockerfile
└── README.md
```
