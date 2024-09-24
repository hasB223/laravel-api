<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthController;

class CheckTokens
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Check if 'mdi-token' and 'ei-session' are in the session
        $authController = new AuthController();

        if (!Session::has('mdi-token')) {
            $authController->generateMdiToken();
        }

        if (!Session::has('ei-session')) {
            $authController->generateEiSession();
        }

        // Continue processing the request
        return $next($request);
    }
}
