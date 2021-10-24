<?php

use Illuminate\Database\Seeder;
Use App\Supplier;

class SupplierTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      for($i=1;$i<=30;$i++){
        $supplier = new Supplier();
        $supplier->name = 'Proveedor '.$i;
        $supplier->state_id = rand(1,4);
        $supplier->phone_number = 'Nro Teléfono' .$i;
        $supplier->email = 'proveedor'.$i.'@proveedores.com';
        $supplier->cuit = '##-#######-#';
        $supplier->taxpayer_type_id = rand(1,2);
        $supplier->address = 'Dirección proveedor '.$i;
        $supplier->city = 'Ciudad';
        $supplier->province = 'Provincia';
        $supplier->country = 'Pais';
        $supplier->currency_id = rand(1,3);
        $supplier->description = 'Descripción proveedor '.$i;
        $supplier->author_id = rand(1, 4);
        $supplier->updater_id = rand(1, 4);
        $supplier->save();
      }
    }
}
