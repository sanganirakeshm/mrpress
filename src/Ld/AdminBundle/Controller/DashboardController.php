<?php

namespace Ld\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    public function indexAction()
    {
        return $this->render('LdAdminBundle:Dashboard:index.html.twig', array(
            // ...
        ));
    }

}
