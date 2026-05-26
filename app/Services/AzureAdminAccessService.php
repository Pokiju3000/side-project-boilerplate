<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class AzureAdminAccessService
{
    public function __construct(
        private HttpFactory $http,
    ) {
    }

    public function userIsAdmin(User $user): bool
    {
        return $this->userIsInConfiguredGroup($user, 'admin_group_id');
    }

    public function userCanAccessPlatform(User $user): bool
    {
        return $this->userIsInConfiguredGroup($user, 'admin_group_id')
            || $this->userIsInConfiguredGroup($user, 'user_group_id');
    }

    private function userIsInConfiguredGroup(User $user, string $configKey): bool
    {
        $guid = trim((string) $user->guid);
        $groupId = trim((string) config("services.azure.{$configKey}"));

        if ($guid === '' || $groupId === '') {
            return false;
        }

        return Cache::remember(
            "azure_group_access.{$configKey}.{$guid}.{$groupId}",
            now()->addMinutes(15),
            fn (): bool => $this->graphUserIsMember($user, $groupId),
        );
    }

    private function graphUserIsMember(User $user, string $groupId): bool
    {
        foreach ($this->userIdentifiers($user) as $identifier) {
            $response = $this->http
                ->acceptJson()
                ->asJson()
                ->withToken($this->accessToken())
                ->post($this->graphUrl("/directoryObjects/{$identifier}/checkMemberGroups"), [
                    'groupIds' => [$groupId],
                ]);

            if ($response->successful() && in_array($groupId, $response->json('value', []), true)) {
                return true;
            }

            Log::info('Vérification Azure checkMemberGroups sans correspondance.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'identifier' => $identifier,
                'group_id' => $groupId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }

        return false;
    }

    /**
     * @return list<string>
     */
    private function userIdentifiers(User $user): array
    {
        return array_values(array_unique(array_filter([
            trim((string) $user->guid),
            trim((string) $user->email),
        ])));
    }

    public function accessToken(): string
    {
        return Cache::remember('azure_admin_access.token', now()->addMinutes(45), function (): string {
            $tenantId = trim((string) config('services.azure.tenant'));
            $clientId = trim((string) config('services.azure.client_id'));
            $clientSecret = (string) config('services.azure.client_secret');

            if ($tenantId === '' || $clientId === '' || $clientSecret === '') {
                throw new RuntimeException('Les variables AZURE_TENANT_ID, AZURE_CLIENT_ID et AZURE_CLIENT_SECRET sont requises.');
            }

            $response = $this->http
                ->asForm()
                ->acceptJson()
                ->timeout(20)
                ->post("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token", [
                    'grant_type' => 'client_credentials',
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'scope' => 'https://graph.microsoft.com/.default',
                ])
                ->throw();

            $token = (string) ($response->json('access_token') ?? '');

            if ($token === '') {
                throw new RuntimeException('Aucun access token Azure admin n\'a été retourné.');
            }

            return $token;
        });
    }

    private function graphUrl(string $path): string
    {
        return rtrim((string) config('services.azure.graph_base_url', 'https://graph.microsoft.com/v1.0'), '/').'/'.ltrim($path, '/');
    }
}
