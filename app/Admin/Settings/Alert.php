<?php

use App\Alert;
use SleepingOwl\Admin\Facades\Admin;
use SleepingOwl\Admin\Facades\Display;
use SleepingOwl\Admin\Facades\TableColumn;
use SleepingOwl\Admin\Model\ModelConfiguration;

Admin::registerModel(Alert::class, function (ModelConfiguration $model) {

    $model->setTitle('Alert');

    $model->setAlias('flight/alert');

    $model->onDisplay(function () {

        $display = Display::datatables();

        $display->setColumns(
            TableColumn::text('alert_id', 'Alert ID'),
            TableColumn::text('name', 'Name'),
            TableColumn::text('description', 'Description'),

            TableColumn::custom()->setLabel('Active')->setCallback(function (Alert $model) {
                return $model->active ? '<i class="fa fa-check"></i>' : '<i class="fa fa-minus"></i>';
            })->setWidth('50px')->setHtmlAttribute('class', 'text-center')->setOrderable(false)

        )->paginate(15);

        return $display;
    });

    $model->onDestroy();
    $model->disableEditing();
});
