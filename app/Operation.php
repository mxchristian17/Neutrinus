<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{

  protected $fillable = array('_token', 'element_id', 'order_number', 'operation_name_id', 'observation', 'preparation_time', 'manufacturing_time', 'cnc_program', 'operation_state_id', 'author_id', 'updater_id');

  public function operation_name()
  {
      return $this->belongsTo('App\Operation_name')->withDefault([
      'id' => 0,
      'name' => 'Tipo de ruta no encontrado',
      'description' => 'Este tipo de ruta se asigna automáticamente a los elementos que tienen un tipo de ruta que ha sido mal asignado o eliminado',
      'usd_for_hour' => '0',
      'state_id' => '1',
      'author_id' => '1',
      'updater_id' => '1'
      ]);
  }

  public function states()
  {
      return $this->hasOne('App\State', 'id', 'operation_state_id');
  }

  public function getPreparationUsdCostAttribute()
  {
      $cost = $this->preparation_time*$this->operation_name->usd_for_hour/60;
      if($cost>0){
        return ($cost);
      }else{
        return 0;
      }
  }
  public function getManufacturingUsdCostAttribute()
  {
      $cost = $this->manufacturing_time*$this->operation_name->usd_for_hour/60;
      if($cost>0){
        return ($cost);
      }else{
        return 0;
      }
  }
}
