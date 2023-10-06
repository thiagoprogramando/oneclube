<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RegisterController extends Controller
{
    public function register($codigo = null)
    {
        return view('register', ['codigo' => $codigo]);
    }

    public function register_action(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ], [
            'name.required' => 'O campo nome é obrigatório.',
            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.unique' => 'O CPF informado já está em uso.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.unique' => 'O e-mail informado já está em uso.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
        ]);

        $attributes = [
            'nome' => $request->name,
            'cpf' => $request->cpf,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'tipo' => 1,
        ];

        if (!empty($request->cupom)) {
            $attributes['cupom'] = $request->cupom;
        }

        $user = User::create($attributes);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function registerAssociado(Request $request)
    {
        return view('registerAssociado');
    }
}
