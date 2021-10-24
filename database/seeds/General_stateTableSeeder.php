<?php

use Illuminate\Database\Seeder;
use App\General_state;

class General_stateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $state = new General_state();
      $state->name = 'Habilitado';
      $state->description = 'Elemento visible y considerado para todos los cálculos del sistema';
      $state->author_id = 1;
      $state->updater_id = 1;
      $state->save();

      $state = new General_state();
      $state->name = 'Deshabilitado';
      $state->description = 'Elemento no visible y no considerado para todos los cálculos del sistema';
      $state->author_id = 1;
      $state->updater_id = 1;
      $state->save();

      $state = new General_state();
      $state->name = 'Oculto';
      $state->description = 'Elemento no visible pero considerado para todos los cálculos del sistema';
      $state->author_id = 1;
      $state->updater_id = 1;
      $state->save();

      $state = new General_state();
      $state->name = 'Eliminado';
      $state->description = 'Elemento en papelera de reciclaje';
      $state->author_id = 1;
      $state->updater_id = 1;
      $state->save();
    }
}
