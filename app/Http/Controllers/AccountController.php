<?php

namespace App\Http\Controllers;

class AccountController extends Controller
{
    public function pending()
    {
        return view('account.pending');
    }
}
