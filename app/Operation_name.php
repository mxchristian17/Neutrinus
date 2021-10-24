<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Operation_name extends Model
{

  protected $fillable = array('_token', 'name', 'description', 'usd_for_hour', 'state_id', 'author_id', 'updater_id');

  public function states()
  {
      return $this->hasOne('App\State', 'id', 'state_id');
  }
}
