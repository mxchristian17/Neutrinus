<?php

use Illuminate\Database\Seeder;
use App\Client;

class ClientTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      for($i=1;$i<=30;$i++){
        $client = new Client();
        $client->name = 'Cliente '.$i;
        $client->state_id = rand(1,4);
        $client->phone_number = 'Nro Teléfono' .$i;
        $client->email = 'Cliente'.$i.'@clientes.com';
        $client->cuit = '##-#######-#';
        $client->taxpayer_type_id = rand(1,2);
        $client->address = 'Dirección cliente '.$i;
        $client->city = 'Ciudad';
        $client->province = 'Provincia';
        $client->country = 'Pais';
        $client->currency_id = rand(1,3);
        $client->description = 'Descripción cliente '.$i;
        $client->author_id = rand(1, 4);
        $client->updater_id = rand(1, 4);
        $client->save();
      }
    }
}
