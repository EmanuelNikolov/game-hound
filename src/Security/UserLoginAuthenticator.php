<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UserLoginAuthenticator extends AbstractFormLoginAuthenticator
{

    use TargetPathTrait;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $encoderFactory
     * @param RouterInterface $router
     */
    public function __construct(
      CsrfTokenManagerInterface $csrfTokenManager,
      EncoderFactoryInterface $encoderFactory,
      RouterInterface $router
    ) {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->encoderFactory = $encoderFactory;
        $this->router = $router;
    }

    public function supports(Request $request)
    {

        return $request->attributes->get('_route') === 'security_login'
          && $request->isMethod('POST');

    }

    public function getCredentials(Request $request)
    {
        $csrfToken = $request->request->get('login_token');

        if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken('authenticate',
            $csrfToken))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token.');
        }

        $credentials = [
          'username' => $request->request->get('username'),
          'password' => $request->request->get('password'),
        ];

        $request->getSession()->set(
          Security::LAST_USERNAME,
          $credentials['username']
        );

        return $credentials;
    }

    public function getUser(
      $credentials,
      UserProviderInterface $userProvider
    ) {
        return $userProvider->loadUserByUsername($credentials['username']);
    }

    public function checkCredentials(
      $credentials,
      UserInterface $user
    ) {
        $encoder = $this->encoderFactory->getEncoder($user);

        return $encoder->isPasswordValid(
          $user->getPassword(),
          $credentials['password'],
          $user->getSalt()
        );
    }

    public function onAuthenticationSuccess(
      Request $request,
      TokenInterface $token,
      $providerKey
    ): ?RedirectResponse {
        $targetPath = $this->getTargetPath($request->getSession(), 'main');

        if (!empty($targetPath)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('home'));
    }

    /**
     * Return the URL to the login page.
     *
     * @return string
     */
    protected function getLoginUrl(): string
    {
        return $this->router->generate("security_login");
    }

    public function start(
      Request $request,
      AuthenticationException $authException = null
    ) {
        $request
          ->getSession()
          ->getFlashBag()
          ->add('notice', 'You must be logged in to do that');

        return parent::start($request, $authException);
    }
}
