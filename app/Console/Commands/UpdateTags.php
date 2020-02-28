<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Document;
class UpdateTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docman:updatetags';

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
        DB::delete("TRUNCATE TABLE document_tags");
        //$docs = Document::all();
        $docs = DB::table('documents')->get();
        foreach($docs as $d) {
          $tagarr = explode(" ",$d->tags);
          foreach($tagarr as $tag) if ($tag != "")
            DB::insert("INSERT INTO document_tags VALUES (?,?)", [$d->id, $tag]);
        }
        
        
    }
}
