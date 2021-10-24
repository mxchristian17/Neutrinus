<?php

use Illuminate\Database\Seeder;
use App\Subset;

class SubsetTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $j=0;
        for($i=1;$i<=20;$i++){
          for($j=1;$j<=10;$j++){
            $subset = new Subset();
            $subset->name = 'Subconjunto '.$j;
            $subset->project_id = $i;
            $subset->subset_number = $j;
            $subset->state_id = rand(1, 4);
            $subset->author_id = rand(0, 4);
            $subset->updater_id = rand(0, 4);
            $subset->save();
          }
        }
    }
}
