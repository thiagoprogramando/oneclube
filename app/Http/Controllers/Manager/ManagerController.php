<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gatway\AssasController;
use App\Http\Controllers\Notification\WhatsAppController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;

use App\Models\User;
use App\Models\Sale;
use App\Models\Address;
use App\Models\Invoice;

class ManagerController extends Controller {

    public function dashboard () {

        $user = auth()->user();

        $sales = Sale::where('id_vendedor', $user->id)->orderBy('created_at', 'desc')->limit(20)->get();

        if($user->apiKey != null) {
            $assas = new AssasController();
            $balance = $assas->balance();
            if($balance == 0 || $balance > 0) {
                $statistics = $assas->statistics();
            }
        } else {
            $balance = 0;
            $statistics = 0;
        }
        

        return view('dashboard.index', [
            'user'          => $user,
            'sales'         => $sales,
            'balance'       => $balance,
            'statistics'    => $statistics
        ]);
    }

    public function validation () {

        $user = auth()->user();
        return view('dashboard.users.validation', [
            'user' => $user,
        ]);
    }
    
    public function createUser(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [ 'required', 'email', 'max:255', 'unique:users'],
            'cpfcnpj' => [ 'required', 'string', 'max:14', 'unique:users'],
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Email/CpfCnpj já cadastrado!');
        }

        $attributes = [
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => bcrypt(str_replace(['.', '-', '/'], '', $request->cpfcnpj)),
            'cpfcnpj'       => str_replace(['.', '-', '/'], '', $request->cpfcnpj),
            'birthDate'     => Carbon::createFromFormat('d-m-Y', $request->birthDate)->format('Y-m-d'),
            'mobilePhone'   => str_replace(['.', '-', '(', ')', ' '], '', $request->mobilePhone),
            'address'       => $request->postalCode.' - '.$request->address.', '.$request->addressNumber.'/'.$request->city,
            'companyType'   => $request->companyType,
            'type'          => 2,
            'status'        => 3,
        ];

        $user = User::create($attributes);
        if($user) {
            $address = new Address();
            $address->idUser        = $user->id;
            $address->postalCode    = $request->postalCode;
            $address->address       = $request->address;
            $address->addressNumber = $request->addressNumber;
            $address->complement    = $request->complement;
            $address->province      = $request->province;
            $address->city          = $request->city;
            $address->state         = $request->state;
            $address->save();

            $invoice = new Invoice();
            $invoice->idUser = $user->id;
            $invoice->name = 'Liberação de Acesso';
            $invoice->description = 'Voucher para liberação e cadastro no Sistema';
            $invoice->type = 1;
            $invoice->value = 59;
            $invoice->status = 'PENDING_PAY';
            $invoice->save();

            $link = "https://grupo7assessoria.com.br/";
            $message = "Prezado Cliente, bem-vindo(a) G7 - Assessoria. Estamos enviando um link para acesso ao nosso Sistema de Parceiros! \r\n \r\n \r\n Para acessar, utilize seu Email: ".$user->email."\r\n E como senha: SEU CPF \r\n \r\n";
            $notification = new WhatsAppController();
            $sendLink = $notification->sendLink($user->mobilePhone, $link, $message);
            if($sendLink) {
                return redirect()->back()->with('success', 'Usuário cadastrado com sucesso! Foram enviados os dados via WhatsApp.');
            }
            
            return redirect()->back()->with('success', 'Usuário cadastrado com sucesso! Não foi possível enviar os dados via WhatsApp.');
        }

        return redirect()->back()->with('error', 'Houve um problema, tente novamente mais tarde!');
    }

    public function listUsers() {

        return view('dashboard.manager.users', ['users' => User::all()]);
    }

    public function invoices($id = null) {

        if($id) {
            $invoices = Invoice::where('idUser', $id)->where('type', 3)->get();
            return view('dashboard.payments.invoice', ['invoices' => $invoices]);
        }
        
        $invoices = Invoice::where('idUser', Auth::id())->where('status', 'PENDING_PAY')->get();
        return view('dashboard.payments.invoice', ['invoices' => $invoices]);
    }
}
