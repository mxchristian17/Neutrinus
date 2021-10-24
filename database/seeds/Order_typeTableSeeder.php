<?php

use Illuminate\Database\Seeder;
use App\Order_type;

class Order_typeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $order_type = new Order_type();
      $order_type->name = 'Normalizado';
      $order_type->description = 'Corresponde a todos aquellos elementos que se compran manufacturados y no hay ninguna medida o forma que caracterice a los mismos.';
      $order_type->d_ext = 0;
      $order_type->d_int = 0;
      $order_type->side_a = 0;
      $order_type->side_b = 0;
      $order_type->large = 0;
      $order_type->width = 0;
      $order_type->thickness = 0;
      $order_type->formula = '0';
      $order_type->original_formula = '0';
      $order_type->state_id = 1;
      $order_type->author_id = 1;
      $order_type->updater_id = 1;
      $order_type->save();

      $order_type = new Order_type();
      $order_type->name = 'Barra';
      $order_type->description = 'Barra cilíndrica definida por su largo y su diámetro exterior';
      $order_type->d_ext = 1;
      $order_type->d_int = 0;
      $order_type->side_a = 0;
      $order_type->side_b = 0;
      $order_type->large = 1;
      $order_type->width = 0;
      $order_type->thickness = 0;
      $order_type->formula = 'pi()*((d_ext/2)**2)*large';
      $order_type->original_formula = 'π$*$($($Øext$/$2$)$^2$)$*$Largo';
      $order_type->state_id = 1;
      $order_type->author_id = 1;
      $order_type->updater_id = 1;
      $order_type->save();

      $order_type = new Order_type();
      $order_type->name = 'Planchuela';
      $order_type->description = 'Planchuela definida por su largo, ancho y espesor';
      $order_type->d_ext = 0;
      $order_type->d_int = 0;
      $order_type->side_a = 0;
      $order_type->side_b = 0;
      $order_type->large = 1;
      $order_type->width = 1;
      $order_type->thickness = 1;
      $order_type->formula = 'large*width*thickness';
      $order_type->original_formula = 'Largo$*$Ancho$*$Espesor';
      $order_type->state_id = 1;
      $order_type->author_id = 1;
      $order_type->updater_id = 1;
      $order_type->save();

      $order_type = new Order_type();
      $order_type->name = 'Caño';
      $order_type->description = 'Caño cilíndrico definido por su largo, diámetro exterior y diámetro interior';
      $order_type->d_ext = 1;
      $order_type->d_int = 1;
      $order_type->side_a = 0;
      $order_type->side_b = 0;
      $order_type->large = 1;
      $order_type->width = 0;
      $order_type->thickness = 0;
      $order_type->formula = 'pi()*((d_ext/2)**2-(d_int/2)**2)*large';
      $order_type->original_formula = 'π$*$($($Øext$/$2$)$^2$-$($Øint$/$2$)$^2$)$*$Largo';
      $order_type->state_id = 1;
      $order_type->author_id = 1;
      $order_type->updater_id = 1;
      $order_type->save();

      $order_type = new Order_type();
      $order_type->name = 'Chapa';
      $order_type->description = 'Chapa definida por su largo, ancho y espesor';
      $order_type->d_ext = 0;
      $order_type->d_int = 0;
      $order_type->side_a = 0;
      $order_type->side_b = 0;
      $order_type->large = 1;
      $order_type->width = 1;
      $order_type->thickness = 1;
      $order_type->formula = 'large*width*thickness';
      $order_type->original_formula = 'Largo$*$Ancho$*$Espesor';
      $order_type->state_id = 1;
      $order_type->author_id = 1;
      $order_type->updater_id = 1;
      $order_type->save();

      $order_type = new Order_type();
      $order_type->name = 'Barra hexagonal';
      $order_type->description = 'Barra de material con sección hexagonal definida por su largo y la distancia entre dos caras opuestas (Lado A)';
      $order_type->d_ext = 0;
      $order_type->d_int = 0;
      $order_type->side_a = 1;
      $order_type->side_b = 0;
      $order_type->large = 1;
      $order_type->width = 0;
      $order_type->thickness = 0;
      $order_type->formula = 'large*width*thickness';
      $order_type->original_formula = 'Largo$*$Ancho$*$Espesor';
      $order_type->state_id = 1;
      $order_type->author_id = 1;
      $order_type->updater_id = 1;
      $order_type->save();

      $order_type = new Order_type();
      $order_type->name = 'Disco';
      $order_type->description = 'Disco cilíndrico definido por su largo y su diámetro exterior';
      $order_type->d_ext = 1;
      $order_type->d_int = 0;
      $order_type->side_a = 0;
      $order_type->side_b = 0;
      $order_type->large = 1;
      $order_type->width = 0;
      $order_type->thickness = 0;
      $order_type->formula = 'pi()*((d_ext/2)**2)*large';
      $order_type->original_formula = 'π$*$($($Øext$/$2$)$^2$)$*$Largo';
      $order_type->state_id = 1;
      $order_type->author_id = 1;
      $order_type->updater_id = 1;
      $order_type->save();

      $order_type = new Order_type();
      $order_type->name = 'Fundido';
      $order_type->description = 'Piezas que se piden de material fundido donde la forma depende del modelo de fundición.';
      $order_type->d_ext = 0;
      $order_type->d_int = 0;
      $order_type->side_a = 0;
      $order_type->side_b = 0;
      $order_type->large = 0;
      $order_type->width = 0;
      $order_type->thickness = 0;
      $order_type->formula = '0';
      $order_type->original_formula = '0';
      $order_type->state_id = 1;
      $order_type->author_id = 1;
      $order_type->updater_id = 1;
      $order_type->save();

      $order_type = new Order_type();
      $order_type->name = 'Corte laser';
      $order_type->description = 'Piezas que se piden cortadas con laser. Donde la forma depende del corte.';
      $order_type->d_ext = 0;
      $order_type->d_int = 0;
      $order_type->side_a = 0;
      $order_type->side_b = 0;
      $order_type->large = 0;
      $order_type->width = 0;
      $order_type->thickness = 0;
      $order_type->formula = '0';
      $order_type->original_formula = '0';
      $order_type->state_id = 1;
      $order_type->author_id = 1;
      $order_type->updater_id = 1;
      $order_type->save();

      $order_type = new Order_type();
      $order_type->name = 'Corte waterjet';
      $order_type->description = 'Piezas que se piden cortadas con chorro de agua. Donde la forma depende del corte.';
      $order_type->d_ext = 0;
      $order_type->d_int = 0;
      $order_type->side_a = 0;
      $order_type->side_b = 0;
      $order_type->large = 0;
      $order_type->width = 0;
      $order_type->thickness = 0;
      $order_type->formula = '0';
      $order_type->original_formula = '0';
      $order_type->state_id = 1;
      $order_type->author_id = 1;
      $order_type->updater_id = 1;
      $order_type->save();

/*      for($i=0;$i<30;$i++){
        $order_type = new Order_type();
        $order_type->name = 'Order_type '.$i;
        $order_type->description = 'Description '.$i;
        $order_type->d_ext = rand(0,1);
        $order_type->d_int = rand(0,1);
        $order_type->side_a = rand(0,1);
        $order_type->side_b = rand(0,1);
        $order_type->large = rand(0,1);
        $order_type->width = rand(0,1);
        $order_type->thickness = rand(0,1);
        $order_type->formula = 'Formula '.$i;
        $order_type->state_id = rand(1,4);
        $order_type->author_id = rand(1, 4);
        $order_type->updater_id = rand(1, 4);
        $order_type->save();
      }*/
    }
}
