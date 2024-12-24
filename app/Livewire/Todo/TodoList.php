<?php

namespace App\Livewire\Todo;

use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

// #[Title('ToDoList')]
#[Layout('layouts.app')]
class TodoList extends Component
{
    use WithPagination;

    #[Rule('required|min:3|max:200')]
    public $task;

    //search by task name
    public $querySearch = null;

    public $editingTodoId;

    #[Rule('required|min:3|max:200')]
    public string $editingTodoTask;

    //store new todo 
    public function handleStoreTodo () {
        $validated = $this->validateOnly('task');

        Todo::create([
            'task' => $validated['task'],
            'completed' => false,
            'favorite' => false,
            'user_id' => Auth::user()->id,
        ]);

        $this->reset('task');

        session()->flash('created', 'Created.');

        $this->resetPage();
    }

    //delete todo 
    public function delete ($todoId) {
        try {
            Todo::findOrfail($todoId)->delete();
        } catch (\Throwable $th) {
            session()->flash('error', 'failed to delete the todo!');
            return;
        }
    }

    //switch between completed and not completed yet
    public function toggleCompletedTask ($todoId) {
        $todo = Todo::findOrfail($todoId);
        $todo->completed = ! $todo->completed;
        $todo->save();
    }

    //prepare for updating process
    public function edit ($todoId) {
        $todo = Todo::findOrfail($todoId);
        $this->editingTodoId = $todoId;
        $this->editingTodoTask = $todo->task;
    }

    //update todo
    public function update () {
        $validated = $this->validateOnly('editingTodoTask');

        Todo::findOrfail($this->editingTodoId)->update([
            "task" => $validated['editingTodoTask']
        ]);

        $this->cancelEdit();

        session()->flash('updated', 'Updated.');
    }

    //cancel the update process
    public function cancelEdit () {
        $this->reset('editingTodoId', 'editingTodoTask');
    }

    //switch between favorite and unfavorite
    public function toggleFavoriteTask ($todoId) {
        $todo = Todo::findOrfail($todoId);
        $todo->favorite = ! $todo->favorite;
        $todo->save();
    }

    public function render()
    {
        //handle search task name
        if(isset($this->querySearch)){
            return view('livewire.todo.todo-list', [
                'todos' =>Todo::where('task', 'like', "%$this->querySearch%")
                    ->where('user_id', Auth::user()->id)->paginate(4)
            ]);
        }

        return view('livewire.todo.todo-list', [
            'todos' => Auth::user()->todos->paginate(4)
        ]);
    }
}
