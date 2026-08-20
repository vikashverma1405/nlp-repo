# Laravel NLQ for PostgreSQL (Azure OpenAI)

A Natural Language Interface for PostgreSQL built with **PHP/Laravel** and **Azure OpenAI**. Users ask questions in plain English; the app generates a **secure, read-only** PostgreSQL query, executes it, and returns results plus a plain-English summary.

## Architecture

```
User (plain English)
      │
      ▼
Laravel Controller ──► Schema Context Builder (introspect PostgreSQL)
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
Format results ──► (optional) LLM summarizes ──► Response
```

## Security model (defense in depth)

1. **Read-only DB role** — the AI-generated SQL runs on a PostgreSQL role that physically cannot write (see `database/sql/nlq_readonly_role.sql`).
2. **SQL Safety Validator** — rejects anything that isn't a single `SELECT`, blocks forbidden keywords, stacked statements, and comments, and enforces a `LIMIT`.
3. **Statement timeout** — enforced at both the role level and per query.
4. **Rate limiting** — applied on the API route.

## Setup

1. Install into an existing Laravel app (or copy these files in).
2. Copy `.env.example` values into your `.env` and fill them in.
3. Create the read-only role:
   ```bash
   psql -d yourdb -f database/sql/nlq_readonly_role.sql
   ```
4. Register the service bindings (they are auto-resolved by Laravel's container since they use constructor injection).
5. Hit the endpoint:
   ```bash
   curl -X POST https://your-app.test/api/nlq/ask \
     -H 'Content-Type: application/json' \
     -H 'Authorization: Bearer <token>' \
     -d '{"question":"How many users signed up last month?"}'
   ```

## Environment variables

See `.env.example`.

## Tests

```bash
php artisan test --filter=SqlSafetyValidatorTest
```

The `SqlSafetyValidator` is the security boundary and is covered by unit tests for malicious inputs.

## License

MIT
