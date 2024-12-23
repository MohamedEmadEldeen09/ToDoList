<div id="content" class="mx-auto" style="max-width:500px;">
    @include('livewire.create-todo')

    @include('livewire.search-box')

    <div id="todos-list">
        @if ($todos)
            @foreach ($todos as $todo)
                @include('livewire.todo-card')
                <hr/>
            @endforeach
        @endif
        
        <div class="my-2">
            {{ $todos->links() }}
        </div>
    </div>
</div>
