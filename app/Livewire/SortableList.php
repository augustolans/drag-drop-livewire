<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Item;

class SortableList extends Component
{
    public $groups;

    protected $listeners = ['itemReordered'];

    public function mount()
    {
        $this->loadItems();
    }

    public function loadItems()
    {
        $this->groups = Item::orderBy('group')
            ->orderBy('position')
            ->get()
            ->groupBy('group');
    }

    public function itemReordered($event)
    {
        $itemId = $event['itemId'];
        $newGroup = $event['newGroup'];
        $newIndex = $event['newIndex'];
        $oldGroup = $event['oldGroup'];

        $item = Item::find($itemId);

        // Atualiza grupo antigo
        if ($oldGroup !== $newGroup) {
            Item::where('group', $oldGroup)
                ->where('position', '>', $item->position)
                ->decrement('position');
        }

        // Atualiza novo grupo
        Item::where('group', $newGroup)
            ->where('position', '>=', $newIndex)
            ->increment('position');

        // Atualiza item movido
        $item->update([
            'position' => $newIndex,
            'group' => $newGroup
        ]);

        $this->loadItems();
    }

    public function render()
    {
        return view('livewire.sortable-list');
    }
}
