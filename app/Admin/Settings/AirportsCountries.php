<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 24.11.2016
 */

use LArtie\Airports\Models\Country;
use SleepingOwl\Admin\Facades\Admin;
use SleepingOwl\Admin\Facades\Display;
use SleepingOwl\Admin\Facades\TableColumn;
use SleepingOwl\Admin\Model\ModelConfiguration;

Admin::registerModel(Country::class, function (ModelConfiguration $model) {

    $model->setTitle('Countries');

    $model->setAlias('airports/countries');

    $model->onDisplay(function () {

        $display = Display::datatablesAsync();

//        $display->with('roles');
////        $display->setFilters(
////            DisplayFilter::related('role_id')->setModel(Role::class),
////            DisplayFilter::field('role.name')->setOperator(\SleepingOwl\Admin\Display\Filter\FilterBase::CONTAINS)
////        );

        $display->setColumns(
            TableColumn::text('name_en', 'English'),
            TableColumn::text('name_ru', 'Russian'),
            TableColumn::text('iso_code', 'ISO'),

            TableColumn::datetime('created_at', 'Created')->setFormat('d.m.Y')->setWidth('150px'),
            TableColumn::datetime('updated_at', 'Updated')->setFormat('d.m.Y')->setWidth('150px')
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