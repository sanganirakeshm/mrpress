<?php

namespace Dhi\UserBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MaintenanceModeHandler {

    private $doctrine;
    private $router;
    private $container;

    public function __construct($doctrine, $router, $container) {
        $this->doctrine = $doctrine;
        $this->router = $router;
        $this->container = $container;
    }

    public function onCheckMaintenance(GetResponseEvent $event) {
        $request = $event->getRequest();
        $route = $this->container->get('request')->get('_route');
        $adminRoutes = array('dhi_service_plan','dhi_user_credit','dhi_user_account');
        $objMaintenance = $this->doctrine->getRepository('DhiAdminBundle:Setting')->findOneByName('maintenance_mode');

        if ($objMaintenance->getValue() == 'True') {

            if (in_array($route, $adminRoutes)) {

                $event->setResponse(new RedirectResponse($this->router->generate('dhi_user_plan_maintenance')));
            }
        }
    }

}
