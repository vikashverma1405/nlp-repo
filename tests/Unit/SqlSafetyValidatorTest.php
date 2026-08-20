<?php

namespace Tests\Unit;

use App\Services\NLQ\SqlSafetyValidator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class SqlSafetyValidatorTest extends TestCase
{
    private SqlSafetyValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new SqlSafetyValidator();
    }

    public function test_allows_a_simple_select(): void
    {
        $sql = 'SELECT id, name FROM users LIMIT 10';
        $this->assertSame($sql, $this->validator->validate($sql));
    }

    public function test_allows_a_cte_with_statement(): void
    {
        $sql = 'WITH recent AS (SELECT id FROM users LIMIT 5) SELECT * FROM recent LIMIT 5';
        $this->assertSame($sql, $this->validator->validate($sql));
    }

    public function test_appends_limit_when_missing(): void
    {
        $result = $this->validator->validate('SELECT id FROM users');
        $this->assertStringContainsString('LIMIT 100', $result);
    }

    public function test_rejects_non_select(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->validator->validate('UPDATE users SET name = \'x\'');
    }

    public function test_rejects_stacked_statements(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->validator->validate('SELECT id FROM users; DROP TABLE users');
    }

    public function test_rejects_forbidden_keyword(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->validator->validate('SELECT id FROM users WHERE 1=1; delete from users');
    }

    public function test_rejects_sql_comments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->validator->validate('SELECT id FROM users -- drop everything');
    }

    public function test_rejects_block_comments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->validator->validate('SELECT id /* sneaky */ FROM users');
    }
}
