<?php

use Illuminate\Database\Seeder;
use App\Purchase;

class PurchaseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      for($i=1;$i<40;$i++)
      {
            $day = rand(1,20);
            $month = rand(1,12);
            $year = '2020';
            $dt = DateTime::createFromFormat("d/m/Y", $day.'/'.$month.'/'.$year );
            $dt2 = DateTime::createFromFormat("d/m/Y", ($day+rand(0,4)).'/'.$month.'/'.$year );
            $dt3 = DateTime::createFromFormat("d/m/Y", ($day+rand(4,8)).'/'.$month.'/'.$year );
            if(rand(0,3) == 0)
            {
              $origStatus = rand(0,6);
            }else{
              $origStatus = 5;
            }
            $quoteQuantity = rand(2,4);
            if($origStatus>1) $kasigned = rand (1,$quoteQuantity);
            for($k=1;$k<$quoteQuantity;$k++){
              if($origStatus>1)
                {
                if($k != $kasigned)
                {
                  $status = 6;
                }else{
                  $status = $origStatus;
                }
              }else{
                $status = $origStatus;
              }
              $purchase = new Purchase();
              $purchase->order_number = $i;
              $purchase->supplier_id = rand(1,10);
              $purchase->emitted_date = $dt;
              $purchase->requested_delivery_date = $dt2;
              $purchase->effective_delivery_date = $dt3;
              $purchase->observations = 'Observación '.$i;
              $purchase->emitter_id = rand(1,4);
              $purchase->recipient_id = rand(1,4);
              $purchase->order_receipt_observations = 'Observaciones de la recepción de la órden '.$i;
              $purchase->status = $status;
              $purchase->quotedValue = rand(0,1)*rand(0,1000.00);
              $purchase->author_id = '1';
              $purchase->updater_id = '1';
              $purchase->save();
            }
      }
    }
}
