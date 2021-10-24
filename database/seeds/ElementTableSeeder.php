<?php

use Illuminate\Database\Seeder;
use App\Element;
use App\Project;

class ElementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i=0;$i<500;$i++){
          $add_mat_cost_val = rand(0,1)*rand(0,6)*100;
          $element = new Element();
          $element->nro = $i;
          $element->add = 0;
          $element->name = 'Elemento '.$i;
          $element->description = 'Descripción elemento '.$i;
          $element->material_id = rand(1, 6);
          $element->shared_material = rand(0,1) == 1;
          $element->order_type_id = rand(1, 10);
          $element->d_ext = rand(30, 50);
          $element->d_int = $element->d_ext-1;
          $element->side_a = $element->d_ext;
          $element->side_b = $element->d_ext-1;
          $element->large = 2*$element->d_ext;
          $element->width = $element->d_ext;
          $element->thickness = $element->d_ext/2;
          $element->quantity_per_manufacturing_series = rand(10,20);
          $element->general_state_id = rand(1, 4); // 1- habilitado 2- deshabilitado 3- oculto 4- eliminado
          $element->additional_material_cost = $add_mat_cost_val;
          $element->additional_material_cost_date = null;
          $element->calculated_material_cost = 0;
          $element->calculated_material_cost_date = null;
          $element->sale_price = $add_mat_cost_val*1.3;
          $element->author_id = rand(1, 4);
          $element->updater_id = rand(1, 4);
          $element->save();
          //$element->projects()->attach(Project::where('id', rand(1,20))->first());

          $element = new Element();
          $element->nro = $i;
          $element->add = 1;
          $element->name = 'Elemento '.$i;
          $element->material_id = rand(1, 4);
          $element->shared_material = rand(0,1) == 1;
          $element->order_type_id = rand(1, 4);
          $element->d_ext = rand(30, 50);
          $element->d_int = $element->d_ext-1;
          $element->side_a = $element->d_ext;
          $element->side_b = $element->d_ext-1;
          $element->large = 2*$element->d_ext;
          $element->width = $element->d_ext;
          $element->thickness = $element->d_ext/2;
          $element->quantity_per_manufacturing_series = rand(10,20);
          $element->general_state_id = rand(1, 4); // 1- habilitado 2- deshabilitado 3- oculto 4- eliminado
          $element->additional_material_cost = $add_mat_cost_val;
          $element->calculated_material_cost = 0;
          $element->sale_price = $add_mat_cost_val*1.3;
          $element->author_id = rand(1, 4);
          $element->updater_id = rand(1, 4);
          $element->save();
          //$element->projects()->attach(Project::where('id', rand(1,20))->first());
        }

    }
}
