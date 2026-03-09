# PHP Backend (Sheet Music Library)

This backend now runs on PHP instead of Node.js.

## Requirements
- PHP 8.1+ with `pdo_mysql`
- MySQL 8+

## Environment
Use `backend/.env` (existing file) with:
- `PORT`
- `HOST`
- `NODE_ENV` (kept for compatibility)
- `DB_HOST`, `DB_PORT`, `DB_USER`, `DB_PASSWORD`, `DB_NAME`
- `CORS_ORIGIN` (single origin, comma-separated origins, or `*`)

## Run API
From `backend/`:

```bash
php -S 0.0.0.0:3000 index.php
```

The API endpoints stay the same:
- `GET /`
- `GET /api/health`
- `POST /api/auth/register`
- `POST /api/auth/login`
- `GET /api/library`
- `GET /api/library/{id}`
- `POST /api/library`
- `PUT /api/library/{id}`
- `DELETE /api/library/{id}`
- `GET /api/users`
- `GET /api/users/{id}`
- `POST /api/users`
- `PUT /api/users/{id}`
- `DELETE /api/users/{id}`

## Database scripts
From `backend/`:

```bash
php scripts/initDatabase.php
php scripts/adjustDatabase.php
```




