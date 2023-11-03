<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiException
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse|array
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse|array
    {
        try {

            return $next($request);

        } catch (\Throwable $e) {

            return [
                'success' => false,
                'data' => [
                    'message' => $e->getMessage()
                ]
            ];
        }
    }
}
