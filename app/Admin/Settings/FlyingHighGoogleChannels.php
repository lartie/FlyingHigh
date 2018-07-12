<?php

use LArtie\Google\Models\Channel;
use SleepingOwl\Admin\Facades\Admin;
use SleepingOwl\Admin\Facades\Display;
use SleepingOwl\Admin\Facades\TableColumn;
use SleepingOwl\Admin\Model\ModelConfiguration;

Admin::registerModel(Channel::class, function (ModelConfiguration $model) {

    $model->setTitle('Google Channels');

    $model->setAlias('flyinghigh/google/channels');

    $model->onDisplay(function () {

        $display = Display::datatables();

        $display->with('user');

        $display->setColumns(
            TableColumn::text('channel_id', 'Channel'),
            TableColumn::text('resource_id', 'Resource ID'),
            TableColumn::datetime('expiration', 'Expiration')->setFormat('d.m.Y H:i:s')->setWidth('150px'),

            TableColumn::text('user.telegram_id', 'User')
        )->paginate(15);

        return $display;
    });

    $model->disableDeleting();
    $model->disableDestroying();
    $model->disableEditing();
});
