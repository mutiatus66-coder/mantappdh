<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HakAksesMiddleware
{
    public function handle(Request $request, Closure $next, string ...$hakAkses)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (in_array(auth()->user()->hak_akses, $hakAkses)) {
            return $next($request);
        }

        abort(403, 'Akses ditolak.');
    }
}