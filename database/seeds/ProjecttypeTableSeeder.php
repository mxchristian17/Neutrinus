<?php

use Illuminate\Database\Seeder;
use App\Projecttype;

class ProjecttypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $projecttype = new Projecttype();
      $projecttype->name = 'Fabricación por proyecto';
      $projecttype->save();

      $projecttype = new Projecttype();
      $projecttype->name = 'Fabricación en serie';
      $projecttype->save();

      $projecttype = new Projecttype();
      $projecttype->name = 'Comercialización de piezas';
      $projecttype->save();
    }
}
