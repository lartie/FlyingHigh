<?php

use LArtie\Airports\Models\City;
use SleepingOwl\Admin\Facades\Admin;
use SleepingOwl\Admin\Facades\Display;
use SleepingOwl\Admin\Facades\TableColumn;
use SleepingOwl\Admin\Model\ModelConfiguration;

Admin::registerModel(City::class, function (ModelConfiguration $model) {

    $model->setTitle('Cities');

    $model->setAlias('airports/cities');

    $model->onDisplay(function () {

        $display = Display::datatablesAsync();

        $display->with('country');

        $display->setColumns(
            TableColumn::text('name_en', 'English'),
            TableColumn::text('name_ru', 'Russian'),

            TableColumn::text('country.name_en', 'Country')
        )->paginate(15);

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
