@foreach ($notifications as $notification)
<tr class="odd">
	<td>
		<a href="javascript:void(0)" data-toggle="modal" data-url="/notification/{{ $notification->id }}/show" data-title="Просмотр" title="Посмотреть">{{ $notification->title }}</a>
	</td>
	<td class="text-center">{{ $notification->city ? $notification->city->name : 'Все' }}</td>
	<td class="text-center">{{ $notification->is_active ? 'Да' : 'Нет' }}</td>
	<td class="text-center align-middle">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/notification/{{ $notification->id }}/edit" data-action="/notification/{{ $notification->id }}" data-method="PUT" data-title="Редактирование">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-target="#modal" data-url="/notification/{{ $notification->id }}/delete" data-action="/notification/{{ $notification->id }}" data-method="DELETE" data-title="Удаление">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>
		@if($notification->is_active)
			&nbsp;&nbsp;&nbsp;
			<a href="javascript:void(0)" data-toggle="modal" data-target="#modal" data-url="/notification/{{ $notification->id }}/send" data-action="/notification/{{ $notification->id }}/send" data-method="POST" data-title="Отправка уведомления">
				<i class="fas fa-bell" aria-hidden="true"></i>
			</a>
		@endif
	</td>
</tr>
@endforeach