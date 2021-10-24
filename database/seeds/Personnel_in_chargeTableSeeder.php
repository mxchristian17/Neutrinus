<?php

use Illuminate\Database\Seeder;
use App\Personnel_in_charge;

class Personnel_in_chargeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $personnel_in_charge = new Personnel_in_charge();
      $personnel_in_charge->user_at_charge_id = 2;
      $personnel_in_charge->user_under_charge_id = 1;
      $personnel_in_charge->state_id = 1;
      $personnel_in_charge->author_id = rand(1, 4);
      $personnel_in_charge->updater_id = rand(1, 4);
      $personnel_in_charge->save();
      $personnel_in_charge = new Personnel_in_charge();
      $personnel_in_charge->user_at_charge_id = 3;
      $personnel_in_charge->user_under_charge_id = 1;
      $personnel_in_charge->state_id = 1;
      $personnel_in_charge->author_id = rand(1, 4);
      $personnel_in_charge->updater_id = rand(1, 4);
      $personnel_in_charge->save();
      $personnel_in_charge = new Personnel_in_charge();
      $personnel_in_charge->user_at_charge_id = 1;
      $personnel_in_charge->user_under_charge_id = 4;
      $personnel_in_charge->state_id = 1;
      $personnel_in_charge->author_id = rand(1, 4);
      $personnel_in_charge->updater_id = rand(1, 4);
      $personnel_in_charge->save();
    }
}
