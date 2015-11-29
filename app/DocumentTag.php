<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentTag extends Model
{
    //
    public function tags()
    {
        return $this->belongsTo('App\Document');
    }
}
