<?php

namespace App\Http\Controllers;

use App\Http\Models\AmoCRM;
use App\RMVC\View\View;

class MainController
{
    public function index()
    {
        return View::view('main.index');
    }

    public function store()
    {
        if(trim($_POST['name']) == null || trim($_POST['email']) == null || trim($_POST['phone']) == null || trim($_POST['price']) == null)
        {
            $error = 'Не все поля были заполнены';
            return View::view('main.index', compact('error'));
        } else {
            $amoCRM = new AmoCRM();
            $amoCRM->store($_POST);
        }
    }
}