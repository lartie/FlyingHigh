<?php

use App\Event;
use SleepingOwl\Admin\Facades\Admin;
use SleepingOwl\Admin\Facades\Display;
use SleepingOwl\Admin\Facades\TableColumn;
use SleepingOwl\Admin\Model\ModelConfiguration;

Admin::registerModel(Event::class, function (ModelConfiguration $model) {

    $model->setTitle('Event');

    $model->setAlias('flight/event');

    $model->onDisplay(function () {

        $display = Display::datatables();

        $display->setColumns(
            TableColumn::text('type', 'Type'),
            TableColumn::text('value', 'Value'),
            TableColumn::text('data_source', 'Data Source'),

            TableColumn::datetime('datetime_recorded', 'Record')->setFormat('d.m.Y H:s')->setWidth('150px')
        )->paginate(15);

        return $display;
    });

    $model->onDestroy();
    $model->disableEditing();
});
