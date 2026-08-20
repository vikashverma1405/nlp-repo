<?php

namespace App\Http\Controllers;

use App\Services\NLQ\QueryExecutor;
use App\Services\NLQ\ResultFormatter;
use App\Services\NLQ\SqlGenerator;
use App\Services\NLQ\SqlSafetyValidator;
use Illuminate\Http\Request;

class NlqController extends Controller
{
    public function __construct(
        private SqlGenerator $generator,
        private SqlSafetyValidator $validator,
        private QueryExecutor $executor,
        private ResultFormatter $formatter,
    ) {}

    public function ask(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
        ]);

        try {
            $rawSql = $this->generator->generate($validated['question']);

            if (str_contains($rawSql, 'CANNOT_ANSWER')) {
                return response()->json([
                    'success' => false,
                    'message' => "I couldn't answer that with the available data.",
                ], 422);
            }

            $safeSql = $this->validator->validate($rawSql);
            $rows    = $this->executor->run($safeSql);
            $summary = $this->formatter->summarize($validated['question'], $rows);

            return response()->json([
                'success'   => true,
                'question'  => $validated['question'],
                'sql'       => $safeSql,
                'summary'   => $summary,
                'columns'   => $rows ? array_keys($rows[0]) : [],
                'rows'      => $rows,
                'row_count' => count($rows),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => 'The generated query was rejected for safety reasons.',
                'reason'  => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong processing your question.',
            ], 500);
        }
    }
}
