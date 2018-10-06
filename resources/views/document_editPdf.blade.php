@extends("deflayout")

@section("toolbar")
<div class="navbar-form navbar-right"><a href='{{ action('DocumentController@show', [$doc->id]) }}' class="btn btn-default">Abbrechen</a></div>
@endsection

@section("main")
<style>
.well h3 { margin-top: 0; }
@media screen and (min-width: 800px) {
  .panel-body { min-height: 150px; }
}
</style>
<br>

<div class="row" style="margin-top: 5px; margin-bottom: 5px;">
<div class="col-md-3">[{{ $doc->doc_mandant }}] <b>{{ $doc->title }}</b>
</div>

 <div class="col-md-3"><span class="glyphicon glyphicon-download-alt"></span> <a href='{{ action('DocumentController@viewFile',[$doc->id,$doc->import_filename]) }}'>{{$doc->import_filename}}</a></div>

<div class="col-md-3"><span class="glyphicon glyphicon-file"></span> @if ($doc->page_count == 1)
eine Seite
@else
{{$doc->page_count}} Seiten
@endif<br>
  ({{ readable_size($doc->file_size)}})
	</div>
<div class="col-md-3" title="Hinzugefügt {{$doc->created_at}}">
<span class="glyphicon glyphicon-calendar"></span> {{ ($doc->doc_date == null) ? "(kein Datum)" : $doc->doc_date->toDateString() }}
<br>
  <span class="glyphicon glyphicon-asterisk"></span> {{ $doc->created_at->toDateString() }}</div>	
</div>




<br>

<div class="row"><div class="col-md-6">
  <form action="{{ action('DocumentController@splitPdf', [$doc->id]) }}" method="post">
    {{ csrf_field() }}

    <div class="panel panel-default">
      <div class="panel-heading">Burst into single-page documents</div>
      <div class="panel-body">
      <input type="submit" name="burstPdf" value="Burst PDF" class="btn btn-primary" {{ $doc->page_count > 1 ? "" : "disabled" }}>
      </div>
    </div>
  </form>

</div><div class="col-md-6">

  <form action="{{ action('DocumentController@splitPdf', [$doc->id]) }}" method="post">
    {{ csrf_field() }}

    <div class="panel panel-default">
      <div class="panel-heading">Merge documents</div>
      <div class="panel-body">
        <p>Insert Document ID: <input type="text" name="mergeDocId" value=""> 
        <select name="mergePosition"><option>before</option><option>after</option></select> the current document.</p>
        <p><label for="chk1"><input type="checkbox" name="deleteMerged" value="1" checked id="chk1"> Delete merged document</label></p>
        <input type="submit" name="mergePdf" value="Merge PDF" class="btn btn-primary">
      </div>
    </div>
  </form>

</div></div>


<br>


<form action="{{ action('DocumentController@splitPdf', [$doc->id]) }}" method="post">
  {{ csrf_field() }}

  <div class="panel panel-default">
    <div class="panel-heading">Extract pages</div>
    <div class="panel-body">
      <p><label for="chk2"><input type="checkbox" name="removeFromOrig" value="1" checked id="chk2"> Remove selected pages from this document</label></p>

      @for($i=1; $i<= $doc->page_count; $i++)
      <div class="thumbnail pull-left">
      <input type="checkbox" name="extractPage[]" value="{{$i}}" id="extractPage_{{ $i }}">
      <label for="extractPage_{{$i}}">
      <img src="{{ action('DocumentController@thumbnail', [$doc->id, $doc->getToken(), $i]) }}" width=150 height=150 alt="{{$i}}">
      </label>
      </div>
      @endfor
      <br style="clear:left">
      <input type="submit" name="extractPages" value="Extract selected pages" class="btn btn-primary pull-right" {{ $doc->page_count > 1 ? "" : "disabled" }}>


    </div>
  </div>

</form>

<br><br>

@endsection

