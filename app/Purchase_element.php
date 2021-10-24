<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase_element extends Model
{
  public function purchase()
  {
      return $this->belongsTo('App\Purchase')->withDefault([
        'purchase_id' => 0,
        'element_id' => 0,
        'quantity' => 0,
        'definition_status' => 2,
        'quantity_received' => 0
      ]);
  }

  public function element()
  {
      return $this->hasOne('App\Element', 'id', 'element_id');
  }
}
