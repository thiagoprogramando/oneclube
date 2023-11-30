<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class UserController extends Controller {

    public function index() {   

        if (isset(auth()->user()->id)) {
            return redirect()->route('dashboard');
        }
        return view('index');
    }

    public function login_action(Request $request) {

        $credentials = $request->only(['email', 'password']);
        $credentials['password'] = $credentials['password'];
        if (Auth::attempt($credentials)) {
            return redirect()->intended('dashboard');
        } else {
            return redirect()->back()->withErrors(['email' => 'As credenciais fornecidas são inválidas.']);
        }
    }

    public function register() {

        if (isset(auth()->user()->id)) {
            return redirect()->route('dashboard');
        }
        return view('register');
    }

    public function register_action(Request $request) {

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
            'tipo' => $request->tipo,
            'status' => 1,
        ];

        $user = User::create($attributes);

        return redirect()->back()->with('success', 'Usuário cadastrado com sucesso!');
    }

    public function perfil () {

        $dados = Auth::User();
        return view('dashboard.perfil', ['dados'=> $dados]);
    }

    public function update(Request $request) {

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
