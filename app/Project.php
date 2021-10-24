<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    // protected $table = 'admin.usuarios'; //Esta linea es necesaria si en la bd la tabla no es el plural del nombre de la clase
    protected $fillable = array('_token', 'name', 'type', 'state_id', 'author_id', 'updater_id');

    protected $attributes = [
        'name' => 'Proyecto eliminado o no existente',
        'type' => 0,
        'state_id' => 1,
        'author_id' => 1,
        'updater_id' => 1,
    ];

    public function author()
    {
        return $this->hasOne('App\User', 'id', 'author_id');
    }

    public function updater()
    {
        return $this->hasOne('App\User', 'id', 'updater_id');
    }

    public function states()
    {
        return $this->hasOne('App\State', 'id', 'state_id');
    }

    public function projecttypes()
    {
        return $this->hasOne('App\Projecttype', 'id', 'type');
    }

    public function projectelements()
    {
        return $this->hasMany('App\Projectelement', 'project_id', 'id');
    }

    public function subsets()
    {
        return $this->hasMany('App\Subset', 'project_id', 'id');
    }

}
