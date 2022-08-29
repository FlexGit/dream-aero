@foreach($promocodes as $promocode)
<tr class="odd">
	<td>
		<a href="javascript:void(0)" data-toggle="modal" data-url="/promocode/{{ $promocode->id }}/show" data-title="Просмотр" title="Посмотреть">{{ $promocode->number }}</a>
	</td>
	<td>
		@foreach($promocode->cities ?? [] as $city)
			<div>{{ $city->name }}</div>
		@endforeach
	</td>
	<td class="text-center">{{ $promocode->location ? $promocode->location->name : '-' }}</td>
	{{--<td class="text-center">{{ $promocode->contractor ? $promocode->contractor->fio() : '-' }}</td>--}}
	<td class="text-center">{{ $promocode->discount ? $promocode->discount->valueFormatted() : '-' }}</td>
	<td class="text-center">{{ $promocode->is_active ? 'Да' : 'Нет' }}</td>
	<td class="text-center">{{ $promocode->active_from_at }}</td>
	<td class="text-center">{{ $promocode->active_to_at }}</td>
	<td class="text-center">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/promocode/{{ $promocode->id }}/edit" data-action="/promocode/{{ $promocode->id }}" data-method="PUT" data-title="Редактирование">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-target="#modal" data-url="/promocode/{{ $promocode->id }}/delete" data-action="/promocode/{{ $promocode->id }}" data-method="DELETE" data-title="Удаление">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>
	</td>
</tr>
@endforeach