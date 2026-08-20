<div class="space-y-6">
    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-sky-600">Laravel 11 + Livewire</p>
                <h1 class="text-3xl font-semibold text-slate-900">Ask a question about your PostgreSQL data</h1>
                <p class="mt-2 max-w-3xl text-sm text-slate-600">
                    Ask in plain English to generate a safe read-only SQL query, review the SQL, and inspect the result table.
                </p>
            </div>
            <div class="rounded-2xl bg-slate-100 px-4 py-3 text-sm text-slate-600">
                API endpoint: <code class="font-medium text-slate-900">POST /api/nlq/ask</code>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        <div class="space-y-4">
            @forelse ($messages as $message)
                @if ($message['role'] === 'user')
                    <div class="flex justify-end">
                        <div class="max-w-3xl rounded-3xl rounded-br-lg bg-sky-600 px-5 py-4 text-sm text-white shadow-sm">
                            {{ $message['content'] }}
                        </div>
                    </div>
                @else
                    @php($response = $message['content'])
                    <div class="flex justify-start">
                        <div class="w-full max-w-4xl rounded-3xl rounded-bl-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                            <div class="space-y-4 text-sm text-slate-700">
                                @if ($response['success'])
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600">Summary</p>
                                        <p class="mt-2 text-base text-slate-900">{{ $response['summary'] }}</p>
                                    </div>

                                    <details class="rounded-2xl bg-slate-950 p-4 text-slate-100" open>
                                        <summary class="cursor-pointer text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Generated SQL</summary>
                                        <pre class="mt-3 overflow-x-auto whitespace-pre-wrap text-sm">{{ $response['sql'] }}</pre>
                                    </details>

                                    <div>
                                        <div class="mb-3 flex items-center justify-between gap-3">
                                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Result rows</p>
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                                {{ $response['row_count'] }} {{ \Illuminate\Support\Str::plural('row', $response['row_count']) }}
                                            </span>
                                        </div>

                                        @if ($response['row_count'] === 0)
                                            <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-slate-500">
                                                No rows matched this question.
                                            </div>
                                        @else
                                            <div class="overflow-hidden rounded-2xl border border-slate-200">
                                                <div class="overflow-x-auto">
                                                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                                                        <thead class="bg-slate-50">
                                                            <tr>
                                                                @foreach ($response['columns'] as $column)
                                                                    <th class="px-4 py-3 font-semibold text-slate-600">{{ $column }}</th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-slate-100 bg-white">
                                                            @foreach ($response['rows'] as $row)
                                                                <tr>
                                                                    @foreach ($response['columns'] as $column)
                                                                        <td class="px-4 py-3 align-top text-slate-700">
                                                                            {{ is_array($row[$column] ?? null) ? json_encode($row[$column]) : ($row[$column] ?? '—') }}
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-amber-900">
                                        <p class="font-semibold">{{ $response['message'] }}</p>
                                        @if (!empty($response['reason']))
                                            <p class="mt-2 text-sm text-amber-800">Reason: {{ $response['reason'] }}</p>
                                        @endif
                                    </div>

                                    @if (!empty($response['sql']))
                                        <details class="rounded-2xl bg-slate-950 p-4 text-slate-100">
                                            <summary class="cursor-pointer text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Generated SQL</summary>
                                            <pre class="mt-3 overflow-x-auto whitespace-pre-wrap text-sm">{{ $response['sql'] }}</pre>
                                        </details>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center text-slate-500 shadow-sm">
                    Start the conversation by asking about counts, trends, top records, or recent activity in your PostgreSQL database.
                </div>
            @endforelse

            <div wire:loading.flex wire:target="submit" class="items-center gap-3 rounded-2xl bg-sky-50 px-4 py-3 text-sm text-sky-700 shadow-sm ring-1 ring-sky-100">
                <svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"></path>
                </svg>
                <span>Generating SQL, validating it, and formatting the answer…</span>
            </div>
        </div>

        <aside class="space-y-4">
            <form wire:submit="submit" class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <label for="question" class="text-sm font-semibold text-slate-800">Your question</label>
                <textarea
                    id="question"
                    wire:model="question"
                    rows="6"
                    maxlength="500"
                    class="mt-3 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-100"
                    placeholder="e.g. Show the top 10 customers by revenue this month"
                ></textarea>
                @error('question')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="submit"
                    class="mt-4 inline-flex w-full items-center justify-center rounded-2xl bg-sky-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-sky-700 disabled:cursor-not-allowed disabled:opacity-70"
                >
                    Ask the NLQ assistant
                </button>
            </form>

            <div class="rounded-3xl bg-slate-900 p-5 text-sm text-slate-200 shadow-sm">
                <h2 class="font-semibold text-white">Before you start</h2>
                <ul class="mt-3 space-y-2 text-slate-300">
                    <li>• Configure Azure OpenAI credentials in <code class="text-white">.env</code>.</li>
                    <li>• Use the read-only <code class="text-white">nlq_readonly</code> PostgreSQL role.</li>
                    <li>• Review generated SQL before sharing results from sensitive tables.</li>
                </ul>
            </div>
        </aside>
    </div>
</div>
