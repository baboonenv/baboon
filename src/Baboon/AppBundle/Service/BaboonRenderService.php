<?php

namespace Baboon\AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Common methods for journal
 */
class BaboonRenderService
{
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var Session
     */
    private $session;

    /**
     * @var Router
     */
    private $router;


    /** @var RequestStack */
    private $requestStack;

    /**
     * @param EntityManager $em
     * @param Session $session
     * @param Router $router
     * @param TokenStorageInterface $tokenStorage
     * @param RequestStack $requestStack
     * @param $defaultPublisherSlug
     */
    public function __construct(
        EntityManager $em,
        Session $session,
        Router $router,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        $defaultPublisherSlug
    )
    {
        $this->session = $session;
        $this->em = $em;
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
        $this->defaultPublisherSlug = $defaultPublisherSlug;
    }
}