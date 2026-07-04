# Finance Tracker API

A RESTful API built with Laravel and Laravel Sanctum for tracking personal income and expenses.

## Requirements

- PHP 8.1+
- Composer
- MySQL / SQLite

## Setup

```bash
git clone <repo-url>
cd finance-tracker
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## Authentication

All transaction endpoints require a Bearer token obtained from register or login.

```
Authorization: Bearer <token>
```

---

## API Endpoints

### Auth

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/register` | Register a new user |
| POST | `/api/login` | Login and receive a token |

#### POST /api/register

**Request Body**
```json
{
  "name": "Ahmed Adel",
  "email": "ahmed@example.com",
  "password": "secret123"
}
```

**Response** `201`
```json
{
  "success": true,
  "user": { "id": 1, "name": "Ahmed Adel", "email": "ahmed@example.com" },
  "token": "1|abc123..."
}
```

#### POST /api/login

**Request Body**
```json
{
  "email": "ahmed@example.com",
  "password": "secret123"
}
```

**Response** `200`
```json
{
  "success": true,
  "user": { "id": 1, "name": "Ahmed Adel", "email": "ahmed@example.com" },
  "token": "2|xyz456..."
}
```

---

### Transactions

All endpoints below require `Authorization: Bearer <token>`.

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/transactions` | List all transactions (paginated) |
| POST | `/api/transactions` | Create a new transaction |
| GET | `/api/transactions/{id}` | Get a single transaction |
| PUT/PATCH | `/api/transactions/{id}` | Update a transaction |
| DELETE | `/api/transactions/{id}` | Delete a transaction |
| GET | `/api/transactions/summary` | Get income/expense summary |
| GET | `/api/transactions/search?query=` | Search transactions |

#### Transaction Fields

| Field | Type | Rules |
|-------|------|-------|
| `title` | string | required, 3–255 chars |
| `amount` | numeric | required, min 0.01 |
| `type` | string | required, `income` or `expense` |
| `category` | string | required, max 100 chars |
| `date` | date | required |
| `description` | string | optional, max 2000 chars |

#### GET /api/transactions

Returns paginated list of the authenticated user's transactions (10 per page), ordered by newest first.

**Response** `200`
```json
{
  "success": true,
  "message": "List all transactions",
  "data": {
    "current_page": 1,
    "data": [ { "id": "uuid", "title": "Salary", "amount": "5000.00", "type": "income" } ],
    "per_page": 10,
    "total": 42
  }
}
```

#### POST /api/transactions

**Request Body**
```json
{
  "title": "Grocery Shopping",
  "amount": 150.75,
  "type": "expense",
  "category": "Food",
  "date": "2026-07-04",
  "description": "Weekly groceries"
}
```

**Response** `201`
```json
{
  "success": true,
  "message": "Transaction created",
  "data": { ... }
}
```

#### GET /api/transactions/{id}

**Response** `200`
```json
{
  "success": true,
  "message": "Transaction details",
  "data": { "id": "uuid", "title": "Grocery Shopping" }
}
```

Returns `403` if the transaction belongs to another user.

#### PUT /api/transactions/{id}

Same request body as POST. Returns `403` if unauthorized.

**Response** `200`
```json
{
  "success": true,
  "message": "Transaction updated",
  "data": { ... }
}
```

#### DELETE /api/transactions/{id}

Returns `204 No Content` on success. Returns `403` if unauthorized.

#### GET /api/transactions/summary

**Response** `200`
```json
{
  "success": true,
  "message": "Dashboard summary retrieved successfully",
  "data": {
    "totalIncome": "15000.00",
    "totalExpense": "8500.00",
    "totalBalance": "-6500.000",
    "currency": "EGP"
  }
}
```

#### GET /api/transactions/search?query=groceries

Searches `title` and `description` fields. Returns paginated results (10 per page).

**Response** `200`
```json
{
  "success": true,
  "message": "Search results retrieved successfully",
  "data": { "current_page": 1, "data": [ ... ], "total": 3 }
}
```

---

## Error Responses

| Status | Meaning |
|--------|---------|
| `401` | Unauthenticated — invalid or missing token |
| `403` | Forbidden — resource belongs to another user |
| `422` | Validation failed — check field errors in response |

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
