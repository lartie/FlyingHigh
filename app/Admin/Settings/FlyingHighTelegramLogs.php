<?php

use App\TelegramLog;
use SleepingOwl\Admin\Facades\Admin;
use SleepingOwl\Admin\Facades\Display;
use SleepingOwl\Admin\Facades\TableColumn;
use SleepingOwl\Admin\Model\ModelConfiguration;

Admin::registerModel(TelegramLog::class, function (ModelConfiguration $model) {

    $model->setTitle('Telegram Logs');

    $model->setAlias('flyinghigh/telegram/logs');

    $model->onDisplay(function () {

        $display = Display::datatablesAsync();

        $display->setColumns(
            TableColumn::text('id', 'ID'),
            TableColumn::text('from', 'From'),
            TableColumn::text('to', 'To'),
            TableColumn::text('message', 'Message'),
            TableColumn::text('sent_at', 'Sent at')
        );
        return $display;
    });

    $model->onDestroy();
    $model->disableEditing();
});
