<?php

use Illuminate\Database\Seeder;
use App\Material_price;

class Material_priceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      for($i=1;$i<7;$i++)
      {
        for($j=1;$j<11;$j++)
        {
          for($k=30;$k<51;$k++)
          {
            $material = new Material_price();
            $material->material_id = $i;
            $material->order_type_id = $j;
            $material->d_ext = $k;
            $material->d_int = $k-1;
            $material->side_a = $k;
            $material->side_b = $k-1;
            $material->width = $k;
            $material->thickness = $k/2;
            $material->price = rand(2.0, 50.0);
            $material->enabled = '1';
            $material->supplier_id = rand(1,10);
            $material->author_id = '1';
            $material->updater_id = '1';
            $material->save();
          }
        }
      }
    }
}
