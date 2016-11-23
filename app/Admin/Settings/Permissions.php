<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 24.11.2016
 */

use App\Admin\Models\Permission;
use SleepingOwl\Admin\Facades\Admin;
use SleepingOwl\Admin\Facades\Display;
use SleepingOwl\Admin\Facades\Form;
use SleepingOwl\Admin\Facades\FormElement;
use SleepingOwl\Admin\Facades\TableColumn;
use SleepingOwl\Admin\Model\ModelConfiguration;

Admin::registerModel(Permission::class, function (ModelConfiguration $model) {

    $model->setTitle('Permissions');

    $model->setAlias('permissions/permissions');

    $model->onDisplay(function () {

        $display = Display::datatables();

        $display->setColumns(

            TableColumn::text('id', 'ID'),
            TableColumn::text('name', 'Name'),
            TableColumn::text('display_name', 'Display Name'),
            TableColumn::text('description', 'Description'),

            TableColumn::datetime('created_at', 'Created')->setFormat('d.m.Y')->setWidth('150px'),
            TableColumn::datetime('updated_at', 'Updated')->setFormat('d.m.Y')->setWidth('150px')
        )->paginate(15);

        return $display;
    });

    $model->onDestroy();
    
    $model->onCreateAndEdit(function() {

        $form = Form::form()->setItems([
            FormElement::text('name', 'Name')->required()->unique(),
            FormElement::text('display_name', 'Display Name')->required()->unique(),
            FormElement::text('description', 'Description')->required(),
        ]);

        $form->getButtons()
            ->setSaveButtonText('Save')
            ->hideSaveAndCloseButton();
        return $form;
    });
});