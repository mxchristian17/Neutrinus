<?php

use Illuminate\Database\Seeder;
use App\Projectelement;

class ProjectelementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i=1;$i<=20;$i++){
          for($j=1;$j<=10;$j++){
            for($k=1;$k<=10;$k++){
              $element = new Projectelement();
              $element->element_id = rand(1, 1000);
              $element->project_id = $i;
              $element->subset_id = ($i-1)*10+$j;
              $element->part = $k;
              $element->subpart = 0;
              $element->version = 0;
              $element->welded_set = 0;
              $element->quantity = rand(1, 10);
              $element->specific_state_id = rand(1, 4); // 1- habilitado 2- deshabilitado 3- oculto 4- eliminado ->lo de la derecha va a ser para elementos en curso //1 - revisar - 2 - Pedir 3 - Pedido - 4 - Produccion - 5 - Finalizado - 6 - Montado - 7 - Corregir
              $element->purchase_order = $i*$j+$k;
              $element->manufacturing_order = $i*$j+$k;
              $element->author_id = rand(1, 4);
              $element->updater_id = rand(1, 4);
              $element->save();
            }
          }
        }
      }
}
