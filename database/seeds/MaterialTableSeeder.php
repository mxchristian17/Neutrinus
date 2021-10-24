<?php

use Illuminate\Database\Seeder;
use App\Material;

class MaterialTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $material = new Material();
        $material->name = 'Indefinido';
        $material->initials = 'Indef.';
        $material->description = 'Utilizado para elementos que no tienen definido un material o son normalizados y tienen varios materiales que lo componen';
        $material->specific_weight = '0';
        $material->state_id = '1';
        $material->author_id = '1';
        $material->updater_id = '1';
        $material->save();

        $material = new Material();
        $material->name = 'Acero SAE 1010';
        $material->initials = 'SAE1010';
        $material->description = 'Acero SAE 1010';
        $material->specific_weight = '7800';
        $material->state_id = '1';
        $material->author_id = '1';
        $material->updater_id = '1';
        $material->save();

        $material = new Material();
        $material->name = 'Acero SAE 1045';
        $material->initials = 'SAE1045';
        $material->description = 'Acero SAE 1045';
        $material->specific_weight = '7800';
        $material->state_id = '1';
        $material->author_id = '1';
        $material->updater_id = '1';
        $material->save();

        $material = new Material();
        $material->name = 'Acero SAE 4140';
        $material->initials = 'SAE4140';
        $material->description = 'Acero SAE 4140';
        $material->specific_weight = '7800';
        $material->state_id = '1';
        $material->author_id = '1';
        $material->updater_id = '1';
        $material->save();

        $material = new Material();
        $material->name = 'Bronce SAE 65';
        $material->initials = 'SAE65';
        $material->description = 'El Bronce UNS C90700 o también llamado SAE 65, es conocido como Bronces de engranes y coronas resistentes a la corrosión. Adecuado para válvulas y cajas de bombas, cojinetes, tornillo sin fin, cuando el servicio es pesado y es necesario un bronce muy duro para mediana velocidad. Es de gran calidad antifriccional así como también de muy buena elasticidad y conductibilidad eléctrica. Soporta altas temperaturas sin perder sus propiedades mecánicas.

Su resistencia de tensión es de 2,450 kg/cm2 y posee una dureza de 65 y 90 Brinell.

El Bronce UNS C90700 | SAE 65 | C907 se emplea en:
Válvulas
Cajas de bombas
Cojinetes
Tornillo sin fin
Engranes
Coronas
Cuando el servicio es pesado y es necesario un bronce muy duro para mediana velocidad';
        $material->specific_weight = '8800';
        $material->state_id = '1';
        $material->author_id = '1';
        $material->updater_id = '1';
        $material->save();

        $material = new Material();
        $material->name = 'Grilón';
        $material->initials = 'Grilón';
        $material->description = 'Termoplástico obtenido a partir de la poliamida 6, que difiere de los plásticos de uso corriente por sus excelentes propiedades mecánicas, eléctricas, térmicas, químicas y la posibilidad de ser modificado con aditivos (MoS2).';
        $material->specific_weight = '1140';
        $material->state_id = '1';
        $material->author_id = '1';
        $material->updater_id = '1';
        $material->save();

      /*for($i=1;$i<=29;$i++){
        $material = new Material();
        $material->name = 'Material '.$i;
        $material->description = 'Color '.$i;
        $material->specific_weight = rand(1.00, 10000.00);
        $material->state_id = rand(1,4);
        $material->author_id = rand(1, 4);
        $material->updater_id = rand(1, 4);
        $material->save();
      }*/
    }
}
