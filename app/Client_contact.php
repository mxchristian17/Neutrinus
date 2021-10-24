<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client_contact extends Model
{
  public function states()
  {
      return $this->hasOne('App\State', 'id', 'state_id');
  }
}
