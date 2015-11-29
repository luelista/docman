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
        $file = $request->file("attachment-1");
		if (!$file) abort(400, "Missing file");
		if ($file->getClientOriginalExtension() != 'pdf') abort(406, "Invalid file type (only pdf accepted)");
		
        $doc = new Document();
        $doc->doc_date = $request->has("doc_date") ? $request->input("doc_date") : date("Y-m-d");
        $doc->import_filename = preg_replace("/[^a-zA-Z0-9._-]+/", "-", $file->getClientOriginalName());
        $doc->title = $request->input("subject");
        $doc->description = $request->input("body-plain");
        $doc->save();
        
        $file->move($doc->getPath(), $doc->import_filename);
        $doc->updatePreview();
        
        var_dump($request->input());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
