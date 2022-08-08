<rss xmlns:yandex="http://news.yandex.ru" xmlns:media="http://search.yahoo.com/mrss/" xmlns:turbo="http://turbo.yandex.ru" version="2.0">
	<channel>
		<title>{{ $page->meta_title }}</title>
		<link>{{ url('news') }}/</link>
		<description>{{ $page->meta_description }}</description>
		<language>ru</language>
		@foreach($items as $item)
			<item turbo="true">
				<link>{{ url('news', $item->alias) }}</link>
				<pubDate>{{ $item->created_at->format('D, d M Y H:i:s O') }}</pubDate>
				<turbo:content>
					<![CDATA[
					<header>
						<h1>{{ $item->title }}</h1>
						@if(isset($item->data_json['photo_preview_file_path']) && $item->data_json['photo_preview_file_path'])
						<figure>
							<img src="{{ url('upload/' . $item->data_json['photo_preview_file_path']) }}">
						</figure>
						@endif
					</header>
					{!! $item->detail_text !!}
					]]>
				</turbo:content>
			</item>
		@endforeach
	</channel>
</rss>