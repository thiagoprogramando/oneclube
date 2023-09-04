<?php

namespace App\Http\Controllers;

use App\Models\Notificacao;
use App\Models\User;
use App\Models\Vendas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function dashboard (){
        $users = auth()->user();

        $vendas = Vendas::where('id_vendedor', $users->id)->limit(15)->get();

        return view('dashboard.index', [
            'users' => $users,
            'vendas' => $vendas
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
