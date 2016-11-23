<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 24.11.2016
 */

namespace App\Console\Commands;

use App\Admin\Models\Role;
use App\Admin\Models\User;
use Illuminate\Console\Command;

/**
 * Class RootAdmin
 * @package App\Console\Commands
 */
class RootAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:root';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make root user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        /** @var Role $role */
        $role = Role::firstOrNew([
            'name' => 'root',
            'display_name' => 'Root',
        ]);

        if (!$role->exists) {
            $role->save();
        }

        /** @var User $user */
        $user = User::firstOrNew([
            'email' => 'log.wil.log@gmail.com',
            'name' => 'Artemy',
        ]);

        if ($user->exists) {
            $this->line('User Already Exists');
            return;
        }

        $password = '46lRbmwW';
        $user->password = $password;
        $user->save();

        $user->attachRole($role);

        $this->line("Email: $user->email \nPassword: $password");
    }
}
