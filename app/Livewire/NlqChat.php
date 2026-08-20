<?php

namespace App\Livewire;

use App\Services\NLQ\NlqPipeline;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class NlqChat extends Component
{
    private const MAX_MESSAGES = 12;

    #[Validate('required|string|max:500')]
    public string $question = '';

    public array $messages = [];

    public function submit(NlqPipeline $pipeline): void
    {
        $this->validate();

        $question = trim($this->question);

        $this->messages[] = [
            'role' => 'user',
            'content' => $question,
        ];

        $response = $pipeline->ask($question);
        unset($response['status']);

        $this->messages[] = [
            'role' => 'assistant',
            'content' => $response,
        ];

        $this->messages = array_slice($this->messages, -self::MAX_MESSAGES);
        $this->question = '';
    }

    public function render(): View
    {
        return view('livewire.nlq-chat');
    }
}
