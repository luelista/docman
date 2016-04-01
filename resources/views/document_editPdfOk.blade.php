@extends("deflayout")

@section("toolbar")
@endsection

@section("main")

<br>

<table class="table table-bordered">
<tr><th>ID</th><th>Title</th><th>Filename</th></tr>
@foreach($newDocs as $doc)
<tr><td><a href="{{ action('DocumentController@show', [$doc->id]) }}">{{ $doc->id}}</a></td><td><a href="{{ action('DocumentController@show', [$doc->id]) }}">{{ $doc->title }}</a></td><td> {{ $doc->import_filename }}</td></tr>
<tr><td></td><td colspan=2>
@for($i=1; $i<= $doc->page_count; $i++)
<div class="thumbnail pull-left">
<img src="{{ action('DocumentController@thumbnail', [$doc->id, $i]) }}" width=150 height=150 alt="{{$i}}">
</div>
@endfor
</td></tr>

@endforeach
</table>

@endsection

