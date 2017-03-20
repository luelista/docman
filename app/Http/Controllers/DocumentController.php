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
      if ($request->has("q")) {
        $query = explode(" ", $request->input("q"));
        $where = ""; $para = array();
        foreach($query as $q) {
          if (substr($q,0,1)=="!") { $where .= " NOT "; $q=substr($q,1); }

          if (preg_match('/^(?:m:(.*)|([A-Z]+))$/', $q, $m)) {
            $where .= " doc_mandant = ? AND "; $para[] = $m[1];
          } else if (preg_match('/^d:([0-9]){4}$/', $q, $m)) {
            $q = intval($m[1]);
            $where .= " year(doc_date) = ? AND "; $para[] = $q;
          } elseif (preg_match('/^d:([0-9]{4})-([0-9]{2})$/', $q)) {
            $where .= " year(doc_date) = ? AND  month(doc_date) = ? AND "; $para[] = $m[1];$para[] = $m[2];
          } elseif (substr($q,0,4)=="tag:") {
            $q = "% ".substr($q,4)." %";
            $where .= " tags LIKE ? AND "; $para[] = $q;
          } elseif (substr($q,0,6)=="title:") {
            $q = "%".substr($q,6)."%";
            $where .= " title LIKE ? AND "; $para[] = $q;
          } else {
            $q = "%$q%";
            $where .= " (title LIKE ? OR description LIKE ? OR tags LIKE ?) AND "; $para[] = $q;$para[] = $q;$para[] = $q;
          }
        }

        $docs = Document::whereRaw("$where 1", $para)->orderBy('created_at', 'DESC')->get();
      } else {
        $docs = Document::orderBy('created_at', 'DESC')->get();
      }

        $mandanten = DB::table('documents')->select(array('doc_mandant', DB::raw('count(doc_mandant) cc')))->groupBy('doc_mandant')->having('doc_mandant', '<>', '')->get();

        return view('document_list', ['docs' => $docs, 'mandanten' => $mandanten]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function importEditor()
    {
        $docs = Document::where(['doc_date' => null])->get();
        return view('document_import', ['docs' => $docs]);
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
				$doc->import_source = "web";
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
        $doc = Document::findOrFail($id);
        return view('document_show', ['doc' => $doc]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function preview($id, $page)
    {
        $doc = Document::find($id);
        $previewFile = $doc->getPagePreviewFilespec($page);
        return response()->download($previewFile, $doc->import_filename . '.jpg', [], 'inline');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function thumbnail($id, $page)
    {
        $doc = Document::find($id);
        $previewFile = $doc->getThumbFilespec($page);
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
      $doc = Document::find($id);
      return view('document_editPdf', ['doc' => $doc]);
    }


    public function splitPdf(Request $request, $id) {
      $doc = Document::findOrFail($id);

      if ($request->input('extractPages')) {
        $newDocList = $doc->extractPdfPages($request->input('extractPage'), $request->input('removeFromOrig'));
      } else if ($request->input('burstPdf')) {
        $newDocList = $doc->burstPdf();
      } else if ($request->input('mergePdf')) {
        $mergeDoc = Document::findOrFail(intval($request->input('mergeDocId')));
        $newDocList = $doc->mergePdf($mergeDoc, $request->input('mergePosition'));
        if ($request->input('deleteMerged')) $mergeDoc->delete();
        else array_push($newDocList, $mergeDoc);
      }
      $docIdList = array();
      foreach($newDocList as $d) array_push($docIdList, $d->id);
      return redirect()->action('DocumentController@listSelected', [ 'docs' => $docIdList ]);
    }

    public function listSelected(Request $request) {
      $docs = Document::whereIn('id', $request->input("docs"))->get();
      return view('document_editPdfOk', ['newDocs' => $docs ]);
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
        $doc->doc_mandant = $request->input('doc_mandant');
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
	return response()->json(["success" => true]);
    }
		
		public function allTags() {
			$tags = DB::select("select count(tag) cc,tag from document_tags group by tag order by cc desc");
			return response()->json(["tags" => $tags]);
		}

    public function updateTags(Request $request) {
      \Artisan::call('docman:updatetags');
      return redirect()->action('DocumentController@index');
    }
}
