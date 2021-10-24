<?php

use Illuminate\Database\Seeder;
use App\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_user = new Role();
    		$role_user->name = 'User';
    		$role_user->description = 'System User';
    		$role_user->save();

        $role_admin = new Role();
    		$role_admin->name = 'Admin';
    		$role_admin->description = 'System Administrator';
    		$role_admin->save();

        $role_author = new Role();
    		$role_author->name = 'Author';
    		$role_author->description = 'Ing. Orengia Christian';
    		$role_author->save();
    }
}
