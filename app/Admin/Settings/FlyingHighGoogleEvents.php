<?php

use LArtie\Google\Models\Event;
use SleepingOwl\Admin\Facades\Admin;
use SleepingOwl\Admin\Facades\Display;
use SleepingOwl\Admin\Facades\TableColumn;
use SleepingOwl\Admin\Model\ModelConfiguration;

Admin::registerModel(Event::class, function (ModelConfiguration $model) {

    $model->setTitle('Google Events');

    $model->setAlias('flyinghigh/google/events');

    $model->onDisplay(function () {

        $display = Display::datatables();

        $display->with('calendar');

        $display->setColumns(
            TableColumn::text('from', 'From'),
            TableColumn::text('to', 'To'),
            TableColumn::datetime('start')->setLabel('Start Event')->setFormat('d.m.Y H:i')->setWidth('150px'),
            TableColumn::text('start_timezone', 'Start Offset'),
//            TableColumn::datetime('end')->setLabel('End Event')->setFormat('d.m.Y H:i')->setWidth('150px'),
//            TableColumn::text('end_timezone', 'End Offset'),

            TableColumn::text('calendar.calendar_id', 'Google Calendar'),

            TableColumn::custom()->setLabel('Identified')->setCallback(function (Event $model) {
                return $model->identified ? '<i class="fa fa-check"></i>' : '<i class="fa fa-minus"></i>';
            })->setWidth('50px')->setHtmlAttribute('class', 'text-center')->setOrderable(false)
        )->paginate(15);

        return $display;
    });

    $model->onDestroy();
    $model->disableEditing();
});
