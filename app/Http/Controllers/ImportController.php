<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Document;

class ImportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Handle Incoming Mail via Mailgun
     * @return \Illuminate\Http\Response
     */
    public function handleMail(Request $request)
    {
        if ($request->query("token") != $_ENV["WEBHOOK_TOKEN"]) abort(403, 'Invalid token');
        $file = $request->file("attachment-1");
        if (!$file) abort(400, "Missing file");
        if ($file->getClientOriginalExtension() != 'pdf') abort(406, "Invalid file type (only pdf accepted)");

        ignore_user_abort(TRUE);
        set_time_limit(100);
        $doc = new Document();
        $doc->doc_date = $request->has("doc_date") ? $request->input("doc_date") : null;
        $doc->import_filename = preg_replace("/[^a-zA-Z0-9._-]+/", "-", $file->getClientOriginalName());
        $doc->import_source = $request->input("from");
        $doc->title = $request->input("subject");
        $doc->description = $request->input("body-plain");

        $doc->save();

        echo $doc->id."\n";
        flush();

        $file->move($doc->getPath(), $doc->import_filename);

        if ($_ENV['UPDATE_PREVIEW_IN_BACKGROUND']) {
            $lockFile = $doc->getPath() . '/_updatePreview.pid';
            $logfile = $doc->getPath() . '/_updatePreview_stdout.log';

            shell_exec("php ".escapeshellarg(base_path() . '/artisan')." docman:updatepreview ".intval($doc->id)." > ".escapeshellarg($logfile)." 2>&1 & echo \$! > ".escapeshellarg($lockFile));
            sleep(5);
        } else {
            $doc->updatePreview();
        }


        #var_dump($request->input());
    }

    /**
     * Updates properties of multiple documents at once. Used by import editor (/import)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massUpdate(Request $request)
    {
        $post = $request->input("doc");
        foreach($post as $docid => $content) {
            $doc = Document::find($docid);
            if ($content['title']) $doc->title = $content['title'];
            if ($content['doc_date']) $doc->doc_date = $content['doc_date'];
            if ($content['tags']) $doc->tags = $content['tags'];
            $doc->save();
        }
        return response()->json(["success"=>"true"]);
    }


    public function fetchLog($id, $token) {
        $doc = Document::findOrFail($id);
        if ($doc->getToken() !== $token)
            abort(404);
        return response()->json(["log" => $doc->getLog()]);
    }

}
