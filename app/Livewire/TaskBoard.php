<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class TaskBoard extends Component
{
    public $todoTasks;
    public $inProgressTasks;
    public $doneTasks;

    public $showCreateModal = false;
    public $newTask = [
        'title' => '',
        'description' => '',
        'status' => 'todo'
    ];

    protected $rules = [
        'newTask.title' => 'required|string|max:255',
        'newTask.description' => 'nullable|string',
        'newTask.status' => 'required|in:todo,in_progress,done'
    ];

    public $columns = [
        'todo' => [
            'title' => 'To Do',
            'color' => 'bg-blue-50',
            'border' => 'border-t-4 border-blue-500',
            'icon' => 'heroicon-o-clipboard-document-list'
        ],
        'in_progress' => [
            'title' => 'In Progress',
            'color' => 'bg-amber-50',
            'border' => 'border-t-4 border-amber-500',
            'icon' => 'heroicon-o-arrow-path'
        ],
        'done' => [
            'title' => 'Done',
            'color' => 'bg-green-50',
            'border' => 'border-t-4 border-green-500',
            'icon' => 'heroicon-o-check-badge'
        ]
    ];

    protected $listeners = ['taskMoved' => 'handleTaskMove'];

    public function mount()
    {
        $this->loadTasks();
    }

    public function loadTasks()
    {
        $this->todoTasks = Task::where('status', 'todo')->ordered()->get();
        $this->inProgressTasks = Task::where('status', 'in_progress')->ordered()->get();
        $this->doneTasks = Task::where('status', 'done')->ordered()->get();
    }

    public function addTask()
    {
        $this->validate();

        DB::transaction(function () {
            $maxOrder = Task::where('status', $this->newTask['status'])->max('order') ?? -1;

            Task::create([
                'title' => $this->newTask['title'],
                'description' => $this->newTask['description'],
                'status' => $this->newTask['status'],
                'order' => $maxOrder + 1
            ]);

            $this->reset(['newTask', 'showCreateModal']);
            $this->loadTasks();
        });
    }

    public function closeModel(){
        $this->reset(['newTask', 'showCreateModal']);

    }



    public function deleteTask($taskId)
    {
        DB::transaction(function () use ($taskId) {
            $task = Task::findOrFail($taskId);
            $status = $task->status;
            $task->delete();

            $this->reorderTasks($status);
            $this->loadTasks();
        });
    }

    public function handleTaskMove($taskId, $newStatus, $newIndex)
    {
        DB::transaction(function () use ($taskId, $newStatus, $newIndex) {
            $task = Task::findOrFail($taskId);
            $oldStatus = $task->status;

            $task->update(['status' => $newStatus]);

            $this->reorderTasks($newStatus, $task, $newIndex);
            if ($oldStatus !== $newStatus) {
                $this->reorderTasks($oldStatus);
            }
        });

        $this->loadTasks();
    }

    private function reorderTasks($status, $movedTask = null, $newIndex = 0)
    {
        $tasks = Task::where('status', $status)
            ->orderBy('order')
            ->get()
            ->filter(fn($t) => $movedTask ? $t->id !== $movedTask->id : true);

        if ($movedTask) {
            $tasks->splice($newIndex, 0, [$movedTask]);
        }

        foreach ($tasks as $index => $task) {
            $task->update(['order' => $index]);
        }
    }

    public function render()
    {
        return view('livewire.task-board');
    }
}
