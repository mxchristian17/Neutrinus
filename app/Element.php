<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Element extends Model
{

    protected $fillable = array('_token', 'nro', 'add', 'name', 'description', 'material_id', 'shared_material', 'order_type_id', 'd_ext', 'd_int', 'side_a', 'side_b', 'large', 'width', 'thickness', 'quantity_per_manufacturing_series', 'general_state_id', 'additional_material_cost', 'additional_material_cost_date', 'calculated_material_cost', 'calculated_material_cost_date', 'sale_price', 'author_id', 'updater_id');

    public function material()
    {
        return $this->belongsTo('App\Material')->withDefault([
        'id' => 0,
        'name' => 'Material no encontrado',
        'initials' => 'INEXIST',
        'description' => 'Este material se asigna automáticamente a los elementos que tienen un material que ha sido mal asignado o eliminado',
        'specific_weight' => '0',
        'state_id' => '1',
        'author_id' => '1',
        'updater_id' => '1'
        ]);
    }

    public function order_type()
    {
        return $this->belongsTo('App\Order_type')->withDefault([
        'id' => 0,
        'name' => 'Tipo de pedido no encontrado',
        'description' => 'Este tipo de pedido se asigna automáticamente a los elementos que tienen un tipo de pedido que ha sido mal asignado o eliminado',
        'formula' => '0',
        'original_formula' => '0',
        'd_ext' => '0',
        'd_int' => '0',
        'side_a' => '0',
        'side_b' => '0',
        'large' => '0',
        'width' => '0',
        'thickness' => '0'
        ]);
    }

    public function general_state()
    {
        return $this->belongsTo('App\General_state')->withDefault();
    }
    public function operation()
    {
        return $this->hasMany('App\Operation');
    }

    public function author()
    {
        return $this->hasOne('App\User', 'id', 'author_id');
    }

    public function updater()
    {
        return $this->hasOne('App\User', 'id', 'updater_id');
    }

    public function suppliers()
    {
        return $this->hasMany('App\item_supplier');
    }

    public function supplier_code()
    {
        return $this->hasMany('App\Supplier_code');
    }

    public function getVolumeAttribute()
    {
        $formula = $this->order_type->formula;
        $formula = str_replace("d_ext", $this->d_ext, $formula);
        $formula = str_replace("d_int", $this->d_int, $formula);
        $formula = str_replace("side_a", $this->side_a, $formula);
        $formula = str_replace("side_b", $this->side_b, $formula);
        $formula = str_replace("large", $this->large, $formula);
        $formula = str_replace("width", $this->width, $formula);
        $formula = str_replace("thickness", $this->thickness, $formula);
        eval( '$volume = '.$formula.';');
        if($volume>0){
          return ($volume/1000);
        }else{
          return 0;
        }
    }

    public function getWeightAttribute()
    {
        $weight = ($this->volume/(1000**2))*$this->material->specific_weight;
        if($weight>0){
          return $weight;
        }else{
          return 0;
        }
    }

    public function getMaterialCostAttribute()
    {
      if($this->calculated_material_cost > 0) return [$this->calculated_material_cost, new Carbon($this->calculated_material_cost_date)];
      $bestMatch = calcMaterialCost($this);
        if($bestMatch)
        {
          $this->calculated_material_cost = $bestMatch->price*$this->weight;
          unset($this->directCost);
          $return = [$this->calculated_material_cost, $bestMatch->updated_at];
          return $return;
          //return ($this->calculated_material_cost);
        }else{
          return [0, new Carbon('2500-01-01')];
        }
    }

    public function getDimensionsAttribute()
    {
      $dimensions = "";
      if($this->order_type->d_ext) $dimensions .= "Ø".$this->d_ext."x";
      if($this->order_type->d_int) $dimensions .= "Ø".$this->d_int."x";
      if($this->order_type->side_a) $dimensions .= $this->side_a."x";
      if($this->order_type->side_b) $dimensions .= $this->side_b."x";
      if($this->order_type->width) $dimensions .= $this->width."x";
      if($this->order_type->thickness) $dimensions .= $this->thickness."x";
      if($this->order_type->large) $dimensions .= $this->large."x";
      $dimensions = rtrim($dimensions, "x");
      if($dimensions != "") $dimensions .= "mm";
      return $dimensions;
    }
}
