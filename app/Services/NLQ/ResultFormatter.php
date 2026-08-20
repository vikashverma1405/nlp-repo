<?php

namespace App\Services\NLQ;

class ResultFormatter
{
    public function __construct(private AzureOpenAIClient $ai) {}

    public function summarize(string $question, array $rows): string
    {
        if (empty($rows)) {
            return 'No results were found for your question.';
        }

        // Keep token usage low: only send a sample
        $sample = array_slice($rows, 0, 20);

        $result = $this->ai->chat([
            ['role' => 'system', 'content' =>
                'You summarize SQL query results in clear, concise plain English for a non-technical user. Do not mention SQL or column names verbatim unless helpful.'],
            ['role' => 'user', 'content' =>
                "Question: {$question}\n\nResults (JSON):\n" . json_encode($sample) .
                "\n\nTotal rows: " . count($rows)],
        ], ['max_tokens' => 300]);

        return trim($result['choices'][0]['message']['content'] ?? '');
    }
}
