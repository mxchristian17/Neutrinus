<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subset extends Model
{

  protected $fillable = array('_token', 'name', 'project_id', 'subset_number', 'state_id', 'author_id', 'updater_id');

  public function project()
  {
      return $this->hasOne('App\Project', 'id', 'project_id');
  }

  public function projectelement()
  {
      return $this->hasMany('App\Projectelement', 'subset_id', 'id');
  }
}
