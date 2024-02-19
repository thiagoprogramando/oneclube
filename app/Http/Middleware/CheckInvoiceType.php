<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;

class CheckInvoiceType {

    public function handle(Request $request, Closure $next): Response {

        if (Auth::check()) {
            $user = Auth::user();

            $hasInvoiceTypeOne = Invoice::where('idUser', $user->id)->where('type', 1)->where('status', 'PENDING_PAY')->exists();
            $allowedRoutes = ['logout', 'invoiceCreate', 'perfil'];
            
            if ($hasInvoiceTypeOne && !$request->is('invoices') && !in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('invoices');
            }

            if($user->term != 1) {
                return redirect()->route('perfil');
            }

            // if($user->status == 2) {
            //     return redirect()->route('validation');
            // }
        }

        return $next($request);
    }
}
