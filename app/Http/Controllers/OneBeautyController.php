<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OneBeautyController extends Controller
{
    public function index($id) {
        return view('franquias.onebeauty', ['id' => $id]);
    }
}
