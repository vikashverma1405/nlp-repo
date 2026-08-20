<?php

namespace App\Services\NLQ;

class NlqPipeline
{
    public function __construct(
        private SqlGenerator $generator,
        private SqlSafetyValidator $validator,
        private QueryExecutor $executor,
        private ResultFormatter $formatter,
    ) {}

    public function ask(string $question): array
    {
        try {
            $rawSql = $this->generator->generate($question);

            if (str_contains($rawSql, SqlGenerator::CANNOT_ANSWER_SENTINEL)) {
                return [
                    'success' => false,
                    'question' => $question,
                    'sql' => null,
                    'summary' => null,
                    'columns' => [],
                    'rows' => [],
                    'row_count' => 0,
                    'message' => "I couldn't answer that with the available data.",
                    'reason' => null,
                    'status' => 422,
                ];
            }

            $safeSql = $this->validator->validate($rawSql);
            $rows = $this->executor->run($safeSql);
            $summary = $this->formatter->summarize($question, $rows);

            return [
                'success' => true,
                'question' => $question,
                'sql' => $safeSql,
                'summary' => $summary,
                'columns' => $rows ? array_keys($rows[0]) : [],
                'rows' => $rows,
                'row_count' => count($rows),
                'message' => null,
                'reason' => null,
                'status' => 200,
            ];
        } catch (\InvalidArgumentException $e) {
            return [
                'success' => false,
                'question' => $question,
                'sql' => null,
                'summary' => null,
                'columns' => [],
                'rows' => [],
                'row_count' => 0,
                'message' => 'The generated query was rejected for safety reasons.',
                'reason' => $e->getMessage(),
                'status' => 422,
            ];
        } catch (\Throwable $e) {
            report($e);

            return [
                'success' => false,
                'question' => $question,
                'sql' => null,
                'summary' => null,
                'columns' => [],
                'rows' => [],
                'row_count' => 0,
                'message' => 'Something went wrong processing your question. Check your Azure OpenAI and database settings, then try again.',
                'reason' => null,
                'status' => 500,
            ];
        }
    }
}
