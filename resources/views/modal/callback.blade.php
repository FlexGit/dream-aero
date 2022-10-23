<form id="popup-callback" class="popup popup-call-back ajax_form">
	<h2>@lang('main.modal-callback.заказать-обратный-звонок')</h2>
	<span>@lang('main.modal-callback.заполните-пару-полей')</span>

	<input type="text" id="name" name="name" placeholder="@lang('main.modal-callback.ваше-имя')" class="popup-input" required>
	<input type="tel" id="phone" name="phone" placeholder="@lang('main.modal-callback.номер-телефона')" class="popup-input new-phone" required>

	<div class="consent-container">
		<label class="cont">
			@lang('main.modal-callback.я-согласен-на-обработку-моих-данных')
			<input type="checkbox" name="consent" value="1">
			<span class="checkmark" style="padding-bottom: 0;"></span>
		</label>
	</div>

	<div style="margin-top: 10px;">
		<div class="alert alert-success hidden" role="alert">
			@lang('main.modal-callback.запрос-успешно-отправлен')
		</div>
		<div class="alert alert-danger hidden" role="alert"></div>
	</div>

	<button type="button" class="popup-submit button-pipaluk button-pipaluk-grey js-callback-btn" disabled><i>@lang('main.common.отправить')</i></button>
</form>
