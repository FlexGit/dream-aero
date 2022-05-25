<h3>{{ $location->name }}</h3>
@if (array_key_exists('scheme_file_path', $location->data_json) && $location->data_json['scheme_file_path'])
	<img src="/upload/{{ $location->data_json['scheme_file_path'] }}" alt="">
@endif
