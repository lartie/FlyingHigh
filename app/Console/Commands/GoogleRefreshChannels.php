<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 24.11.2016
 */

namespace App\Console\Commands;

use Carbon\Carbon;
use Google_Auth_Exception;
use Google_Service_Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use App\Core\GoogleAccount;
use App\User;

/**
 * Class GoogleRefreshChannels
 * @package App\Console\Commands
 */
final class GoogleRefreshChannels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:rc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh google channels';

    /**
     * Create a new command instance.
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
        $users = $this->getUsers();
        $nextDateUpdate = $this->nextDateUpdate()->toDateTimeString();

        try {
            foreach ($users as $user) {
                if ($user->googleAccount) {
                    if (is_null($user->googleChannel)) {
                        $this->reconnect($user);
                    } else {
                        foreach ($user->googleChannel as $channel) {
                            if ($channel->expiration < $nextDateUpdate) {
                                $this->reconnect($user);
                            }
                        }
                    }
                }
            }
        } catch (Google_Service_Exception $e) {
            Log::critical('Error: ' . $e->getCode() . ' ' . $e->getMessage() . ' File: ' . $e->getFile() . ' Line: ' . $e->getLine());
        } catch (Google_Auth_Exception $e) {
            Log::critical('Error: ' . $e->getCode() . ' ' . $e->getMessage() . ' File: ' . $e->getFile() . ' Line: ' . $e->getLine());
        }

        Log::alert('[' . __CLASS__ . '] The channels have been updated successfully.');
    }

    /**
     * @param User $user
     */
    private function reconnect(User $user)
    {
        $account = new GoogleAccount($user);

        $account->deleteChannel();
        $account->addChannel();
    }

    /**
     * @return Collection|\App\User[]
     */
    private function getUsers() : Collection
    {
        return User::with('googleChannel')->with('googleAccount')->get();
    }

    /**
     * @return Carbon
     */
    private function nextDateUpdate() : Carbon
    {
        $carbon = new Carbon();
        $carbon->addDays(5);

        return $carbon;
    }
}
