<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OnePositiveController extends Controller
{
    public function index($id) {
        return view('franquias.onepositive', ['id' => $id]);
    }
}
