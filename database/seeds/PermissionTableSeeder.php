<?php

use Illuminate\Database\Seeder;
use App\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      for($j=1;$j<=100;$j++){
        $permission = new Permission();
        $permission->user_id = 1;
        $permission->code_id = $j;
        $permission->state = 1;
        $permission->author_id = 1;
        $permission->updater_id = 1;
        $permission->save();
      }
      for($i=2;$i<=4;$i++){
        for($j=1;$j<=100;$j++){
          $permission = new Permission();
          $permission->user_id = $i;
          $permission->code_id = $j;
          $permission->state = 0;
          $permission->author_id = 1;
      		$permission->updater_id = 1;
          $permission->save();
        }
      }
    }
}
