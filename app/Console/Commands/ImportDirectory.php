<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Document;

class ImportDirectory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docman:importdirectory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import PDF files in specified import directory';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dir = env('IMPORT_DIRECTORY');
        $files = glob("$dir/*.pdf", GLOB_BRACE);
        foreach ($files as $file) {
          $doc = new Document();
          $doc->doc_date = null;
          $doc->import_filename = preg_replace("/[^a-zA-Z0-9._-]+/", "-", basename($file));
          $doc->import_source = "filesystem";
          $doc->title = basename($file);
          $doc->description = "";
          $doc->save();
          rename($file, $doc->getPath().'/'.$doc->import_filename);
          $doc->updatePreview();
        }
    }
}
