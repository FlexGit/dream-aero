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
										В преддверии Вашего Дня Рождения мы рады напомнить, что у Вас есть возможность воспользоваться скидкой {{ ($promo->discount->valueFormatted() ?? '') }} на полет в Авиатренажере.
										<br>
									</p>
									<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%" style="background:#fff;border-collapse:collapse;color:#333;font-size:16px;line-height:1.5">
										<tbody>
										<tr style="border-color:transparent">
											<td style="border-collapse:collapse;border-color:transparent">
												<table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;color:#333;font-size:16px;line-height:1.5">
													<tbody>
													<tr style="border-color:transparent">
														<td colspan="3" height="15" width="100%" style="border-collapse:collapse;border-color:transparent">&nbsp;</td>
													</tr>
													<tr style="border-color:transparent">
														<td height="100%" width="15" style="border-collapse:collapse;border-color:transparent;width:15px !important">&nbsp;</td>
														<td valign="top" width="370" style="border-collapse:collapse;border-color:transparent;vertical-align:top">
															<div>
																<table border="0" cellpadding="0" cellspacing="0" height="28" style="background:#888;border:0;border-collapse:collapse;border-radius:5px;color:#333;font-size:16px;line-height:1.5;margin-left:auto;margin-right:auto">
																	<tbody>
																	<tr style="border-color:transparent">
																		<td width="21" style="border:0 transparent;border-collapse:collapse;padding:0;width:21px">&nbsp;&nbsp;</td>
																		<td align="center" height="28" valign="middle" style="border:0 transparent;border-collapse:collapse;border-radius:5px;height:28px;padding:0;text-align:center;vertical-align:middle;width:auto">
																			<table border="0" cellpadding="0" cellspacing="0" width="100%" style="border:0;border-collapse:collapse;color:#333;font-size:16px;line-height:1.5">
																				<tbody>
																				<tr style="border-color:transparent">
																					<td align="center" style="border:0 transparent;border-collapse:collapse;line-height:1;padding:0">
																						<a href="https://dream-aero.ru/{{ $city ? $city->alias : 'msk' }}/price?utm_source=dreamaero&utm_medium=email&utm_campaign=birthday_promo" style="color:#ffffff;font:bold 16px 'arial' , 'helvetica neue' , 'helvetica' , sans-serif;text-decoration:none" target="_blank" rel="noopener noreferrer">Подробнее</a>
																					</td>
																				</tr>
																				</tbody>
																			</table>
																		</td>
																		<td width="21" style="border:0 transparent;border-collapse:collapse;padding:0;width:21px">&nbsp;&nbsp;</td>
																	</tr>
																	</tbody>
																</table>
															</div>
														</td>
														<td height="100%" width="15" style="border-collapse:collapse;border-color:transparent;width:15px !important">&nbsp;</td>
													</tr>
													<tr style="border-color:transparent">
														<td colspan="3" height="15" width="100%" style="border-collapse:collapse;border-color:transparent">&nbsp;</td>
													</tr>
													</tbody>
												</table>
											</td>
										</tr>
										</tbody>
									</table>
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




