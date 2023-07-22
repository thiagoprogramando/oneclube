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

        $notfic = Notificacao::where(function ($query) use ($users) {
            if ($users->profile === 'admin') {
                $query->where(function ($query) {
                    $query->where('tipo', '!=', '')->orWhere('tipo', 0);
                });
            } else {
                $query->where(function ($query) use ($users) {
                    $query->where('tipo', 0)->orWhere('tipo', $users->id);
                });
            }
        })->get();

        return view('dashboard.perfil',['notfic'=> $notfic],['dados'=> $dados]);

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
            $user->passwordHash = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->back()->with('success', 'Perfil atualizado com sucesso.');
    }




}
