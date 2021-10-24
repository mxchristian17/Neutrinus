<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{

  protected $fillable = array('_token', 'name', 'state_id', 'phone_number', 'email', 'cuit', 'taxpayer_type_id', 'address', 'city', 'province', 'country', 'currency_id', 'description', 'author_id', 'updater_id');

  public function contacts()
  {
      return $this->hasMany('App\Client_contact', 'client_id', 'id');
  }

  public function states()
  {
      return $this->hasOne('App\State', 'id', 'state_id');
  }

  public function author()
  {
      return $this->hasOne('App\User', 'id', 'author_id');
  }

  public function updater()
  {
      return $this->hasOne('App\User', 'id', 'updater_id');
  }

  public function currency()
  {
      return $this->hasOne('App\Currency', 'id', 'currency_id');
  }

  public function getcompleteAddressAttribute()
  {
    if($this->city != '') $this->city= ', '.$this->city;
    if($this->province != '') $this->province= ', '.$this->province;
    if($this->country != '') $this->country= ', '.$this->country;
    return $this->address. $this->city. $this->province. $this->country;
  }

  public function getTaxPayerNameAttribute()
  {
      switch($this->taxpayer_type_id)
      {
        case 1: return 'Monotributista'; break;
        case 2: return 'Responsable Inscripto'; break;
        default: return '';
      }
  }
}
