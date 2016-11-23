<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 24.11.2016
 */

use App\User;
use SleepingOwl\Admin\Facades\Admin;
use SleepingOwl\Admin\Facades\Display;
use SleepingOwl\Admin\Facades\TableColumn;
use SleepingOwl\Admin\Model\ModelConfiguration;

Admin::registerModel(User::class, function (ModelConfiguration $model) {

    $model->setTitle('Telegram Users');

    $model->setAlias('flyinghigh/telegram/users');

    $model->onDisplay(function () {

        $display = Display::datatables();

        $display->setColumns(
            TableColumn::link('telegram_id', 'Telegram ID'),
            TableColumn::link('username', 'Username'),
            TableColumn::text('first_name', 'First Name'),
            TableColumn::text('last_name', 'Last Name'),

            TableColumn::datetime('created_at', 'Created')->setFormat('d.m.Y')->setWidth('150px'),
            TableColumn::datetime('updated_at', 'Updated')->setFormat('d.m.Y')->setWidth('150px')
        )->paginate(15);

        return $display;
    });

    $model->onDestroy();
    $model->disableEditing();
});