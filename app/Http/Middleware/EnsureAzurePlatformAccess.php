<?php

namespace App\Http\Middleware;

use App\Services\AzureAdminAccessService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EnsureAzurePlatformAccess
{
    public function __construct(
        private AzureAdminAccessService $accessService,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

        if ((bool) $request->session()->get('demo_access') && config('services.demo_access.enabled')) {
            return $next($request);
        }

        try {
            if ($this->accessService->userCanAccessPlatform($user)) {
                return $next($request);
            }
        } catch (Throwable $exception) {
            Log::warning('Verification des groupes Azure de la plateforme échouée.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);
        }

        Log::warning('Acces plateforme refusé: utilisateur hors groupes Azure autorisés.', [
            'user_id' => $user->id,
            'email' => $user->email,
            'guid' => $user->guid,
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors([
                'azure_access' => "Votre compte Microsoft est authentifié, mais il n'est pas membre d'un groupe autorisé pour cette plateforme. Contactez un administrateur si l'accès est requis.",
            ]);
    }
}
