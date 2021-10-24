<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order_type extends Model
{
  protected $fillable = array('_token', 'name', 'description', 'd_ext', 'd_int', 'side_a', 'side_b', 'large', 'width', 'thickness', 'formula', 'original_formula', 'state_id', 'author_id', 'updater_id');

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
