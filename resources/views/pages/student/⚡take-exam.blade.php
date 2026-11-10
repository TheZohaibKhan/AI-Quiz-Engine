    <form wire:submit="submitExam" class="space-y-4">
        @foreach ($this->filteredQuestions as $index => $question)
            @php $isFlagged = $answers[$question->id]['flagged'] ?? false; @endphp
            <div
                class="bento-flat space-y-4 transition-colors duration-150"
                style="{{ $isFlagged ? 'border-color: #5EEAD4; background-color: #F0FDFA;' : '' }}"
                wire:key="q-{{ $question->id }}"
            >
                <div style="display:flex; align-items:flex-start; gap:.75rem;">
                    <span style="display:inline-flex; align-items:center; justify-content:center; min-width:1.75rem; height:1.75rem; border-radius:8px; background:#F0FDFA; color:#0F766E; font-size:.75rem; font-weight:700; flex-shrink:0; margin-top:1px;">
                        {{ $loop->index + 1 }}
                    </span>
                    <flux:heading style="flex:1; padding-top:2px;">{{ $question->question }}</flux:heading>

                    {{-- Bookmark / flag button --}}
                    <button
                        type="button"
                        wire:click="toggleFlag({{ $question->id }})"
                        wire:loading.attr="disabled"
                        wire:target="toggleFlag({{ $question->id }})"
                        title="{{ $isFlagged ? 'Remove flag' : 'Flag for review' }}"
                        class="shrink-0 mt-0.5 p-1 rounded-md transition-colors {{ $isFlagged ? 'text-teal-600 hover:text-teal-800' : 'text-charcoal-400 hover:text-charcoal-700' }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="{{ $isFlagged ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                        </svg>
                    </button>
                </div>

                @if ($question->options)
                    <div class="space-y-2 pt-1">
                        @foreach ($question->options as $option)
                            <label class="quiz-option" wire:key="opt-{{ $question->id }}-{{ $loop->index }}">
                                <input
                                    type="radio"
                                    wire:model="answers.{{ $question->id }}.value"
                                    value="{{ $option }}"
                                />
                                <span>{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                @else
                    <flux:input
                        wire:model="answers.{{ $question->id }}.value"
                        placeholder="Type your answer…"
                    />

                    {{-- Hint section for short-answer questions --}}
                    <div
                        x-data="{ showHint: {{ isset($hints[$question->id]) ? 'true' : 'false' }}, loading: false }"
                        class="space-y-2"
                    >
                        <div class="flex justify-end">
                            <flux:button
                                size="sm"
                                variant="ghost"
                                x-bind:disabled="loading"
                                x-on:click="loading = true; showHint = true; $wire.streamHint({{ $question->id }}).then(() => loading = false)"
                                :loading="false"
                            >
                                <span x-show="!loading" class="inline-flex items-center gap-1">
                                    <flux:icon.light-bulb class="size-4" />
                                    Get a Hint
                                </span>
                                <span x-show="loading" x-cloak class="inline-flex items-center gap-1">
                                    <svg class="animate-spin size-3.5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                                    Thinking…
                                </span>
                            </flux:button>
                        </div>

                        <div
                            x-show="showHint"
                            style="display: none"
                            class="p-3 rounded-lg bg-amber-50 border border-amber-200 text-sm text-amber-800"
                        >
                            <div class="flex items-start gap-2">
                                <flux:icon.light-bulb class="size-4 mt-0.5 shrink-0 text-amber-600" />
                                <span wire:stream="hint-{{ $question->id }}">{{ $hints[$question->id] ?? '' }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach

        <div class="flex justify-end gap-3 pt-2 pb-4">
            @if ($isPreview)
                <flux:callout variant="warning" icon="eye" class="w-full">
                    <flux:callout.text>Preview mode — submission is disabled.</flux:callout.text>
                </flux:callout>
            @else
                <flux:button
                    variant="primary"
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="submitExam"
                >
                    <span wire:loading.remove wire:target="submitExam" class="inline-flex items-center gap-1">
                        <flux:icon.check class="size-4" />
                        Submit Exam
                    </span>
                    <span wire:loading wire:target="submitExam" class="inline-flex items-center gap-1">
                        <svg class="animate-spin size-4" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                        Grading…
                    </span>
                </flux:button>
            @endif
        </div>
    </form>
</div>
