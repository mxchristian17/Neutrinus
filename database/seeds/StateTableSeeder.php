<?php

use Illuminate\Database\Seeder;
use App\State;

class StateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $state = new State();
        $state->name = 'Habilitado';
        $state->description = 'Este elemento se encuentra habilitado y será contemplado en todos los cálculos, eventos y visualizaciones';
        $state->author_id = rand(1, 4);
        $state->updater_id = rand(1, 4);
        $state->save();

        $state = new State();
        $state->name = 'Deshabilitado';
        $state->description = 'Este elemento se encuentra deshabilitado y por lo tanto no será contemplado en todos los calculos y eventos. Pero si se permite su visualización';
        $state->author_id = rand(1, 4);
        $state->updater_id = rand(1, 4);
        $state->save();

        $state = new State();
        $state->name = 'Oculto';
        $state->description = 'Este elemento se encuentra activo, pero oculto, y por lo tanto solo se podrá ver con permisos especiales';
        $state->author_id = rand(1, 4);
        $state->updater_id = rand(1, 4);
        $state->save();

        $state = new State();
        $state->name = 'Eliminado';
        $state->description = 'Este elemento fue eliminado, y por lo tanto se encuentra en la papelera de reciclaje';
        $state->author_id = rand(1, 4);
        $state->updater_id = rand(1, 4);
        $state->save();
    }
}
