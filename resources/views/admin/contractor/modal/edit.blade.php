<input type="hidden" id="id" name="id" value="{{ $contractor->id }}">

<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="name">Имя</label>
			<input type="text" class="form-control" id="name" name="name" value="{{ $contractor->name }}" placeholder="Имя">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="name">Фамилия</label>
			<input type="text" class="form-control" id="lastname" name="lastname" value="{{ $contractor->lastname }}" placeholder="Фамилия">
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="email">E-mail</label>
			<input type="email" class="form-control" id="email" name="email" value="{{ $contractor->email }}" placeholder="E-mail">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="phone">Телефон</label>
			<input type="tel" class="form-control" id="phone" name="phone" value="{{ $contractor->phone }}" placeholder="+71234567890">
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="city_id">Город</label>
			<select class="form-control" id="city_id" name="city_id">
				<option value=""></option>
				@foreach($cities ?? [] as $city)
					<option value="{{ $city->id }}" @if($city->id == $contractor->city_id) selected @endif>{{ $city->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col">
		<label for="birthdate">Дата рождения</label>
		<input type="date" class="form-control" id="birthdate" name="birthdate" value="{{ \Carbon\Carbon::parse($contractor->birthdate)->format('Y-m-d') }}" placeholder="Дата рождения">
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="is_active">Активность</label>
			<select class="form-control" id="is_active" name="is_active">
				<option value="1" @if($contractor->is_active) selected @endif>Да</option>
				<option value="0" @if(!$contractor->is_active) selected @endif>Нет</option>
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="is_subscribed">Подписан на рассылку</label>
			<select class="form-control" id="is_subscribed" name="is_subscribed">
				<option value="1" @if($contractor->is_subscribed) selected @endif>Да</option>
				<option value="0" @if(!$contractor->is_subscribed) selected @endif>Нет</option>
			</select>
		</div>
	</div>
</div>