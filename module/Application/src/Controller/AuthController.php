<?php

namespace Application\Controller;

use Application\Service\EveAuthService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\Http\PhpEnvironment\Response;

class AuthController extends AbstractActionController
{
    public function __construct(
        private readonly EveAuthService $eveAuthService
    ) {
    }

    /**
     * Redirect the user to EVE SSO login.
     */
    public function loginAction(): Response
    {
        $redirectUri = getenv('ESI_CALLBACK_URL') ?: 'http://localhost:8080/auth/callback';
        $authorizeUrl = $this->eveAuthService->buildAuthorizeUrl($redirectUri);

        return $this->redirect()->toUrl($authorizeUrl);
    }

    /**
     * Handle EVE SSO callback.
     */
    public function callbackAction(): Response
    {
        $request = $this->getRequest();
        $query = $request->getQuery();

        $error = $query->get('error');
        if ($error) {
            $_SESSION['auth_error'] = $error;
            return $this->redirect()->toRoute('home');
        }

        $code = $query->get('code');
        $state = $query->get('state');

        if (!$code || !$state || !$this->eveAuthService->isValidState($state)) {
            $_SESSION['auth_error'] = 'Invalid EVE SSO callback state.';
            return $this->redirect()->toRoute('home');
        }

        try {
            $redirectUri = getenv('ESI_CALLBACK_URL') ?: 'http://localhost:8080/auth/callback';
            $tokenData = $this->eveAuthService->exchangeCodeForToken($code, $redirectUri);
            $user = $this->eveAuthService->verifyAccessToken($tokenData['access_token']);

            $this->eveAuthService->setAuthenticatedUser([
                'character_id' => $user['character_id'],
                'character_name' => $user['character_name'],
                'expires_on' => $user['expires_on'],
                'scopes' => $user['scopes'],
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? null,
            ]);

            return $this->redirect()->toRoute('home');
        } catch (\RuntimeException $e) {
            $_SESSION['auth_error'] = $e->getMessage();
            return $this->redirect()->toRoute('home');
        }
    }

    /**
     * Logout and clear the session.
     */
    public function logoutAction(): Response
    {
        $this->eveAuthService->clearAuthenticatedUser();
        return $this->redirect()->toRoute('home');
    }

    /**
     * Return current auth status as JSON.
     */
    public function statusAction(): JsonModel
    {
        $user = $this->eveAuthService->getAuthenticatedUser();

        return new JsonModel([
            'authenticated' => $user !== null,
            'user' => $user,
        ]);
    }
}
