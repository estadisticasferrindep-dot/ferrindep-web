@if ($type == 'list-group')
    <div class="list-group">
        @foreach ($categories as $cat)
            <div class="list-group-item">
                {{ $cat->name }}
                <div class="btn-group">
                    <a href="{{ route('categories.edit', $cat) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                    <a href="{{ route('categories.destroy', $cat) }}" onclick="return confirm('¿Estas seguro?');" class="btn btn-danger"><i class="fas fa-trash"></i></a>
                </div>
            </div>
            @if ($cat->categories()->count())
                <div class="list-group-item">
                    @include('categories.categories-list', ['categories' => $cat->categories, 'type' => 'list-group'])
                </div>
            @endif
        @endforeach
    </div>
@endif
@if ($type == 'select')
        @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" {{ $cat->id == old('parent_id', isset($category) ? $category->parent_id : null ) ? 'selected' : '' }}>{{ $cat->name }}</option>
            @if ($cat->categories()->count())
                <optgroup label="{{ $cat->name }}">
                    @include('categories.categories-list', ['categories' => $cat->categories, 'type' => 'select'])
                </optgroup>
            @endif
        @endforeach
@endif