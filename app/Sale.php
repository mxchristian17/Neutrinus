<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Sale extends Model
{
  protected $fillable = [
      'order_number', 'client_id', 'observations', 'work_order_emitter_id', 'work_order_observations', 'status', 'currency_id', 'bill_number', 'retentions', 'perceptions', 'requested_delivery_date', 'scheduled_delivery_date', 'quote_request_date', 'quote_date', 'purchase_order_reception_date', 'ready_to_deliver_date', 'delivered_date', 'state_id', 'author_id', 'updater_id'
  ];

  public function items()
  {
      return $this->hasMany('App\Sale_item', 'sale_id', 'id');
  }

  public function client()
  {
      return $this->belongsTo('App\Client')->withDefault([
      'id' => 0,
      'name' => 'Cliente no encontrado',
      'state_id' => '1',
      'author_id' => '1',
      'updater_id' => '1'
      ]);
  }

  public function currency()
  {
      return $this->hasOne('App\Currency', 'id', 'currency_id');
  }

  public function state()
  {
      return $this->hasOne('App\State', 'id', 'state_id');
  }

  public function workOrderEmitter()
  {
      return $this->hasOne('App\User', 'id', 'work_order_emitter_id')->withDefault();
  }

  public function author()
  {
      return $this->hasOne('App\User', 'id', 'author_id');
  }

  public function updater()
  {
      return $this->hasOne('App\User', 'id', 'updater_id');
  }

  public function getStatusNameAttribute()
  {
    switch(intval($this->status))
    {
      case 1: return 'Cotizar'; break;
      case 2: return 'Cotizado'; break;
      case 3: return 'Órden de compras recibida'; break;
      case 4: return 'Facturado'; break;
      case 5: return 'Listo para entregar'; break;
      case 6: return 'Entregado'; break;
      case 7: return 'Órden cerrada'; break;
      case 8: return 'Órden anulada'; break;
      default: return FALSE; break;
    }
  }

  public function getStatusBtnClassAttribute()
  {
    switch(intval($this->status))
    {
      case 1: return 'btn-danger'; break;
      case 2: return 'btn-warning'; break;
      case 3: return 'btn-warning'; break;
      case 4: return 'btn-warning'; break;
      case 5: return 'btn-primary'; break;
      case 6: return 'btn-success'; break;
      case 7: return 'btn-success'; break;
      case 8: return 'btn-dark'; break;
      default: return FALSE; break;
    }
  }

  public function getTodoActiveAttribute()
  {
    if($this->status>2 AND $this->status<6 AND ($this->state_id==1 OR $this->state_id==3))
    {
      return TRUE;
    }else{
      return FALSE;
    }
  }
  public function getSaleNameAttribute()
  {
    $return = Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->year.'-'.intval($this->id);
    return $return;
  }

}
