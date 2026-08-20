<?php

namespace App\Services\NLQ;

class SqlGenerator
{
    public const CANNOT_ANSWER_SENTINEL = "CANNOT_ANSWER";

    public function __construct(
        private AzureOpenAIClient $ai,
        private SchemaIntrospector $schema,
    ) {}

    public function generate(string $question): string
    {
        $schemaContext = $this->schema->getSchemaContext();

        $cannotAnswer = self::CANNOT_ANSWER_SENTINEL;

        $systemPrompt = <<<PROMPT
You are a PostgreSQL expert. Convert the user's natural language question into a single, valid, READ-ONLY PostgreSQL SELECT query.

STRICT RULES:
- Output ONLY the SQL query. No explanations, no markdown, no code fences.
- Use ONLY SELECT statements. Never INSERT, UPDATE, DELETE, DROP, ALTER, TRUNCATE, GRANT, or any DDL/DML.
- Only reference tables and columns that exist in the provided schema.
- Always include a LIMIT clause (max 100) unless the user requests an aggregate/count.
- Use proper JOINs based on the relationships provided.
- Use ILIKE for case-insensitive text matching.
- If the question cannot be answered with the schema, return exactly: SELECT '{$cannotAnswer}' AS error;

{$schemaContext}
PROMPT;

        $result = $this->ai->chat([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user',   'content' => $question],
        ]);

        $sql = trim($result['choices'][0]['message']['content'] ?? '');

        // Strip accidental markdown fences
        $sql = preg_replace('/^```(?:sql)?|```$/m', '', $sql);

        return trim($sql);
    }
}
