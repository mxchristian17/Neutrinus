<?php

use Illuminate\Database\Seeder;
use App\Operation;

class OperationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      for($i=1;$i<=1000;$i++){
        for($j=1;$j<rand(2,6);$j++){
          $operation = new Operation();
          $operation->element_id = $i;
          $operation->order_number = $j;
          $operation->operation_name_id = rand(1,24);
          $operation->observation = 'Observaciones de ruta '.$j;
          $operation->preparation_time = rand(1.00, 120.00);
          $operation->manufacturing_time = rand(1.00, 120.00);
          $operation->cnc_program = 'Programa '.$i.'-'.$j;
          $operation->operation_state_id = rand(1, 4); //1- habilitada, 2- Deshabilitada, 3- Oculta, 4- Eliminada
          $operation->author_id = rand(1, 4);
          $operation->updater_id = rand(1, 4);
          $operation->save();
        }
      }
    }
}
