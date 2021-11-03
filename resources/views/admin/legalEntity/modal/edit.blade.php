<input type="hidden" id="id" name="id" value="{{ $legalEntity->id }}">
<div class="form-group">
	<label for="name">Наименование</label>
	<input type="text" class="form-control" id="name" name="name" value="{{ $legalEntity->name }}" placeholder="Наименование">
</div>
<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control" id="is_active" name="is_active">
		<option value="1" @if($legalEntity->is_active) selected @endif>Да</option>
		<option value="0" @if(!$legalEntity->is_active) selected @endif>Нет</option>
	</select>
</div>
