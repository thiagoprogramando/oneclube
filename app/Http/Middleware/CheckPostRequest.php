<?php

namespace App\Http\Middleware;

use Closure;

class CheckPostRequest
{
    public function handle($request, Closure $next)
    {
        if ($request->method() === 'GET') {
            return redirect()->route('cupom');
        }
    
        return redirect()->route('cupom');
    }
    
}
