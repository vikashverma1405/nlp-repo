<?php

namespace App\Livewire;

use App\Services\NLQ\NlqPipeline;
use Livewire\Attributes\Validate;
use Livewire\Component;

class NlqChat extends Component
{
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

        $this->question = '';
    }

    public function render()
    {
        return view('livewire.nlq-chat');
    }
}
