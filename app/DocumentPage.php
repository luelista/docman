<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentPage extends Model {
    public $timestamps = false;
    protected $fillable = ['page_index', 'page_number', 'ocrtext'];

    public function document() {
        return $this->belongsTo('App\Document');
    }
}
