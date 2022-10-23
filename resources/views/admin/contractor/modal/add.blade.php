<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="name">Имя</label>
			<input type="text" class="form-control" id="name" name="name" placeholder="Имя">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="name">Фамилия</label>
			<input type="text" class="form-control" id="lastname" name="lastname" placeholder="Фамилия">
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="email">E-mail</label>
			<input type="email" class="form-control" id="email" name="email" placeholder="E-mail">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="phone">Телефон</label>
			<input type="tel" class="form-control new-phone" id="phone" name="phone" placeholder="+7__________">
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
				<option value="{{ $city->id }}">{{ $city->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col">
		<label for="birthdate">Дата рождения</label>
		<input type="date" class="form-control" id="birthdate" name="birthdate" placeholder="Дата рождения">
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="is_active">Активность</label>
			<select class="form-control" id="is_active" name="is_active">
				<option value="1" selected>Да</option>
				<option value="0">Нет</option>
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="is_subscribed">Подписан на рассылку</label>
			<select class="form-control" id="is_subscribed" name="is_subscribed">
				<option value="1" selected>Да</option>
				<option value="0">Нет</option>
			</select>
		</div>
	</div>
</div>
