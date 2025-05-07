<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TemplateController extends Controller
{
    function index(){
        return view('frontend/home');
    }
}
