<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AzureAdminAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;
use RuntimeException;

class AzureAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function redirectToAzure(): RedirectResponse
    {
        return Socialite::driver('azure')
            ->redirectUrl((string) config('services.azure.redirect'))
            ->redirect();
    }

    public function handleAzureCallback(Request $request, AzureAdminAccessService $accessService): RedirectResponse
    {
        try {
            if (! $request->has('code')) {
                throw new RuntimeException("Le code d'autorisation Azure est introuvable.");
            }

            $azureUser = Socialite::driver('azure')
                ->redirectUrl((string) config('services.azure.redirect'))
                ->user();

            $email = (string) ($azureUser->getEmail() ?? '');
            $guid = (string) ($azureUser->getId() ?? '');

            if ($email === '') {
                throw new RuntimeException("Azure n'a retourné aucune adresse courriel exploitable.");
            }

            /** @var User $user */
            $user = User::query()->firstOrNew(['email' => $email]);
            $user->forceFill([
                'name' => $azureUser->getName() ?: Str::before($email, '@'),
                'guid' => $guid !== '' ? $guid : $user->guid,
                'password' => $user->exists ? $user->password : Hash::make(Str::random(32)),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
            $user->save();

            try {
                $canAccessPlatform = $accessService->userCanAccessPlatform($user);
            } catch (\Throwable $accessException) {
                Log::warning('Vérification des groupes Azure impossible pendant la connexion.', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'guid' => $guid,
                    'error' => $accessException->getMessage(),
                ]);

                return redirect()
                    ->route('login')
                    ->withErrors([
                        'azure_access' => 'Votre compte Microsoft est authentifié, mais la verification des groupes autorisés est temporairement impossible. Reessayez plus tard ou contactez un administrateur.',
                    ]);
            }

            if (! $canAccessPlatform) {
                Log::warning('Connexion Azure refusée: utilisateur hors groupes autorisés.', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'guid' => $guid,
                ]);

                return redirect()
                    ->route('login')
                    ->withErrors([
                        'azure_access' => "Votre compte Microsoft est authentifié, mais il n'est pas membre d'un groupe autorisé pour cette plateforme. Contactez un administrateur si l'accès est requis.",
                    ]);
            }

            Auth::login($user, remember: true);
            $request->session()->regenerate();

            Log::info('Connexion Azure réussie.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'guid' => $guid,
            ]);

            return redirect()->route('dashboard');
        } catch (\Throwable $exception) {
            Log::error('Connexion Azure échouée.', [
                'error' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('login')
                ->withErrors(['azure' => 'Erreur lors de la connexion Azure : '.$exception->getMessage()]);
        }
    }

    public function loginAsDemoUser(Request $request): RedirectResponse
    {
        abort_unless(config('services.demo_access.enabled'), 404);

        /** @var User $user */
        $user = User::query()->firstOrNew([
            'email' => (string) config('services.demo_access.email', 'demo@example.test'),
        ]);

        $user->forceFill([
            'name' => (string) config('services.demo_access.name', 'Demo User'),
            'guid' => $user->guid ?: 'demo-user',
            'password' => $user->password ?: Hash::make(Str::random(32)),
            'email_verified_at' => $user->email_verified_at ?? now(),
        ]);
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('demo_access', true);

        return redirect()->route('dashboard')->with('status', 'Session demo active.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Vous êtes maintenant déconnecté.');
    }
}
