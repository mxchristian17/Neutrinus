<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = array('_token', 'user_id', 'sale_id', 'element_id', 'project_id', 'task_start', 'task_estimated_end', 'activated', 'percentage', 'showed', 'repeat', 'repeat_days_interval', 'title', 'content', 'author_id', 'updater_id');

    public function author()
    {
        return $this->hasOne('App\User', 'id', 'author_id');
    }

    public function getBgColorAttribute()
    {
      switch(true)
      {
        case ($this->percentage <=33): return 'bg-danger'; break;
        case ($this->percentage >33 AND $this->percentage <=66): return 'bg-warning text-dark'; break;
        case ($this->percentage >66): return 'bg-success'; break;
      }
    }
}
