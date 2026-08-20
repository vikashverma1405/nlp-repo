<?php

namespace App\Services\NLQ;

use InvalidArgumentException;

class SqlSafetyValidator
{
    private array $forbidden = [
        'insert', 'update', 'delete', 'drop', 'alter', 'truncate',
        'create', 'grant', 'revoke', 'comment', 'merge', 'call',
        'copy', 'vacuum', 'pg_sleep', 'pg_read_file', 'pg_ls_dir',
        'dblink', 'lo_import', 'lo_export', 'into outfile', 'set',
    ];

    public function validate(string $sql): string
    {
        $normalized = strtolower(trim($sql));

        // 1. Must start with SELECT (or WITH ... SELECT)
        if (!preg_match('/^\s*(with|select)\b/i', $normalized)) {
            throw new InvalidArgumentException('Only SELECT queries are allowed.');
        }

        // 2. No multiple statements (block stacked queries)
        $withoutTrailing = rtrim($normalized, "; \n\r\t");
        if (str_contains($withoutTrailing, ';')) {
            throw new InvalidArgumentException('Multiple statements are not allowed.');
        }

        // 3. Block forbidden keywords (word-boundary matched)
        foreach ($this->forbidden as $word) {
            if (preg_match('/\b' . preg_quote(trim($word), '/') . '\b/i', $normalized)) {
                throw new InvalidArgumentException("Forbidden keyword detected: {$word}");
            }
        }

        // 4. Block SQL comments (used to smuggle payloads)
        if (str_contains($normalized, '--') || str_contains($normalized, '/*')) {
            throw new InvalidArgumentException('SQL comments are not allowed.');
        }

        // 5. Enforce a LIMIT (append if the model forgot)
        if (!preg_match('/\blimit\b/i', $normalized)) {
            $sql = rtrim($sql, "; \n\r\t") . ' LIMIT 100';
        }

        return $sql;
    }
}
