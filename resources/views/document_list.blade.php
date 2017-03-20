@extends("deflayout")

@section("toolbar")
<form action="{{ action('DocumentController@index') }}" method="get" class="form-inline" style="display:inline">
	
	<input type="text" placeholder="Suche" name="q" value="{{ Input::get("q") }}" class="form-control" size=60>
	<a href="{{action('DocumentController@importEditor')}}" class="pull-right btn btn-default">Import</a>
	<a href="{{action('DocumentController@updateTags')}}" class="pull-right btn btn-default" style="margin-right:.5rem;">Tags aktualisieren</a>
</form>
@endsection


@section("main")
<div class="row">
	<div class="col-md-3">
<h2>Neu</h2>
<form action="{{ action('DocumentController@store') }}" method="post" enctype="multipart/form-data">
	{{ csrf_field() }}
	<div class=form-group>
		<input type="date" name="doc_date" value="{{ date("Y-m-d") }}" class="form-control">
	</div>
	<div class=form-group>
		<input type="text" name="title" placeholder="Dokument-Titel" class="form-control">
	</div>
	<div class=form-group>
		<input type="file" name="document">
	</div>
	
		<input type="submit" value="OK" class="btn btn-primary">
</form>
	
	<h2>Tags</h2>
    <div id="taglist" class=list-group></div>

  <h2>Mandanten</h2>
  <div class="list-group">
    @foreach($mandanten as $m)
    <a class="list-group-item" href="?q=m:{{$m->doc_mandant}}">{{$m->doc_mandant}} <span class="badge">{{$m->cc}}</span></a>
    @endforeach
  </div>
</div>
<div class="col-md-9">
<h2>Dokumente</h2>

@foreach($docs as $doc)
<div class="document">
<a href="{{ action('DocumentController@show', [$doc->id]) }}">
	<img src="{{ action('DocumentController@thumbnail', [$doc->id, 1]) }}" width=150 height=150>
</a>
	<a href="?q=m:{{$doc->doc_mandant}}" class="pull-right btn btn-xs btn-default">{{$doc->doc_mandant}}</a>
<small>{{ $doc->displayDate() }} </small>	<br>
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
<div class='docdesc'>{{ substr($doc->description,0,200)  }}</div>
</small>
<br style="clear:left">
</div>
@endforeach
</div>
</div>
<style>
	.document {padding: 6px;background: #e2f5f4; margin: 4px 0;}
  .document img { float: left; margin-right: 10px; }
.docdesc { color: #666; font-style: italic; margin-top: 5px; }
</style>
<script>
var $list = $("#taglist");
if (window.innerWidth > 800) {
  loadtags();
} else {
  $list.append("<a href='#' class=list-group-item>Laden</a>");
  $list.find("a").click(loadtags);
}
function loadtags(){
  $list.html("");
  $.get("/tags", function(r) {
    r.tags.forEach(function(d) {
      $list.append("<a href='?q=tag:"+d.tag+"' class=list-group-item><span class='badge'>"+d.cc+"</span> " + d.tag + "</a></li>");
    });
  });
}
</script>
@endsection
