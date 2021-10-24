<?php

use Illuminate\Database\Seeder;
use App\Purchase_element;

class Purchase_elementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      for($i=1;$i<40;$i++)
      {
        for($j=1;$j<30;$j++)
        {
            $purchase_element = new Purchase_element();
            $purchase_element->purchase_id = $i;
            $purchase_element->element_id = rand(1,500);
            $purchase_element->quantity = rand(1,25);
            $purchase_element->definition_status = rand(0,2);
            $purchase_element->quantity_received = rand(1,4);
            $purchase_element->save();
        }
      }
    }
}
