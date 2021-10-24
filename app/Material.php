<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
  protected $fillable = array('_token', 'name', 'initials', 'description', 'specific_weight', 'state_id', 'author_id', 'updater_id');

  public function scopeNameAscending($query)
  {
    return $query->orderBy('name', 'ASC');
  }

  public function states()
  {
      return $this->hasOne('App\State', 'id', 'state_id');
  }

  public function author()
  {
      return $this->hasOne('App\User', 'id', 'author_id');
  }

  public function updater()
  {
      return $this->hasOne('App\User', 'id', 'updater_id');
  }

}
