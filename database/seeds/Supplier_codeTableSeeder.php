<?php

use Illuminate\Database\Seeder;
use App\Supplier_code;

class Supplier_codeTableSeeder extends Seeder
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
          $supplier_code = new Supplier_code();
          $supplier_code->element_id = $i;
          $supplier_code->supplier_id = rand(1,30);
          $supplier_code->state_id = rand(1,4);
          $supplier_code->description = 'Observaciones de codigo de proveedor';
          $supplier_code->code = rand(1000000, 9000000);
          $supplier_code->author_id = rand(1, 4);
          $supplier_code->updater_id = rand(1, 4);
          $supplier_code->save();
        }
      }
    }
}
