<?php

namespace App\Wrappers;

use Google_Client;
use LArtie\Google\Models\Token;

/**
 * Class GoogleApiWrapper
 * @package App\Wrappers
 */
final class GoogleApiWrapper
{
    /**
     * Инициализация клиента для работы с google api
     *
     * @return Google_Client
     */
    public static function getClient() : Google_Client
    {
        $client = new Google_Client();

        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));

        return $client;
    }

    /**
     * Генерация массива токена для google клиента
     *
     * @param Token $token
     * @return array
     */
    public static function generateAccessToken(Token $token) : array
    {
        return [
            'access_token' => $token->access_token,
            'refresh_token' => $token->refresh_token,
            'id_token' => $token->id_token,
            'created' => time(),
            'token_type' => $token->token_type,
            'expires_in' => 0,
        ];
    }

    /**
     * @return string
     */
    public static function getGoogleWebhookUri() : string
    {
        return route('telegram.flyinghighbot.google.webhook');
    }
}
