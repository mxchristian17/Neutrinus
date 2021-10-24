<?php

use Illuminate\Database\Seeder;
use App\Tax;

class TaxTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      for($i=1;$i<=30;$i++){

        $day = rand(1,20);
        $month = rand(1,12);
        $year = '2021';
        $dt = DateTime::createFromFormat("d/m/Y", $day.'/'.$month.'/'.$year );

        $tax = new Tax();
        $tax->provider_id = rand(0,20);
        $tax->state_id = rand(1,4);
        $tax->description = 'Descripción impuesto '.$i;
        $tax->currency_id = rand(1,3);
        $tax->tax_amount = rand(1,200.0);
        $tax->due_date = $dt;
        $tax->status = rand(0,1);
        $tax->author_id = rand(1, 4);
        $tax->updater_id = rand(1, 4);
        $tax->save();
      }
    }
}
