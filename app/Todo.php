<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
  public function sale()
  {
      return $this->belongsTo('App\Sale')->withDefault([
      'id' => 0,
      'order_number' => 'No encontrada',
      'state_id' => '1',
      'author_id' => '1',
      'updater_id' => '1'
      ]);
  }

  public function sale_item()
  {
      return $this->belongsTo('App\Sale_item')->withDefault([
      'id' => 0,
      'sale_id' => 0,
      'author_id' => '1',
      'updater_id' => '1'
      ]);
  }

  public function element()
  {
      return $this->belongsTo('App\Element')->withDefault([
      'id' => 0,
      'name' => 'Elemento no encontrado',
      'state_id' => '1',
      'author_id' => '1',
      'updater_id' => '1'
      ]);
  }
}
