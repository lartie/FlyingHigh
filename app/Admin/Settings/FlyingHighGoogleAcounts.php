<?php

use LArtie\Google\Models\Account;
use SleepingOwl\Admin\Facades\Admin;
use SleepingOwl\Admin\Facades\Display;
use SleepingOwl\Admin\Facades\TableColumn;
use SleepingOwl\Admin\Model\ModelConfiguration;

Admin::registerModel(Account::class, function (ModelConfiguration $model) {

    $model->setTitle('Google Accounts');

    $model->setAlias('flyinghigh/google/accounts');

    $model->onDisplay(function () {

        $display = Display::datatables();

        $display->with('user');

        $display->setColumns(
            TableColumn::text('name', 'Username'),
            TableColumn::email('email', 'Email'),

            TableColumn::text('user.telegram_id', 'User'),

            TableColumn::datetime('created_at', 'Created')->setFormat('d.m.Y')->setWidth('150px'),
            TableColumn::datetime('updated_at', 'Updated')->setFormat('d.m.Y')->setWidth('150px')
        )->paginate(15);

        return $display;
    });

    $model->onDestroy();
    $model->disableEditing();
});
