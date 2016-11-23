<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 24.11.2016
 */

use App\FlightStatus;
use SleepingOwl\Admin\Facades\Admin;
use SleepingOwl\Admin\Facades\Display;
use SleepingOwl\Admin\Facades\TableColumn;
use SleepingOwl\Admin\Model\ModelConfiguration;

Admin::registerModel(FlightStatus::class, function (ModelConfiguration $model) {

    $model->setTitle('FlightStatus');

    $model->setAlias('flight/status');

    $model->onDisplay(function () {

        $display = Display::datatables();

        $display->setColumns(
            TableColumn::text('departure_iata', 'D Iata'),
            TableColumn::text('arrival_iata', 'A Iata'),

            TableColumn::text('departure_terminal', 'D Term'),
            TableColumn::text('arrival_terminal', 'A Term'),

            TableColumn::text('departure_gate', 'D Gate'),
            TableColumn::text('arrival_gate', 'A Gate'),

//            TableColumn::datetime('departure_date_local', 'D Loc')->setFormat('d.m.Y H:s')->setWidth('150px'),
//            TableColumn::datetime('arrival_date_local', 'A Date Loc')->setFormat('d.m.Y H:s')->setWidth('150px'),

            TableColumn::datetime('departure_date_utc', 'D Date UTC')->setFormat('d.m.Y H:s')->setWidth('150px'),
            TableColumn::datetime('arrival_date_utc', 'A Date UTC')->setFormat('d.m.Y H:s')->setWidth('150px')
        )->paginate(15);

        return $display;
    });

    $model->onDestroy();
    $model->disableEditing();
});