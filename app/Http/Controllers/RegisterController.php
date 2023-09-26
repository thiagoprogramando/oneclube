<?php

namespace App\Http\Controllers;

use App\Mail\Forgout;
use App\Models\User;
use App\Models\Token;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        if (isset(auth()->user()->id)) {
            return redirect()->route('dashboard');
        }
        return view('register');
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
            'status' => 1,
        ];

        if (isset($request->login)) {
            $attributes['login'] = $request->login;
        }

        if (isset($request->id_assas)) {
            $attributes['id_assas'] = $request->id_assas;
        }

        if (isset($request->tipo)) {
            $attributes['tipo'] = $request->tipo;
        }

        $user = User::create($attributes);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function registerAssociado(Request $request)
    {
        return view('registerAssociado');
    }

    public function forgout(Request $request)
    {
        if (isset(auth()->user()->id)) {
            return redirect()->route('dashboard');
        }
        return view('forgout');
    }

    public function forgout_action(Request $request) {
        $user = User::where('email', $request->input('email'))->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Conta não encontrada com Email informado!');
        }

        $codigoAleatorio = mt_rand(1000, 9999);
        $sent = Mail::to($user->email, 'One Clube')->send(new Forgout([
            'fromName' => $user->nome,
            'fromEmail' => env('MAIL_USERNAME'),
            'subject' => 'Recuperação de Dados!',
            'message' => $codigoAleatorio,
        ]));

        if ($sent) {
            Token::create([
                'email' => $user->email,
                'token' => $codigoAleatorio,
            ]);

            return view('forgout', compact('codigoAleatorio'));
        }
    }

    public function forgout_token(Request $request) {
        $token = Token::where('token', $request->input('token'))->first();
        if($token) {
            $user = User::where('email', $token->email)->first();
            $user->password = Hash::make($request->input('senha'));
            $user->save();

            $token->delete();

            return redirect()->route('login')->with('success', 'Senha atualizada com Sucesso! Acesse sua conta.');
        }

        return redirect()->back()->with('error', 'Código inválido!');
    }
}
