<div class="container mx-auto p-4">
    <div class="flex gap-4"
         x-data
         wire:sortable-group.item-reordered="itemReordered">

        @foreach($groups as $groupName => $items)
            <div class="w-64 bg-gray-100 p-4 rounded-lg shadow"
                 wire:sortable-group.group="{{ $groupName }}">

                <h2 class="text-xl font-bold mb-4 capitalize">{{ $groupName }}</h2>

                <div class="space-y-2"
                     wire:sortable-group.item-group="{{ $groupName }}"
                     wire:sortable-group.options="{ animation: 150 }">

                    @foreach($items as $item)
                        <div wire:sortable-group.item="{{ $item->id }}"
                             wire:key="item-{{ $item->id }}-{{ $groupName }}"
                             class="bg-white p-4 rounded shadow cursor-move hover:shadow-md transition">
                            {{ $item->name }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

    </div>
</div>

@push('styles')
<style>
    .wire-sortable-group-dragging {
        opacity: 0.5;
        background: #f3f4f6;
        border: 2px dashed #4f46e5;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/livewire-sortable@1.0.0/dist/livewire-sortable.js"></script>
@endpush
