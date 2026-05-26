<?php

namespace App\Http\Middleware;

use App\Services\AzureAdminAccessService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EnsureAzureAdmin
{
    public function __construct(
        private AzureAdminAccessService $adminAccess,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

        if (
            (bool) $request->session()->get('demo_access')
            && config('services.demo_access.enabled')
            && config('services.demo_access.admin')
        ) {
            return $next($request);
        }

        try {
            if ($this->adminAccess->userIsAdmin($user)) {
                return $next($request);
            }
        } catch (Throwable $exception) {
            Log::warning('Verification du groupe Azure admin echouee.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);
        }

        abort(403, "Vous n'avez pas acces a cette page d'administration.");
    }
}
