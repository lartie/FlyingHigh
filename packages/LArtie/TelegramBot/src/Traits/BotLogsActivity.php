<?php

namespace LArtie\TelegramBot\Traits;

use App\TelegramLog;
use Carbon\Carbon;
use Telegram\Bot\Objects\Message;

/**
 * Class BotLogsActivity
 * @package LArtie\TelegramBot\Traits
 */
trait BotLogsActivity
{
    /**
     * @param Message $message
     * @param string $response
     * @return TelegramLog
     */
    private function writeLog($message, string $response = '')
    {
        if ($message instanceof Message) {
            $logData = $this->prepareLogData($message, $response);
            return TelegramLog::create($logData);
        }
    }

    /**
     * @param $chatMessage
     * @param string $response
     * @return array
     */
    private function prepareLogData($chatMessage, $response = '')
    {
        if (empty($response)) {
            $from_id = $chatMessage->getFrom()->getId();
            $to_id = 'TelegramBot';
            $message = $chatMessage->getText();
        } else {
            $to_id = $chatMessage->getChat()->getId();
            $from_id = 'TelegramBot';
            $message = $response;
        }

        $sent_at = $this->getDateTime($chatMessage->getDate());

        return [
            'from_id' => $from_id,
            'to_id' => $to_id,
            'message' => isset($message) ? $message : 'empty',
            'sent_at' => $sent_at,
        ];
    }

    /**
     * @param $date
     * @return string
     */
    private function getDateTime($date)
    {
        $carbon = new Carbon();

        return $carbon->createFromTimestamp($date)->toDateTimeString();
    }
}
