@extends("deflayout")

@section("toolbar")
<form action="{{ action('DocumentController@index') }}" method="get" class="form-inline" style="display:inline">
	
	<input type="text" placeholder="Suche" name="q" value="{{ Input::get("q") }}" class="form-control">
</form>
@endsection


@section("main")
<h2>Neu</h2>
<form action="{{ action('DocumentController@store') }}" method="post" enctype="multipart/form-data" class="form-inline">
	{{ csrf_field() }}
	<input type="date" name="doc_date" value="{{ date("Y-m-d") }}" class="form-control">
	<input type="text" name="title" placeholder="Dokument-Titel" class="form-control">
	<input type="file" name="document" style="display:inline">
	<input type="submit" value="OK" class="btn btn-primary">
</form>

<h2>Dokumente</h2>

@foreach($docs as $doc)
<a href="{{ action('DocumentController@show', [$doc->id]) }}">
	<img src="{{ action('DocumentController@thumbnail', [$doc->id]) }}" width=150 height=150 style=" float: left;">
</a>
<small>{{ $doc->doc_date }} </small>	<br>
<a href="{{ action('DocumentController@show', [$doc->id]) }}">

	{{ $doc->title }}</a>
<br>
@if ($doc->page_count>1)
<small> {{ $doc->page_count }} Seiten</small><br>
@endif
<small>
@if ($doc->tags)
@foreach($doc->getTags() as $tag)
<a href="?q=tag:{{$tag}}" class="btn btn-xs btn-default">{{$tag}}</a>
@endforeach
@endif
</small>

<br style="clear:left">
@endforeach

@endsection