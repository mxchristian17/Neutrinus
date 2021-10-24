<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale_item extends Model
{
  public function sale()
  {
      return $this->belongsTo('App\Sale')->withDefault([
      'id' => 0
      ]);
  }

  public function subset()
  {
    if($this->subset_id)
    {
      return $this->hasOne('App\subset', 'id', 'subset_id');
    }else{
      return null;
    }
  }

  public function element()
  {
    if($this->element_id)
    {
      return $this->hasOne('App\element', 'id', 'element_id');
    }else{
      return null;
    }
  }

}
