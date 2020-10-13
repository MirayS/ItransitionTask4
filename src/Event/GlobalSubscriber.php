<?php


namespace App\Event;


use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GlobalSubscriber implements EventSubscriberInterface
{

    /** @var RouterInterface $router */
    private $router;

    /** @var TokenStorageInterface $tokenStorage */
    private $tokenStorage;

    /**
     * @param RouterInterface $router
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(RouterInterface $router, TokenStorageInterface $tokenStorage)
    {
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if(!$event->isMasterRequest())
            return;

        $this->isUserBlocked($event);
    }

    /**
     * Проверка блокировки пользователя
     * @param RequestEvent $event
     */
    private function isUserBlocked(RequestEvent $event)
    {
        if (empty($this->tokenStorage->getToken()) || !$this->tokenStorage->getToken()->getUser() instanceof User) return;

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof User)
            if (!$user->getIsEnabled())
                $event->setResponse(new RedirectResponse($this->router->generate('app_logout')));


    }

    public static function getSubscribedEvents()
    {
        return [
            // Правка от @Flying
            KernelEvents::REQUEST => 'onKernelRequest'
            // Старый вариант - RequestEvent::class => 'onKernelRequest'
        ];
    }
}