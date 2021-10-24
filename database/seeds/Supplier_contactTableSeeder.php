<?php

use Illuminate\Database\Seeder;
use App\Supplier_contact;

class Supplier_contactTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      for($i=1;$i<=30;$i++){
        for($j=1;$j<=5;$j++){
          $supplier_contact = new Supplier_contact();
          $supplier_contact->supplier_id = $i;
          $supplier_contact->name = 'Contacto '.$j;
          $supplier_contact->email = 'contacto_proveedor'.$i.'_'.$j.'@proveedores.com';
          $supplier_contact->phone_number = 'Nro Teléfono' .$i.$j;
          $supplier_contact->state_id = rand(1,4);
          $supplier_contact->description = 'Descripción proveedor '.$i.$j;
          $supplier_contact->author_id = rand(1, 4);
          $supplier_contact->updater_id = rand(1, 4);
          $supplier_contact->save();
        }
      }
    }
}
