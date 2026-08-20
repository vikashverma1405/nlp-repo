# Laravel NLQ for PostgreSQL (Azure OpenAI)

A Natural Language Interface for PostgreSQL built with **Laravel 11**, **Livewire 3**, and **Azure OpenAI**. Users ask questions in plain English; the app generates a **secure, read-only** PostgreSQL query, executes it, and returns result rows plus a plain-English summary.

## Architecture

```
User (plain English)
      │
      ▼
Blade + Livewire chat UI / API controller
      │
      ▼
NLQ pipeline ──► Schema Context Builder (introspect PostgreSQL)
      │                        │
      ▼                        ▼
Azure OpenAI (Chat Completions) ──► generates SQL (SELECT only)
      │
      ▼
SQL Safety Validator (whitelist, read-only, LIMIT enforced)
      │
      ▼
Execute via read-only DB connection
      │
      ▼
Format results ──► plain-English summary ──► Response
```

## Security model (defense in depth)

1. **Read-only DB role** — the AI-generated SQL runs on a PostgreSQL role that physically cannot write (see `database/sql/nlq_readonly_role.sql`).
2. **SQL Safety Validator** — rejects anything that isn't a single `SELECT`, blocks forbidden keywords, stacked statements, and comments, and enforces a `LIMIT`.
3. **Statement timeout** — enforced at both the role level and per query.
4. **Rate limiting** — applied on the API route.

## Standalone installation

1. Install PHP and Composer dependencies:
   ```bash
   composer install
   ```
2. Copy environment variables and generate an application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Install frontend dependencies:
   ```bash
   npm install
   ```
4. Configure your PostgreSQL credentials in `.env`:
   - `DB_*` for the main Laravel connection (defaults to `pgsql`)
   - `NLQ_DB_*` for the read-only connection used by generated SQL
   - `AZURE_OPENAI_*` for the Azure OpenAI deployment
5. Run the read-only role SQL against your database:
   ```bash
   psql -d yourdb -f database/sql/nlq_readonly_role.sql
   ```
6. Run the standard Laravel migration(s):
   ```bash
   php artisan migrate
   ```
7. Start the app:
   ```bash
   npm run dev
   php artisan serve
   ```
   Or build assets for production:
   ```bash
   npm run build
   ```

## UI and API

- Chat UI: `GET /`
- API endpoint: `POST /api/nlq/ask`

The chat page renders even before Azure OpenAI or PostgreSQL credentials are valid. Submission failures are surfaced as friendly errors so you can finish configuration incrementally.

## Configuration notes

- `config/services.php` includes the Azure OpenAI settings under `azure_openai`.
- `config/database.php` now includes the `nlq_readonly` PostgreSQL connection. The former helper file `config/database_nlq_connection.php` has been merged into the main database config.
- Keep the `nlq_readonly` user restricted to only the schemas, tables, and columns that are safe to expose.

## Development commands

```bash
php artisan test
php artisan test --filter=SqlSafetyValidatorTest
```

The `SqlSafetyValidator` remains the primary security boundary and is covered by unit tests. An additional feature test verifies that the Livewire chat page loads successfully.

## License

MIT
