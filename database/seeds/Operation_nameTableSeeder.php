<?php

use Illuminate\Database\Seeder;
use App\Operation_name;

class Operation_nameTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      for($i=1;$i<=25;$i++){
        $operation_name = new Operation_name();
        $operation_name->name = 'Ruta '.$i;
        $operation_name->description = 'Descripcion Ruta '.$i;
        $operation_name->usd_for_hour = rand(0.0, 10.0);
        $operation_name->state_id = rand(1,4);
        $operation_name->author_id = rand(1, 4);
        $operation_name->updater_id = rand(1, 4);
        $operation_name->save();
      }
    }
}
