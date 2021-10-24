<?php

use Illuminate\Database\Seeder;
use App\Reminder;
use Carbon\Carbon;

class ReminderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $reminder = new Reminder();
      $reminder->user_id = 1;
      $reminder->reminder_date = Carbon::now();
      $reminder->activated = 1;
      $reminder->showed = 0;
      $reminder->repeat = 0;
      $reminder->repeat_days_interval = 2;
      $reminder->title = 'Título de recordatorio';
      $reminder->content = 'Contenido del recordatorio...';
      $reminder->author_id = rand(1, 4);
      $reminder->updater_id = rand(1, 4);
      $reminder->save();

      $reminder = new Reminder();
      $reminder->user_id = 1;
      $reminder->reminder_date = Carbon::now();
      $reminder->activated = 1;
      $reminder->showed = 0;
      $reminder->repeat = 1;
      $reminder->repeat_days_interval = 1;
      $reminder->title = 'Título de recordatorio';
      $reminder->content = 'Contenido del recordatorio...';
      $reminder->author_id = rand(1, 4);
      $reminder->updater_id = rand(1, 4);
      $reminder->save();
    }
}
