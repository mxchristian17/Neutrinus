<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('editPermissions', function($user) {
            $is_authorized = $user->roles->where('name', 'Admin')->count();
            $is_authorized = $is_authorized + $user->roles->where('name', 'Author')->count();
            return $is_authorized;
        });
        Gate::define('seeUsersInformation', function($user) {
            $is_authorized = $user->roles->where('name', 'Admin')->count();
            $is_authorized = $is_authorized + $user->roles->where('name', 'Author')->count();
            return $is_authorized;
        });
        Gate::define('editUserStatus', function($user) {
            $is_authorized = $user->roles->where('name', 'Admin')->count();
            $is_authorized = $is_authorized + $user->roles->where('name', 'Author')->count();
            return $is_authorized;
        });
        Gate::define('editUsers', function($user) {
            $is_authorized = $user->roles->where('name', 'Admin')->count();
            $is_authorized = $is_authorized + $user->roles->where('name', 'Author')->count();
            return $is_authorized;
        });
        Gate::define('deleteUsers', function($user) {
            $is_authorized = $user->roles->where('name', 'Admin')->count();
            $is_authorized = $is_authorized + $user->roles->where('name', 'Author')->count();
            return $is_authorized;
        });
        //
    }
}
