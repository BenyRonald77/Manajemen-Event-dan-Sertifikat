<?php

namespace App\Livewire\Public;

use App\Models\Event;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EventList extends Component
{
    public function render(): View
    {
        $events = Event::query()
            ->where('ends_at', '>=', now())
            ->orderBy('starts_at')
            ->get();

        return view('livewire.public.event-list', [
            'events' => $events,
        ])->layout('layouts.public');
    }
}
