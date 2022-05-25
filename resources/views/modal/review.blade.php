<form id="popup-review" class="popup popup-review ajax_form">
	<p class="popup-title">
		@lang('main.modal-review.остались-под-впечатлением')
	</p>
	<p class="popup-description">
		@lang('main.modal-review.оставьте-свой-отзыв')
	</p>
	<fieldset>
		<input type="text" id="name" name="name" class="popup-input" placeholder="@lang('main.modal-review.ваше-имя')" required>
		<textarea id="body" name="body" class="popup-area" placeholder="@lang('main.modal-review.текст-отзыва')"></textarea>

		<div class="consent-container">
			<label class="cont">
				@lang('main.modal-callback.я-согласен-на-обработку-моих-данных')
				<input type="checkbox" name="consent" value="1">
				<span class="checkmark" style="padding-bottom: 0;"></span>
			</label>
		</div>

		<div style="margin-top: 10px;">
			<div class="alert alert-success hidden" role="alert">
				@lang('main.modal-review.отзыв-успешно-отправлен')
			</div>
			<div class="alert alert-danger hidden" role="alert"></div>
		</div>

		<button type="button" class="popup-submit button-pipaluk button-pipaluk-grey js-review-btn" disabled><i>@lang('main.common.отправить')</i></button>
	</fieldset>
</form>
