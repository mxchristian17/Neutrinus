<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier_code extends Model
{
    protected $fillable = array('_token', 'element_id', 'supplier_id', 'state_id', 'description', 'code', 'author_id', 'updater_id');

    public function element()
    {
        return $this->hasOne('App\Element', 'id', 'element_id');
    }

    public function supplier()
    {
        return $this->hasOne('App\Supplier', 'id', 'supplier_id');
    }

    public function author()
    {
        return $this->hasOne('App\User', 'id', 'author_id');
    }

    public function states()
    {
        return $this->hasOne('App\State', 'id', 'state_id');
    }
}
