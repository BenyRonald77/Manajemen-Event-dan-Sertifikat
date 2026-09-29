<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class EventIndex extends Component
{
    public bool $showCreateForm = false;

    public string $name = '';

    public string $description = '';

    public string $location = '';

    public string $starts_at = '';

    public string $ends_at = '';

    public ?int $quota = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'location' => ['nullable', 'string', 'max:200'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'quota' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function create(): void
    {
        $validated = $this->validate();

        $event = Event::create($validated);

        $this->reset(['name', 'description', 'location', 'starts_at', 'ends_at', 'quota', 'showCreateForm']);

        $this->redirectRoute('panitia.events.show', $event, navigate: true);
    }

    public function render(): View
    {
        $events = Event::query()
            ->withCount('registrations')
            ->orderByDesc('starts_at')
            ->get();

        return view('livewire.admin.event-index', [
            'events' => $events,
        ])->layout('layouts.app', [
            'header' => new HtmlString('<h2 class="text-xl font-semibold leading-tight text-slate-800">Acara</h2>'),
        ]);
    }
}
