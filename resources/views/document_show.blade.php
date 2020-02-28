@extends("deflayout")

@section("head_title")
#{{$doc->id}}
{{$doc->title}}

@endsection

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
<p><textarea name="description" style="width: 100%; height: 190px;" class="form-control">{{ $doc->description }}</textarea></p>

@if ($editable)
<p><input type="submit" value="Änderungen speichern" class="btn btn-primary" /></p>
@endif

<hr />

@if ($editable)
<p><input type="text" readonly value="{{ action('DocumentController@showShareLink', [$doc->id, $doc->getToken()]) }}" class="form-control" style="width:100%" onmouseenter="this.select()"></p>
@endif

<p class="hoverThumbs">
@for($i=1; $i<= $doc->page_count; $i++)
<img src="{{ action('DocumentController@thumbnail', [$doc->id, $doc->getToken(), $i]) }}" width=80 height=80 alt="{{$i}}" data-page-no="{{$i}}">
@endfor
</p>

@if ($editable)
<p><input type="button" id="updatePreview" value="Vorschau aktualisieren" class="btn btn-default" /></p>
@endif
	</div>
<div class="col-md-9 col-md-pull-3">
@if ($doc->page_count == 0)
<div class="alert alert-info">
<h4>Dokument wird im Hintergrund bearbeitet</h4>
<progress id="processingProgress" style="width:100%"></progress>
<pre id="processingLog">Eile mit Weile</pre>
<script>
function getLog(){
    $.get("{{ action('ImportController@fetchLog', [$doc->id, $doc->getToken() ]) }}", function(res) {
        $("#processingLog").text(res.log.log+"\n"+res.log.stdout);
        var p = res.log.progress.split(/\//);
        $("#processingProgress").attr({'max':p[1],'value':p[0]});
        setTimeout(getLog, 1500);
    },"json");
}
getLog();
</script>
</div>
@endif

<div class="toolbar">
<button type="button" class="pull-left page-nav" data-dir="-1"><span class="glyphicon glyphicon-arrow-left"></button>
<button type="button" class="pull-right page-nav" data-dir="1"><span class="glyphicon glyphicon-arrow-right"></button>
<center>Seite <span id='selected_page'>1</span> von {{ $doc->page_count }}</center>
</div>

<img src="{{ action('DocumentController@preview', [$doc->id, $doc->getToken(), 1]) }}" id="mainImage" style="max-width: 100%">
</div>
</div>
@if ($editable)
</form>
@else
<script> $("input,textarea").attr("readonly","true"); $("a[href]").attr("href",null); </script>
@endif
<style>
.hoverThumbs img { border: 1px solid white; }
.hoverThumbs img.current { border-color: #f00; }
.toolbar { color:white;z-index:1;background:rgba(3,3,3,0.8);opacity:0.1;height:2.6em; box-sizing:border-box; }
#mainImage{margin-top:-2.6em}
.toolbar button { background: black; border: 0; padding: 0.6em 2.6em;  }
.toolbar center { padding: 0.6em; }
.toolbar:hover{opacity:0.9;}
.tagify.form-control {height:auto}
</style>
<script>
var secure_token = '{{ csrf_token() }}';
var selected_page = 1;
iniDocumentShow()
$(".hoverThumbs img").mouseover(function() {gotoPage(this)});
function gotoPage(pag) {
  if (typeof pag=="number") pag = document.querySelector("img[data-page-no=\""+pag+"\"]");
  $(".hoverThumbs .current").removeClass("current");
  $(pag).addClass("current");
  $('#mainImage').attr('src',pag.src.replace(/thumbnail/, 'preview'));
  selected_page = pag.getAttribute("data-page-no");
  $("#selected_page").text(selected_page);
  history.replaceState('','',"#p="+selected_page);
}
$("button.page-nav").click(function() {
  gotoPage(+selected_page + +this.getAttribute("data-dir"));
});
$("#updatePreview").click(function(){
    $(this).attr("disabled",true);
    $.post("{{ action('DocumentController@updatePreview', [$doc->id]) }}", {_token:secure_token}, function(r) {
        console.log(r);
        $("#updatePreview").val("Gestartet");
        setTimeout(function(){
            location=location;
        },200);
    }, "json");
});
if(location.hash.startsWith("#p=")) {
    gotoPage(parseInt(location.hash.substr(3)));
}

var tagInput = document.querySelector("input[name=tags]"),
    tagTagify = new Tagify(tagInput, {
        delimiters: ' ',
        keepInvalidTags: true,
        outputDelimiter: " ",
    }).on('input', onInput);


// on character(s) added/removed (user is typing/deleting)
function onInput(e){
    console.log("onInput: ", e.detail);
    tagTagify.settings.whitelist.length = 0; // reset current whitelist
    tagTagify.loading(true).dropdown.hide.call(tagTagify) // show the loader animation

    // get new whitelist from a delayed mocked request (Promise)
    fetch('/tags').then(response => response.json())
    .then(function(result){
            // https://stackoverflow.com/q/30640771/104380
            // replace tagify "whitelist" array values with new values (result)
            tagTagify.settings.whitelist.splice(0, result.tags.length, ...result.tags)
            // render the suggestions dropdown. "newValue" is when "input" event is called while editing a tag
            tagTagify.loading(false).dropdown.show.call(tagTagify, e.detail.value);
        })
}

</script>
@endsection
