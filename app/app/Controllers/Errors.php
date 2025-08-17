<?php

namespace App\Controllers;

class Errors extends BaseController
{
    public function forbidden()
    {
        return view('errors/forbidden');
    }
}