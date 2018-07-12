<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use SleepingOwl\Admin\Templates\TemplateDefault;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        view()->composer($this->getViewPath('_partials.header'), function($view) {
            $view->getFactory()->inject(
                'navbar.right', view('auth.partials.navbar_admin', [
                    'user' => auth()->user()
                ])
            );
        });
    }

    /**
     * @param $view
     * @return string
     */
    public function getViewPath($view)
    {
        return $this->getViewNamespace().'default.'.$view;
    }

    /**
     * @return string
     */
    public function getViewNamespace()
    {
        return 'sleeping_owl::';
    }
}
