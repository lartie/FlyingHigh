<?php

namespace App\Repositories;

use App\User;
use App\UserVerification;

/**
 * Class UserVerificationRepository
 * @package App\Repositories
 */
final class UserVerificationRepository
{
    /**
     * @var User
     */
    private $user;

    /**
     * UserVerificationRepository constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return UserVerification
     */
    public function create() : UserVerification
    {
        return $this->user->verify()->create([
            'token' => $this->newToken()
        ]);
    }

    /**
     * @param string $token
     * @return UserVerification
     */
    public function deactivate(string $token)
    {
        return $this->user->verify()->where('token', $token)->update([
            'active' => false
        ]);
    }

    /**
     * @param string $token
     * @return UserVerification
     */
    public function getIfActive(string $token)
    {
        return $this->user->verify()->where('token', $token)->where('active', true)->first();
    }

    /**
     * @param int $len
     * @return string
     */
    private function newToken(int $len = 25) : string
    {
        return str_random($len);
    }
}
