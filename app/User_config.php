<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_config extends Model
{
  protected $fillable = [
      'user_id', 'show_panel_home', 'show_element_general_search'
  ];
}
