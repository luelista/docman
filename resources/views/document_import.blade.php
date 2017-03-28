@extends("deflayout")



@section("main")
<h2>Tagging</h2>

<form action="javascript:" method="post" id="editform" onsubmit="submitForm()">
  {{csrf_field()}}

@foreach($docs as $doc)
<div class="row document">
	<div class="col-md-3">
  
<small><a href="{{ action('DocumentController@show', [ $doc->id ]) }}" target="_blank">{{ $doc->displayDate() }}</a>
 &nbsp; &bull; &nbsp; {{ $doc->page_count }} Seite(n)</small><br>
<span class="glyphicon glyphicon-text-width"></span> <input type="text" value="{{ $doc->title }}" name="doc[{{$doc->id}}][title]"><br>
<span class="glyphicon glyphicon-calendar"></span> <input type="text" value="{{ $doc->doc_date }}" name="doc[{{$doc->id}}][doc_date]" class="doc-date"><br>
<span class="glyphicon glyphicon-tags"></span> <input type="text" value="{{ trim($doc->tags) }}" name="doc[{{$doc->id}}][tags]">
<br>
</small>
  
</div>
<div class="col-md-9">
<div class="preview">
<a href="{{ action('DocumentController@viewFile', [$doc->id,"view.pdf"]) }}" onclick="window.open(this.href,'foo','width=800,height=800');return false;" target="_blank">
	<img src="{{ action('DocumentController@preview', [$doc->id, $doc->getToken(), 1]) }}">
</a>
  </div>
  </div>
</div>
@endforeach
<input type="submit" class="btn btn-primary">
  </form>
<style>
	.preview {height:220px; overflow: auto;border: 2px solid #ddd;}
	.preview img { max-width: 100%; }
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
  </script>
@endsection
