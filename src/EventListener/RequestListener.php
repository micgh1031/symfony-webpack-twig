<?php

// src/EventListener/RequestListener.php

namespace App\EventListener;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RequestListener
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $uri = $request->getRequestUri();

        if ('/admin' == substr($uri, 0, 6)) {
            $superUsersArray = explode(',', (getenv('SUPERUSERS')));

            if (!$superUsersArray) {
                throw new RuntimeException('Missing required superuser configuration');
            }
            $user = $this->tokenStorage->getToken()->getUser();

            if (!in_array($user->getUsername(), $superUsersArray)) {
                $event->setResponse(new Response('Unauthorized!', 401));
            }
        }
    }
}
