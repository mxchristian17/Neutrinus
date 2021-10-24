<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recent_project extends Model
{
  public function projectData() {
    return $this->hasone('App\Project', 'id', 'project_id')->withDefault(
      [
        'name' => 'Proyecto eliminado...',
        'noExistente' => true
      ]
    );
  }
}
