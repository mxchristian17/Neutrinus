<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Material_price extends Model
{
    protected $fillable = array('_token', 'material_id', 'order_type_id', 'd_ext', 'd_int', 'side_a', 'side_b', 'large', 'width', 'thickness', 'price', 'enabled', 'supplier_id', 'author_id', 'updater_id');

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

    public function supplier()
    {
        return $this->hasOne('App\Supplier', 'id', 'supplier_id');
    }

    public function author()
    {
        return $this->hasOne('App\User', 'id', 'author_id');
    }

    public function updater()
    {
        return $this->hasOne('App\User', 'id', 'updater_id');
    }

}
