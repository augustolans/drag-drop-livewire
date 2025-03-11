<div class="p-6 min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Task Board</h1>
        <button wire:click="$set('showCreateModal', true)"
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
            @svg('heroicon-o-plus', 'h-5 w-5')
            Nova Tarefa
        </button>
    </div>

    <!-- Modal de Criação -->
    @if ($showCreateModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-xl">
                <h2 class="text-xl font-bold mb-4">Nova Tarefa</h2>
                <form wire:submit.prevent="addTask">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Título</label>
                            <input type="text" wire:model="newTask.title"
                                class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500" autofocus
                                required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Descrição</label>
                            <textarea wire:model="newTask.description" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500"
                                rows="3"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Status</label>
                            <select wire:model="newTask.status"
                                class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                                <option value="todo">To Do</option>
                                <option value="in_progress">In Progress</option>
                                <option value="done">Done</option>
                            </select>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" wire:click="$set('showCreateModal', false)"
                                class="px-4 py-2 text-gray-600 hover:text-gray-800">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                Criar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Colunas -->
    <div class="flex gap-6 overflow-x-auto pb-4">
        @foreach ($columns as $statusKey => $column)
            <div class="flex-1 min-w-[300px] {{ $column['color'] }} rounded-lg shadow-sm {{ $column['border'] }}"
                wire:key="column-{{ $statusKey }}">
                <div class="p-4">
                    <div class="flex items-center gap-2 mb-4 ">
                        @svg($column['icon'], 'h-6 w-6 text-gray-600')
                        <h2 class="text-lg font-bold text-gray-700">
                            {{ $column['title'] }}
                            <span class="text-sm ml-1 text-gray-500">
                                ({{ count(${$statusKey === 'todo' ? 'todoTasks' : ($statusKey === 'in_progress' ? 'inProgressTasks' : 'doneTasks')}) }})
                            </span>
                        </h2>
                    </div>

                    <div class="space-y-3 sortable-column" data-status="{{ $statusKey }}">
                        @foreach (${$statusKey === 'todo' ? 'todoTasks' : ($statusKey === 'in_progress' ? 'inProgressTasks' : 'doneTasks')} as $task)
                            <div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-all cursor-move relative group "
                                data-task-id="{{ $task->id }}"
                                wire:key="task-{{ $task->id }}-{{ $statusKey }}"
                                wire:click="$set('showCreateModal', true)">
                                <button wire:click="deleteTask({{ $task->id }})"
                                    class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity text-red-400 hover:text-red-600"
                                    onclick="return confirm('Excluir tarefa?')">
                                    @svg('heroicon-o-trash', 'h-5 w-5')
                                </button>

                                <div class="flex items-start gap-3">
                                    <div class="drag-handle">
                                        @svg('heroicon-o-arrows-pointing-out', 'h-5 w-5 text-gray-400')
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-800">{{ $task->title }}</h3>
                                        @if ($task->description)
                                            <p class="mt-2 text-sm text-gray-600">{{ $task->description }}</p>
                                        @endif
                                        <div class="mt-3 text-sm text-gray-500">
                                            Criado: {{ $task->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@once
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            const initSortable = () => {
                document.querySelectorAll('.sortable-column').forEach(column => {
                    new Sortable(column, {
                        group: 'tasks',
                        animation: 150,
                        swapClass: 'highlight',
                        ghostClass: 'opacity-50',
                        onEnd: (evt) => {
                            const taskId = evt.item.dataset.taskId;
                            const newStatus = evt.to.closest('[data-status]').dataset
                            .status;
                            const newIndex = evt.newIndex;

                            Livewire.dispatch('taskMoved', [taskId, newStatus, newIndex]);
                        }
                    });
                });
            };

            initSortable();
            Livewire.hook('commit', ({
                component
            }) => {
                if (component.name === 'task-board') {
                    setTimeout(initSortable, 10);
                }
            });
        });
    </script>
@endonce
