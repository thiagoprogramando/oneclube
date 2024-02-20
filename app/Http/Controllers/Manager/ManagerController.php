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
use App\Models\Lista;
use App\Models\Mkt;

class ManagerController extends Controller {

    public function dashboard () {

        $user = auth()->user();

        if($user->term != 1) {
            return redirect()->route('profile')->with('error', 'Assine o termo de Responsabilidade!');
        }

        if($user->status == 2) {
            return redirect()->route('profile')->with('error', 'Você tem documentos pendentes, envie-os para obter todas às funcionalidades!');
        }

        $sales = Sale::where('id_vendedor', $user->id)->orderBy('created_at', 'desc')->limit(20)->get();
        if($user->apiKey != null) {
            $assas = new AssasController();
            $balance = $assas->balance();
            if($balance == 0 || $balance > 0) {
                $statistics  = $assas->statistics();
                $accumulated = $assas->accumulated();
            }
        } else {
            $balance = 0;
            $statistics = 0;
        }

        $lista = Lista::where('dateEnd', '>', Carbon::now())->orderBy('dateEnd', 'asc')->first();
        
        return view('dashboard.index', [
            'user'          => $user,
            'sales'         => $sales,
            'balance'       => $balance,
            'statistics'    => $statistics,
            'accumulated'   => $accumulated,
            'lista'         => $lista,
        ]);
    }

    public function validation () {

        $user = auth()->user();
        if($user->term != 1) {
            return redirect()->route('profile')->with('error', 'Assine o termo de Responsabilidade!');
        }
        return view('dashboard.users.validation', [
            'user' => $user,
        ]);
    }
    
    public function createUser(Request $request) {

        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'name'            => [ 'required', 'string', 'max:255'],
            'email'           => [ 'required', 'email',  'max:255', 'unique:users'],
            'cpfcnpj'         => [ 'required', 'string', 'max:14', 'unique:users'],
            'postalCode'      => [ 'required'],
            'address'         => [ 'required'],
            'addressNumber'   => [ 'required'],
            'complement'      => [ 'required'],
            'province'        => [ 'required'],
            'city'            => [ 'required'],
            'state'           => [ 'required'],
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Analise às informações, foi encontrado um erro!');
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
            'lider'         => $user->id,
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
            $invoice->value = 99;
            $invoice->commission = 20;
            $invoice->status = 'PENDING_PAY';
            $invoice->save();

            $link = "https://grupo7assessoria.com/";
            $message = "✅✅ Prezado Parceiro, bem-vindo(a) G7 - Assessoria. \r\n Estamos enviando um link para acesso ao nosso Sistema de Parceiros! \r\n \r\n Para acessar, utilize seu Email: ".$user->email."\r\n E como senha: SEU CPF (sem pontos ou traços) \r\n 👇🏼👇🏼👇🏼👇🏼👇🏼";
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

    public function listMkt() {
        
        $mtks = Mkt::all();
        return view('dashboard.mkt.list', ['mkts' => $mtks]);
    }

    public function mkt() {
        
        $mtks = Mkt::all();
        return view('dashboard.mkt.mkt', ['mkts' => $mtks]);
    }

    public function createMkt(Request $request) {

        $arquivo = $request->file('arquivo');
        $mkt = new Mkt();
        $mkt->id_product    = 1;
        $mkt->title         = $request->title;
        $mkt->description   = $request->description;
        $mkt->file          = $arquivo->store('mkt');
        if($mkt->save()) {
            return redirect()->back()->with('success', 'Material cadastro com Sucesso!');
        }

        return redirect()->back()->with('error', 'Houve um problema, tente novamente mais tarde!');
    }

    public function deleteMkt(Request $request) {

        $mkt = Mkt::find($request->id);
        if($mkt) {

            $mkt->delete();
            return redirect()->back()->with('success', 'Material excluída com Sucesso!');
        }

        return redirect()->back()->with('error', 'Houve um problema, tente novamente mais tarde!');
    }

    public function lista() {

        $listas = Lista::all();
        return view('dashboard.lista.lista', ['listas' => $listas]);
    }

    public function createLista(Request $request) {

        $lista = new Lista();
        $lista->name      = $request->name;
        $lista->dateEnd   = $request->dateEnd;
        if($lista->save()) {
            return redirect()->back()->with('success', 'Lista cadastrada com Sucesso!');
        }

        return redirect()->back()->with('error', 'Houve um problema, tente novamente mais tarde!');
    }

    public function deleteLista(Request $request) {

        $lista = Lista::find($request->id);
        if($lista) {

            $lista->delete();
            return redirect()->back()->with('success', 'Lista excluída com Sucesso!');
        }

        return redirect()->back()->with('error', 'Houve um problema, tente novamente mais tarde!');
    }
}
