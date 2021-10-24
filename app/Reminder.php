<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable = array('_token', 'user_id', 'reminder_date', 'activated', 'showed', 'repeat', 'repeat_days_interval', 'title', 'content', 'author_id', 'updater_id');
}
