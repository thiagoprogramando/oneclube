<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User;
use App\Models\Invoice;
use Carbon\Carbon;

class GenerateInvoices extends Command {

    protected $signature = 'app:generate-invoices';

    protected $description = 'Command description';

    public function handle() {
        
        $today = Carbon::today();

        $users = User::whereMonth('created_at', $today->month)->whereDay('created_at', $today->day)->get();

        foreach ($users as $user) {
            $invoice = new Invoice();
            $invoice->idUser        = $user->id;
            $invoice->name          = "Mensalidade";
            $invoice->description   = "Assinatura Mensal G7";
            $invoice->value         = 99;
            $invoice->commission    = 20.00;
            $invoice->type          = 2;
            $invoice->status        = "PENDING_PAY";
            $invoice->dueDate       = $today;
            $invoice->save();
        }

        $this->info('Invoices generated successfully.');
    }
}
