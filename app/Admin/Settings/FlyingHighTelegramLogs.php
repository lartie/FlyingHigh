<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 24.11.2016
 */

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