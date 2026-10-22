<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    public function index()
    {
        return view('frontend.account.index');
    }
}
