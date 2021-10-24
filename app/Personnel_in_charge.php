<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Personnel_in_charge extends Model
{
    protected $table = 'personnel_in_charges';

    public function user_under_charge(){
  		return $this->hasOne('App\User', 'id', 'user_under_charge_id')->withDefault();
  	}
    public function user_at_charge(){
  		return $this->hasOne('App\User', 'id', 'user_at_charge_id')->withDefault();
  	}
}
