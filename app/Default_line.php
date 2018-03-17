<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Default_line extends Model
{
    public function user(){
    	return $this->belongsTo('App\User');
    }
}
