@extends("deflayout")

@section("toolbar")
<a href='{{ action('DocumentController@index') }}' class="btn btn-default">Zurück zur Liste</a>
<input type="button" onclick="document.forms.updatefrm.submit()" class="btn btn-primary" value="Änderungen speichern">
<span class="pull-right">
<a href="{{ action('DocumentController@edit', [$doc->id]) }}" title="Dokument bearbeiten" class="btn btn-default">&nbsp;<span class="glyphicon glyphicon-edit"></span>&nbsp;</a>

<a href="javascript://" title="Dokument löschen" onclick="deleteDoc('{{ action('DocumentController@destroy', [$doc->id]) }}')" class="btn btn-danger">&nbsp;<span class="glyphicon glyphicon-trash"></span>&nbsp;</a>

</span>
@endsection

@section("main")
<form action="{{ action('DocumentController@update', [$doc->id]) }}" method="post" name=updatefrm>
		{{ csrf_field() }}
<h2><input type="text" name="title" value="{{ $doc->title }}" style="font-size: 14pt; width:100%"></h2>
<div class="row"><div class="col-md-3 col-md-push-9">
<p><b><input type="date" name="doc_date" value="{{ ($doc->doc_date == null) ? "" : $doc->doc_date->toDateString() }}" style="width:100%;"></b></p>
	<p><input type="text" name="doc_mandant" value="{{ $doc->doc_mandant }}" size=10 placeholder=Mandant style="width:100%;"></p>
 <p><span class="glyphicon glyphicon-download-alt"></span> <a target=_blank href='{{ action('DocumentController@viewFile',[$doc->id,$doc->import_filename]) }}'>{{$doc->import_filename}}</a>
	({{ readable_size($doc->file_size)}})</p>
<p><span class="glyphicon glyphicon-file"></span> @if ($doc->page_count == 1)
eine Seite
@else
{{$doc->page_count}} Seiten
@endif
	</p>
<p title="Hinzugefügt {{$doc->created_at}}">	<span class="glyphicon glyphicon-asterisk"></span> {{ $doc->created_at->toDateString() }}</p>	

<p><input type="text" placeholder="Tags" name="tags" value="{{ trim($doc->tags) }}" style=" width:100%"></p>
<textarea name="description" style="width: 100%; height: 190px;">{{ $doc->description }}</textarea>
  <input type="submit">

<p class="hoverThumbs">
@for($i=1; $i<= $doc->page_count; $i++)
<img src="{{ action('DocumentController@thumbnail', [$doc->id, $i]) }}" width=80 height=80 alt="{{$i}}">
@endfor
</p>

	</div>
<div class="col-md-9 col-md-pull-3">
<img src="{{ action('DocumentController@preview', [$doc->id, 1]) }}" id="mainImage" style="max-width: 100%">
</div>
</div>
</form>
<form action="" method="post" name="deletefrm">
	{{ csrf_field() }}
	<input type="hidden" name="_method" value="DELETE">
</form>
<style>
.hoverThumbs img { border: 1px solid white; }
.hoverThumbs img.current { border-color: #f00; }
</style>
<script>
iniDocumentShow()
$(".hoverThumbs img").mouseover(function(e) {
  $(".hoverThumbs .current").removeClass("current");
  $(this).addClass("current");
  $('#mainImage').attr('src',location.href+'/preview/'+$(this).attr('alt'));
});
</script>
@endsection
