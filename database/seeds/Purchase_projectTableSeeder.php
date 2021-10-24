<?php

use Illuminate\Database\Seeder;
use App\Purchase_project;

class Purchase_projectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      for($i=1;$i<200;$i++)
      {
        for($j=1;$j<rand(1,4);$j++)
        {
            $purchase_project = new Purchase_project();
            $purchase_project->purchase_id = $i;
            $purchase_project->project_id = rand(1,20);
            $purchase_project->save();
        }
      }
    }
}
