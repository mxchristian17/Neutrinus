<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase_project extends Model
{
  public function project()
  {
      return $this->hasOne('App\Project', 'id', 'project_id');
  }
}
