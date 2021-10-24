<?php

use Illuminate\Database\Seeder;
use App\User_config;

class User_configTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      for($i=1;$i<5;$i++)
      {
        $user = new User_config();
        $user->user_id = $i;
        $user->show_panel_home = 1;
        $user->show_element_general_search = 1;
        $user->save();
      }
    }
}
