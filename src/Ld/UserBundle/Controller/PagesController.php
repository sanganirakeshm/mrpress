<?php

namespace Ld\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PagesController extends Controller
{
    public function homeAction()
    {
        return $this->render('LdUserBundle:Pages:home.html.twig', array(
            // ...
        ));
    }

}
