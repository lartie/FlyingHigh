<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 24.11.2016
 */

use App\Admin\Models\User;
use SleepingOwl\Admin\Facades\Admin;
use SleepingOwl\Admin\Facades\Display;
use SleepingOwl\Admin\Facades\Form;
use SleepingOwl\Admin\Facades\FormElement;
use SleepingOwl\Admin\Facades\TableColumn;
use SleepingOwl\Admin\Model\ModelConfiguration;

Admin::registerModel(User::class, function (ModelConfiguration $model) {

    $model->setTitle('Users');

    $model->setAlias('permissions/users');

    $model->onDisplay(function () {

        $display = Display::datatables();

        $display->with('roles');
//        $display->setFilters(
//            DisplayFilter::related('role_id')->setModel(Role::class),
//            DisplayFilter::field('role.name')->setOperator(\SleepingOwl\Admin\Display\Filter\FilterBase::CONTAINS)
//        );

        $display->setColumns(

            TableColumn::text('id', 'ID'),
            TableColumn::text('name', 'Name'),
            TableColumn::email('email', 'Email'),

            TableColumn::lists('roles.display_name')->setLabel('Roles'),

            TableColumn::datetime('created_at', 'Created')->setFormat('d.m.Y')->setWidth('150px'),
            TableColumn::datetime('updated_at', 'Updated')->setFormat('d.m.Y')->setWidth('150px')
        )->paginate(15);

        return $display;
    });

    $model->onDestroy();
    
    $model->onCreateAndEdit(function() {

        $form = Form::form()->setItems([
            FormElement::text('name', 'Name')->required(),
            FormElement::text('email', 'Email')->required()->unique(),
        ]);

        $form->getButtons()
            ->setSaveButtonText('Save')
            ->hideSaveAndCloseButton();
        return $form;
    });
});