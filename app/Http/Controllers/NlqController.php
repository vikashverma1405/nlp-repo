<?php

namespace App\Http\Controllers;

use App\Services\NLQ\NlqPipeline;
use Illuminate\Http\Request;

class NlqController extends Controller
{
    public function __construct(private NlqPipeline $pipeline) {}

    public function ask(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
        ]);

        $result = $this->pipeline->ask($validated['question']);
        $status = $result['status'] ?? 200;

        unset($result['status']);

        return response()->json($result, $status);
    }
}
