<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan pengguna sudah login DAN memiliki kolom 'admin' bernilai true
        if (Auth::check() && Auth::user()->admin) {
            return $next($request);
        }

        // Jika bukan admin, arahkan pengguna ke home biasa atau halaman home
        // Atau Anda bisa mengembalikan response 403 Forbidden
        return redirect('/')->with('error', 'Anda tidak memiliki akses sebagai Admin.');
        
        // Alternatif untuk Error 403
        // abort(403, 'Akses Dilarang.');
    }
}