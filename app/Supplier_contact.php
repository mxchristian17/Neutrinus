<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier_contact extends Model
{
  protected $fillable = array('_token', 'name', 'phone_number', 'email', 'state_id', 'description', 'author_id', 'updater_id');

  public function states()
  {
      return $this->hasOne('App\State', 'id', 'state_id');
  }
}
