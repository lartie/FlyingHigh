<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 24.11.2016
 */

use LArtie\Google\Models\Calendar;
use SleepingOwl\Admin\Facades\Admin;
use SleepingOwl\Admin\Facades\Display;
use SleepingOwl\Admin\Facades\TableColumn;
use SleepingOwl\Admin\Model\ModelConfiguration;

Admin::registerModel(Calendar::class, function (ModelConfiguration $model) {

    $model->setTitle('Google Calendars');

    $model->setAlias('flyinghigh/google/calendars');

    $model->onDisplay(function () {

        $display = Display::datatables();

        $display->with('account');

        $display->setColumns(
            TableColumn::text('calendar_id', 'Calendar ID'),
            //TableColumn::text('sync_token', 'Sync Token'),
            TableColumn::text('timezone', 'Timezone'),

            TableColumn::text('account.email')->setLabel('Google Account'),

            TableColumn::datetime('created_at', 'Created')->setFormat('d.m.Y')->setWidth('150px'),
            TableColumn::datetime('updated_at', 'Updated')->setFormat('d.m.Y')->setWidth('150px')
        )->paginate(15);

        return $display;
    });

    $model->onDestroy();
    $model->disableEditing();
});
