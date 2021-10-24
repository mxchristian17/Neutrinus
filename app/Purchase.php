<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
  public function elements()
  {
      return $this->hasMany('App\Purchase_element', 'purchase_id', 'id');
  }

  public function projects()
  {
      return $this->hasMany('App\Purchase_project', 'purchase_id', 'id');
  }

  public function supplier()
  {
    return $this->hasOne('App\Supplier', 'id', 'supplier_id')->withDefault([
      'id' => 0,
      'name' => 'No existente',
      'state_id' => 0,
      'phone_number' => '',
      'email' => '',
      'cuit' => '',
      'taxpayer_type_id' => '',
      'address' => '',
      'city' => '',
      'province' => '',
      'country' => '',
      'currency_id' => '',
      'description' => '',
      'author_id' => 1,
      'updater_id' => 1
    ]);
  }

  public function emitter()
  {
      return $this->hasOne('App\User', 'id', 'emitter_id');
  }

  public function recipient()
  {
      return $this->hasOne('App\User', 'id', 'recipient_id');
  }

  public function author()
  {
      return $this->hasOne('App\User', 'id', 'author_id');
  }

  public function updater()
  {
      return $this->hasOne('App\User', 'id', 'updater_id');
  }

  function getOrderNameAttribute() {
    switch($this->status)
    {
      case 0: case 1: $prefix = 'SC'; break;
      case 2: case 3: case 4: case 5: $prefix = 'OC'; break;
      case 6: $prefix = 'ANULADA '; break;
      default: $prefix = 'ERROR'; break;
    }
    return $prefix.str_pad($this->order_number,6,'0',STR_PAD_LEFT);
  }

  function getOrderTypeAttribute() {
    switch($this->status)
    {
      case 0: case 1: $description = 'Solicitud de cotización'; break;
      case 2: case 3: case 4: case 5: $description = 'Órden de compras'; break;
      case 6: $description = 'Proceso de órden anulada'; break;
      default: $description = 'ERROR'; break;
    }
    return $description;
  }

  public function getStatusNameAttribute()
  {
    switch($this->status)
    {
        case 0:
          return 'Esperando cotización';
        break;
        case 1:
          return 'Cotización recibida';
        break;
        case 2:
          return 'Adjudicar órden de compras';
        break;
        case 3:
          return 'Esperando pedido';
        break;
        case 4:
          return 'Chequear elementos recibidos';
        break;
        case 5:
          return 'Órden cerrada';
        break;
        case 6:
          return 'Proceso anulado';
        break;
    }
  }

  public function getnextStatusNameAttribute()
  {
    switch($this->status+1)
    {
        case 7:
          return 'Esperando cotización';
        break;
        case 1:
          return 'Cotización recibida';
        break;
        case 2:
          return 'Adjudicar órden de compras';
        break;
        case 3:
          return 'Esperando pedido';
        break;
        case 4:
          return 'Chequear elementos recibidos';
        break;
        case 5:
          return 'Órden cerrada';
        break;
        case 6:
          return 'Proceso anulado';
        break;
    }
  }

  public function getStatusBtnClassAttribute()
  {
    switch($this->status)
    {
        case 0:
          return 'btn-secondary';
        break;
        case 1:
          return 'btn-outline-danger';
        break;
        case 2:
          return 'btn-danger';
        break;
        case 3:
          return 'btn-warning';
        break;
        case 4:
          return 'btn-outline-success';
        break;
        case 5:
          return 'btn-primary';
        break;
        case 6:
          return 'btn-dark';
        break;
    }
  }

  public function getEstimatedValueAttribute()
  {
    $cost = 0;
      foreach($this->elements as $element)
      {
        $cost = $cost + $element->element->materialCost[0];
      }
    return round($cost, 2);
  }
}
