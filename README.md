# Product Pricing API

Laravel REST API for product management and additional product prices in different currencies.

## Functional scope

- Full product CRUD.
- Explicit product prices per currency.
- JSON responses using Laravel API Resources.
- Eloquent persistence with referential integrity.
- Feature and domain unit tests.

## Requirements

- PHP version compatible with the chosen Laravel release (PHP 8.3+ for Laravel 13).
- Composer 2.
- SQLite with the `pdo_sqlite` extension, or another supported database configured in `.env`.

## Installation (PowerShell)

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
# If using SQLite and the file does not already exist:
New-Item -ItemType File -Force database/database.sqlite
# Configure DB_CONNECTION=sqlite in .env, without an invalid DB_DATABASE override.
php artisan migrate:fresh --seed
php artisan serve
```

Base URL: `http://127.0.0.1:8000` (no `/api` prefix; configured in `bootstrap/app.php`).

> `migrate:fresh --seed` deletes all existing tables and data. Use it only for local setup, tests, or disposable databases.

## Endpoints

| Method | URL | Successful status |
|---|---|---|
| GET | `/products` | 200 |
| POST | `/products` | 201 |
| GET | `/products/{id}` | 200 |
| PUT | `/products/{id}` | 200 |
| DELETE | `/products/{id}` | 204 |
| GET | `/products/{id}/prices` | 200 |
| POST | `/products/{id}/prices` | 201 |

### Create a product

```http
POST /products
Content-Type: application/json
Accept: application/json
```

```json
{
  "name": "Mechanical Keyboard",
  "description": "Mechanical keyboard",
  "price": "120.00",
  "currency_id": 1,
  "tax_cost": "12.00",
  "manufacturing_cost": "50.00"
}
```

### Register an additional price

```http
POST /products/1/prices
Content-Type: application/json
Accept: application/json
```

```json
{
  "currency_id": 2,
  "price": "110.40"
}
```

`currency_id` values depend on your seeded currencies. Review the seeders before running the examples.

## API documentation

- [OpenAPI 3.0.3](docs/openapi.yaml)
- [Postman collection](docs/postman_collection.json)
- [Insomnia JSON collection (legacy v4 import)](docs/insomnia_collection.json)

To render Swagger UI, paste or load `docs/openapi.yaml` in a compatible OpenAPI viewer such as [Swagger Editor](https://editor.swagger.io/). This repository does **not** expose a `/docs` endpoint by default.

## Architecture

Pragmatic DDD / Clean Architecture:

- `app/Domain/Catalog`: domain entities, value objects, business exceptions and repository contracts; no Eloquent dependencies.
- `app/Application/Catalog`: use cases and input DTOs.
- `app/Infrastructure/Persistence/Eloquent`: ORM models, repository implementations and mapping to domain entities.
- `app/Http`: controllers, form requests, and JSON resources.

Eloquent is used to persist data, while application use cases depend on repository contracts. This separation is intentionally proportional to the small domain and is not a complete implementation of tactical DDD.

### Data and pricing decisions

- `products.price` is the product's base price, denominated in `products.currency_id`.
- `product_prices` stores **explicitly registered** additional prices, not automatic conversions.
- `GET /products/{id}/prices` does not include the base price unless it was independently registered in `product_prices`.
- A database unique constraint prevents duplicate `(product_id, currency_id)` registrations.
- Deleting a product cascades to its additional prices, but deleting a referenced currency is restricted.
- Amounts are stored in `DECIMAL` columns and represented as strings in API responses.
- Currency exchange rates supplied by seeders are demonstration data, not live market rates.
- `PUT` requires a complete update payload; `PATCH` is outside the test scope.

## Tests

```powershell
php artisan test
php artisan route:list --path=products
```

Tests use `RefreshDatabase`. CI runs against SQLite in-memory on PHP 8.3; align the CI PHP version with `composer.json` if a different runtime is required.

## Error responses

- `404` product does not exist.
- `422` validation error, with `message` and `errors`.
- `409` product price already registered for that product and currency.
- `204` successful delete, with an empty body.

## Security notes

This is a technical-test API. The supplied requirement does not specify authentication or authorization, and the current Form Requests use `authorize(): true`. **The endpoints are not authenticated.** Do not expose the service publicly with sensitive data in this state.

Existing safeguards include input validation, controlled mass assignment, and foreign-key/uniqueness constraints. For deployment, add authentication and authorization (for example Sanctum with policies), rate limiting, HTTPS, secure environment variables, monitoring, and `APP_DEBUG=false`.

## Delivery

A suggested branch flow is `feat/*` → `develop` → `main`, via pull requests. Run tests before merging; tag a verified release as `v1.0.0`.