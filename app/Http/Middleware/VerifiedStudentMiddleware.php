<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifiedStudentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isVerifiedStudent()) {
            return redirect()->route('kyc.show')->with('warning', 'Complete student verification to trade on DelsuMart.');
        }

        return $next($request);
    }
}
