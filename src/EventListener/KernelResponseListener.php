<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use App\Service\BalancerService;

class KernelResponseListener
{
    private BalancerService $balancerService;

    public function __construct(BalancerService $balancerService)
    {
        $this->balancerService = $balancerService;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $method = $event->getRequest()->getMethod();
        $response = $event->getResponse();

        if ($response instanceof JsonResponse && in_array($method, ['POST', 'DELETE'])) {
            $this->balancerService->balance();
        }
    }
}
