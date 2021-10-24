<?php

use Illuminate\Database\Seeder;
use App\Client_contact;

class Client_contactTableSeeder extends Seeder
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
          $client_contact = new Client_contact();
          $client_contact->client_id = $i;
          $client_contact->name = 'Contacto '.$j;
          $client_contact->email = 'contacto_cliente'.$i.'_'.$j.'@clientes.com';
          $client_contact->phone_number = 'Nro Teléfono' .$i.$j;
          $client_contact->state_id = rand(1,4);
          $client_contact->description = 'Descripción cliente '.$i.$j;
          $client_contact->author_id = rand(1, 4);
          $client_contact->updater_id = rand(1, 4);
          $client_contact->save();
        }
      }
    }
}
