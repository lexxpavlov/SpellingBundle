<?php

namespace Lexxpavlov\SpellingBundle\Feature;

use Lexxpavlov\SpellingBundle\Event\SpellingNewErrorEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthCheck
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;

    /** @var string */
    private $role;

    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker, $role)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->role = $role;
    }

    public function onNewError(SpellingNewErrorEvent $event)
    {
        if (!$this->role) {
            return;
        }

        $isGranted = false;
        $token = $this->tokenStorage->getToken();
        if (null !== $token) {
            $user = $token->getUser();
            if (is_object($user) && $this->authorizationChecker->isGranted($this->role, $user)) {
                $isGranted = true;
            }
        }
        if (!$isGranted) {
            $event->addError('access_denied');
        }
    }
}