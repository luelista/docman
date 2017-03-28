@extends("deflayout")

@section("toolbar")
<ul class="nav navbar-nav">
@if ($doc->doc_mandant)
<li><a href='{{ action('DocumentController@index') }}?q=m:{{ $doc->doc_mandant }}'>&#187; {{ $doc->doc_mandant }}</a></li>
@endif
@if ($doc->doc_date)
<li><a href='{{ action('DocumentController@index') }}?q=m:{{ $doc->doc_mandant }} {{ $doc->doc_date->year }}'>&#187; {{ $doc->doc_date->year }}</a></li>
@endif
</ul>
@if ($editable)
<form class="nav navbar-form navbar-right">
<input type="button" onclick="document.forms.updatefrm.submit()" class="btn btn-primary" value="Änderungen speichern">
<a href="{{ action('DocumentController@edit', [$doc->id]) }}" title="Dokument bearbeiten" class="btn btn-default">&nbsp;<span class="glyphicon glyphicon-edit"></span>&nbsp;</a>
<a href="javascript://" title="Dokument löschen" onclick="deleteDoc({{ $doc->id }}, '{{ action('DocumentController@destroy', [$doc->id]) }}')" class="btn btn-danger">&nbsp;<span class="glyphicon glyphicon-trash"></span>&nbsp;</a>
</form>
@endif
@endsection

@section("main")
@if ($editable)
<form action="{{ action('DocumentController@update', [$doc->id]) }}" method="post" name=updatefrm>
    {{ csrf_field() }}
@endif

<h2><input type="text" name="title" value="{{ $doc->title }}" class="form-control" style="font-size: 14pt; width:100%"></h2>
<div class="row"><div class="col-md-3 col-md-push-9">
<p><b><input type="date" name="doc_date" value="{{ ($doc->doc_date == null) ? "" : $doc->doc_date->toDateString() }}" class="form-control"></b></p>

<p><input type="text" name="doc_mandant" value="{{ $doc->doc_mandant }}" class="form-control" placeholder=Mandant size=10></p>

<p style=" word-wrap: break-word; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; ">
  <span class="glyphicon glyphicon-download-alt"></span>
  <a href='{{ action('DocumentController@viewFile',[$doc->id,$doc->import_filename]) }}'>
  {{$doc->import_filename}}</a>
</p>

<p><span class="glyphicon glyphicon-file"></span> @if ($doc->page_count == 1)
eine Seite
@else
{{$doc->page_count}} Seiten
@endif
  ({{ readable_size($doc->file_size)}})
</p>

<p title="Hinzugefügt {{$doc->created_at}}">	<span class="glyphicon glyphicon-asterisk"></span> {{ $doc->created_at->toDateString() }}</p>

<p><input type="text" placeholder="Tags" name="tags" value="{{ trim($doc->tags) }}" style=" width:100%" class="form-control"></p>
<textarea name="description" style="width: 100%; height: 190px;" class="form-control">{{ $doc->description }}</textarea>
@if ($editable)
  <input type="submit">
<input type="text" readonly value="{{ action('DocumentController@showShareLink', [$doc->id, $doc->getToken()]) }}" class="form-control" style="width:100%" onmouseenter="this.select()">
@endif

<p class="hoverThumbs">
@for($i=1; $i<= $doc->page_count; $i++)
<img src="{{ action('DocumentController@thumbnail', [$doc->id, $doc->getToken(), $i]) }}" width=80 height=80 alt="{{$i}}">
@endfor
</p>

	</div>
<div class="col-md-9 col-md-pull-3">
<img src="{{ action('DocumentController@preview', [$doc->id, $doc->getToken(), 1]) }}" id="mainImage" style="max-width: 100%">
</div>
</div>
@if ($editable)
</form>
<form action="" method="post" name="deletefrm">
	{{ csrf_field() }}
	<input type="hidden" name="_method" value="DELETE">
</form>
@else
<script> $("input,textarea").attr("readonly","true"); $("a[href]").attr("href",null); </script>
@endif
<style>
.hoverThumbs img { border: 1px solid white; }
.hoverThumbs img.current { border-color: #f00; }
</style>
<script>
iniDocumentShow()
$(".hoverThumbs img").mouseover(function(e) {
  $(".hoverThumbs .current").removeClass("current");
  $(this).addClass("current");
  $('#mainImage').attr('src',this.src.replace(/thumbnail/, 'preview'));
});
</script>
@endsection
