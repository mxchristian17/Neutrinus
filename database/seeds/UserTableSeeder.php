<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		    $role_user = Role::where('name', 'User')->first();
    		$role_author = Role::where('name', 'Author')->first();
    		$role_admin = Role::where('name', 'Admin')->first();

          $user = new User();
      		$user->name = 'Christian';
          $user->last_name = 'Orengia';
      		$user->email = 'orengiachristian@gmail.com';
      		$user->password = bcrypt('7898527342100');
          $user->gender = 'M';
          $user->branch_office = null;
          $user->address = null;
          $user->city = null;
          $user->country = null;
          $user->phone_number = null;
          $user->date_of_birth = null;
          $user->blocked_date = null;
          $user->state_id = 1;
      		$user->save();
      		$user->roles()->attach($role_author);

      		$user = new User();
      		$user->name = 'Gisberto';
      		$user->last_name = 'Solano Garay';
      		$user->email = 'gisberto.garay@neutrinus.com';
      		$user->password = bcrypt('user 2');
          $user->gender = 'M';
          $user->branch_office = null;
          $user->address = null;
          $user->city = null;
          $user->country = null;
          $user->phone_number = null;
          $user->date_of_birth = null;
          $user->blocked_date = null;
          $user->state_id = 1;
      		$user->save();
      		$user->roles()->attach($role_author);

      		$user = new User();
      		$user->name = 'Maximiano';
      		$user->last_name = 'Pantoja Gaitán';
      		$user->email = 'maximiliano.gaitan@neutrinus.com';
      		$user->password = bcrypt('user 3');
          $user->gender = 'O';
          $user->branch_office = null;
          $user->address = null;
          $user->city = null;
          $user->country = null;
          $user->phone_number = null;
          $user->date_of_birth = null;
          $user->blocked_date = null;
          $user->state_id = 1;
      		$user->save();
      		$user->roles()->attach($role_admin);

          $user = new User();
      		$user->name = 'Zina';
      		$user->last_name = 'Corrales Suárez';
      		$user->email = 'zina.suarez@neutrinus.com';
      		$user->password = bcrypt('user 4');
          $user->gender = 'F';
          $user->branch_office = null;
          $user->address = null;
          $user->city = null;
          $user->country = null;
          $user->phone_number = null;
          $user->date_of_birth = null;
          $user->blocked_date = null;
          $user->state_id = 1;
      		$user->save();
      		$user->roles()->attach($role_admin);
    }
}
