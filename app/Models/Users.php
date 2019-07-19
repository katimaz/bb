<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';

    public function offerlistobj()
    {
        return $this->hasMany('App\Models\OfferListObj', 'mem_id', 'id');
    }

    public function member_addr_recodes()
    {
        return $this->hasMany('App\Models\Member_addr_recode', 'u_id', 'id');
    }
}