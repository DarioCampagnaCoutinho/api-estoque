# Curls para Insomnia

Configure no ambiente do Insomnia:

```json
{
  "base_url": "http://localhost:8080/api",
  "token": "cole-o-token-retornado-no-login"
}
```

Importe os comandos pelo Insomnia em `Create > Import from Clipboard` ou `Import Data > From Clipboard`.

## Health

```bash
curl --request GET '{{ _.base_url }}/health' \
  --header 'Accept: application/json'
```

## Auth

```bash
curl --request POST '{{ _.base_url }}/auth/login' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --data '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

```bash
curl --request GET '{{ _.base_url }}/auth/me' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}'
```

```bash
curl --request POST '{{ _.base_url }}/auth/logout' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}'
```

## Usuarios

```bash
curl --request GET '{{ _.base_url }}/users?per_page=20&search=admin' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}'
```

```bash
curl --request GET '{{ _.base_url }}/users/1' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}'
```

```bash
curl --request POST '{{ _.base_url }}/users' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}' \
  --header 'Content-Type: application/json' \
  --data '{
    "name": "Usuario Teste",
    "email": "usuario.teste@example.com",
    "password": "password",
    "roles": [2]
  }'
```

```bash
curl --request PUT '{{ _.base_url }}/users/2' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}' \
  --header 'Content-Type: application/json' \
  --data '{
    "name": "Usuario Teste Atualizado",
    "email": "usuario.teste@example.com"
  }'
```

```bash
curl --request PUT '{{ _.base_url }}/users/2/roles' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}' \
  --header 'Content-Type: application/json' \
  --data '{
    "roles": [2, 3]
  }'
```

```bash
curl --request PUT '{{ _.base_url }}/users/2/permissions' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}' \
  --header 'Content-Type: application/json' \
  --data '{
    "permissions": [15, 19]
  }'
```

```bash
curl --request DELETE '{{ _.base_url }}/users/2' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}'
```

## Roles

```bash
curl --request GET '{{ _.base_url }}/roles' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}'
```

```bash
curl --request GET '{{ _.base_url }}/roles/1' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}'
```

```bash
curl --request POST '{{ _.base_url }}/roles' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}' \
  --header 'Content-Type: application/json' \
  --data '{
    "name": "operator",
    "description": "Operador de produtos e estoque",
    "permissions": [15, 19, 20]
  }'
```

```bash
curl --request PUT '{{ _.base_url }}/roles/2' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}' \
  --header 'Content-Type: application/json' \
  --data '{
    "name": "manager",
    "description": "Gerente de produtos e estoque"
  }'
```

```bash
curl --request PUT '{{ _.base_url }}/roles/2/permissions' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}' \
  --header 'Content-Type: application/json' \
  --data '{
    "permissions": [15, 16, 17, 18, 19, 20]
  }'
```

```bash
curl --request DELETE '{{ _.base_url }}/roles/3' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}'
```

## Permissoes

```bash
curl --request GET '{{ _.base_url }}/permissions' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}'
```

```bash
curl --request POST '{{ _.base_url }}/permissions' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}' \
  --header 'Content-Type: application/json' \
  --data '{
    "name": "reports.view",
    "description": "Visualizar relatorios",
    "group": "reports"
  }'
```

```bash
curl --request PUT '{{ _.base_url }}/permissions/1' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}' \
  --header 'Content-Type: application/json' \
  --data '{
    "description": "Visualizar usuarios"
  }'
```

```bash
curl --request DELETE '{{ _.base_url }}/permissions/21' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}'
```

## Produtos

```bash
curl --request GET '{{ _.base_url }}/products?per_page=20&search=produto&active=true' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}'
```

```bash
curl --request GET '{{ _.base_url }}/products/1' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}'
```

```bash
curl --request POST '{{ _.base_url }}/products' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}' \
  --header 'Content-Type: application/json' \
  --data '{
    "name": "Produto Exemplo",
    "sku": "PROD-001",
    "description": "Produto para teste da API",
    "price": 29.9,
    "cost": 15,
    "unit": "un",
    "active": true,
    "min_quantity": 5
  }'
```

```bash
curl --request PUT '{{ _.base_url }}/products/1' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}' \
  --header 'Content-Type: application/json' \
  --data '{
    "name": "Produto Exemplo Atualizado",
    "price": 34.9,
    "cost": 17.5,
    "active": true
  }'
```

```bash
curl --request DELETE '{{ _.base_url }}/products/1' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}'
```

## Estoque

```bash
curl --request GET '{{ _.base_url }}/stock?per_page=20&low=false' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}'
```

```bash
curl --request GET '{{ _.base_url }}/stock/movements?per_page=20&type=in&product_id=1' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}'
```

```bash
curl --request GET '{{ _.base_url }}/stock/product/1' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}'
```

```bash
curl --request GET '{{ _.base_url }}/stock/product/1/movements?per_page=20&type=in' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}'
```

```bash
curl --request POST '{{ _.base_url }}/stock/movement' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}' \
  --header 'Content-Type: application/json' \
  --data '{
    "product_id": 1,
    "type": "in",
    "quantity": 100,
    "unit_cost": 15,
    "reference": "NF-001",
    "reason": "Compra de fornecedor",
    "notes": "Entrada inicial de estoque"
  }'
```

```bash
curl --request POST '{{ _.base_url }}/stock/movement' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}' \
  --header 'Content-Type: application/json' \
  --data '{
    "product_id": 1,
    "type": "out",
    "quantity": 10,
    "reason": "Venda",
    "reference": "PED-001"
  }'
```

```bash
curl --request POST '{{ _.base_url }}/stock/movement' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {{ _.token }}' \
  --header 'Content-Type: application/json' \
  --data '{
    "product_id": 1,
    "type": "adjustment",
    "quantity": 50,
    "reason": "Inventario",
    "notes": "Ajuste apos contagem fisica"
  }'
```
