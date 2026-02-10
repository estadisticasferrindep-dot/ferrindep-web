<div class="form-row">
    <div class="form-group col-md-4">
        <label>Categoria Padre</label>
        <select name="parent_id" class="form-control">
            <option value="">N/A</option>
            @include('categories.categories-list', ['categories' => $categories, 'type' => 'select'])
        </select>
    </div>
    <div class="form-group col-md-4">
        <label>Orden</label>
        <input type="text" name="order" value="{{ old('order', isset($category) ? $category->order : null ) }}" class="form-control" placeholder="Order">
    </div>
    <div class="form-group col-md-8">
        <label>Nombre</label>
        <input type="text" name="name" value="{{ old('order', isset($category) ? $category->name : null ) }}" class="form-control" placeholder="Nombre">
    </div>
</div>

<div class="form-group">
    <label>Imagen</label>
    <input type="file" accept="image/*" name="image" class="form-control-file">
</div>


<div class="form-check ">
    <input class="form-check-input" type="checkbox" {{ old('show', isset($category) ? $category->show : null ) == 1 ? 'checked' : '' }} name="show" value="1">
    <label class="form-check-label">Mostrar</label>
</div>

@if (isset($category))
    <img src="{{ asset(Storage::url($category->image)) }}" style="max-height: 200px; max-width: 200px;" class="img-thumbnail">
@endif
<br>
<br>
<button type="submit" class="btn btn-primary mb-2">Enviar Clase</button>