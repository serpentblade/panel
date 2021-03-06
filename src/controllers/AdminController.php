<?php

namespace Serverfireteam\Panel;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Serverfireteam\Panel\CrudController;

use Hash;
use Request;
/**
 * Description of PagePanel
 *
 * @author alireza
 */
class AdminController extends CrudController{

    public function all($entity){
        parent::all($entity);

        $this->filter = \DataFilter::source(Admin::with('roles'));
        $this->filter->add('id', 'ID', 'text');
        $this->filter->add('first_name', 'First name', 'text');
        $this->filter->add('last_name', 'Last Name', 'text');
        $this->filter->add('email', 'Email', 'text');
        $this->filter->submit('search');
        $this->filter->reset('reset');
        $this->filter->build();

        $this->grid = \DataGrid::source($this->filter);
        $this->grid->add('id','ID', true)->style("width:100px");
        $this->grid->add('{{ $first_name }} {{ $last_name}}','first name');
        $this->grid->add('email','Email');
        $this->grid->add('{{ implode(", ", $roles->pluck("name")->all()) }}', 'Role');

        $this->addStylesToGrid();
        return $this->returnView();
    }

    public function  edit($entity){

        $showPassword = true;
        $pass = Request::input('password');

        if (trim($pass))
        {
            $new_input = array('password' => Hash::make($pass));
            Request::merge($new_input);
        }
        else
        {
            Request::merge(['password' => null]);

            if(in_array(Request::method(), ['PATCH', 'POST', 'PUT']))
            {
                $showPassword = false;
            }
        }

        parent::edit($entity);

        $this->edit = \DataEdit::source(new Admin());

        $this->edit->label('Edit Admin');
        $this->edit->link("rapyd-demo/filter","Articles", "TR")->back();
        $this->edit->add('email','Email', 'text')->rule('required|min:5');
        $this->edit->add('first_name', 'firstname', 'text');
        $this->edit->add('last_name', 'lastname', 'text');

        // hate to do this but the rapyd framework sucks something awful
        if($showPassword)
        {
            $this->edit->add('password', 'password', 'password');
        }
        $this->edit->add('permissions', 'permissions', 'text')->rule('required');
        $this->edit->add('roles','Roles','checkboxgroup')->options(Role::pluck('name', 'id')->all());

        return $this->returnEditView();
    }
}
