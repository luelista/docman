@extends("deflayout")

@section("toolbar")
<a href='{{ action('DocumentController@index') }}' class="btn btn-default">Zurück zur Liste</a>
<input type="button" onclick="document.forms.updatefrm.submit()" class="btn btn-primary" value="Änderungen speichern">
<a href="javascript://" title="Dokument löschen" onclick="document.forms.deletefrm.submit()" class="btn btn-danger pull-right">&nbsp;<span class="glyphicon glyphicon-trash"></span>&nbsp;</a>
@endsection

@section("main")
<form action="{{ action('DocumentController@update', [$doc->id]) }}" method="post" name=updatefrm>
		{{ csrf_field() }}
<h2><input type="text" name="title" value="{{ $doc->title }}" style="font-size: 14pt; width:80%"></h2>
<p><b><input type="date" name="doc_date" value="{{ $doc->doc_date->toDateString() }}"></b>
	| <a href='{{ action('DocumentController@viewFile',[$doc->id,$doc->import_filename]) }}'>{{$doc->import_filename}}</a> |
@if ($doc->page_count == 1)
eine Seite
@else
{{$doc->page_count}} Seiten
@endif
	|
	Hinzugefügt {{ $doc->created_at->toDateString() }}
</p>
<p><input type="text" placeholder="Tags" name="tags" value="{{ trim($doc->tags) }}" style=" width:80%"></p>
<textarea name="description" style="width: 80%; height: 70px;">{{ $doc->description }}</textarea>
</form>

<img src="{{ action('DocumentController@preview', [$doc->id]) }}" style="max-width: 90%">

<form action="{{ action('DocumentController@destroy', [$doc->id]) }}" method="post" name="deletefrm">
	{{ csrf_field() }}
	<input type="hidden" name="_method" value="DELETE">
</form>
<script>
	window.onkeydown=function(e) {
		if (e.which==13 && e.ctrlKey) {
			
			document.forms.updatefrm.submit();
		}
		if (e.which==27) {
			location='{{ action('DocumentController@index') }}';
		}
	}
</script>
@endsection