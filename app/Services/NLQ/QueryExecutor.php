<?php

namespace App\Services\NLQ;

use Illuminate\Support\Facades\DB;

class QueryExecutor
{
    public function run(string $sql): array
    {
        // Belt-and-suspenders: set a per-query timeout on this session.
        DB::connection('nlq_readonly')->statement("SET statement_timeout = 5000");

        $rows = DB::connection('nlq_readonly')->select($sql);

        return array_map(fn ($row) => (array) $row, $rows);
    }
}
