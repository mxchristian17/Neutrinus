<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
  protected $fillable = array('_token', 'user_id', 'code_id', 'state', 'author_id', 'updater_id');
}
