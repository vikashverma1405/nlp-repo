-- Create a read-only PostgreSQL role for AI-generated queries.
-- Run once against your database: psql -d yourdb -f nlq_readonly_role.sql

CREATE ROLE nlq_readonly WITH LOGIN PASSWORD 'strong_password';
GRANT CONNECT ON DATABASE yourdb TO nlq_readonly;
GRANT USAGE ON SCHEMA public TO nlq_readonly;
GRANT SELECT ON ALL TABLES IN SCHEMA public TO nlq_readonly;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT SELECT ON TABLES TO nlq_readonly;

-- Enforce a query timeout at the role level (5 seconds)
ALTER ROLE nlq_readonly SET statement_timeout = '5000';
