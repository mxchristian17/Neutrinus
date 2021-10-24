<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Subset;

class Projectelement extends Model
{

  protected $fillable = array('_token', 'element_id', 'project_id', 'subset_id', 'part', 'subpart', 'version', 'welded_set', 'quantity', 'specific_state_id', 'purchase_order', 'manufacturing_order', 'author_id', 'updater_id');

  public function element()
  {
      return $this->belongsTo('App\Element')->withDefault();
  }

  public function project()
  {
      return $this->belongsTo('App\Project')->withDefault();
  }

  public function author(){
    return $this->belongsTo('App\User')->withDefault();
  }

  public function subset(){
    return $this->belongsTo('App\Subset')->withDefault();
  }

  public function specific_state()
  {
      return $this->belongsTo('App\Specific_state')->withDefault();
  }

  public function getSubsetNumberAttribute()
  {
    $subset = Subset::find($this->subset_id);
    return $subset->subset_number;
  }

  public function getProjectCodeAttribute()
  {
    return str_pad($this->project_id, 3, '0', STR_PAD_LEFT).'-'.str_pad($this->subsetNumber, 2, '0', STR_PAD_LEFT).'-'.str_pad($this->part, 2, '0', STR_PAD_LEFT).'-'.$this->subpart.'-'.$this->version;
  }

}
