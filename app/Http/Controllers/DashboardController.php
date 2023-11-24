<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Vendas;
use App\Models\VendaLance;

class DashboardController extends Controller
{
    public function dashboard (){
        $user = auth()->user();

        $vendas = Vendas::where('id_vendedor', $user->id)->limit(15)->get();

        return view('dashboard.index', [
            'user' => $user,
            'vendas' => $vendas,
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
