<?php

namespace App\Http\Controllers;

use App\Models\Notificacao;
use App\Models\User;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
{
    public function perfil ()
    {
        $dados = Auth::User();
        $users = auth()->user();

        return view('dashboard.perfil', ['dados'=> $dados]);

    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if ($request->filled('nome')) {
            $user->nome = $request->input('nome');
        }

        if ($request->filled('email')) {
            $user->email = $request->input('email');
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->back()->with('success', 'Perfil atualizado com sucesso.');
    }
}
