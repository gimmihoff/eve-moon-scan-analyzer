<?php

namespace Application\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class EveAuthService
{
    private const EVE_AUTH_URL = 'https://login.eveonline.com/oauth/authorize';
    private const EVE_TOKEN_URL = 'https://login.eveonline.com/oauth/token';
    private const EVE_VERIFY_URL = 'https://login.eveonline.com/oauth/verify';

    public function __construct(
        private readonly Client $httpClient = new Client()
    ) {
    }

    /**
     * Build the EVE SSO authorization URL.
     */
    public function buildAuthorizeUrl(string $redirectUri): string
    {
        $clientId = getenv('ESI_CLIENT_ID') ?: '';
        $state = bin2hex(random_bytes(16));
        $_SESSION['eve_oauth_state'] = $state;

        $params = [
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'client_id' => $clientId,
            'scope' => 'esi-wallet.read_character.wallet.v1 esi-universe.read_structures.v1 esi-markets.read_market_prices.v1',
            'state' => $state,
        ];

        return self::EVE_AUTH_URL . '?' . http_build_query($params);
    }

    /**
     * Exchange authorization code for OAuth tokens.
     *
     * @return array<string, mixed>
     */
    public function exchangeCodeForToken(string $code, string $redirectUri): array
    {
        $clientId = getenv('ESI_CLIENT_ID') ?: '';
        $secretKey = getenv('ESI_SECRET_KEY') ?: '';

        if ($clientId === '' || $secretKey === '') {
            throw new \RuntimeException('EVE SSO is not configured. Set ESI_CLIENT_ID and ESI_SECRET_KEY.');
        }

        try {
            $response = $this->httpClient->post(self::EVE_TOKEN_URL, [
                'auth' => [$clientId, $secretKey],
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $redirectUri,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

            if (!isset($body['access_token'])) {
                throw new \RuntimeException('Missing access_token in EVE SSO response.');
            }

            return $body;
        } catch (GuzzleException | \JsonException $e) {
            throw new \RuntimeException('Failed to exchange EVE SSO code: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Verify the access token and return character info.
     *
     * @return array<string, mixed>
     */
    public function verifyAccessToken(string $accessToken): array
    {
        try {
            $response = $this->httpClient->get(self::EVE_VERIFY_URL, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

            if (!isset($body['CharacterID'], $body['CharacterName'])) {
                throw new \RuntimeException('Invalid EVE SSO verification response.');
            }

            return [
                'character_id' => (int) $body['CharacterID'],
                'character_name' => $body['CharacterName'],
                'expires_on' => $body['ExpiresOn'] ?? null,
                'scopes' => $body['Scopes'] ?? '',
                'token_type' => $body['TokenType'] ?? 'Bearer',
            ];
        } catch (GuzzleException | \JsonException $e) {
            throw new \RuntimeException('Failed to verify EVE SSO token: ' . $e->getMessage(), 0, $e);
        }
    }

    public function isValidState(string $state): bool
    {
        $expected = $_SESSION['eve_oauth_state'] ?? null;
        unset($_SESSION['eve_oauth_state']);

        return $expected !== null && hash_equals($expected, $state);
    }

    public function setAuthenticatedUser(array $user): void
    {
        $_SESSION['eve_user'] = $user;
    }

    public function getAuthenticatedUser(): ?array
    {
        return $_SESSION['eve_user'] ?? null;
    }

    public function clearAuthenticatedUser(): void
    {
        unset($_SESSION['eve_user']);
    }
}
