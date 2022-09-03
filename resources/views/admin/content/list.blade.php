@foreach ($contents as $content)
	<tr class="odd" data-id="{{ $content->id }}">
		<td class="align-middle">
			{{ $content->title }}
		</td>
		<td class="text-center align-middle">
			{{ $content->city ? $content->city->name : 'Любой' }}
		</td>
		@switch($type)
			@case(app('\App\Models\Content')::PROMOBOX_TYPE)
			<td class="text-center align-middle">
				{{ $content->is_active ? 'Да' : 'Нет' }}
			</td>
			<td class="text-center align-middle">
				{{ $content->published_at ? $content->published_at->format('Y-m-d') : '' }}
			</td>
			<td class="text-center align-middle">
				{{ $content->published_end_at ? $content->published_end_at->format('Y-m-d') : '' }}
			</td>
			@break
			@case(app('\App\Models\Content')::PAGES_TYPE)
			@break
			@default
			<td class="text-center align-middle">
				{{ $content->published_at ? $content->published_at->format('Y-m-d') : '' }}
			</td>
			<td class="text-center align-middle">
				{{ $content->is_active ? 'Да' : 'Нет' }}
			</td>
			@break
		@endswitch
		<td class="text-center align-middle">
			<a href="javascript:void(0)" data-toggle="modal" data-url="/site/{{ $version }}/{{ $type }}/{{ $content->id }}/edit" data-action="/site/{{ $version }}/{{ $type }}/{{ $content->id }}" data-method="PUT" data-type="content" data-title="Редактирование" title="Редактировать">
				<i class="fa fa-edit" aria-hidden="true"></i>
			</a>&nbsp;&nbsp;&nbsp;
			<a href="javascript:void(0)" data-toggle="modal" data-url="/site/{{ $version }}/{{ $type }}/{{ $content->id }}/delete" data-action="/site/{{ $version }}/{{ $type }}/{{ $content->id }}" data-method="DELETE" data-title="Удаление" title="Удалить">
				<i class="fa fa-trash" aria-hidden="true"></i>
			</a>
		</td>
	</tr>
@endforeach