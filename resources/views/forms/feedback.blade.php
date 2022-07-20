<div class="feedback">
	<div class="container">
		<div class="row">
			<div class="col-md-2"></div>
			<div class="col-md-8">
				<div class="form wow fadeInRight" data-wow-duration="2s">
					<form class="ajax_form" action="#" method="post">
						<input type="text" id="name" name="name" placeholder="@lang('main.feedback.как-вас-зовут') *">
						<input type="text" id="parent_name" name="parent_name" placeholder="@lang('main.feedback.имя-родителя')">
						<input type="text" id="age" name="age" placeholder="@lang('main.feedback.возраст-участника') *">
						<input type="tel" id="phone" name="phone" placeholder="@lang('main.feedback.ваш-телефон') *">
						<input type="email" id="email" name="email" placeholder="@lang('main.feedback.ваш-email') *">
						{{--<textarea id="body" name="body" placeholder="@lang('main.feedback.введите-сообщение')"></textarea>--}}

						<div class="consent-container" style="text-align: center;color: #fff;">
							<label class="cont" style="padding-top: 0;font-size: 14px;">
								@lang('main.modal-callback.я-согласен-на-обработку-моих-данных')
								<input type="checkbox" name="consent" value="1">
								<span class="checkmark" style="padding-top: 0;"></span>
							</label>
						</div>

						<div>
							<div class="alert alert-success hidden" style="background-color: transparent;border-color: transparent;color: #fff;" role="alert">
								@lang('main.feedback.сообщение-успешно-отправлено')
							</div>
							<div class="alert alert-danger hidden" style="background-color: transparent;border-color: transparent;color: #fff;" role="alert"></div>
						</div>

						<button type="button" class="button-pipaluk button-pipaluk-white js-feedback-btn" disabled>
							<i>@lang('main.common.отправить')</i>
						</button>
					</form>
				</div>
			</div>
			<div class="col-md-2"></div>
		</div>
	</div>
</div>