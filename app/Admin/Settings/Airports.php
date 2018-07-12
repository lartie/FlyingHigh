<?php

use LArtie\Airports\Models\Airport;
use SleepingOwl\Admin\Facades\Admin;
use SleepingOwl\Admin\Facades\Display;
use SleepingOwl\Admin\Facades\TableColumn;
use SleepingOwl\Admin\Model\ModelConfiguration;

Admin::registerModel(Airport::class, function (ModelConfiguration $model) {

    $model->setTitle('Airports');

    $model->setAlias('airports/list');

    $model->onDisplay(function () {

        $display = Display::datatablesAsync();

        $display->with('city');

        $display->setColumns(
            TableColumn::text('iata_code', 'IATA'),
            TableColumn::text('icao_code', 'ICAO'),
            TableColumn::text('gmt_offset', 'GMT Offset'),
            TableColumn::text('timezone', 'Timezone'),
            TableColumn::text('city.name_en', 'City')
        )->paginate();

        return $display;
    });
//
//    $model->onDestroy();
//
//    $model->onCreateAndEdit(function() {
//
//        $form = Form::form()->setItems([
//            FormElement::text('name', 'Name')->required(),
//            FormElement::text('email', 'Email')->required()->unique(),
//        ]);
//
//        $form->getButtons()
//            ->setSaveButtonText('Save')
//            ->hideSaveAndCloseButton();
//        return $form;
//    });
});
