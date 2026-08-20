<?php

namespace Tests\Feature;

use Tests\TestCase;

class ChatPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_chat_page_loads(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Ask a question about your PostgreSQL data');
        $response->assertSee('POST /api/nlq/ask');
    }
}
