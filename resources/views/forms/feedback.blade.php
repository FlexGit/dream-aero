<div class="questions">
	<div class="container">
		<div class="row">
			<div class="col-md-7">
				<h2>@lang('main.feedback.у-вас-остались-вопросы')</h2>
				<span>@lang('main.feedback.напишите-менеджеру-компании')</span>
				<img src="{{ asset('img/bplane.webp') }}" alt="" width="100%" height="auto">
			</div>
			<div class="col-md-5">
				<div class="form wow fadeInRight" data-wow-duration="2s">
					<form class="ajax_form" action="#" method="post">
						<input type="text" name="name" placeholder="@lang('main.feedback.как-вас-зовут')">
						<input type="email" name="email" placeholder="@lang('main.feedback.ваш-email')" required>
						<textarea name="question" placeholder="@lang('main.feedback.введите-сообщение')" required></textarea>
						<input type="text" name="workemail" class="field"/>
						<button type="submit" class="button-pipaluk button-pipaluk-white"><i>@lang('main.feedback.отправить')</i></button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
