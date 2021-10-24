<?php

use Illuminate\Database\Seeder;
use App\Project;

class ProjectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $project = new Project();
        $project->name = 'Motor Ciclo Otto';
        $project->type = 1;
        $project->state_id = 1;
        $project->author_id = 1;
        $project->updater_id = 1;
        $project->save();

        for($i=2;$i<21;$i++){
          $project = new Project();
      		$project->name = 'Proyecto '.$i;
      		$project->type = rand(1, 3);
      		$project->state_id = rand(1, 4);
      		$project->author_id = rand(1, 4);
      		$project->updater_id = rand(1, 4);
      		$project->save();
        }
    }
}
