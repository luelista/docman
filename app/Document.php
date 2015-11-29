<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;
    protected $dates = ['doc_date', 'created_at', 'updated_at', 'deleted_at'];
    
    public function save(array $options = []) {
	    $this->tags = " ".trim($this->tags)." ";
	    parent::save($options);
	    if ($this->doc_name == null) {
		    $this->doc_name = $this->doc_date->getDateString() . "_" . $this->id;
		    parent::save($options);
		}
	    
	    $path = $this->getPath();
	    @mkdir($path);
	    file_put_contents($path . '/' . '_metadata.json', json_encode([
		    "doc_date" => $this->doc_date,
		    "title" => $this->title,
		    "description" => $this->description,
		    "page_count" => $this->page_count,
		    "created_at" => $this->created_at->getTimestamp(),
		    "updated_at" => $this->updated_at->getTimestamp()
	    ]));
	    file_put_contents($path . '/' . '_description', $this->description);
	    file_put_contents($path . '/' . '_title', $this->title);
	    
    }
    
    public function getTags() {
	    $t = trim($this->tags);
	    if ($t == "") return [];
	    return explode(" ", $t);
    }
    
    public function getPath() {
	    if ($this->doc_name) $dir = $this->doc_name;
	    else $dir = $this->doc_date->getDateString() . "_" . $this->id;
	    $path = $_ENV["DOC_DIRECTORY"] . '/' . $dir;
	    return $path;
    }
    
    public function updatePreview() {
	    $src = escapeshellarg($this->getPath() . '/' . $this->import_filename);
	    $dst = escapeshellarg($this->getPath() . '/' . '_firstpage.jpg');
	    $dst2 = escapeshellarg($this->getPath() . '/' . '_thumb.jpg');
	    
	    $cmd = "gs -dBATCH -dNOPAUSE -dQUIET -sDEVICE=jpeg -sOutputFile=$dst -r72 $src";
	    shell_exec($cmd);
	    $tsize = 150;
	    $cmd = "convert $dst -resize '{$tsize}x{$tsize}^' -crop '{$tsize}x{$tsize}+0+0' $dst2";
	    shell_exec($cmd);
	    
	    $pagecount = exec('/usr/bin/pdfinfo '.$src.' | awk \'/Pages/ {print $2}\'', $output);
		$this->page_count = $pagecount;
		$this->save();
	    
    }
    
}
