<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentTag extends Model
{
    public $timestamps = false;
    
    public function document()
    {
        return $this->belongsTo('App\Document');
    }
}
