<table width="100%" bgcolor="#F2F3FC" cellpadding="0" cellspacing="0" border="0">
	<tbody>
	<tr>
		<td style="padding:40px 0;">
			<table cellpadding="0" cellspacing="0" width="100%" border="0" align="center">
				<tbody>
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" border="0" width="90%" style="max-width:600px;margin:0 auto">
							<tbody>
							<tr>
								<td width="4" height="4"><p style="margin:0;font-size:1px;line-height:1px;">&nbsp;</p></td>
								<td colspan="3" rowspan="3" bgcolor="#FFFFFF" style="padding:10px 0 30px;text-align:center">
									<a href="https://dream-aero.ru" style="display:flex;width:167px;height:auto;margin:0 auto;" target="_blank" rel=" noopener noreferrer">
										<img src="https://dream-aero.ru/assets/img/logo-new-mail.png" width="172px" alt="logo" style="display:flex;border:0;margin:0;">
									</a>
									<p style="margin:15px 30px 33px;text-align:left;font-size:16px;line-height:30px;color:#484a42;">
										<b>Здравствуйте{{ $contractor->name ? ', ' . $contractor->name : '' }}!</b>
									</p>
									<p style="margin:15px 30px 33px;text-align:left;font-size:14px;line-height:30px;color:#484a42;">
										Только до {{ \Carbon\Carbon::parse($promocode->active_to_at)->format('d.m.Y') }} у Вас есть уникальная возможность приобрести сертификат или забронировать полёт со скидкой {{ $promocode->discount->valueFormatted() ?? '' }}. Просто введите промо-код <span style="font-size: 18px;font-weight: bold;">{{ $promocode->number ?? '' }}</span> на нашем сайте или в приложении.
										<br>
									</p>
									<p style="border-top:2px solid #e5e5e5;font-size:5px;line-height:5px;margin:0 30px 29px;">&nbsp;</p>
									<table cellpadding="0" cellspacing="0" border="0" width="100%">
										<tbody>
										<tr valign="top">
											<td width="30"><p style="margin:0;font-size:1px;line-height:1px;">&nbsp;</p></td>
											<td style="text-align:left;">
												<p style="margin:0 0 4px;font-weight:bold;color:#333333;font-size:14px;line-height:22px;">Если у Вас остались вопросы,</p>
												<p style="margin:0;color:#333333;font-size:11px;line-height:18px;">наш администратор готов ответить на них.<br>Позвоните <a target="_blank" rel=" noopener noreferrer"><span class="js-phone-number">{{ $city->phone ?? '' }}</span></a> или напишите <a href="mailto:{{ $city->email ?? '' }}" target="_blank" rel=" noopener noreferrer">{{ $city->email ?? '' }}</a>
												</p>
												<p style="margin:10px 0;font-size:1px;line-height:1px;">&nbsp;</p>
												<a href="https://t.me/dreamaero/" target="_blank" rel="noopener noreferrer" style="text-decoration: none;">
													<img src="https://dream-aero.ru/assets/img/telegram.png" width="24" height="24" alt="Telegram">
												</a>
												<a href="https://vk.com/dream.aero" target="_blank" rel="noopener noreferrer" style="text-decoration: none;">
													<img src="https://dream-aero.ru/assets/img/vk.png" width="24" height="24" alt="vk">
												</a>
												<a href="https://www.instagram.com/dream.aero/" target="_blank" rel=" noopener noreferrer" style="text-decoration: none;">
													<img src="https://dream-aero.ru/assets/img/inst.png" width="24" height="24" alt="instagram">
												</a>
												<a href="https://www.youtube.com/channel/UC3huC7ltIlfkNMsz8Jt4qnw" target="_blank" rel=" noopener noreferrer" style="text-decoration: none;">
													<img src="https://dream-aero.ru/assets/img/you.png" width="24" height="24" alt="youtube">
												</a>
											</td>
											<td width="30">
												<p style="margin:0;font-size:1px;line-height:1px;">&nbsp;</p>
											</td>
										</tr>
										</tbody>
									</table>
								</td>
							</tr>
							</tbody>
						</table>
					</td>
				</tr>
				</tbody>
			</table>
		</td>
	</tr>
	</tbody>
</table>




