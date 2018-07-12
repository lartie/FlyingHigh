<?php

use App\FlightNumber;
use SleepingOwl\Admin\Facades\Admin;
use SleepingOwl\Admin\Facades\Display;
use SleepingOwl\Admin\Facades\TableColumn;
use SleepingOwl\Admin\Model\ModelConfiguration;

Admin::registerModel(FlightNumber::class, function (ModelConfiguration $model) {

    $model->setTitle('FlightNumber');

    $model->setAlias('flight/number');

    $model->onDisplay(function () {

        $display = Display::datatables();

        $display->with('flightStatus');

        $display->setColumns(

            TableColumn::text('carrier_code', 'Carrier'),
            TableColumn::text('flight_number', 'Flight Number'),

            TableColumn::text('flightStatus.id', 'Flight Status'),

            TableColumn::datetime('departure_time', 'Departure')->setFormat('d.m.Y H:s')->setWidth('150px'),
            TableColumn::datetime('arrival_time', 'Arrival')->setFormat('d.m.Y H:s')->setWidth('150px')
        )->paginate(15);

        return $display;
    });

    $model->onDestroy();
    $model->disableEditing();
});
