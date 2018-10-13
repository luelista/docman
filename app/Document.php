<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;
    protected $dates = ['doc_date', 'created_at', 'updated_at', 'deleted_at'];
    
    public function pages() {
        return $this->hasMany('App\DocumentPage');
    }
    public function tags() {
        return $this->hasMany('App\DocumentTag');
    }

    public function save(array $options = []) {
        $this->tags = " ".trim($this->tags)." ";
        parent::save($options);
        if ($this->doc_name == null) {
            $my_date = ($this->doc_date != null) ? $this->doc_date->toDateString()
                                 : $this->created_at->toDateString();
            $this->doc_name = $my_date . "_" . $this->id;
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


    public function getToken() {
        return md5($_ENV["APP_KEY"] . $this->id . $this->doc_name . $this->created_at);
    }

    public function getTags() {
        $t = trim($this->tags);
        if ($t == "") return [];
        return explode(" ", $t);
    }

    public function getPath() {
        if (!$this->doc_name) throw new \Exception("missing doc_name, must save instance first");
        $path = env("DOC_DIRECTORY") . '/' . $this->doc_name;
        return $path;
    }

    public function displayDate() {
        if ($this->doc_date != null)
            return $this->doc_date->toDateString();
        else
            return "(" .    $this->created_at->toDateString() . ")";
    }

    public function getThumbFilespec($page = 1) {
        return $this->getPath() . '/' . '_thumb' . $page . '.jpg';
    }
    public function getPagePreviewFilespec($page = 1) {
        return $this->getPath() . '/' . '_page' . $page . '.jpg';
    }
    public function debugLog($str, $clear=FALSE, $page = FALSE, $count = FALSE) {
        echo date("H:i:s")." $str\n";
        if ($page !==FALSE) file_put_contents($this->getPath() . '/_progress.log', $page.'/'.$count);
    }
    public function getLog() {
        return ["log"=>"",
            "progress"=>@file_get_contents($this->getPath() . '/_progress.log'),
            "stdout"=>@file_get_contents($this->getPath() . '/_updatePreview_stdout.log')];
    }
    public function updatePreview() {
        $this->debugLog("updating preview...",true, 0, 0);
        $this->page_count = 0;
        $this->save();
        $src = escapeshellarg($this->getPath() . '/' . $this->import_filename);
        $tmp = escapeshellarg($this->getPath() . '/' . '_tmp%d.tiff');
        $dst1 = escapeshellarg($this->getPagePreviewFilespec(1));

        $pagecount = exec('/usr/bin/pdfinfo '.$src.' | awk \'/Pages/ {print $2}\'');
        $this->page_count = $pagecount;
        $this->file_size = filesize($this->getPath() . '/' . $this->import_filename);
        $this->ocrtext = '';

        $this->debugLog("Splitting PDF", 1, $pagecount+1);
        $cmd = "gs -dNOPAUSE -dSAFER -sDEVICE=tiff24nc -sOutputFile=$tmp -r144 $src ";
        exec($cmd);

        $this->pages()->delete();
        for($pag = 1; $pag <= $pagecount; $pag++) {   $this->debugLog("Handling page $pag/$pagecount", FALSE, $pag, $pagecount+1);
            $dst1 = escapeshellarg($this->getPagePreviewFilespec($pag));
            $dst2 = escapeshellarg($this->getThumbFilespec($pag));

            $tsize = 800;    $this->debugLog("Thumbnail $tsize px");
            $cmd = "convert ".sprintf($tmp, $pag)." -quality 70 -resize '{$tsize}x{$tsize}^' -trim -fuzz 70% +repage $dst1";
            shell_exec($cmd);

            $tsize = 150;    $this->debugLog("Thumbnail $tsize px");
            $cmd = "convert ".sprintf($tmp, $pag)." -resize '{$tsize}x{$tsize}^' -crop '{$tsize}x{$tsize}+0+0' -trim -fuzz 70% $dst2";
            shell_exec($cmd);

            if ($pag < env("OCR_PAGE_LIMIT")) {  $this->debugLog("OCR");
                $cmd = "tesseract -l deu ".sprintf($tmp, $pag)." ".escapeshellarg($this->getPath() . "/_ocrtext" . $pag);
                shell_exec($cmd);
                $ocrtext = file_get_contents($this->getPath() . "/_ocrtext" . $pag . ".txt") . "\n";
            } else $ocrtext = NULL;
            $this->debugLog("Saving");
            $pageInfo = new DocumentPage([ 'page_index' => $pag, 'page_number' => NULL, 'ocrtext' => $ocrtext ]);
            $this->pages()->save($pageInfo);
        }

        $this->save();
        exec("rm " . escapeshellarg($this->getPath()) . '/' . '_tmp*.tiff');
        $this->debugLog("Done", FALSE, $pagecount+1, $pagecount+1);
        //shell_exec("rm " . escapeshellarg($this->getPath()) . '/' . '_ocrtext*.txt');
    }


    public function extractPdfPages($pages, $removeFromOrig) {
        $src = escapeshellarg($this->getPath() . '/' . $this->import_filename);
        $newOrig = ""; $newExtr = "";

        if ($this->page_count < 2) throw new \Exception("Can only extract pages from multi-page documents");

        for($i = 1; $i <= $this->page_count; $i++) {
            if (array_search("$i", $pages) === false)
                $newOrig .= "$i ";
            else
                $newExtr .= "$i ";
        }
        if ($newOrig == "" || $newExtr == "")
            throw new \Exception("Refusing to create empty document");
        $doc = new Document();
        $doc->doc_date = $this->doc_date;
        $doc->import_filename = str_replace(" ", ".", $newExtr) . "__" . $this->import_filename;
        $doc->import_source = $this->import_source."_split";
        $doc->title = "(extracted) ".$this->title;
        $doc->description = "";
        $doc->save();

        $pdftk_cmd = env("PDFTK_BIN") . " " . $src . " cat " . $newExtr . " output " . escapeshellarg($doc->getPath() . "/" . $doc->import_filename);
        shell_exec($pdftk_cmd);
        $doc->updatePreview();

        if ($removeFromOrig) {
            $this->import_filename = str_replace(" ", ".", $newOrig) . "__" . $this->import_filename;
            $pdftk_cmd = env("PDFTK_BIN") . " " . $src . " cat " . $newOrig . " output " . escapeshellarg($this->getPath() . "/" . $this->import_filename);
            shell_exec($pdftk_cmd);
            $this->updatePreview();
            $this->save();
        }

        return array($this, $doc);
    }

    public function burstPdf() {
        if ($this->page_count < 2) throw new \Exception("Can only burst multi-page documents");

        $src = escapeshellarg($this->getPath() . '/' . $this->import_filename);
        $target = escapeshellarg($this->getPath() . '/pg_%d.pdf');
        $pdftk_cmd = env("PDFTK_BIN") . " " . $src . " burst output " . $target;
        shell_exec($pdftk_cmd);

        $docList = array();
        for($i = 1; $i <= $this->page_count; $i++) {
            $doc = new Document();
            $doc->doc_date = null;
            $doc->import_filename = $i . "__" . $this->import_filename;
            $doc->import_source = $this->import_source."_burst";
            $doc->title = "page $i of ".$this->title;
            $doc->description = "";
            $doc->save();
            rename($this->getPath() . '/pg_' . $i . '.pdf', $doc->getPath() . '/' . $doc->import_filename);
            $doc->updatePreview();
            array_push($docList, $doc);
        }

        return $docList;
    }

    public function mergePdf($otherDoc, $position) {
        $src1 = escapeshellarg($this->getPath() . '/' . $this->import_filename);
        $src2 = escapeshellarg($otherDoc->getPath() . '/' . $otherDoc->import_filename);
        $this->import_filename = 'merged_' . $this->import_filename;
        $dst = escapeshellarg($this->getPath() . '/' . $this->import_filename);

        if ($position == "before")
            $pdftk_cmd = env("PDFTK_BIN") . " " . $src2 . " " . $src1 . " cat output " . $dst;
        else
            $pdftk_cmd = env("PDFTK_BIN") . " " . $src1 . " " . $src2 . " cat output " . $dst;

        shell_exec($pdftk_cmd);
        $this->save();
        $this->updatePreview();

        return [ $this ];
    }


}
