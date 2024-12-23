<?php

namespace App\Livewire;

use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('ToDoList')]
#[Layout('layouts.app')]
class TodoList extends Component
{
    use WithPagination;

    #[Rule('required|min:3|max:200')]
    public $task;
    public $querySearch = null;

    public $editingTodoId;
    public string $editingTodoTask;

    public function handleStoreTodo () {
        $validated = $this->validateOnly('task');

        Todo::create([
            'task' => $validated['task'],
            'completed' => false,
            'favorite' => false,
            'user_id' => Auth::user()->id,
        ]);

        $this->reset('task');

        session()->flash('success', 'Created.');
    }

    public function delete ($todoId) {
        Todo::findOrfail($todoId)->delete();
    }

    public function toggleOpenDoneTask ($todoId) {
        $todo = Todo::findOrfail($todoId);
        $todo->completed = ! $todo->completed;
        $todo->save();
    }

    public function edit ($todoId) {
        $todo = Todo::findOrfail($todoId);
        $this->editingTodoId = $todoId;
        $this->editingTodoTask= $todo->task;
    }

    public function render()
    {
        if(isset($this->querySearch)){
            return view('livewire.todo-list', [
                'todos' =>Todo::where('task', 'like', "%$this->querySearch%")
                    ->where('user_id', Auth::user()->id)->paginate(4)
            ]);
        }

        return view('livewire.todo-list', [
            'todos' => Auth::user()->todos->paginate(4)
        ]);
    }
}
