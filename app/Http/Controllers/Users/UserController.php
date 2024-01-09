<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gatway\AssasController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class UserController extends Controller {

    public function index() {   

        if (isset(auth()->user()->id)) {
            return redirect()->route('dashboard');
        }
        return view('index');
    }

    public function login(Request $request) {

        $credentials = $request->only(['email', 'password']);
        $credentials['password'] = $credentials['password'];
        if (Auth::attempt($credentials)) {
            return redirect()->intended('dashboard');
        } else {
            return redirect()->back()->withErrors(['email' => 'As credenciais fornecidas são inválidas.']);
        }
    }
    
    public function profile() {
        
        $dados = Auth::User();

        $myAccount = new AssasController();
        $myDocuments = $myAccount->myAccount();

        return view('dashboard.users.profile', ['dados'=> $dados, 'myDocuments' => $myDocuments]);
    }

    public function profileUpdate(Request $request) {

        $user = Auth::user();
        $user = User::where('id', $user->id)->first();

        if ($request->filled('name')) {
            $user->name = $request->input('name');
        }

        if ($request->filled('email')) {
            $user->email = $request->input('email');
        }

        if ($request->filled('mobilePhone')) {
            $user->mobilePhone = $request->input('mobilePhone');
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();
        return redirect()->back()->with('success', 'Perfil atualizado com sucesso.');
    }

    public function logout() {

        Auth::logout();
        return redirect()->route('login');
    }

}
