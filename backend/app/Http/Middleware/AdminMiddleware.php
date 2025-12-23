<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // TẠM THỜI BỎ CHECK AUTH ĐỂ TEST VỚI JMETER - NHỚ BẬT LẠI SAU KHI TEST XONG!
        // if (!$request->user() || $request->user()->role !== 'admin') {
        //     return response()->json([
        //         'message' => 'Forbidden'
        //     ], 403);
        // }

        return $next($request);
    }

}
