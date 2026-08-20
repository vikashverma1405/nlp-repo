<?php

namespace App\Services\NLQ;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SchemaIntrospector
{
    public function getSchemaContext(): string
    {
        return Cache::remember('nlq_schema_context', now()->addHours(6), function () {
            $columns = DB::connection('nlq_readonly')->select("
                SELECT table_name, column_name, data_type, is_nullable
                FROM information_schema.columns
                WHERE table_schema = 'public'
                ORDER BY table_name, ordinal_position
            ");

            $foreignKeys = DB::connection('nlq_readonly')->select("
                SELECT
                    tc.table_name, kcu.column_name,
                    ccu.table_name AS foreign_table,
                    ccu.column_name AS foreign_column
                FROM information_schema.table_constraints tc
                JOIN information_schema.key_column_usage kcu
                    ON tc.constraint_name = kcu.constraint_name
                JOIN information_schema.constraint_column_usage ccu
                    ON ccu.constraint_name = tc.constraint_name
                WHERE tc.constraint_type = 'FOREIGN KEY'
                    AND tc.table_schema = 'public'
            ");

            return $this->format($columns, $foreignKeys);
        });
    }

    private function format(array $columns, array $foreignKeys): string
    {
        $tables = [];
        foreach ($columns as $col) {
            $tables[$col->table_name][] =
                "  {$col->column_name} {$col->data_type}"
                . ($col->is_nullable === 'NO' ? ' NOT NULL' : '');
        }

        $schema = "DATABASE SCHEMA (PostgreSQL):\n\n";
        foreach ($tables as $table => $cols) {
            $schema .= "TABLE {$table} (\n" . implode(",\n", $cols) . "\n)\n\n";
        }

        if ($foreignKeys) {
            $schema .= "RELATIONSHIPS:\n";
            foreach ($foreignKeys as $fk) {
                $schema .= "  {$fk->table_name}.{$fk->column_name} -> "
                         . "{$fk->foreign_table}.{$fk->foreign_column}\n";
            }
        }

        return $schema;
    }
}
