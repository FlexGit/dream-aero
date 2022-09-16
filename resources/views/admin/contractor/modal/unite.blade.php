<input type="hidden" id="id" name="id" value="{{ $contractor->id }}">
<input type="hidden" id="contractor_id" name="contractor_id">

<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="contractor_search">Поиск объединяющего контрагента</label>
			<input type="text" class="form-control" id="contractor_search" name="contractor_search" placeholder="Поиск по ФИО, E-mail, телефону">
			<div class="js-contractor-container hidden">
				<span class="js-contractor"></span> <i class="fas fa-times js-contractor-delete" title="Удалить" style="cursor: pointer;color: red;"></i>
			</div>
		</div>
	</div>
</div>
