@extends("deflayout")



@section("main")
<h2>Tagging</h2>

<form action="javascript:" method="post" id="editform" onsubmit="submitForm()">
  {{csrf_field()}}

<b>Für alle setzen:</b><br />
<span><span class="glyphicon glyphicon-calendar" style="vertical-align: top;"></span> <input type="text" value="" id="bulk-date" class="doc-date import-field" placeholder="Datum" style="vertical-align: top;"> <a href="javascript://" onclick="setToday(this)" style="vertical-align: top;">heute</a></span>&nbsp;&nbsp;
<span class="glyphicon glyphicon-tags" style="vertical-align: top;"></span> <input type="text" value="" id="bulk-tags" placeholder="Tags" class="import-field" style="vertical-align: top;">&nbsp;&nbsp;
<span class="glyphicon glyphicon-user" style="vertical-align: top;"></span> <input type="text" value="" id="bulk-mandant" placeholder="Mandant" class="import-field" style="vertical-align: top;">&nbsp;&nbsp;
<span class="glyphicon glyphicon-pencil" style="vertical-align: top;"></span> <textarea id="bulk-desc" placeholder="Beschreibung" class="import-field"></textarea>&nbsp;&nbsp;
<span class="btn btn-primary" style="vertical-align: top;" onclick="bulkSet()">Übernehmen</span>
<hr />

@foreach($docs as $doc)
<div class="row document">
	<div class="col-md-3">
  
<small><a href="{{ action('DocumentController@show', [ $doc->id ]) }}" target="_blank">{{ $doc->displayDate() }}</a>
 &nbsp; &bull; &nbsp; {{ $doc->page_count }} Seite(n)</small><br><br>
<span class="glyphicon glyphicon-text-width"></span> <input type="text" value="{{ $doc->title }}" name="doc[{{$doc->id}}][title]" placeholder="Titel" class="import-field"><br>
<span><span class="glyphicon glyphicon-calendar"></span> <input type="text" value="{{ $doc->doc_date }}" name="doc[{{$doc->id}}][doc_date]" class="doc-date import-field field-date" placeholder="Datum"> <a href="javascript://" onclick="setToday(this)">heute</a></span><br>
<span class="glyphicon glyphicon-tags"></span> <input type="text" value="{{ trim($doc->tags) }}" name="doc[{{$doc->id}}][tags]" placeholder="Tags" class="import-field field-tags"><br>
<span class="glyphicon glyphicon-user"></span> <input type="text" value="{{ $doc->doc_mandant }}" name="doc[{{$doc->id}}][doc_mandant]" placeholder="Mandant" class="import-field field-mandant"><br>
<span class="glyphicon glyphicon-pencil" style="vertical-align: top;"></span> <textarea name="doc[{{$doc->id}}][description]" placeholder="Beschreibung" class="import-field field-desc">{{ $doc->description }}</textarea>
<br>
</small>
  
</div>
<div class="col-md-9">
<small>Quelle: {{ $doc->import_source }}</small><br><br>
<div class="preview">
<a href="{{ action('DocumentController@viewFile', [$doc->id,"view.pdf"]) }}" onclick="window.open(this.href,'foo','width=800,height=800');return false;" target="_blank">
	<img src="{{ action('DocumentController@preview', [$doc->id, 1]) }}">
</a>
  </div>
  </div>
</div>
<hr />
@endforeach
<input type="submit" class="btn btn-primary">
  </form>
<div style="padding-bottom: 2rem;"></div>
<style>
	.preview {height:220px; overflow: auto;border: 2px solid #ddd;}
	.preview img { max-width: 100%; }
  .import-field { width: 180px; }
  textarea.import-field { height: 100px; }
</style>
  <script>
  $(function() {
    var els=document.querySelectorAll(".preview");
    for (var i = 0; i < els.length; i++) {
      var el = els[i];
      el.scrollTop=100;
    }
  });
  function submitForm() {
    var submitUrl = "{{ action('ImportController@massUpdate') }}";
    $("<div style='position:fixed;background:#aaffaa;padding:20px;top:0;left:0;'>Daten werden gespeichert, Eile mit Weile</div>").prependTo("body");
    $.post(submitUrl, $("#editform").serialize(), function(ok) {
      if (ok.success=="true") {
        window.onbeforeunload=null;
        location.reload();
      }
    });
  }
  window.onbeforeunload=function() {
    return "Vorher speichern???";
  }
  window.onkeydown=function(e) {
    if (e.which==112) {
      var el =$(document.activeElement); console.log(el);
      el = el.closest(".document").next();
      goEl(el);
      return false;
    }
    if (e.which==27) {
      var el =$(document.activeElement); console.log(el);
      el = el.closest(".document").prev();
      goEl(el);
      return false;
    }
  }
  function goEl(el) {
      if (!el || !el.length) {
        el = $(".document").first();
      }
      el.find("input.doc-date").focus();console.log(el.offset())
      window.scrollTo(0, el.offset().top-50);
  }
  function setToday(el) {
    var D = new Date(),
        y = D.getFullYear(),
        m = ("0"+D.getMonth()).slice(-2),
        d = ("0"+D.getDate()).slice(-2);
    el.parentElement.getElementsByTagName('input')[0].value = ""+y+"-"+m+"-"+d;
  }
  function bulkSet() {
    if (!confirm("Das überschreibt die Felder. Sicher?")) return;
    $('.field-date').val($('#bulk-date').val());
    $('.field-tags').val($('#bulk-tags').val());
    $('.field-mandant').val($('#bulk-mandant').val());
    $('.field-desc').val($('#bulk-desc').val());
  }
  </script>
@endsection
