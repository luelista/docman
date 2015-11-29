<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Document;
use DB;
use Illuminate\Support\Facades\Event;
class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
#	    Event::listen('illuminate.query', function($query, $params, $time, $conn) {     dd(array($query, $params, $time, $conn));});
	    if ($request->has("q")) {
		    //$docs = Document::whereRaw('MATCH (title,description) AGAINST (? IN NATURAL LANGUAGE MODE)', [$request->input("q")])->get();
		    $query = explode(" ", $request->input("q"));
		    $where = ""; $para = array();
		    foreach($query as $q) {
			    if (substr($q,0,1)=="!") { $where .= " NOT "; $q=substr($q,1); }
			    
			    if (preg_match('/^[0-9]{4}$/', $q)) {
				    $q = intval($q);
				    $where .= " year(doc_date) = ? AND "; $para[] = $q;
			    } elseif (preg_match('/^[0-9]{4}-[0-9]{2}$/', $q)) {
				    $q = explode("-",$q);
				    $where .= " year(doc_date) = ? AND  month(doc_date) = ? AND "; $para[] = $q[0];$para[] = $q[1];
			    } elseif (substr($q,0,4)=="tag:") {
				    $q = "% ".substr($q,4)." %";
				    $where .= " tags LIKE ? AND "; $para[] = $q;
			    } elseif (substr($q,0,6)=="title:") {
				    $q = "%".substr($q,6)."%";
				    $where .= " title LIKE ? AND "; $para[] = $q;
			    } else {
				    $q = "%$q%";
				    $where .= " (title LIKE ? OR description LIKE ?) AND "; $para[] = $q;$para[] = $q;
			    }
			    
			    
		    }
		    
		    $docs = Document::whereRaw("$where 1", $para)->get();
	    } else {
	        $docs = Document::orderBy('created_at', 'DESC')->get();
	    }


        return view('document_list', ['docs' => $docs]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $file = $request->file("document");
		if (!$file) abort(400, "Missing file");
		if ($file->getClientOriginalExtension() != 'pdf') abort(406, "Invalid file type (only pdf accepted)");
		
        $doc = new Document();
        $doc->doc_date = $request->input("doc_date");
        $doc->import_filename = preg_replace("/[^a-zA-Z0-9._-]+/", "-", $file->getClientOriginalName());
        $doc->title = $request->input("title");
        $doc->description = "";
        $doc->save();
        
        $file->move($doc->getPath(), $doc->import_filename);
        $doc->updatePreview();
        
        return redirect()->action('DocumentController@show', [$doc->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $doc = Document::find($id);
        return view('document_show', ['doc' => $doc]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function preview($id)
    {
        $doc = Document::find($id);
        $previewFile = $doc->getPath() . '/_firstpage.jpg';
        return response()->download($previewFile, $doc->import_filename . '.jpg', [], 'inline');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function thumbnail($id)
    {
        $doc = Document::find($id);
        $previewFile = $doc->getPath() . '/_thumb.jpg';
        return response()->download($previewFile, $doc->import_filename . '.jpg', [], 'inline');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function viewFile($id, $filename)
    {
        $doc = Document::find($id);
        $previewFile = $doc->getPath() . '/' . $doc->import_filename;
        return response()->download($previewFile, $doc->import_filename, [], 'inline');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $doc = Document::find($id);
        $doc->title = $request->input('title');
        $doc->description = $request->input('description');
        $doc->doc_date = $request->input('doc_date');
        $doc->tags = $request->input('tags');
        
        $doc->save();
        return redirect()->action('DocumentController@show', [$doc->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $doc = Document::find($id);
        $doc->delete();
        return redirect()->action('DocumentController@index');
    }
}
