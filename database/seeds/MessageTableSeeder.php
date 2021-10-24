<?php

use Illuminate\Database\Seeder;
use App\Message;

class MessageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      for($j=1;$j<20;$j++){
        for($i=1;$i<=4;$i++){
          for($n=1;$n<5;$n++){
            if($i != $n)
            {
              $message = new Message();
              $message->user_sender_id = $i;
              $message->user_receiver_id = $n;
              $message->message = 'Mensaje de demostración numero '.$j.' del usuario '.$i.' al usuario '.$n;
              $message->status = 0;
              $message->seen = null;
              $message->author_id = $i;
              $message->updater_id = $i;
              $message->save();
              if(rand(0,1))
              {
                $message = new Message();
                $message->user_sender_id = $n;
                $message->user_receiver_id = $i;
                $message->message = 'Mensaje de demostración de respuesta de usuario numero '.$j.' del usuario '.$n.' al usuario '.$i;
                $message->status = 0;
                $message->seen = null;
                $message->author_id = $n;
                $message->updater_id = $n;
                $message->save();
              }
            }
          }
        }
      }
    }
}
