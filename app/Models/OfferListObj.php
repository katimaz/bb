<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferListObj extends Model
{
    protected $table = 'OfferListObj';

    public function comments()
    {
        return $this->hasMany('App\Users', 'id', 'mem_id');
    }
}