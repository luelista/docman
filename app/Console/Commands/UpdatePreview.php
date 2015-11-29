<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Document;

class UpdatePreview extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docman:updatepreview {fileid?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
	    if ($this->argument("fileid")) {
		    $docs = array(Document::find($this->argument("fileid")));
	    } else {
	        $docs = Document::all();
	    }
        foreach($docs as $doc) {
	        $this->info("Processing Doc #".$doc->id);
	        $doc->updatePreview();
	        
        }
    }
}
